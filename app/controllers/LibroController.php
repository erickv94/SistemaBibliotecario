<?php

use App\Models\Libros;
use App\Models\Materialesbibliograficos;
use App\Models\Bibliotecarios;
use App\Models\Unidades;
use App\Models\Categorias;
use App\Models\Subcategorias;
use App\Models\Autores;
use App\Models\MaterialesAutores;
use Phalcon\Http\Response;
use App\Models\Users;
use App\Validations\ValidacionLibro;

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../../');
$dotenv->load();

class jsonDataO {
    public $file = "";
    public $api_key = "";
    public $timestamp = "";
    public $signature = "";
}


class LibroController extends \Phalcon\Mvc\Controller
{
    protected $idSesion;
    protected $user;
    protected $rol;
    protected $biblioteca;

    //esta ruta se ejecuta antes de cada funcion en el controlador
    public function initialize()
    {
        
        if($this->session->has('id'))
        {  

            //crea la busqueda si existe id
        $this->idSesion = $this->session->get('id');
        $this->user=Users::findFirst($this->idSesion);
        $this->rol=$this->user->roles->nombre;
        
        // redirige si el rol cargado es diferente
            switch($this->rol){
                case 'Administrador': 
                case 'Prestamista':
                $this->response->redirect('/401');
                break;
                case 'Bibliotecario':
                $this->biblioteca=$this->user->bibliotecarios[0]->bibliotecas; 
                $this->view->biblioteca=$this->biblioteca;
                break;
                            }
        }
        else
        {
            $this->response->redirect('/401');
        }
  

    }
    public function indexAction()
    {
        $idusuario = $this->session->get('id');
        $bibliotecario = Bibliotecarios::findFirst([
            'columns'    => 'idbiblioteca',
            'conditions' => 'iduser = ?1',
            'bind'       => [
                    1 => $idusuario,
                ]
        ]);

        $libros = Libros::find();

        
        $this->view->pick('libro/consultar');
        $this->view->libros= $libros;
        $this->view->bib= $bibliotecario->idbiblioteca;

    }

    public function crearAction()
    {
        
        $this->view->pick('libro/crear');

        $idusuario = $this->session->get('id');
        $bibliotecario = Bibliotecarios::findFirst([
            'columns'    => 'idbiblioteca',
            'conditions' => 'iduser = ?1',
            'bind'       => [
                    1 => $idusuario,
                ]
        ]);

        $subcategorias= Subcategorias::find();
        $autores = Autores::find("idbiblioteca='".$bibliotecario->idbiblioteca."'");
        
        $this->view->subcategorias = $subcategorias;
        $this->view->autores = $autores;


        $libro=new Libros;
        $material=new Materialesbibliograficos;
        $unidades= new Unidades;
        $material->idbiblioteca=$bibliotecario->idbiblioteca;

        if ($this->request->isPost()) {

                $validacion= new ValidacionLibro;
                $mensajes= $validacion->obtenerMensajes($_POST);

                
                if(!empty($mensajes))
                {   
                    $this->flashSession->error('No se ha guardado Libro, algunos errores en los campos mencionados');
                    $validacion->gettingFlashMessages($mensajes);
                   //redirige al mismo formulario
                    return $this->response->redirect('/libro/crear');
                }
 
           
            $nombre = $this->request->getPost('nomLibro');
            $esexterno=$this->request->getPost('exLibro');
            $cantunidades=$this->request->getPost('cantidadLibro');
            if($nombre and $cantunidades){
                $material->nombre=$nombre;
                $material->descripcion=$this->request->getPost('descLibro');
                $libro->isbn=$this->request->getPost('isbnLibro');
                $libro->editorial=$this->request->getPost('editLibro');
                $libro->volumen=$this->request->getPost('volLibro');
                $libro->sinopsis=$this->request->getPost('sinLibro');
                if($this->request->getPost('fpub'))
                {
                    $material->fechapublicacion=$this->request->getPost('fpub');
                }
                
                if($esexterno)
                {
                    $material->esexterno = true;
                }
                else
                {
                    $material->esexterno = false;
                }

                $logourl=$this->request->getUploadedFiles('imagenLibro'); //esto debe ser traido por cloud dinary
                $material->imagenurl=$this->guardarCloudinary($logourl);

                $material->nombreimagen=$this->request->getPost('nomImgLibro');
                $material->idsubcategoria = $this->request->getPost('subLibro');
                $material->save();
                $unidades->unidadesexistentes=$cantunidades;
                $unidades->idmaterial=$material->id;
                $unidades->save();
                
                $libro->idmaterial=$material->id;
                $libro->save();
                
                foreach ($this->request->getPost('autoresLibro') as $aut){
                        $MaterialAutor = new MaterialesAutores;
                        $MaterialAutor->idautor=$aut;
                        $MaterialAutor->idmaterial=$material->id;
                        $MaterialAutor->save();
                }
                
                
            }
            $response = new Response();
            $this->flashSession->success('Libro guardado con exito');
            $response->redirect('/libro'); //Retornar a libro
            return $response;          
        }
        
    }
    
    public function editarAction()
    {
        $this->view->pick('libro/editar');
        $idusuario = $this->session->get('id');
        $bibliotecario = Bibliotecarios::findFirst([
            'columns'    => 'idbiblioteca',
            'conditions' => 'iduser = ?1',
            'bind'       => [
                    1 => $idusuario,
                ]
        ]);
        $id = $this->dispatcher->getParam('id'); //Obtener el parametros de la Url
        $libro = Libros::findFirst($id);
        $unidades = Unidades::findFirst("idmaterial='".$libro->idmaterial."'");
        $categorias= Categorias::find();
        $subcategorias= Subcategorias::find();
        $autores = Autores::find("idbiblioteca='".$bibliotecario->idbiblioteca."'");
        $MatAut = MaterialesAutores::find("idmaterial='".$libro->idmaterial."'");
        $this->view->libro = $libro;
        $this->view->unidades = $unidades;
        $this->view->categorias = $categorias;
        $this->view->subcategorias = $subcategorias;
        $this->view->autores = $autores;
        $this->view->mataut = $MatAut;

        if ($this->request->isPost()) {
            $validacion= new ValidacionLibro;
            $mensajes = $validacion->obtenerMensajes($_POST); //recoge las variables globales post

            if(!empty($mensajes))
            {   
                $this->flashSession->error('No se ha guardado libro, algunos errores en los campos mencionados');
                $validacion->gettingFlashMessages($mensajes);    
               //redirige al mismo formulario
                return $this->response->redirect('/libro/editar/'.$id);
            }
            
           // Accedemos a los datos POST            
            $nombre = $this->request->getPost('nomLibro');
            $esexterno=$this->request->getPost('exLibro');
            if($nombre){
                $libro->MaterialesBibliograficos->nombre=$nombre;
                $libro->MaterialesBibliograficos->descripcion=$this->request->getPost('descLibro');
                $libro->isbn=$this->request->getPost('isbnLibro');
                $libro->editorial=$this->request->getPost('editLibro');
                $libro->volumen=$this->request->getPost('volLibro');
                $libro->sinopsis=$this->request->getPost('sinLibro');
                if($this->request->getPost('fpub'))
                {
                    $libro->MaterialesBibliograficos->fechapublicacion=$this->request->getPost('fpub');
                }
                
                if($esexterno)
                {
                    $libro->MaterialesBibliograficos->esexterno = true;
                }
                else
                {
                    $libro->MaterialesBibliograficos->esexterno = false;
                }

                foreach ($MatAut as $autmat){
                    $i=0;
                    foreach ($this->request->getPost('autoresLibro') as $aut){
                        if($aut==$autmat->idautor){
                            $i++;
                        }
                    }
                    if($i==0){
                        $autmat->delete();
                    }
                }
                
                foreach ($this->request->getPost('autoresLibro') as $aut){
                    
                    if (count(MaterialesAutores::find("idmaterial = '".$libro->idmaterial."' and idautor = '".$aut."'"))==0){
                        $MaterialAutor = new MaterialesAutores;
                        $MaterialAutor->idautor=$aut;
                        $MaterialAutor->idmaterial=$libro->idmaterial;
                        $MaterialAutor->save();
                    }
                }

                $logourl=$this->request->getUploadedFiles('imagenLibro'); //esto debe ser traido por cloud dinary
                if($logourl){
                $libro->MaterialesBibliograficos->imagenurl=$this->guardarCloudinary($logourl);
                }
                $libro->MaterialesBibliograficos->nombreimagen=$this->request->getPost('nomImgLibro');
                $libro->Materialesbibliograficos->idsubcategoria = $this->request->getPost('subLibro');
                $unidades->unidadesexistentes=$this->request->getPost('cantidadLibro');
                $unidades->save();
                $libro->MaterialesBibliograficos->save();
                $libro->save();
            }
            $response = new Response();
            $this->flashSession->success('Libro actualizado con exito');
            $response->redirect('/libro'); //Retornar a libro
            return $response;          
        }
    }

    public function eliminarAction()
    {
        $this->view->pick('libro/eliminar');
        $id = $this->dispatcher->getParam('id'); //Obtener el parametros de la Url
        $libro = Libros::findFirst($id);
        $this->view->libro = $libro;
        if ($this->request->isPost()) {
            $material=Materialesbibliograficos::findFirst($libro->idmaterial);
            $unidades=Unidades::findFirst("idmaterial='".$libro->idmaterial."'");
            $MatAut = MaterialesAutores::find("idmaterial='".$libro->idmaterial."'");
            $libro->delete();
            $unidades->delete();
            foreach ($MatAut as $autmat){
                $autmat->delete();
            }
            $material->delete();
            $response = new Response();
            $this->flashSession->success('Libro eliminado con exito');
            $response->redirect('/libro'); //Retornar al index formato
            return $response;
        }     
    }

    public function verAction(){
        
        $this->view->pick('libro/ver');
        $idusuario = $this->session->get('id');
        $bibliotecario = Bibliotecarios::findFirst([
            'columns'    => 'idbiblioteca',
            'conditions' => 'iduser = ?1',
            'bind'       => [
                    1 => $idusuario,
                ]
        ]);
        $id = $this->dispatcher->getParam('id'); //Obtener el parametros de la Url
        $libro = Libros::findFirst($id);
        $unidades = Unidades::findFirst("idmaterial='".$libro->idmaterial."'");
        $categorias= Categorias::find();
        $subcategorias= Subcategorias::find();
        $autores = Autores::find("idbiblioteca='".$bibliotecario->idbiblioteca."'");
        $MatAut = MaterialesAutores::find("idmaterial='".$libro->idmaterial."'");
        $this->view->libro = $libro;
        $this->view->unidades = $unidades;
        $this->view->categorias = $categorias;
        $this->view->subcategorias = $subcategorias;
        $this->view->autores = $autores;
        $this->view->mataut = $MatAut;
    }

    // Funcion usada en crear y editar para guardar la imagen en cloudinary
    public function guardarCloudinary($logourl){
        
        //preparando parametros para cloudinary
        $cloud_name = getenv("CLOUDINARY_cloudName");
        $api_key = getenv("CLOUDINARY_apiKey");
        $api_secret = getenv("CLOUDINARY_apiSecret");
        $timestamp = time();
        $signature = sha1("timestamp=".(string)$timestamp.$api_secret);
        foreach ($logourl as $url){
            $tmpDir=$url->getTempName();
        }
        //imagen a base64
        $data = file_get_contents($tmpDir);
        $base64 = 'data:image/jpeg;base64,' . base64_encode($data);
        //POST a cloudinary
        $url="https://api.cloudinary.com/v1_1/".$cloud_name."/image/upload";
        $ch = curl_init($url);

        $jsonData = new jsonDataO;
        $jsonData->file = $base64;
        $jsonData->api_key = $api_key;
        $jsonData->timestamp = $timestamp;
        $jsonData->signature = $signature;

        $payload = json_encode($jsonData);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //$result = new jsonR;
        $result = curl_exec($ch);
        curl_close($ch);
        $url = json_decode($result);

        return $url->{'url'};              
    }

}
<?php

namespace App\Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

class Bibliotecas extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $nombre;

    /**
     *
     * @var string
     */
    public $ubicacion;

    /**
     *
     * @var string
     */
    public $telefono;

    /**
     *
     * @var string
     */
    public $clasificacion;

    /**
     *
     * @var string
     */
    public $habilitado;

    /**
     *
     * @var string
     */
    public $logourl;

    /**
     *
     * @var string
     */
    public $nombrelogo;

    /**
     *
     * @var string
     */
    public $email;

    

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("bibliotecas");
        $this->hasMany('id', 'App\Models\Autores', 'idbiblioteca', ['alias' => 'Autores']);
        $this->hasMany('id', 'App\Models\Bibliotecarios', 'idbiblioteca', ['alias' => 'Bibliotecarios']);
        $this->hasMany('id', 'App\Models\Prestamistas', 'idbiblioteca', ['alias' => 'Prestamistas']);
        $this->hasMany('id', 'App\Models\Materialesbibliograficos', 'idbiblioteca', ['alias' => 'Materialesbibliograficos']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'bibliotecas';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Bibliotecas[]|Bibliotecas|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Bibliotecas|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}

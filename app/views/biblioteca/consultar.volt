{% extends "layouts/admin.volt" %}

{%  block titulo %} Biblioteca {% endblock %}

{% block iconActual %}
<h1><i class="fa fa-cog"></i> Biblioteca </h1>
<p>Sección para configurar bibliotecas</p>
{% endblock %} 

{% block contenido %}
  <div style="padding-left: 90%;">
    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" href="{{url(biblioteca/crear)}}">    </button>-->
    <a href="{{url('biblioteca/crear')}}" class="btn btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i>Agregar</a>

 
  </div>
  <br>
                      
  <table class="table table-hover table-bordered" id="sampleTable">
     <thead class="bg-primary">
        <tr>
          <th>Nombre</th>
          <th>Ubicación</th>
          <th>Télefono</th>
          <th>Clasificación</th>
          <th>Email</th>
          <th width="30%">Acción</th>
        </tr>
     </thead>
     <tbody>
        {% for biblioteca in bibliotecas %}
          <tr>
            <td>{{ biblioteca.nombre}}</td>
            <td>{{ biblioteca.ubicacion}}</td>
            <td>{{ biblioteca.telefono}}</td>
            <td>{{ biblioteca.clasificacion}}</td>
            <td>{{ biblioteca.email}}</td>
            <td>
            {% if biblioteca.habilitado %}
              <a href="{{url('biblioteca/editar/'~ biblioteca.id)}}" class="btn btn-success"><i class="fa fa-pencil" aria-hidden="true"></i>Editar</a>
              <a href="{{url('biblioteca/ver/'~ biblioteca.id)}}" class="btn btn-info"><i class="fa fa-eye" aria-hidden="true"></i> Ver </a>
              <a onclick="return abrir_modal('{{url('biblioteca/deshabilitar/'~ biblioteca.id)}}')" class="btn btn-warning"><i class="fa fa-lock" aria-hidden="true"></i>Deshabilitar</a>
            {% else %}
               <a onclick="return abrir_modal('{{url('biblioteca/deshabilitar/'~ biblioteca.id)}}')" class="btn btn-success"><i class="fa fa-unlock" aria-hidden="true"></i>Habilitar</a>
            {% endif %}
            </td>
          </tr>
        {% endfor %}
      </tbody>
    </table>
        <!-- Modal Eliminar -->
<div id="popup" class="modal fade" role="dialog">
</div>


{% endblock %}

{% block extraJS %}
<script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">$('#sampleTable').DataTable({'language': {'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'}});</script>

</script>
<script type="text/javascript">
var modal;

function abrir_modal(url) {
    $('#popup').load(url, function() {
        $(this).modal('show');
    });
    return false;
}

function cerrar_modal() {
    $('#popup').modal('hide');
    return false;
}
</script>
{% endblock %}
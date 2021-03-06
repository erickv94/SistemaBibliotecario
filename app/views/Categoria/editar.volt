{% extends 'layouts/bibliotecario.volt' %}
{% block titulo %} Editar Categoria {{categoria.id}} {% endblock %}
{% block iconActual%}
<h1><i class="fa fa-list-ul"></i> Editar categoria </h1>
<p>Editar una categoria </p>
{% endblock %} 
{% block contenido %}
<div class="container">
    <div class="row">
        <div class="col">
            <form action="" method="post">
                <div class="form-group">
                    <label class="control-label">Nombre</label>
                    <input name="nombreCat" class="form-control" type="text" placeholder="Ingrese nombre de la categoria"    value="{{categoria.nombre}}" required>
                </div>
                <div class="form-group">
                    <label class="control-label">Descripcion</label>
                    <textarea name="descCat" class="form-control" rows="4" placeholder="Ingrese la descripción de la categoria" required>{{categoria.descripcion}}</textarea>
                </div>
                <div class="form-group">
                    <label class="control-label">Codigo</label>
                    <input name="codCat" class="form-control" type="text" placeholder="Codigo de la categoria"    value="{{categoria.codigo}}" required> 
                </div>
                <div class="form-group">                    
                    <button type="Submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
{% endblock %}
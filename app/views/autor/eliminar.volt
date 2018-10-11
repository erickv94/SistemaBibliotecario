<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Eliminar Formato</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="col-lg-12">
                <p> ¿Desea eliminar el autor <strong>{{autor.nombre}}</strong>? </p>
            </div>
        </div>
        <div class="modal-footer">
            <div class="col-lg-12 text-right">
                <form role="form" action="{{url('autor/borrar/'~ autor.id)}}" method="post">
                    <input type="submit" class="btn btn-danger" id="despedir" value="Si">
                    <button type="button" class="btn btn-secondary" onclick="return cerrar_modal()"> No </button>
                </form>
            </div>
        </div>
    </div>
</div>
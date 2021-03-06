{% if hoy <= reserva.fechareserva %}
<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Préstamo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <p> ¿Desea confirmar el préstamo? </p>
                    <p> Usuario: <strong>{{reserva.Prestamistas.Users.username}}</strong></p>
                    <p> Material bibliográfico: <strong>{{reserva.MaterialesBibliograficos.nombre}}</strong></p>
                    <p> Fecha de devolución: <strong>{{fechadevolucion}}</strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-lg-12 text-right">
                    <form role="form" action="{{url('reserva/prestar/'~ reserva.id)}}" method="post">
                        <input type="submit" class="btn btn-primary" id="despedir" value="Si">
                        <button type="button" class="btn btn-secondary" onclick="return cerrar_modal()"> No </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
{% else %}
<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reservación Vencida</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <p> La reservación esta vencida, ya no se puede realizar el préstamo </p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-lg-12 text-right">
                    <form role="form" action="{{url('reserva/cancelar/'~ reserva.id)}}" method="post">
                        <button type="button" class="btn btn-secondary" onclick="return cerrar_modal()"> Aceptar </button>
                        <input type="submit" class="btn btn-danger" id="despedir" value="Eliminar Reservación">
                    </form>
                </div>
            </div>
        </div>
    </div>
{% endif %}
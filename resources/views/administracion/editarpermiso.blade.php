<div id="editarpermiso">
    {{Form::model($permiso,['route'=>['editar.permisop',$permiso], 'class'=>'form-horizontal', 'id'=>'ediper'])}}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title">Editar permiso</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="form-group">
                    <label class="col-md-2 control-label">Nombre</label>
                    <div class="col-md-10">
                        {{Form::text('name', null ,['class'=>'form-control'])}}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label">Slug</label>
                    <div class="col-md-10">
                        {{Form::text('slug', null ,['class'=>'form-control'])}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Descripción</label>
                    <div class="col-md-10">
                        {{Form::textarea('description', null ,['class'=>'form-control', 'rows'=>5])}}
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-custom waves-effect waves-light">Guardar</button>
        </div>
    {{Form::close()}}
</div>

<script>
    $(function () {
        $("#ediper").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            modalBs.modal('hide');
            $.ajax({
                url : form.attr('action'),
                data : form.serialize(),
                type : 'POST',
                dataType : 'json',
                beforeSend: function () {
                    cargando();
                },
                success : function(result) {
                    if(result.estado){
                       swal(
                            {
                                title: 'Bien!!',
                                text: result.mensaje,
                                type: 'success',
                                confirmButtonColor: '#4fa7f3'
                            }
                        )
                    }else{
                        swal(
                            'Error!!',
                            result.mensaje,
                            'error'
                        )
                    }
                    table.ajax.reload();
                },
                error : function(xhr, status) {
                    var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                    swal(
                        'Error!!',
                        message,
                        'error'
                    )
                },
                // código a ejecutar sin importar si la petición falló o no
                complete : function(xhr, status) {
                    fincarga();
                 }
            });
        })
    })
</script>
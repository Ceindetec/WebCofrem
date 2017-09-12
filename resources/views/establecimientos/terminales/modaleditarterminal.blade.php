<div id="modaleditarterminal">
    {{Form::model($terminal,['route'=>['terminal.editarp',$terminal->id], 'class'=>'form-horizontal', 'id'=>'editarterminal'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Agregar terminal</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label">Numero activo</label>
                <div class="col-md-10">
                    {{Form::text('numero_activo', null ,['class'=>'form-control', "required", "maxlength"=>"10", "data-parsley-type"=>"number"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Celular</label>
                <div class="col-md-10">
                    {{Form::text('celular', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Contraseña</label>
                <div class="col-md-10">
                    {{Form::password('password', ['class'=>'form-control',  "maxlength"=>"4", "id"=>"password", "data-parsley-type"=>"number"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Confirmar</label>
                <div class="col-md-10">
                    {{Form::password('password_confirmation', ['class'=>'form-control', "maxlength"=>"4", "data-parsley-equalto"=>"#password"])}}
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
        $("#editarterminal").parsley();
        $("#editarterminal").submit(function (e) {
            e.preventDefault();
            var form = $(this);
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
                        modalBs.modal('hide');
                    }else if(result.estado == false){
                        swal(
                            'Error!!',
                            result.mensaje,
                            'error'
                        )
                    }else{
                        html='';
                        for(i=0; i<result.length;i++){
                            html+=result[i]+'\n\r';
                        }
                        swal(
                            'Error!!',
                            html,
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
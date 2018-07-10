<div id="modalcreartarjetasbloque">
    {{Form::open(['route'=>['tarjetas.crearbloquep'], 'class'=>'form-horizontal', 'id'=>'creartarjetasbloque'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Agregar Tarjetas en Bloque</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label">Número de la primera tarjeta</label>
                <div class="col-md-10">
                {{Form::text('numero_primer_tarjeta', null ,['class'=>'form-control', "required", "maxlength"=>"6", "data-parsley-type"=>"number"])}} <!-- "data-parsley-type"=>"number"] -->
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Cantidad de tarjetas</label>
                <div class="col-md-10">
                {{Form::number('cantidad', null ,['class'=>'form-control', "required","min"=>1,"max"=>10000])}} <!-- "data-parsley-type"=>"number"] -->
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Tipo de servicio</label>
                <div class="col-md-10">
                    {{Form::select("servicio_codigo",$servicios,null,['class'=>'form-control', "tabindex"=>"2",'id'=>'tarjeta_codigo', "required"])}}
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
        $("#creartarjetasbloque").parsley();
        $("#creartarjetasbloque").submit(function (e) {
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
                success: function (result) {
                    if (result.estado) {
                        swal(
                            {
                                title: 'Bien!!',
                                text: result.mensaje,
                                type: 'success',
                                confirmButtonColor: '#4fa7f3'
                            }
                        );
                        modalBs.modal('hide');
                    } else if (result.estado == false) {
                        swal(
                            'Error!!',
                            result.mensaje,
                            'error'
                        );

                    } else {
                        html = '';
                        for (i = 0; i < result.length; i++) {
                            html += result[i] + '\n\r';
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
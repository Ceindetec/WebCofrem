<div id="modalcrearcontratos">
    {{Form::open(['route'=>['contrato.crear'], 'class'=>'form-horizontal', 'id'=>'crearcontratos'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Agregar contrato</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label">Número de contrato</label>
                <div class="col-md-10">
                    {{Form::text('n_contrato', null ,['class'=>'form-control', "required", "tabindex"=>"1", 'id'=>'n_contrato'])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Valor contrato</label>
                <div class="col-md-10">
                    {{Form::text('valor_contrato', null ,['class'=>'form-control money', "required", "data-parsley-type"=>"number", "tabindex"=>"2",'id'=>'valor_contrato'])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Valor del impuesto</label>
                <div class="col-md-10">
                    {{Form::text('valor_impuesto', null ,['class'=>'form-control money', "required", "data-parsley-type"=>"number", "tabindex"=>"3",'id'=>'impuesto'])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Fecha</label>
                <div class="col-md-4">
                    {{Form::text("fecha", null,['class'=>'form-control', "required", "tabindex"=>"4", 'id'=>'fecha'])}}
                </div>
            </div>


            <div class="form-group">
                <label class="col-md-2 control-label">Empresa</label>
                <div class="col-md-10">
                    {{Form::text('nit', null, ['class'=>'form-control', "required", "tabindex"=>"4", "maxlength"=>"10", "data-parsley-type"=>"number", 'id'=>'nit'])}}
                </div>
            </div>


            <div class="form-group">
                <label class="col-md-2 control-label">Número de tarjetas</label>
                <div class="col-md-10">
                    {{Form::text('n_tarjetas', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"3", "tabindex"=>"5", 'id'=>'n_tarjetas'])}}
                </div>
            </div>


            <div class="form-group">
                <label class="col-md-2 control-label">Forma de pago</label>
                <div class="col-md-10">
                    {{Form::select('forma_pago', ['E' => 'Efectivo', 'C' => 'Consumo'], 'E',['class'=>'form-control', "tabindex"=>"6", "required", 'id'=>'forma_pago'])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Documentos</label>
                <div class="col-md-10">
                    {{Form::text('pdf', null ,['class'=>'form-control', "required", "maxlength"=>"10", "tabindex"=>"7", 'id'=>'pdf'])}}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-2">Consumo</label>
                <br>
                <div class="col-md-10">
                    <div class="radio radio-custom form-check form-check-inline" style="display: inline">

                        {{Form::radio('consumo', '1', true)}}
                        <label class="form-check-label">Mensual</label>
                    </div>


                    <div class="radio radio-custom form-check form-check-inline" style="display: inline">

                        <input type="radio" name="consumo" id="radio03" value="0">
                        <label class="form-check-label">Días de consumo</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Días Consumo</label>
                <div class="col-md-10">
                    {{Form::text('dias_consumo', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "tabindex"=>"8", "maxlength"=>"3", 'id'=>'dias_consumo'])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Porcentaje Administracion</label>
                <div class="col-md-4">
                    {{Form::select("adminis_tarjeta_id",$administracion,null,['class'=>'form-control', "tabindex"=>"9", 'id'=>'administracion'])}}
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
        $('#fecha').datepicker({
            autoclose: true,
            startDate: moment().format(),
            format: 'dd/mm/yyyy',
            language: 'es'
        });

        $("#crearcontratos").parsley();
        $("#crearcontratos").submit(function (e) {
            e.preventDefault();
            var form = $(this);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'POST',
                dataType: 'json',
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
                        table.ajax.reload();
                        modalBs.modal('hide');
                    } else if (result.estado == false) {
                        swal(
                            'Error!!',
                            result.mensaje,
                            'error'
                        )
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
                },
                error: function (xhr, status) {
                    var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                    swal(
                        'Error!!',
                        message,
                        'error'
                    )
                },
                // código a ejecutar sin importar si la petición falló o no
                complete: function (xhr, status) {
                    fincarga();
                }
            });
        });


    });


</script>
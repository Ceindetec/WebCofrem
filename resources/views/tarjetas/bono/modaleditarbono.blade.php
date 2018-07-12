<div id="modaleditartarjeta">
    {{Form::model($detalle,['route'=>['bono.editarp',$detalle->id], 'class'=>'form-horizontal', 'id'=>'editartarjeta'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Editar tarjeta bono</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label">Número de tarjeta</label>
                <div class="col-md-10">
                {{Form::text('numero_tarjeta', null ,['class'=>'form-control', "required",  "data-parsley-type"=>"number", 'id'=>'numero_tarjeta','disabled'])}} <!-- "data-parsley-type"=>"number"] -->
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Fecha de vencimiento</label>
                <div class="col-md-10">
                {{ Form::text('fecha_vencimiento', null ,['class'=>'form-control ', "required", 'id'=>'fecha_vencimiento'])}} <!-- "data-parsley-type"=>"number"] -->
                    <!-- { ! ! Form::text('fecha_vencimiento', null, ['class' => 'date']) !!} -->

                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Monto</label>
                <div class="col-md-10">
                {{Form::text('monto_inicial', null ,['class'=>'form-control money', "required", "maxlength"=>"10", "data-parsley-type"=>"number", "tabindex"=>"1",'id'=>'monto', "onkeypress"=>"return justNumbers(event)"])}} <!-- "data-parsley-type"=>"number"] -->
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
    var fecha;
    $(function () {
        fecha = $('#fecha_vencimiento').val(moment($('#fecha_vencimiento').val()).format('DD/MM/YYYY'));
        console.log(fecha.val());
        $('#fecha_vencimiento').datepicker({
            format:'dd/mm/yyyy',
            startDate: fecha.val(),
            language: 'es'
        });

        $('.money').mask('000.000.000.000.000', {reverse: true});
        $("#editartarjeta").parsley();
        $("#editartarjeta").submit(function (e) {
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


        $("#selectroles").select2({
            placeholder: "Seleccione...",
            minimumInputLength: 1,
            ajax: {
                url: "{{route('selectroles')}}",
                dataType: 'json',
                type: "GET",
                quietMillis: 50,
                data: function (params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
            },
            language: "es",
            cache: true
        });
    })

</script>
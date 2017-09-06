<div id="modaleditartarjetas">
    {{Form::model($tarjeta,['route'=>['tarjetas.editarp',$tarjeta->id], 'class'=>'form-horizontal', 'id'=>'editartarjetas'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Editar Tarjeta</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label">Número de tarjeta</label>
                <div class="col-md-10">
                {{Form::text('numero_tarjeta', null ,['class'=>'form-control',  "id"=>"numero_tarjeta","tabindex"=>"1","required", "maxlength"=>"7", "data-parsley-type"=>"number","readonly", "disabled"])}} <!-- "data-parsley-type"=>"number"] -->
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Tipo</label>
                <div class="col-md-10">
                    {{Form::select('tipo', ['A' => 'Afiliado', 'R' => 'Regalo', 'B' => 'Bono'],null, ['class'=>'form-control',"id"=>"tipo", "required", "tabindex"=>"2"])}}
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
        $("#editartarjetas").parsley();
        $("#editartarjetas").submit(function (e) {
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
                        resetInfo(result.data);
                    } else if (result.estado == false) {
                        swal(
                            'Error!!',
                            result.mensaje,
                            'error'
                        );
                        resetInfo(result.data);

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

        $('#editarcheck').change(function () {
            if ($(this).is(':checked')) {
                $('#editartarjetas input, #editartarjetas select').attr('disabled', false)
            } else {
                $('#editartarjetas input, #editartarjetas select').attr('disabled', true)
            }
        })
    });

    function resetInfo(data) {
        $('#tipo').val(data.tipo);
        setTimeout(function () {
            if ($('#editarcheck').is(':checked')) {
                $('#editarcheck').trigger('click');
            }
        }, 200);

    }

</script>
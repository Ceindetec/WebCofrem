<div id="modaltrasladarterminal">
    {{Form::open(['route'=>['terminal.trasladarp',$terminal->id], 'class'=>'form-horizontal', 'id'=>'trasladarterminal'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Trasladar terminal</h4>
    </div>
    <div class="modal-body">
            <div class="m-l-10 m-r-10">
                <div class="form-group">
                    <label class="control-label">Establecimiento</label>
                    {{Form::select("establecimiento_id",$establecimientos,null,['class'=>'form-control', "tabindex"=>"2",'id'=>'establecimiento'])}}
                </div>
                <div class="form-group">
                    <label class="control-label">Sucursal</label>
                    <select name="sucursal_id" id="sucursales" tabindex="3" class="form-control" required>
                        <option>Seleccione...</option>
                    </select>
                </div>
            </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-custom waves-effect waves-light">Trasladar</button>
    </div>
    {{Form::close()}}
</div>

<script>
    $(function () {
        $("#trasladarterminal").parsley();
        $("#trasladarterminal").submit(function (e) {
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
                        )
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


        setTimeout(getSucursales, '300');
        $("#establecimiento").change(function () {
            getSucursales();
        });


    });

    function getSucursales() {
        var dept = $("#establecimiento").val();
        $.get('{{route('sucursales')}}', {data: dept}, function (result) {
            $('#sucursales').html("");
            $('#sucursales').append($('<option>').text('Seleccione...').attr('value', ''));
            $.each(result, function (i, value) {
                $('#sucursales').append($('<option>').text(value.nombre).attr('value', value.id));
            });
        })
    }


</script>
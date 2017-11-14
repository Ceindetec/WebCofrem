<div id="modaleditarempresa">

    {{Form::model($empresa,['route'=>['empresa.editarp', $empresa->id], 'class'=>'form-horizontal', 'id'=>'editarempresa'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Editar empresa</h4>
    </div>
    <div class="modal-body">


        <div class="form-group">
            <label class="col-md-2 control-label">Nit</label>
            <div class="col-md-10">
                {{Form::text('nit', null ,['class'=>'form-control', "id"=>"nit", "required", "tabindex"=>"1","maxlength"=>"10", "data-parsley-type"=>"number"])}}
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-2 control-label">Razón social</label>
            <div class="col-md-10">
                {{Form::text('razon_social', null ,['class'=>'form-control', "id"=>"razon_social", "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$"])}}
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">Representante Legal</label>
            <div class="col-md-10">
                {{Form::text('representante_legal', null ,['class'=>'form-control', "id"=>"razon_social", "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$"])}}
            </div>
        </div>



        <div class="form-group">
            <label class="col-md-2 control-label">Departamento</label>
            <div class="col-md-4">
                {{Form::select("departamento_codigo",$departamentos,null,['class'=>'form-control', "tabindex"=>"2", 'id'=>'departamento'])}}
            </div>
            <label class="col-md-2 control-label">Ciudad</label>
            <div class="col-md-4">
                <select name="municipio_codigo" id="municipio" tabindex="3" class="form-control">
                    <option>Seleccione...</option>
                </select>
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-2 control-label">E-mail</label>
            <div class="col-md-10">
                {{Form::email('email', null ,['class'=>'form-control', "required"])}}
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-2 control-label">Teléfono</label>
            <div class="col-md-10">
                {{Form::text('telefono', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10"])}}
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-2 control-label">Celular</label>
            <div class="col-md-10">
                {{Form::text('celular', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10"])}}
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-2 control-label">Dirección</label>
            <div class="col-md-10">
                {{Form::text('direccion', null ,['class'=>'form-control', "required", "maxlength"=>"40"])}}
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">Tipo</label>
            <div class="col-md-10">
                {{Form::select('tipo', ['T' => 'Tercero', 'A' => 'Afiliado'], 'T', ['class'=>'form-control', "tabindex"=>"2", "required"])}}
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

    var valdep = '{{$depar->codigo}}';
    var valmuni = '{{$empresa->municipio_codigo}}';

        $(function () {
        setTimeout(function () {
            $('#nombre').focus();
        },1000);
        $("#editarempresa").parsley();
        $("#editarempresa").submit(function (e) {
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



        setTimeout(getMunicipios, '300');

        $("#departamento").change(function () {
            getMunicipios();
        });

       $('#departamento > option[value='+valdep+']').attr('selected', 'selected');

    });


    function getMunicipios() {
        var dept = $("#departamento").val();
        $.get('{{route('municipios')}}', {data: dept}, function (result) {
            $('#municipio').html("");
            $.each(result, function (i, value) {
                $('#municipio').append($('<option>').text(value.descripcion).attr('value', value.codigo));
            });
          $('#municipio > option[value='+valmuni+']').attr('selected', 'selected');

        })
    }

</script>
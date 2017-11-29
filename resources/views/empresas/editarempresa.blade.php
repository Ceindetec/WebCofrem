<div id="modaleditarempresa">

    {{Form::model($empresa,['route'=>['empresa.editarp', $empresa->id], 'class'=>'form-horizontal', 'id'=>'editarempresa'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Editar empresa</h4>
    </div>
    <div class="modal-body">
        @if($empresa->tipo=="A")
            <div class="form-group">
                <div class="widget-inline-box" align="center">
                    <a href="#" onclick="consultarWS()" id="botonConsultar" data-modal class="btn btn-custom waves-effect waves-light" data-target="#modalrol">Actualizar con SevenAs</a>
                </div>
            </div>
            <div name="mensaje" id="mensaje" style="visibility: hidden;"><font color="red">No existe en SevenAs</font></div>
            <div name="mensajeok" id="mensajeok" style="visibility: hidden;"><font color="green">Actualizado con SevenAs</font></div>
            <div class="form-group">
                <label class="col-md-2 control-label">Tipo de documento</label>
                <div class="col-md-4">
                    {{Form::select("tipo_documento",$tipoDocu,null,['id'=>'tipo_documento', 'class'=>'form-control', "tabindex"=>"1", "onchange"=>"limpiar()", "readonly", "disabled"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Número de documento</label>
                <div class="col-md-10">
                {{Form::text('nit', null ,['id'=>'nit','class'=>'form-control', "required", "maxlength"=>"10", "tabindex"=>"2", "onchange"=>"limpiar()", "readonly"])}} <!-- , "data-parsley-type"=>"number" -->
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Razón social</label>
                <div class="col-md-10">
                    {{Form::text('razon_social', null ,['id'=>'razon_social', 'class'=>'form-control', "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$", "tabindex"=>"3", "readonly"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Representante Legal</label>
                <div class="col-md-10">
                    {{Form::text('representante_legal', null ,['id'=>'representante_legal', 'class'=>'form-control', "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$", "tabindex"=>"4", "readonly"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Departamento</label>
                <div class="col-md-4">
                    {{Form::select("departamento_codigo",$departamentos,null,['class'=>'form-control', "tabindex"=>"5", 'id'=>'departamento'])}}
                </div>
                <label class="col-md-2 control-label">Ciudad</label>
                <div class="col-md-4">
                    <select name="municipio_codigo" id="municipio" tabindex="6" class="form-control">
                        <option>Seleccione...</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Direccion</label>
                <div class="col-md-10">
                    {{Form::text('direccion', null ,['id'=>'direccion', 'class'=>'form-control', "required", "maxlength"=>"40", "tabindex"=>"7", "readonly"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">E-mail</label>
                <div class="col-md-10">
                    {{Form::email('email', null ,['id'=>'email', 'class'=>'form-control', "required", "tabindex"=>"8", "readonly"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Telefono</label>
                <div class="col-md-10">
                    {{Form::text('telefono', null ,['id'=>'telefono', 'class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10", "tabindex"=>"9", "readonly"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Celular</label>
                <div class="col-md-10">
                    {{Form::text('celular', null ,['id'=>'celular', 'class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10", "tabindex"=>"10", "readonly"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Tipo</label>
                <div class="col-md-10">
                    <!-- { {Form::select('tipo', [ 'T' => 'Tercero', 'A' => 'Afiliado'], 'T',['id'=>'tipo', 'class'=>'form-control', "tabindex"=>"11", "required", "readonly", "onmouseover"=>"this.disabled=true", "onmouseout"=>"this.disabled=false"])}} -->
                    {{Form::text('tipo_name', 'Afiliado' ,['id'=>'tipo_name', 'class'=>'form-control', "required", "readonly", "tabindex"=>"11"])}}

                </div>
            </div>
        @endif
        @if($empresa->tipo=="T")
        <div class="form-group">
            <label class="col-md-2 control-label">Tipo de documento</label>
            <div class="col-md-4">
                {{Form::select("tipo_documento",$tipoDocu,null,['class'=>'form-control', "tabindex"=>"1", 'id'=>'tipo_documento'])}}
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">Número de documento</label>
            <div class="col-md-10">
                {{Form::text('nit', null ,['class'=>'form-control', "id"=>"nit", "required", "tabindex"=>"1","maxlength"=>"10", "tabindex"=>"2"])}} <!-- , "data-parsley-type"=>"number" -->
            </div>
        </div>
                <div class="form-group">
                    <div class="widget-inline-box" align="center">
                        <a href="#" onclick="consultarWS()" id="botonConsultar" data-modal class="btn btn-custom waves-effect waves-light" data-target="#modalrol">Consultar en SevenAs</a>
                    </div>
                </div>
                <div name="mensaje" id="mensaje" style="visibility: hidden;"><font color="red">No existe en SevenAs</font></div>
                <div name="mensajeok" id="mensajeok" style="visibility: hidden;"><font color="green">Actualizado con SevenAs</font></div>
        <div class="form-group">
            <label class="col-md-2 control-label">Razón social</label>
            <div class="col-md-10">
                {{Form::text('razon_social', null ,['class'=>'form-control', "id"=>"razon_social", "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$", "tabindex"=>"3"])}}
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">Representante Legal</label>
            <div class="col-md-10">
                {{Form::text('representante_legal', null ,['class'=>'form-control', "id"=>"razon_social", "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$", "tabindex"=>"4"])}}
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">Departamento</label>
            <div class="col-md-4">
                {{Form::select("departamento_codigo",$departamentos,null,['class'=>'form-control', "tabindex"=>"5", 'id'=>'departamento'])}}
            </div>
            <label class="col-md-2 control-label">Ciudad</label>
            <div class="col-md-4">
                <select name="municipio_codigo" id="municipio" tabindex="6" class="form-control">
                    <option>Seleccione...</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">E-mail</label>
            <div class="col-md-10">
                {{Form::email('email', null ,['class'=>'form-control', "required", "tabindex"=>"7"])}}
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">Teléfono</label>
            <div class="col-md-10">
                {{Form::text('telefono', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10", "tabindex"=>"8"])}}
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">Celular</label>
            <div class="col-md-10">
                {{Form::text('celular', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10", "tabindex"=>"9"])}}
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">Dirección</label>
            <div class="col-md-10">
                {{Form::text('direccion', null ,['class'=>'form-control', "required", "maxlength"=>"40", "tabindex"=>"10"])}}
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">Tipo</label>
            <div class="col-md-10">
                <!-- { {Form::select('tipo', ['T' => 'Tercero', 'A' => 'Afiliado'], 'T', ['class'=>'form-control', "tabindex"=>"11", "required"])}} -->
                {{Form::text('tipo_name', "Tercero" ,['id'=>'tipo_name', 'class'=>'form-control', "required", "readonly", "tabindex"=>"11"])}}
            </div>
        </div>
            @endif

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
    function limpiar() {
        document.getElementById("mensaje").style.visibility = "hidden";
        document.getElementById("mensajeok").style.visibility = "hidden";
        //document.getElementById('tipo').selectedIndex = "T";

        $('#tipo_name').attr('readonly', true);
        //$('#tipo option:not(:selected)').attr('disabled',true);
        document.getElementById('tipo_name').value = "Tercero";
        $('#razon_social').removeAttr('readonly');
        $('#representante_legal').removeAttr('readonly');
        $('#direccion').removeAttr('readonly');
        $('#email').removeAttr('readonly');
        $('#telefono').removeAttr('readonly');
        $('#celular').removeAttr('readonly');
        document.getElementById('razon_social').value = "";
        document.getElementById('representante_legal').value = "";
        document.getElementById('direccion').value = "";
        document.getElementById('email').value = "";
        document.getElementById('telefono').value = "";
        document.getElementById('celular').value = "";
    }
    function consultarWS() {
        var tipo = $("#tipo_documento").val();
        var num = $("#nit").val();
        $.ajax({
            method: "get",
            url: "{!!route('consultaraportante')!!}",
            data: {
                "tipo": tipo,
                "num": num
            },
            error: function (data) {
                //something went wrong with the request
                alert("Error");
            },
            success: function (data) {

                if(data.tipo=="A")
                {
                    document.getElementById("mensaje").style.visibility = "hidden";
                    document.getElementById("mensajeok").style.visibility = "visible";
                    document.getElementById('razon_social').value = data.razon_social;
                    document.getElementById('representante_legal').value = data.representante_legal;
                    document.getElementById('direccion').value = data.direccion;
                    document.getElementById('email').value = data.email;
                    document.getElementById('telefono').value = data.telefono;
                    document.getElementById('celular').value = data.celular;
                    document.getElementById('tipo_name').value = "Afiliado";//data.tipo
                    document.getElementById('departamento').value = data.departamento_codigo;
                    getMunicipios();
                    document.getElementById('municipio').value = data.municipio_codigo;
                    $('#razon_social').attr('readonly', true);
                    $('#representante_legal').attr('readonly', true);
                    $('#direccion').attr('readonly', true);
                    if(data.email == "NA")
                        $('#email').removeAttr('readonly');
                    else
                        $('#email').attr('readonly', true);
                    $('#telefono').attr('readonly', true);
                    if(data.celular == 0)
                        $('#celular').removeAttr('readonly');
                    else
                        $('#celular').attr('readonly', true);
                    $('#tipo_name').attr('readonly', true);
                    //$('#tipo option:not(:selected)').attr('disabled',true);
                    //$('#dias_consumo').removeAttr('disabled');
                }
                else
                {
                    //limpiar();
                    document.getElementById("mensaje").style.visibility = "visible";
                    document.getElementById("mensajeok").style.visibility = "hidden";
                    document.getElementById('tipo_name').value = "Tercero";//data.tipo
                }
            }
        });

    }
    function bloquear(){
        $('#razon_social').attr('readonly', true);
        $('#representante_legal').attr('readonly', true);
        $('#direccion').attr('readonly', true);
        if(document.getElementById('email').value == "NA")
            $('#email').removeAttr('readonly');
        else
            $('#email').attr('readonly', true);
        $('#telefono').attr('readonly', true);
        if(document.getElementById('celular').value == 0)
            $('#celular').removeAttr('readonly');
        else
            $('#celular').attr('readonly', true);
        $('#tipo_name').attr('readonly', true);
    };

</script>
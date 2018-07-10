<div id="modalcrearempresas">
    {{Form::open(['route'=>['empresa.crearp'], 'class'=>'form-horizontal', 'id'=>'crearempresas'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Agregar empresa</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label">Tipo de documento</label>
                <div class="col-md-4">
                    {{Form::select("tipo_documento",$tipoDocu,null,['id'=>'tipo_documento', 'class'=>'form-control', "tabindex"=>"1", "onchange"=>"limpiar()"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Número de documento</label>
                <div class="col-md-10">
                    {{Form::text('nit', null ,['id'=>'nit','class'=>'form-control', "required", "maxlength"=>"10", "tabindex"=>"2", "onchange"=>"limpiar()"])}} <!-- , "data-parsley-type"=>"number" -->
                </div>
            </div>
            <div class="form-group">
            <div class="widget-inline-box" align="center">
                <a href="#" onclick="consultarWS()" id="botonConsultar" data-modal class="btn btn-custom waves-effect waves-light" data-target="#modalrol">Consultar en SevenAs</a>
            </div>
            </div>
            <div name="mensaje" id="mensaje" style="visibility: hidden;"><font color="red">No existe en SevenAs</font></div>
            <div class="form-group">
                <label class="col-md-2 control-label">Razón social</label>
                <div class="col-md-10">
                    {{Form::text('razon_social', null ,['id'=>'razon_social', 'class'=>'form-control', "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$", "tabindex"=>"3"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Representante Legal</label>
                <div class="col-md-10">
                    {{Form::text('representante_legal', null ,['id'=>'representante_legal', 'class'=>'form-control', "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+(\s*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]*)*[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ]+$", "tabindex"=>"4"])}}
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
                <label class="col-md-2 control-label">Dirección</label>
                <div class="col-md-10">
                    {{Form::text('direccion', null ,['id'=>'direccion', 'class'=>'form-control', "required", "maxlength"=>"40", "tabindex"=>"7"])}}
                </div>
            </div>

            <div class="form-group">
                    <label class="col-md-2 control-label">E-mail</label>
                    <div class="col-md-10">
                        {{Form::email('email', null ,['id'=>'email', 'class'=>'form-control', "required", "tabindex"=>"8"])}}
                    </div>
            </div>


                <div class="form-group">
                    <label class="col-md-2 control-label">Teléfono</label>
                    <div class="col-md-10">
                        {{Form::text('telefono', null ,['id'=>'telefono', 'class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10", "tabindex"=>"9"])}}
                    </div>
                </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Celular</label>
                <div class="col-md-10">
                    {{Form::text('celular', null ,['id'=>'celular', 'class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10", "tabindex"=>"10"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Tipo</label>
                <div class="col-md-10">
                      <!-- { {Form::select('tipo', [ 'T' => 'Tercero', 'A' => 'Afiliado'], 'T',['id'=>'tipo', 'class'=>'form-control', "tabindex"=>"11", "required", "readonly", "onmouseover"=>"this.disabled=true", "onmouseout"=>"this.disabled=false"])}} -->
                     {{Form::text('tipo', 'Tercero' ,['id'=>'tipo', 'class'=>'form-control', "required", "readonly", "tabindex"=>"11"])}}

                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-custom waves-effect waves-light" >Guardar</button>
    </div>
    {{Form::close()}}
</div>

<script>
    $(function () {

        setTimeout(function () {
            $('#nombre').focus();
        },1000);
        $("#crearempresas").parsley();
        $("#crearempresas").submit(function (e) {
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
    });


    function getMunicipios() {
        var dept = $("#departamento").val();
        $.get('{{route('municipios')}}', {data: dept}, function (result) {
            $('#municipio').html("");
            $.each(result, function (i, value) {
                $('#municipio').append($('<option>').text(value.descripcion).attr('value', value.codigo));
            });
        })
    }
    function limpiar() {
        document.getElementById("mensaje").style.visibility = "hidden";
        //document.getElementById('tipo').selectedIndex = "T";

        $('#tipo').attr('readonly', true);
        //$('#tipo option:not(:selected)').attr('disabled',true);
        document.getElementById('tipo').value = "Tercero";
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
                    document.getElementById('razon_social').value = data.razon_social;
                    document.getElementById('representante_legal').value = data.representante_legal;
                    document.getElementById('direccion').value = data.direccion;
                    document.getElementById('email').value = data.email;
                    document.getElementById('telefono').value = data.telefono;
                    document.getElementById('celular').value = data.celular;
                    document.getElementById('tipo').value = "Afiliado";//data.tipo
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
                    $('#tipo').attr('readonly', true);
                    //$('#tipo option:not(:selected)').attr('disabled',true);
                    //$('#dias_consumo').removeAttr('disabled');
                }
                else
                {
                    limpiar();
                    document.getElementById("mensaje").style.visibility = "visible";

                }
            }
        });
    }

</script>
@extends('layouts.admin')

@section('styles')
    {!!Html::style('plugins/jquery-autocomplete/jquery.autocomplete.css')!!}
    {!!Html::style('plugins/sweet-alert2/animate.css')!!}
@endsection

@section('contenido')

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Registro Individual de Cupo Rotativo</h4>
            </div>
        </div> <!-- end row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box widget-inline">
                    {{Form::open(['route'=>['rotativo.crearindividual'], 'class'=>'form-horizontal', 'id'=>'creartarjetacupo'])}}
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Empresa (convenio)</label>
                            <div class="col-sm-7">
                        {{Form::select("empresa",$empresas,null,['class'=>'form-control', "tabindex"=>"1",'id'=>'empresa', "required"=>"required","data-placeholder"=>"Seleccione ..." ])}}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Número de tarjeta</label>
                            <div class="col-sm-7">
                            {{Form::number('numero_tarjeta', null ,['class'=>'form-control', "required", "maxlength"=>"6", "data-parsley-type"=>"number", "tabindex"=>"2",'id'=>'numero_tarjeta'])}} <!-- "data-parsley-type"=>"number"] -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Cupo</label>
                            <div class="col-sm-7">
                                {{Form::text('monto', null ,['class'=>'form-control money', "required", "data-parsley-type"=>"number", "tabindex"=>"3",'id'=>'monto'])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Identificación</label>
                            <div class="col-sm-7">
                                {{Form::number('identificacion', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "tabindex"=>"4",'id'=>'identificacion'])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="widget-inline-box" align="center">
                                <a href="#" onclick="consultarWS()" id="botonConsultar" class="btn btn-custom waves-effect waves-light" data-target="#modalrol">Consultar en SevenAs</a>
                            </div>
                        </div>
                        <div name="mensaje" id="mensaje" style="visibility: hidden;"><font color="red">No existe en SevenAs</font></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Salario</label>
                            <div class="col-sm-7">
                                {{Form::text('salario', null ,['class'=>'form-control', "readonly", "tabindex"=>"5",'id'=>'salario'])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Nombres</label>
                            <div class="col-sm-7">
                                {{Form::text('nombres', null ,['class'=>'form-control', "required", "tabindex"=>"6",'id'=>'nombres', "readonly", "data-parsley-pattern"=>"/^[A-Z üÜáéíóúÁÉÍÓÚñÑ]{1,50}$/i"])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Apellidos</label>
                            <div class="col-sm-7">
                                {{Form::text('apellidos', null ,['class'=>'form-control', "required", "tabindex"=>"7",'id'=>'apellidos',"readonly","data-parsley-pattern"=>"/^[A-Z üÜáéíóúÁÉÍÓÚñÑ]{1,50}$/i"])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-7">
                                {{Form::text('email', null ,['class'=>'form-control', "readonly", "tabindex"=>"8",'id'=>'email', "data-parsley-type"=>"email"])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Teléfono</label>
                            <div class="col-sm-7">
                                {{Form::text('telefono', null ,['class'=>'form-control', "readonly", "tabindex"=>"9",'id'=>'telefono', "data-parsley-type"=>"number"])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Celular</label>
                            <div class="col-sm-7">
                                {{Form::text('celular', null ,['class'=>'form-control', "readonly", "tabindex"=>"10",'id'=>'celular', "data-parsley-type"=>"number"])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Estado del afiliado</label>
                            <div class="col-sm-7">
                                {{Form::text('estado', null ,['class'=>'form-control', "readonly", "tabindex"=>"11",'id'=>'estado'])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Categoría</label>
                            <div class="col-sm-7">
                                {{Form::text('categoria', null ,['class'=>'form-control', "readonly", "tabindex"=>"12",'id'=>'categoria'])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Ubicación</label>
                            <div class="col-sm-7">
                                <div id="map2" style="width: 100%;height: 250px"></div>
                            </div>
                            {{Form::hidden('latitud',null,['id'=>'latitud'])}}
                            {{Form::hidden('longitud',null,['id'=>'longitud'])}}
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Departamento</label>
                            <div class="col-sm-7">
                                {{Form::select("departamento_codigo",$departamentos,null,['class'=>'form-control', "tabindex"=>"13",'id'=>'departamento'])}}
                            </div>
                            <label class="col-sm-3 control-label">Ciudad</label>
                            <div class="col-sm-7">
                                <select name="municipio_codigo" id="municipio" tabindex="14" class="form-control">
                                    <option>Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Dirección</label>
                            <div class="col-sm-7">
                                {{Form::text('direccion', null ,['class'=>'form-control', "disabled", "tabindex"=>"15",'id'=>'direccion',"data-parsley-pattern"=>"/^[A-Z üÜáéíóúÁÉÍÓÚñÑ]{1,50}$/i"])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Vía principal</label>
                            <div class="col-sm-7">
                                <select class="form-control obtener" id="viaprincipal" tabindex="16" name="vp">
                                    <option value="AP">AUTOPISTA</option>
                                    <option value="AV">AVENIDA</option>
                                    <option value="AC">AVENIDA CALLE</option>
                                    <option value="AK">AVENIDA CARRERA</option>
                                    <option value="CL" selected="selected">CALLE</option>
                                    <option value="CRA">CARRERA</option>
                                    <option value="CIRC">CIRCUNVALAR</option>
                                    <option value="DG">DIAGONAL</option>
                                    <option value="MANZ">MANZANA</option>
                                    <option value="TV">TRANSVERSAL</option>
                                    <option value="VIA">VIA</option>
                                </select>
                            </div>
                            <label class="col-sm-3 control-label">Número</label>
                            <div class="col-sm-7">
                                <input type="text" id="numerovia" name="nv" tabindex="17" class="form-control obtener"
                                       data-parsley-type="alphanum" maxlength="5" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">#</label>
                            <div class="col-sm-7">
                                <input type="text" id="numero1" name="n1" tabindex="18" class="form-control obtener"
                                       data-parsley-type="alphanum" maxlength="5" required>
                            </div>
                            <label class="col-sm-3 control-label">-</label>
                            <div class="col-sm-7">
                                <input type="text" id="numero2" name="n2" tabindex="19" class="form-control obtener"
                                       data-parsley-type="number"  maxlength="5" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Complemento</label>
                            <div class="col-sm-7">
                                <input type="text" id="complemento" name="complemento" tabindex="20" class="form-control obtener"
                                       data-parsley-pattern="^[a-zA-Z0-9]+(\s*[a-zA-Z0-9]*)*[a-zA-Z0-9]+$"  maxlength="60">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tipo</label>
                            <div class="col-sm-7">
                                {{Form::text('tipo', null ,['class'=>'form-control', "readonly", "tabindex"=>"21",'id'=>'tipo'])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Sexo</label>
                            <div class="col-sm-7">
                                {{Form::text('sexo', null ,['class'=>'form-control', "readonly", "tabindex"=>"22",'id'=>'sexo'])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Fecha Nacimiento</label>
                            <div class="col-sm-7">
                                {{Form::text('nacimiento', null ,['class'=>'form-control', "readonly", "tabindex"=>"23",'id'=>'nacimiento'])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" id="CrearIndividual" class="btn btn-custom waves-effect waves-light" disabled="disabled">Crear</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{Form::close()}}
            </div>
        </div>

    </div>
@endsection
@section('scripts')
    <script src="{{asset('plugins/jQuery-Mask-Plugin/dist/jquery.mask.min.js')}}"></script>
    {!!Html::script('plugins/jquery-autocomplete/jquery.autocomplete.min.js')!!}
    <script src="http://maps.google.com/maps/api/js?key=AIzaSyB1hUpbneHQgqsTgVZMvWc0jqUBKdQUobM&sensor=true"></script>
    <script src="{{asset('plugins/gmaps/gmaps.min.js')}}"></script>
    <script>
        $(function () {
            $('.money').mask('000.000.000.000.000', {reverse: true});

            $('#numero_tarjeta').autocomplete({
                serviceUrl: '{{route("autoCompleNumTarjeta")}}',
                lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                    return re.test(suggestion.value);
                }
            });
            /*
            $('#numero_contrato').autocomplete({
                serviceUrl: '{ {route("autoCompleNumContrato")}}',
                lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                    return re.test(suggestion.value);
                }
            });*/

            $("#creartarjetacupo").submit(function (e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    type: "POST",
                    context: document.body,
                    url: '{{route("rotativo.crearindividual")}}',
                    data: form.serialize(),
                    beforeSend: function () {
                        cargando();
                    },
                    success: function (data) {
                        if (data.estado) {
                            swal(
                                {
                                    title: 'Bien!!',
                                    text: "La tarjeta de cupo rotativo fue creada",
                                    type: 'success',
                                    confirmButtonColor: '#4fa7f3'
                                }
                            );
                            form.reset();
                        } else {
                            swal(
                                'Error!!',
                                data.mensaje,
                                'error'
                            )
                        }
                    },
                    error: function (xhr, status) {
                    },
                    // código a ejecutar sin importar si la petición falló o no
                    complete: function (xhr, status) {
                        fincarga();
                    }
                });
            });

            initMap2();
            setTimeout(getMunicipios, '300');
            $("#departamento").change(function () {
                getMunicipios();
            });

            $('.obtener').blur(function () {
                geolocalizarDireccion();
            });
        });
        var map2;
        function initMap2() {
            setTimeout(function () {
                map2 = new GMaps({
                    div: '#map2',
                    lat: 4.1405896,
                    lng: -73.6369522
                });
            }, 200);
        };

        function geolocalizarDireccion() {
            var textmunicipio = $("#municipio option:selected").html();
            var numerovia = $('#numerovia').val();
            var numero1 = $('#numero1').val();
            var numero2 = $('#numero2').val();
            var viaprincipal = $("#viaprincipal").val();
            if(textmunicipio !=""&&numerovia!=""&&numero1!=""&&numero2!=""&&viaprincipal!=""){
                var adress = viaprincipal+" "+numerovia+" #"+numero1+"-"+numero2+" "+textmunicipio;
                GMaps.geocode({
                    address: adress,
                    callback: function(results, status) {
                        if (status == 'OK') {
                            var latlng = results[0].geometry.location;
                            map2.setCenter(latlng.lat(), latlng.lng());
                            map2.removeMarkers();
                            map2.addMarker({
                                lat: latlng.lat(),
                                lng: latlng.lng()
                            });
                            $('#latitud').val(latlng.lat());
                            $('#longitud').val(latlng.lng());
                        }
                    }
                });
            }
        }
        function addMarket() {
            var latitud = $('#latitud').val();
            var longitud = $('#longitud').val();
            var nombre = $('#nombre').val();
            map.setCenter(latitud, longitud);
            map.addMarker({
                lat: latitud,
                lng: longitud,
                title: nombre,
                infoWindow: {
                    content: '<p>'+nombre+'</p>'
                }
            });
        }
        /*
        function getMarket() {
            $.get("{ {route('marketsucursales',['id'=>$establecimiento->id])}}",{},function (data) {
                for(i=0;i<data.length;i++){
                    map.setCenter(data[0].latitud, data[0].longitud);
                    map.addMarker({
                        lat: data[i].latitud,
                        lng: data[i].longitud,
                        title: data[i].nombre,
                        infoWindow: {
                            content: '<p>'+data[i].nombre+'</p>'
                        }
                    });
                }
            })
        }
        */
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
            $('#CrearIndividual').attr('disabled', true);
            $('#tipo').attr('readonly', true);
            $('#email').attr('readonly', true);
            //$('#tipo option:not(:selected)').attr('disabled',true);
            document.getElementById('tipo').value = "Tercero";
            $('#razon_social').removeAttr('readonly');
            $('#representante_legal').removeAttr('readonly');
            $('#direccion').removeAttr('readonly');
            $('#email').removeAttr('readonly');
            $('#telefono').removeAttr('readonly');
            $('#celular').removeAttr('readonly');

            document.getElementById('categoria').value = "";
            document.getElementById('apellidos').value = "";
            document.getElementById('nombres').value = "";
            document.getElementById('direccion').value = "";
            document.getElementById('email').value = "";
            document.getElementById('sexo').value = "";
            document.getElementById('nacimiento').value = "";
            document.getElementById('telefono').value = "";
            document.getElementById('celular').value = "";
            document.getElementById('estado').value = "";
            document.getElementById('salario').value = "";

        }
        function consultarWS() {
           // var tipo = $("#tipo_documento").val();
            //var num = $("#nit").val();
            var empresa = $("#empresa").val();
            var identificacion = $("#identificacion").val();
            $.ajax({
                method: "get",
                url: "{!!route('consultartrabajador')!!}",
                data: {
                    "empresa": empresa,
                    "identificacion": identificacion
                },
                error: function (data) {
                    //something went wrong with the request
                    alert("Error");
                },
                success: function (data) {

                    if(data.tipo=="A")
                    {
                        document.getElementById("mensaje").style.visibility = "hidden";
                        document.getElementById('categoria').value = data.categoria;
                        document.getElementById('apellidos').value = data.apellidos;
                        document.getElementById('nombres').value = data.nombres;
                        document.getElementById('direccion').value = data.direccion;
                        document.getElementById('email').value = data.email;
                        document.getElementById('sexo').value = data.sexo;
                        document.getElementById('nacimiento').value = data.fecha_nacimiento;
                        document.getElementById('telefono').value = data.telefono;
                        document.getElementById('celular').value = data.celular;
                        document.getElementById('estado').value = data.estado;
                        document.getElementById('salario').value = data.salario;
                        document.getElementById('tipo').value = "Afiliado";//data.tipo
                        document.getElementById('departamento').value = data.departamento_codigo;
                        getMunicipios();
                        document.getElementById('municipio').value = data.municipio_codigo;
                       /* $('#razon_social').attr('readonly', true);
                        $('#representante_legal').attr('readonly', true);
                        $('#direccion').attr('readonly', true);*/
                        if(data.email == "NA")
                            $('#email').removeAttr('readonly');
                        else
                            $('#email').attr('readonly', true);
                        $('#CrearIndividual').removeAttr('disabled');

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
@endsection
<div id="modalcrearsucursal">
    {{Form::open(['route'=>['sucursal.crearp', $establecimiento_id], 'class'=>'form-horizontal', 'id'=>'crearsucursal'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Agregar sucursal</h4>
    </div>
    <div class="modal-body">

        <div class="form-group">
            <label class="col-md-2 control-label">Nombre</label>
            <div class="col-md-10">
                {{Form::text('nombre', null ,['class'=>'form-control', "required", "maxlength"=>"60", "data-parsley-pattern"=>"^[a-zA-Z0-9]+(\s*[a-zA-Z0-9]*)*[a-zA-Z0-9]+$"])}}

            </div>
        </div>

        <div class="form-group">
            <label class="col-md-12 ">Ubicacion</label>
            <div class="col-md-12">
                <div id="map2" style="width: 100%;height: 250px"></div>
            </div>
            {{Form::hidden('latitud',null,['id'=>'latitud'])}}
            {{Form::hidden('longitud',null,['id'=>'longitud'])}}
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">Departamento</label>
            <div class="col-md-4">
                {{Form::select("departamento_codigo",$departamentos,null,['class'=>'form-control', 'id'=>'departamento'])}}
            </div>
            <label class="col-md-2 control-label">Ciudad</label>
            <div class="col-md-4">
                <select name="municipio_codigo" id="municipio" class="form-control">
                    <option>Seleccione...</option>
                </select>
            </div>
        </div>
        <div class="form-group">

            <label class="col-md-2 control-label">Via principal</label>
            <div class="col-md-4">
                <select class="form-control obtener" id="viaprincipal" name="vp">
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
            <label class="col-md-2 control-label">Numero</label>
            <div class="col-md-4">
                <input type="text" id="numerovia" name="nv" class="form-control obtener"
                       data-parsley-type="alphanum" maxlength="5" required>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label">#</label>
            <div class="col-md-4">
                <input type="text" id="numero1" name="n1" class="form-control obtener"
                       data-parsley-type="alphanum" maxlength="5" required>
            </div>
            <label class="col-md-2 control-label">-</label>
            <div class="col-md-4">
                <input type="text" id="numero2" name="n2" class="form-control obtener"
                       data-parsley-type="number"  maxlength="5" required>
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-2 control-label">Complemento</label>
            <div class="col-md-10">
                <input type="text" id="complemento" name="complemento" class="form-control obtener"
                data-parsley-pattern="^[a-zA-Z0-9]+(\s*[a-zA-Z0-9]*)*[a-zA-Z0-9]+$"  maxlength="60" required>
            </div>
        </div>


        <div class="form-group">
            <label class="col-md-2 control-label">Contraseña</label>
            <div class="col-md-10">
                {{Form::password('password', ['class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10"])}}
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
        $("#crearsucursal").parsley();
        $("#crearsucursal").submit(function (e) {
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
                lat: -12.043333,
                lng: -77.028333
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


    function getMunicipios() {
        var dept = $("#departamento").val();
        $.get('{{route('municipios')}}', {data: dept}, function (result) {
            $('#municipio').html("");
            $.each(result, function (i, value) {
                $('#municipio').append($('<option>').text(value.descripcion).attr('value', value.codigo));
            });
        })
    }

</script>
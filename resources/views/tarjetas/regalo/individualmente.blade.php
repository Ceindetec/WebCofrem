@extends('layouts.admin')

@section('styles')
    {!!Html::style('plugins/jquery-autocomplete/jquery.autocomplete.css')!!}
@endsection

@section('contenido')

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Crear Tarjetas Regalo Individualmente</h3>
                    </div>
                    <div class="panel-body">
                        <form id="formCrearTajertaRegalo" class="form-horizontal m-t-10" role="form">
                            <div class="form-group">
                                <label for="numero_factura" name="numero_factura" class="col-sm-3 control-label">Número Factura</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="numero_factura" name="numero_factura" placeholder="Número Factura">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="numero_tarjeta" class="col-sm-3 control-label">Número Tarjeta</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="numero_tarjeta" name="numero_tarjeta" placeholder="Número Tarjeta">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="monto" class="col-sm-3 control-label">Monto</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control money" id="monto" name="monto" placeholder="Monto">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nit" class="col-sm-3 control-label">NIT</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="nit" name="nit" placeholder="NIT">
                                </div>
                            </div>

                            <div id="result" class="col-sm-offset-2 col-sm-8">

                            </div>

                            <div class="form-group m-b-0">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button id="btnCrearTRegalo" type="submit" class="btn btn-info">Crear Tarjeta</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection

@section('scripts')
    <script src="{{asset('plugins/jQuery-Mask-Plugin/dist/jquery.mask.min.js')}}"></script>
    {!!Html::script('plugins/jquery-autocomplete/jquery.autocomplete.min.js')!!}
    <script>
        $(function () {
            $('.money').mask('000.000.000.000.000', {reverse: true});

            $('#numero_tarjeta').autocomplete({
                serviceUrl: '{{route("autoCompleNumTarjeta")}}',
                lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                    return re.test(suggestion.value);
                },
                onSelect: function(suggestion) {
                    //console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
                    $("#btnCrearTRegalo").removeClass("btn-primary").addClass("btn-success");
                    $("#btnCrearTRegalo").html("Asociar Servicio Regalo");
                    $("#btnCrearTRegalo").removeClass("buscar").addClass("agregar");
                },
                onHint: function (hint) {
                    //$('#producto-x').val(hint);
                },
                onInvalidateSelection: function() {
                    $("#btnCrearTRegalo").removeClass("btn-success").addClass("btn-primary");
                    $("#btnCrearTRegalo").html("Crear Tarjeta");
                    $("#btnCrearTRegalo").removeClass("agregar").addClass("buscar");
//                    $("#buscarProducto").attr("disabled",true);
                }
            });




            $("#formCrearTajertaRegalo").submit(function (e) {
                e.preventDefault();
                var form = $(this);

                console.log(form.serialize());

                $.ajax({
                    type:"POST",
                    context: document.body,
                    url: '{{route("addTarjetaRegalo")}}',
                    data:form.serialize(),
                    beforeSend: function () {
                        cargando();
                    },
                    success: function (data) {
                        if (data.estado) {
                            alertResul("mdi-check-all","Perfecto!","La tarjeta fue creada","success");
                        } else  {
                            alertResul("mdi-block-helper","Error!",data.mensaje,"danger");
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
        });
        /**
         * Fin de Ready
         */

        function alertResul(icono,titulo,mensaje,alert) {

            var html = "<div class='alert alert-icon alert-"+alert+" alert-dismissible fade in' role='alert'>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                "<span aria-hidden='true'>×</span>" +
                "</button>" +
                "<i class='mdi "+icono+"'></i>" +
                "<strong>"+titulo+"</strong> " + mensaje +
                "</div>";
            $("#result").html(html);
        }

    </script>
@endsection
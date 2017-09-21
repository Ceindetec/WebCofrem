@extends('layouts.admin')

@section('styles')
    {!!Html::style('plugins/jquery-autocomplete/jquery.autocomplete.css')!!}
    {!!Html::style('plugins/sweet-alert2/animate.css')!!}
@endsection

@section('contenido')

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Crear Tarjetas Regalo Individualmente </h4>
            </div>
        </div> <!-- end row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box widget-inline">

                    <div class="row">
                        <form id="formCrearTajertaRegalo" class="form-horizontal m-t-10" role="form">
                            <div class="form-group">
                                <label for="nit" class="col-sm-3 control-label">NIT</label>
                                <div class="col-sm-7">
                                    {{Form::text('nit', null ,['class'=>'form-control', "required", "required","placeholder"=>"NIT", "maxlength"=>"15"])}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="numero_factura" name="numero_factura" class="col-sm-3 control-label">Número
                                    Factura</label>
                                <div class="col-sm-7">
                                    {{Form::text('numero_factura', null ,['class'=>'form-control', "required","placeholder"=>"Número de la Factura", "maxlength"=>"15"])}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="numero_tarjeta" class="col-sm-3 control-label">Número Tarjeta</label>
                                <div class="col-sm-7">
{{--                                    {{Form::text('numero_tarjeta', null ,['class'=>'form-control','id'=>'numero_tarjeta', "required","placeholder"=>"Número Tarjeta", "maxlength"=>"7", "onkeypress"=>"return justNumbers(event)"])}}--}}
                                    <input type="text" class="form-control" id="numero_tarjeta" name="numero_tarjeta" placeholder="Número Tarjeta">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="monto" class="col-sm-3 control-label">Monto</label>
                                <div class="col-sm-7">
                                    {{Form::text('monto', null ,['class'=>'form-control money', "required", "maxlength"=>"10", "data-parsley-type"=>"number" ,"placeholder"=>"Monto"])}}
                                </div>
                            </div>

                            <div class="form-group m-b-0">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button id="btnCrearTRegalo" type="submit"
                                            class="btn btn-custom waves-effect waves-light">Crear Tarjeta
                                    </button>
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
                lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                    return re.test(suggestion.value);
                },
                onSelect: function (suggestion) {
                    //console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
                    $("#btnCrearTRegalo").removeClass("btn-custom").addClass("btn-success");
                    $("#btnCrearTRegalo").html("Asociar Servicio Regalo");
                },
                onHint: function (hint) {
                    //$('#producto-x').val(hint);
                },
                onInvalidateSelection: function () {
                    $("#btnCrearTRegalo").removeClass("btn-success").addClass("btn-custom");
                    $("#btnCrearTRegalo").html("Crear Tarjeta");
                }
            });


            $("#formCrearTajertaRegalo").submit(function (e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    type: "POST",
                    context: document.body,
                    url: '{{route("addTarjetaRegalo")}}',
                    data: form.serialize(),
                    beforeSend: function () {
                        cargando();
                    },
                    success: function (data) {
                        if (data.estado) {
                            swal(
                                {
                                    title: 'Bien!!',
                                    text: "La tarjeta fue creada",
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
        });

        /**
         * Fin de Ready
         */



    </script>
@endsection
@extends('layouts.admin')

@section('styles')
    {!!Html::style('plugins/jquery-autocomplete/jquery.autocomplete.css')!!}
    {!!Html::style('plugins/sweet-alert2/animate.css')!!}
@endsection

@section('contenido')

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Registrar Bono Empresarial Individual</h4>
            </div>
        </div> <!-- end row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box widget-inline">
                    {{Form::open(['route'=>['bono.crearindividual'], 'class'=>'form-horizontal', 'id'=>'creartarjetabono'])}}
        <div class="row">
            <div class="form-group">
                <label class="col-sm-3 control-label">Número de contrato</label>
                <div class="col-sm-7">
                {{Form::text('numero_contrato', null ,['class'=>'form-control', "required", "tabindex"=>"1",'id'=>'numero_contrato'])}} <!-- "data-parsley-type"=>"number"] -->
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Número de tarjeta</label>
                <div class="col-sm-7">
                {{Form::text('numero_tarjeta', null ,['class'=>'form-control', "required", "maxlength"=>"7", "data-parsley-type"=>"number", "tabindex"=>"2",'id'=>'numero_tarjeta'])}} <!-- "data-parsley-type"=>"number"] -->
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Monto</label>
                <div class="col-sm-7">
                {{Form::text('monto', null ,['class'=>'form-control money', "required", "data-parsley-type"=>"number", "tabindex"=>"3",'id'=>'monto'])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Identificación</label>
                <div class="col-sm-7">
                    {{Form::text('identificacion', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "tabindex"=>"4",'id'=>'identificacion'])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Nombres</label>
                <div class="col-sm-7">
                    {{Form::text('nombres', null ,['class'=>'form-control', "required", "tabindex"=>"5",'id'=>'nombres'])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Apellidos</label>
                <div class="col-sm-7">
                    {{Form::text('apellidos', null ,['class'=>'form-control', "required", "tabindex"=>"6",'id'=>'apellidos'])}}
                </div>
            </div>
            <!--
            <div class="form-group">
                <label class="col-sm-3 control-label">Tipo de persona</label>
                <div class="col-sm-7">
                    { {Form::select("tipo_persona",['A'=>'Afiliado','T'=>'Tercero'],null,['class'=>'form-control', "tabindex"=>"8",'id'=>'tipo_persona', "required"])}}
                </div>
            </div>
            -->
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                <button type="submit" id="CrearBIndividual" class="btn btn-custom waves-effect waves-light">Crear</button>
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
    <script>
        $(function () {
            $('.money').mask('000.000.000.000.000', {reverse: true});

            $('#numero_tarjeta').autocomplete({
                serviceUrl: '{{route("autoCompleNumTarjeta")}}',
                lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                    return re.test(suggestion.value);
                }
                /*
                 ,
                 onSelect: function (suggestion) {
                 //console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
                 $("#CrearBIndividual").removeClass("btn-custom").addClass("btn-success");
                 $("#CrearBIndividual").html("Asociar Servicio Bono");
                 },
                 onHint: function (hint) {
                 //$('#producto-x').val(hint);
                 },
                 onInvalidateSelection: function () {
                 $("#CrearBIndividual").removeClass("btn-success").addClass("btn-custom");
                 $("#CrearBIndividual").html("Crear Tarjeta Bono");
                 }

                 */
            });

            $('#numero_contrato').autocomplete({
                serviceUrl: '{{route("autoCompleNumContrato")}}',
                lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                    return re.test(suggestion.value);
                },
                onSelect: function (suggestion) {
                    //console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
                    $("#CrearBIndividual").removeClass("btn-custom").addClass("btn-success");
                    $("#CrearBIndividual").html("Asociar Contrato Bono");
                },
                onHint: function (hint) {
                    //$('#producto-x').val(hint);
                },
                onInvalidateSelection: function () {
                    $("#CrearBIndividual").removeClass("btn-success").addClass("btn-custom");
                    $("#CrearBIndividual").html("Crear Tarjeta Bono");
                }
            });

            $("#creartarjetabono").submit(function (e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    type: "POST",
                    context: document.body,
                    url: '{{route("bono.crearindividual")}}',
                    data: form.serialize(),
                    beforeSend: function () {
                        cargando();
                    },
                    success: function (data) {
                        if (data.estado) {
                            swal(
                                {
                                    title: 'Bien!!',
                                    text: "La tarjeta bono fue creada",
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
            $('#identificacion').blur(function(e){
                $.get('{{route("getNombre")}}',{identificacion:$(this).val()},function(result)
                {
                    $("#nombres").val("hola1");
                   if(result)
                   {
                       $("#nombres").val(result.nombres);
                       $("#apellidos").val(result.apellidos);
                   }
                });
            });


        });

        /**
         * Fin de Ready
         */
    </script>
@endsection
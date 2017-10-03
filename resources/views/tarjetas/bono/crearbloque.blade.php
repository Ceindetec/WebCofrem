@extends('layouts.admin')
@section('styles')
    {!!Html::style('plugins/jquery-autocomplete/jquery.autocomplete.css')!!}
@endsection
@section('contenido')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Crear Tarjetas Bono En Bloque </h4>
            </div>
        </div> <!-- end row -->

        <div class="row">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box widget-inline">
                        <div class="row">
                        {{Form::open(['route'=>['bono.crearbloque'],'files'=>'true' ,'class'=>'form-horizontal', 'id'=>'creartarjetabonob', 'target'=>"_blank",'role'=>'form','method'=>'POST'])}}
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Número de contrato</label>
                                <div class="col-sm-7">
                                {{Form::text('numero_contrato', null ,['class'=>'form-control', "required", "tabindex"=>"1",'id'=>'numero_contrato'])}} <!-- "data-parsley-type"=>"number"] -->
                                </div>
                            </div>
                           <!-- <div class="form-group">
                                <label class="col-sm-3 control-label">Cantidad</label>
                                <div class="col-sm-7">
                                    {{Form::number('cantidad', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "min"=>"1","tabindex"=>"2",'id'=>'cantidad'])}}
                                </div>
                            </div> -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Número de tarjeta inicial</label>
                                <div class="col-sm-7">
                                {{Form::text('numero_tarjeta_inicial', null ,['class'=>'form-control', "required", "maxlength"=>"6", "data-parsley-type"=>"number", "tabindex"=>"3",'id'=>'numero_tarjeta_inicial'])}} <!-- "data-parsley-type"=>"number"] -->
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Seleccionar archivo</label>
                                <div class="col-sm-7">
                                    {{Form::file('archivo',['class'=>'form-control', "required"=>"true", "tabindex"=>"4",'id'=>'archivo','accept'=>'.txt'])}}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" id="CrearB" class="btn btn-custom waves-effect waves-light">Crear</button>
                                </div>
                            </div>
                        {{Form::close()}}
                        </div>
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
            $('#numero_contrato').autocomplete({
                serviceUrl: '{{route("autoCompleNumContrato")}}',
                lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
                    var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
                    return re.test(suggestion.value);
                }
            });
            $("#creartarjetabonob").submit(function (e) {
                e.preventDefault();
                var form = $(this);

                var formData = new FormData(form[0]);
                formData.append( 'archivo', $( '#archivo' )[0].files[0] );

                $.ajax({
                    type: "POST",
                    context: document.body,
                    url: '{{route("bono.crearbloque")}}',
                    processData: false,
                    contentType: false,
                    data: formData,
                    beforeSend: function () {
                        cargando();
                    },
                    success: function (data) {
                        if (data.estado) {
                            swal(
                                {
                                    title: 'Bien!!',
                                    text: data.mensaje,
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
    </script>
@endsection
@extends('layouts.admin')

@section('styles')

@endsection

@section('contenido')

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Crear Tarjetas Regalo En Bloque </h4>
            </div>
        </div> <!-- end row -->

        <div class="row">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-box widget-inline">

                        <div class="row">
                            <form id="formCrearTajertaBloque" class="form-horizontal m-t-10" role="form">
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
                                        {{Form::text('numero_factura', null ,['class'=>'form-control', "required","placeholder"=>"Número de la factura", "maxlength"=>"15"])}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="numero_tarjeta_inicial" class="col-sm-3 control-label">Número Tarjeta Inicial</label>
                                    <div class="col-sm-7">
                                    {{Form::text('numero_tarjeta_inicial', null ,['class'=>'form-control', "required","placeholder"=>"Número de la primera tarjeta", "maxlength"=>"7", "onkeypress"=>"return justNumbers(event)"])}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cantidad" class="col-sm-3 control-label">Cantidad</label>
                                    <div class="col-sm-7">
                                        {{Form::text('cantidad', null ,['class'=>'form-control', "required","maxlength"=>"10","placeholder"=>"Cantidad","onkeypress"=>"return justNumbers(event)"])}}
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
                                                class="btn btn-custom waves-effect waves-light">Crear Tarjeta en Bloque
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{asset('plugins/jQuery-Mask-Plugin/dist/jquery.mask.min.js')}}"></script>
    <script>
        $(function () {
            $('.money').mask('000.000.000.000.000', {reverse: true});

            $("#formCrearTajertaBloque").submit(function (e) {
                e.preventDefault();
                var form = $(this);

                $.ajax({
                    type: "POST",
                    context: document.body,
                    url: '{{route("addTarjetaRegaloBloque")}}',
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

    </script>
@endsection
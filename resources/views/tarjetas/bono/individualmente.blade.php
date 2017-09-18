@extends('layouts.admin')

@section('styles')
    <link href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/buttons.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/responsive.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/scroller.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/dataTables.colVis.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/dataTables.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/fixedColumns.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('contenido')
    {{Form::open(['route'=>['bono.crearindividual'], 'class'=>'form-horizontal', 'id'=>'creartarjetabono'])}}
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Creación Individual de Tarjeta Bono</h4>
            </div>
        </div> <!-- end row -->

        <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label">Número de contrato</label>
                <div class="col-md-10">
                {{Form::text('numero_tarjeta', null ,['class'=>'form-control', "required", "maxlength"=>"7", "data-parsley-type"=>"number", "tabindex"=>"1",'id'=>'numero_tarjeta'])}} <!-- "data-parsley-type"=>"number"] -->
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Número de tarjeta</label>
                <div class="col-md-10">
                {{Form::text('numero_tarjeta', null ,['class'=>'form-control', "required", "maxlength"=>"7", "data-parsley-type"=>"number", "tabindex"=>"1",'id'=>'numero_tarjeta'])}} <!-- "data-parsley-type"=>"number"] -->
                </div>
            </div>
           <!-- <div class="form-group">
                <label class="col-md-2 control-label">Tipo de servicio</label>
                <div class="col-md-10">
                    { {Form::select("servicio_codigo",$servicios,null,['class'=>'form-control', "tabindex"=>"2",'id'=>'tarjeta_codigo', "required"])}}
                </div>
            </div>-->
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-custom waves-effect waves-light">Crear</button>
    </div>
    {{Form::close()}}
@endsection

@section('scripts')
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/buttons.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/jszip.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/pdfmake.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/vfs_fonts.js')}}"></script>
    <script src="{{asset('plugins/datatables/buttons.html5.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/buttons.print.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.keyTable.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/responsive.bootstrap.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.scroller.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.colVis.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.fixedColumns.min.js')}}"></script>
    <script src="{{asset('plugins/parsleyjs/parsley.min.js')}}"></script>
    <script src="{{asset('plugins/parsleyjs/idioma/es.js')}}"></script>

    <script>
        $(function () {
            $("#creartarjetabono").parsley();
            $("#creartarjetabono").submit(function (e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url : form.attr('action'),
                    data : form.serialize(),
                    type : 'POST',
                    dataType : 'json',
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
                        } else if (result.estado == false) {
                            swal(
                                'Error!!',
                                result.mensaje,
                                'error'
                            );
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
                    error : function(xhr, status) {
                        var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                        swal(
                            'Error!!',
                            message,
                            'error'
                        )
                    },
                    // código a ejecutar sin importar si la petición falló o no
                    complete : function(xhr, status) {
                        fincarga();
                    }
                });
            })

        })

    </script>
@endsection
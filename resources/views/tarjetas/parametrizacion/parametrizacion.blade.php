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

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Parametrización de servicios </h4>
            </div>
        </div> <!-- end row -->


        <div class="card-box">
            <h4 class="m-t-0">Valor del plástico</h4>
            <br>
            {{Form::open(['route'=>['tarjeta.parametro.valor'], 'class'=>'form-inline', 'id'=>'parametrovalor'])}}
            <div class="form-group">
                <label for="valor">Valor: </label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                    <input type="text" class="form-control dinero" name="valor"
                           value="{{$valorTarjeta==null?'':$valorTarjeta->valor}}" id="valor">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-custom">Guardar</button>
            </div>
            {{Form::close()}}
            <br>
            <div class="table-responsive m-b-20">
                <table id="datatablevalorplatico" class="table table-striped table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Fecha creación</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20 ">Parametrizar servicio </h4>
            </div>
        </div> <!-- end row -->

        <div class="card-box">
            <h4 class="m-t-0">Seleccione servicio a parametrizar</h4>
            <br>
            <div class="form-group">
                <div class="col-md-8">
                {{Form::select("tarjeta_codigo",$tipotarjetas,null,['class'=>'form-control', "tabindex"=>"2",'id'=>'serviciocodigo'])}}
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-custom" id="selecparametrizar">Parametrizar</button>
                </div>
            </div>
            <br>

        </div>

        <div id="contenparametrizacion">

        </div>



    </div>

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
    <script src="{{asset('plugins/jQuery-Mask-Plugin/dist/jquery.mask.min.js')}}"></script>

    <script>
        var table;
        $(function () {

            $('.dinero').mask('000.000.000.000,00', {reverse: true});

            $("#parametrovalor").parsley();

            $("#parametrovalor").submit(function (e) {
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
            
            table = $('#datatablevalorplatico').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": "{!!route('datatable_es')!!}"
                },
                ajax: {
                    url: "{!!route('gridvalorplatico')!!}",
                    "type": "get"
                },
                columns: [
                    {
                        data: 'valor',
                        name: 'valor',
                        render: function (data) {
                            return "$ "+enmascarar(data);
                        }
                    },
                    {data: 'estado', name: 'estado',
                        render: function(data){
                            if(data=='A'){
                                html= '<div class="label-success" > <strong style="color:#fff">Activo</strong></div>';
                                return html;
                            }
                            else
                                return 'Inactivo';
                        }
                    },
                    {data: 'created_at', name: 'created_at'},
                ],
                "order": [[ 2, "desc" ]]
            });

            $('#selecparametrizar').click(function () {
                $('#contenparametrizacion').load("{{route('viewparametrizarservicio')}}"+"?codigo="+$('#serviciocodigo').val(), function (response, status, xhr) {
                    switch (status) {
                        case "success":

                            break;

                        case "error":
                            var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                            if (xhr.status == 403) {
                                swal(
                                    'Error!!',
                                    response,
                                    'error'
                                )
                            }
                            else {
                                swal(
                                    'Error!!',
                                    message,
                                    'error'
                                )
                            }
                            break;
                    }

                });
            })
            
        });


    </script>
@endsection
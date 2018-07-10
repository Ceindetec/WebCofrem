@extends('layouts.admin')

@section('styles')
    <link href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/buttons.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/responsive.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/scroller.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/dataTables.colVis.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/dataTables.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/fixedColumns.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <style>
        tfoot input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }
    </style>
@endsection

@section('contenido')

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Traslado de terminales </h4>
            </div>
        </div> <!-- end row -->

        <div class="col-sm-12">
            <div class="card-box widget-inline">
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center">
                            <h3 class="m-t-10"><i class="text-primary fa fa-university"></i> <b
                                        data-plugin="counterup">{{count($establecimientos)}}</b></h3>
                            <p class="text-muted">Establecimientos activos</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center">
                            <h3 class="m-t-10"><i class="text-custom mdi mdi-airplay"></i> <b
                                        data-plugin="counterup">{{count($sucursales)}}</b></h3>
                            <p class="text-muted">Sucursales activas</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center">
                            <h3 class="m-t-10"><i class="text-custom mdi mdi-cellphone-link"></i> <b
                                        data-plugin="counterup">{{count($terminalesActivas)}}</b></h3>
                            <p class="text-muted">Terminales activas</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="widget-inline-box text-center b-0">
                            <h3 class="m-t-10"><i class="text-danger mdi mdi-cellphone-link"></i> <b
                                        data-plugin="counterup">{{count($terminalesInactivas)}}</b></h3>
                            <p class="text-muted">Terminales Inactivas</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive m-b-20">
                    <table id="datatable" class="table table-striped table-bordered" width="100%">
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Celular</th>
                            <th>Número activo</th>
                            <th>Establecimiento</th>
                            <th>Sucursal</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Código</th>
                            <th>Celular</th>
                            <th>Número activo</th>
                            <th>Establecimiento</th>
                            <th>Sucursal</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
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
    <script>
        var table;
        $(function () {

            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": "{!!route('datatable_es')!!}"
                },
                ajax: {
                    url: "{!!route('gridterminalestraslado')!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'codigo', name: 'codigo'},
                    {data: 'celular', name: 'celular'},
                    {data: 'numero_activo', name: 'numero_activo'},
                    {data: 'get_sucursal.get_establecimiento.razon_social', name: 'Establecimiento'},
                    {data: 'get_sucursal.nombre', name: 'Sucursal'},
                    {
                        data: 'estado',
                        name: 'estado',
                        render: function (data) {
                            if (data == "A") {
                                return 'Activa';
                            } else {
                                return 'Inactiva';
                            }
                        }
                    },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                initComplete: function () {
                    this.api().columns().every(function () {
                        var column = this;
                        if(column.footer().innerHTML != ""){
                            var input = document.createElement("input");
                            $(input).appendTo($(column.footer()).empty())
                                .on('keyup', function () {
                                    column.search($(this).val(), false, false, true).draw();
                                });
                        }
                    });
                }
            });


        });

    </script>
@endsection
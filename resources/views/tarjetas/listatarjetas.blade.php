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
        div.ajuste li{
            font-size: 3em !important;
        }
    </style>
@endsection

@section('contenido')

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Inventario general de tarjetas </h4>
            </div>
        </div> <!-- end row -->
        <div class="card-box col-md-4">
            <h4 class="m-t-0">Regalo</h4>
            <div class="col-md-12"><label>Total en el sistema: &nbsp; </label><span>{{count($totalRegalo)}}</span></div>
            <div class="col-md-12"><label>Sin asignar: &nbsp;</label>{{count($totalRegaloSin)}}</div>
            <div class="col-md-12"><label>Asignadas: &nbsp;</label>{{count($totalRegaloAsin)}}</div>
        </div>
        <div class="card-box col-md-4">
            <h4 class="m-t-0">Bono</h4>
            <div class="col-md-12"><label>Total en el sistema: &nbsp;</label><span>{{count($totalBono)}}</span></div>
            <div class="col-md-12"><label>Sin asignar: &nbsp;</label>{{count($totalBonoSin)}}</div>
            <div class="col-md-12"><label>Asignadas: &nbsp;</label>{{count($totalBonoAsin)}}</div>
        </div>
        <div class="card-box col-md-4">
            <h4 class="m-t-0">Cupo</h4>
            <div class="col-md-12"><label>Total en el sistema: &nbsp;</label><span>{{count($totalCupo)}}</span></div>
            <div class="col-md-12"><label>Sin asignar: &nbsp;</label>{{count($totalCupoSin)}}</div>
            <div class="col-md-12"><label>Asignadas: &nbsp;</label>{{count($totalCupoAsin)}}</div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <h5>Acciones</h5>
                <div class="card-box widget-inline">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="widget-inline-box">
                                <a href="{{route('tarjetas.crear')}}" data-modal
                                   class="btn btn-custom waves-effect waves-light" data-toggle="modal"
                                   data-target="#modalrol">Agregar Tarjeta</a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="widget-inline-box">
                                <a href="{{route('tarjetas.crearbloque')}}" data-modal
                                   class="btn btn-custom waves-effect waves-light" data-toggle="modal"
                                   data-target="#modalrol">Agregar Tarjetas en Bloque</a>
                            </div>
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
                            <th>Número de tarjeta</th>
                            <th>Tipo</th>
                            <th>Cambio clave</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
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
    <script src="{{asset('plugins/parsleyjs/parsley.min.js')}}"></script>
    <script src="{{asset('plugins/parsleyjs/idioma/es.js')}}"></script>

    <script>
        var table;
        $(function () {
            console.log();
            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": "{!!route('datatable_es')!!}"
                },
                ajax: {
                    url: "{!!route('gridtarjetas')!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'numero_tarjeta', name: 'numero_tarjeta'},
                    {
                        data: 'servicios', name: 'servicios'

                    },
                    {
                        data: 'cambioclave', name: 'cambioclave',
                        render: function (data) {
                            if (data == '0')
                                return 'Pendiente';
                            else
                                return 'Hecho';
                        }
                    },
                    {
                        data: 'estado',
                        name: 'estado',
                        render: function (data) {
                            if(data == 'C'){
                                return 'Sin asignar'
                            }else if(data == 'A'){
                                return 'Activa'
                            }else{
                                return 'Inactiva'
                            }
                        }
                    },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']]
            });
        })
    </script>
@endsection
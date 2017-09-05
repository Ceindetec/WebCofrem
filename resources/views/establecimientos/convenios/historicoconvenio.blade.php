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
        .dt-right{
            text-align: right;
        }
    </style>

@endsection

@section('contenido')

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Historico convenio No. {{$convenio->numero_convenio}} del establecimiento {{$convenio->getEstablecimiento->razon_social}}
                    <span class="pull-right"><a href="{{route('establecimiento.editar',[$convenio->establecimiento_id])}}" class="btn btn-custom"
                                                style="position: relative; top: -7px"><i class="ti-arrow-left"></i> Volver</a> </span>
                </h4>
            </div>
        </div> <!-- end row -->

        <div class="card-box">
            <h4 class="m-t-0">Historico de rangos</h4>
            <div class="table-responsive m-b-20">
                <table id="datatablehistorial" class="table table-striped table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>Evento</th>
                        <th>Antrior valor</th>
                        <th>Nuevo valor</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="card-box">
            <h4 class="m-t-0">Historico de frecuencia de cortes</h4>
            <div class="table-responsive m-b-20">
                <table id="datatablehistorialcorde" class="table table-striped table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>Evento</th>
                        <th>Antrior valor</th>
                        <th>Nuevo valor</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="card-box">
            <h4 class="m-t-0">Plazos de pago</h4>
            <div class="table-responsive m-b-20">
                <table id="datatablehistorialpagos" class="table table-striped table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>Evento</th>
                        <th>Antrior valor</th>
                        <th>Nuevo valor</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                    </tr>
                    </thead>
                </table>
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
    <script src="{{asset('plugins/moment/moment.js')}}"></script>





    <script>

        $(function () {
            $('#datatablehistorial').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": "{!!route('datatable_es')!!}"
                },
                ajax: {
                    url: "{!!route('gridHitorialrangos',['id'=>$convenio->id])!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'auditable_id', name: 'auditable_id'},
                    {data: 'event', name: 'event'},
                    {data: 'old_values', name: 'old_values'},
                    {data: 'new_values', name: 'new_values'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'name', name: 'name'}
                ],
                order: [[1, 'asc']]
            });

            $('#datatablehistorialcorde').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": "{!!route('datatable_es')!!}"
                },
                ajax: {
                    url: "{!!route('gridFrecuencia',['id'=>$convenio->id])!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'auditable_id', name: 'auditable_id'},
                    {data: 'event', name: 'event'},
                    {data: 'old_values', name: 'old_values'},
                    {data: 'new_values', name: 'new_values'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'name', name: 'name'}
                ],
                order: [[1, 'asc']]
            });

            $('#datatablehistorialpagos').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": "{!!route('datatable_es')!!}"
                },
                ajax: {
                    url: "{!!route('gridFrecuencia',['id'=>$convenio->id])!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'auditable_id', name: 'auditable_id'},
                    {data: 'event', name: 'event'},
                    {data: 'old_values', name: 'old_values'},
                    {data: 'new_values', name: 'new_values'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'name', name: 'name'}
                ],
                order: [[1, 'asc']]
            });

        });



    </script>
@endsection
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
                <h4 class="header-title m-t-0 m-b-20">Gestionar empresas </h4>
            </div>
        </div> <!-- end row -->

        <div class="row">
            <div class="col-sm-12">
                <h5>Acciones</h5>
                <div class="card-box widget-inline">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="widget-inline-box">
                              <a href="{{route('empresa.crear')}}" data-modal class="btn btn-custom waves-effect waves-light" data-toggle="modal" data-target="#modalrol">Agregar Empresa</a>
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
                            <th>Nit</th>
                            <th>Razon social</th>
                            <th>Representante Legal</th>
                            <th>Departamento</th>
                            <th>Municipio</th>
                            <th>Email</th>
                            <th>Telefono</th>
                            <th>Celular</th>
                            <th>Direccion</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Nit</th>
                            <th>Razon social</th>
                            <th>Representante Legal</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
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
                    url: "{!!route('gridempresas')!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'nit', name: 'nit'},
                    {data: 'razon_social', name: 'razon_social'},
                    {data: 'representante_legal', name: 'representante_legal'},
                    {data: 'get_municipio.get_departamento.descripcion', name: 'descripcion'},
                    {data: 'get_municipio.descripcion', name: 'descripcion'},
                    {data: 'email', name: 'email'},
                    {data: 'telefono', name: 'telefono'},
                    {data: 'celular', name: 'celular'},
                    {data: 'direccion', name: 'direccion'},
                    {data: 'tipo', name: 'tipo',
                        render: function (data) {
                            if(data=='A'){
                                return 'Afiliado'
                            }else{
                                return 'Tercero'
                            }
                        }
                    },//como hacer que no me traiga el codigo sino descripcion

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
                },
                order: [[1, 'asc']]
            });
        })
    </script>
@endsection
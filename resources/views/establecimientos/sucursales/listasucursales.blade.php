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
                <h4 class="header-title m-t-0 m-b-20">Sucursales de {{$establecimiento->razon_social}}
                    <span class="pull-right"><a href="{{route('establecimientos')}}" class="btn btn-custom" style="position: relative; top: -7px"><i class="ti-arrow-left"></i> Volver</a> </span></h4>
            </div>
        </div> <!-- end row -->

        <div class="card-box">
            <h4>Informacion del establecimiento</h4>
            <p>&nbsp;</p>
            <div class="row">
                <div class="col-sm-12">
                    <label class="col-sm-2">Nit:</label>
                    <div class="col-sm-10">
                        {{$establecimiento->nit}}
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="col-sm-2">Rozon social:</label>
                    <div class="col-sm-10">
                        {{$establecimiento->razon_social}}
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="col-sm-2">Estado</label>
                    <div class="col-sm-10">
                        {{$establecimiento->estado=='A'?'Activo':'Inactivo'}}
                    </div>
                </div>
            </div>
        </div>

        <div class="card-box">
                <h4>Sucursales activas</h4>
                <p>&nbsp;</p>
            <div class="row">
                <div id="map" style="width: 100%;height: 250px"></div>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">
                <h5>Acciones</h5>
                <div class="card-box widget-inline">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="widget-inline-box">
                                <a href="{{route('sucursal.crear',['id'=>$establecimiento->id])}}" data-modal="modal-lg"
                                   class="btn btn-custom waves-effect waves-light" data-toggle="modal">Agregar sucursal</a>
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
                            <th>Nombre</th>
                            <th>Ciudad</th>
                            <th>Direccion</th>
                            <th>Email</th>
                            <th>Telefono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Nombre</th>
                            <th>Ciudad</th>
                            <th></th>
                            <th>Email</th>
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

    <script src="http://maps.google.com/maps/api/js?key=AIzaSyB1hUpbneHQgqsTgVZMvWc0jqUBKdQUobM&sensor=true"></script>
    <script src="{{asset('plugins/gmaps/gmaps.min.js')}}"></script>

    <script>
        var table;
        $(function () {
            //iniciamos el data table
            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": "{!!route('datatable_es')!!}"
                },
                ajax: {
                    url: "{!!route('gridsuscursales',['id'=>$establecimiento->id])!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'nombre', name: 'nombre'},
                    {data: 'get_municipio.descripcion', name: 'ciudad'},
                    {data: 'direccion', name: 'direccion'},
                    {data: 'email', name: 'email'},
                    {data: 'telefono', name: 'telefono'},
                    {
                        data: 'estado',
                        name: 'estado',
                        render: function (data) {
                            if(data=='A'){
                                return 'Activa'
                            }else{
                                return 'Inactiva'
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
                },
                order: [[1, 'asc']]
            });

            initMap();//inicializa el mapa
            setTimeout(getMarket,500);
        });

        var map;//variable que contendra el mapa de google
        function initMap() {
            setTimeout(function () {
                map = new GMaps({
                    div: '#map',
                    lat: -12.043333,
                    lng: -77.028333
                });
            },200);
        };

        function getMarket() {
            $.get("{{route('marketsucursales',['id'=>$establecimiento->id])}}",{},function (data) {
                for(i=0;i<data.length;i++){
                    map.setCenter(data[0].latitud, data[0].longitud);
                    map.addMarker({
                        lat: data[i].latitud,
                        lng: data[i].longitud,
                        title: data[i].nombre,
                        infoWindow: {
                            content: '<p>'+data[i].nombre+'</p>'
                        }
                    });
                }
            })
        }

    </script>
@endsection
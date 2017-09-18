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
                <h4 class="header-title m-t-0 m-b-20">Gestionar terminales de {{$sucursal->nombre}}
                    <span class="pull-right"><a href="{{route('listsucursales',[$sucursal->getEstablecimiento->id])}}"
                                                class="btn btn-custom" style="position: relative; top: -7px"><i
                                    class="ti-arrow-left"></i> Volver</a> </span></h4>
            </div>
        </div> <!-- end row -->

        <div class="card-box">
            <h4>Informacion del establecimiento</h4>
            <p>&nbsp;</p>
            <div class="row">
                <div class="col-sm-12">
                    <label class="col-sm-2">Nit:</label>
                    <div class="col-sm-10">
                        {{$sucursal->getEstablecimiento->nit}}
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="col-sm-2">Rozon social:</label>
                    <div class="col-sm-10">
                        {{$sucursal->getEstablecimiento->razon_social}}
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="col-sm-2">Sucursal:</label>
                    <div class="col-sm-10">
                        {{$sucursal->nombre}}
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="col-sm-2">Estado:</label>
                    <div class="col-sm-10">
                        {{$sucursal->estado=='A'?'Activa':'Inactiva'}}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <h5>Acciones</h5>
                <div class="card-box widget-inline">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="widget-inline-box">
                                <a href="{{route('terminal.crear',["id"=>$sucursal->id])}}" data-modal
                                   class="btn btn-custom waves-effect waves-light" data-toggle="modal"
                                   data-target="#modalrol">Agregar terminal</a>
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
                            <th>Codigo</th>
                            <th>uid</th>
                            <th>mac</th>
                            <th>Imei</th>
                            <th>Celular</th>
                            <th>Numero activo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Codigo</th>
                            <th>uid</th>
                            <th>mac</th>
                            <th>Imei</th>
                            <th>Celular</th>
                            <th>Numero activo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
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
                    url: "{!!route('gridterminales',['id'=>$sucursal->id])!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'codigo', name: 'codigo'},
                    {
                        data: 'uid',
                        name: 'uid',
                        render: function (data) {
                            if (data == "") {
                                return 'Sin asignar';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: 'mac',
                        name: 'mac',
                        render: function (data) {
                            if (data == "") {
                                return 'Sin asignar';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: 'imei',
                        name: 'imei',
                        render: function (data) {
                            if (data == "") {
                                return 'Sin asignar';
                            } else {
                                return data;
                            }
                        }
                    },
                    {data: 'celular', name: 'celular'},
                    {data: 'numero_activo', name: 'numero_activo'},
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
                        var input = document.createElement("input");
                        $(input).appendTo($(column.footer()).empty())
                            .on('keyup', function () {
                                column.search($(this).val(), false, false, true).draw();
                            });
                    });
                },
                order: [[1, 'asc']]
            });
        });

        function cambiarEstado(id) {
            swal({
                    title: '¿Estas seguro?',
                    text: "¡¡Desea cambiar estado esta terminal!!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Si',
                    cancelButtonText: 'No',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger m-l-10',
                    buttonsStyling: false
                },
                function () {
                    $.ajax({
                        url: '{{route('terminal.cambiarestado')}}',
                        data: {id: id},
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
                                )
                                modalBs.modal('hide');
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
        }
    </script>
@endsection
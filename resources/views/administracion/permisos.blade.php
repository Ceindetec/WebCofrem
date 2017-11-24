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
                <h4 class="header-title m-t-0 m-b-20">Gestión de permisos</h4>
            </div>
        </div> <!-- end row -->

        <div class="row">
            <div class="col-sm-12">
                <h5>Acciones</h5>
                <div class="card-box widget-inline">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="widget-inline-box">
                                <button class="btn btn-custom waves-effect waves-light" data-toggle="modal"
                                        data-target="#modalpermiso">Agregar permiso
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive m-b-20">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Descripcion</th>
                            <th>Accion</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div> <!-- end row -->

    </div>


    <div id="modalpermiso" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal" role="form" id="agrepermiso" name="agrepermiso"
                      action="{{route('agrepermiso')}}" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Agregar permiso</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Nombre</label>
                                <div class="col-md-10">
                                    <input type="text" id="nombre" name="nombre" class="form-control"
                                           placeholder="editar usuario">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Slug</label>
                                <div class="col-md-10">
                                    <input type="text" id="slug" name="slug" class="form-control"
                                           placeholder="editar.usuario">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Descripción</label>
                                <div class="col-md-10">
                                    <textarea class="form-control" rows="5" id="descripcion"
                                              name="descripcion"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-custom waves-effect waves-light">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.modal -->



    <!-- end container -->
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
                ajax: "{!!route('gridpermisos')!!}",

                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'slug', name: 'slug'},
                    {data: 'description', name: 'description'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });


            $('#agrepermiso').submit(function (e) {
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
                        console.log(result);
                        if (result.estado) {
                            swal(
                                {
                                    title: 'Bien!!',
                                    text: result.mensaje,
                                    type: 'success',
                                    confirmButtonColor: '#4fa7f3'
                                }
                            )
                        } else {
                            swal(
                                'Error!!',
                                result.mensaje,
                                'error'
                            )
                        }
                        $('#modalpermiso').modal('hide');
                        document.getElementById("agrepermiso").reset();
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
            })
        });



        /**
         * Funcion elimina un permiso del sistema
         * @param id el identificador del permiso que se desea eliminar
         */
        function eliminar(id) {
            swal({
                title: 'Estas seguro?',
                text: "Deseas eliminar este permiso!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si, eliminar!',
                cancelButtonText: 'No, cancelar!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                buttonsStyling: false
            },
                function () {
                    $.ajax({
                        url: "{{route('eliminarpermiso')}}",
                        data: {'id': id},
                        type: 'POST',
                        dataType: 'json',
                        beforeSend: function () {
                            cargando();
                        },
                        success: function (result) {
                            setTimeout(function () {
                                if (result.estado) {
                                    swal({
                                        title: 'Bien!!',
                                        text: result.mensaje,
                                        type: 'success',
                                        confirmButtonColor: '#4fa7f3'
                                    });
                                    table.ajax.reload();
                                } else {
                                    swal(
                                        'Error!!',
                                        result.mensaje,
                                        'error'
                                    )
                                }

                            }, 200);
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
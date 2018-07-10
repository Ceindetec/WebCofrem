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
                <h4 class="header-title m-t-0 m-b-20">Lista de usuarios</h4>
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
                                        data-target="#modalrol">Agregar rol
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
                            <th></th>
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


    <div id="modalrol" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal" role="form" id="agrerol" name="agrerol" action="{{route('agrerol')}}"
                      method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Agregar rol</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Nombre</label>
                                <div class="col-md-10">
                                    <input type="text" id="nombre" name="nombre" class="form-control"
                                           placeholder="Administrador">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Slug</label>
                                <div class="col-md-10">
                                    <input type="text" id="slug" name="slug" class="form-control" placeholder="admin">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Descripción</label>
                                <div class="col-md-10">
                                    <textarea class="form-control" rows="5" id="descripcion"
                                              name="descripcion"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Permisos</label>
                                <div class="col-md-10">
                                    <select class="select2 form-control" multiple="multiple" name="permisos[]" data-placeholder="Seleccione ..." style="width: 100%">

                                    </select>
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


    <script src="{{asset('plugins/handlebars/handlebars-v4.0.10.js')}}" type="text/javascript"></script>

    <!-- Script que se usa con ayuda de la libreria handlebars para crear template -->
    <script id="details-template" type="text/x-handlebars-template">
        <div class="label label-info">lista de permisos del Rol @{{ name }} </div>
        <ul class="listpermisos-@{{ id }}">

        </ul>
    </script>

    <script>

        var table; //variable en donde se alamacenera el data table para la lista general de roles
        var template = Handlebars.compile($("#details-template").html()); // variable que guarda el template que sera usado para ver la lista de permisos que tiene un rol
        $(function () {

            /**
             * se crea el datatable para listar los roles existentes
             * @type {jQuery}
             */
            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": "{!!route('datatable_es')!!}"
                },
                ajax: "{!!route('gridroles')!!}",

                columns: [
                    {
                        "className": 'details-control',
                        "orderable": false,
                        "searchable": false,
                        "data": null,
                        "defaultContent": '<span class="glyphicon glyphicon-plus-sign"></span>'
                    },
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'slug', name: 'slug'},
                    {data: 'description', name: 'description'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']]
            });

            /**
             *se captura el evento del formulario de agregar rol, y se envia por ajax
             */
            $('#agrerol').submit(function (e) {
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
                            )
                        } else {
                            swal(
                                'Error!!',
                                result.mensaje,
                                'error'
                            )
                        }
                        $('#modalrol').modal('hide');
                        table.ajax.reload();
                    },
                    error: function (xhr, status) {
                        alert('Disculpe, existió un problema');
                    },
                    // código a ejecutar sin importar si la petición falló o no
                    complete: function (xhr, status) {
                        fincarga();
                    }
                });
            })

            /**
             * metodo para cargar un multiselelect en el frmulario de crear rol
             */
            $(".select2").select2({
                placeholder: "Seleccione...",
                minimumInputLength: 1,
                ajax: {
                    url: "{{route('selectpermisos')}}",
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: function (params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    },
                },
                language: "es",
                allowClear: true,
                cache: true
            });


            //se agrega el evento para abrir y cerrar los detalles de un rol en la grid
            $('#datatable tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var tableId = 'Rol-' + row.data().id;
                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).html('<span class="glyphicon glyphicon-plus-sign"></span>');
                } else {
                    row.child(template(row.data())).show();
                    initListaPermisos(row.data());
                    tr.addClass('shown');
                    tr.next().find('td').addClass('no-padding bg-gray');
                    $(this).html('<span class="glyphicon glyphicon-minus-sign"></span>');
                }
            });

        });

        /**
         * funcion de que trae la lista de permisos para mostrar en el detalle del rol
         * @param data trae la informacio de la fila, que corresponde al rol que se quiere ver la lista de permisos que tiene
         */
        function initListaPermisos(data) {
            $.ajax({
                url: data.details_url, //obtengo la informacion de la ruta, que viene alamacenada en la fila en el campo datails_url
                //data: form.serialize(),
                type: 'GET',
                dataType: 'json',
                success: function (result) {
                    console.log(result);
                    $(".listpermisos-"+data.id).html("");
                    result.forEach(function (element) {
                        $(".listpermisos-"+data.id).append("<li>" + element.name + "</li>");
                    });

                },
            });
        }

        function eliminar(id) {
            swal({
                    title: 'Estas seguro?',
                    text: "Deseas eliminar este Rol!",
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
                        url: "{{route('eliminarrol')}}",
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
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
                <h4 class="header-title m-t-0 m-b-20">Gestionar usuarios</h4>
            </div>
        </div> <!-- end row -->

hola
        <div class="row">
            <div class="col-sm-12">
                <h5>Acciones</h5>
                <div class="card-box widget-inline">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="widget-inline-box">
                                <a href="{{route('usuario.crear')}}" data-modal class="btn btn-custom waves-effect waves-light" data-toggle="modal" data-target="#modalrol">Agregar usuario</a>
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
                            <th></th>
                            <th>id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div> <!-- end row -->


    </div>

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
        <div class="label label-info">lista de roles para el usuario @{{ name }} </div>
        <ul class="listroles-@{{ id }}">

        </ul>
    </script>

    <script>
        var table;
        var template = Handlebars.compile($("#details-template").html());
        $(function () {
            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": "{!!route('datatable_es')!!}"
                },
                ajax: {
                    url: "{!!route('gridusuarios')!!}",
                    "type": "get"
                },
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
                    {data: 'email', name: 'email'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']]
            });

            //se agrega el evento para abrir y cerrar los detalles de un usuario en la grid
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
                    initListaRoles(row.data());
                    tr.addClass('shown');
                    tr.next().find('td').addClass('no-padding bg-gray');
                    $(this).html('<span class="glyphicon glyphicon-minus-sign"></span>');
                }
            });

        });

        /**
         * funcion de que trae la lista de roles para mostrar en el detalle del usuario
         * @param data trae la informacio de la fila, que corresponde al usuario que se quiere ver la lista de roles que tiene
         */
        function initListaRoles(data) {
            $.ajax({
                url: data.details_url, //obtengo la informacion de la ruta, que viene alamacenada en la fila en el campo datails_url
                //data: form.serialize(),
                type: 'GET',
                dataType: 'json',
                success: function (result) {
                    console.log(result);
                    $(".listroles-"+data.id).html("");
                    result.forEach(function (element) {
                        $(".listroles-"+data.id).append("<li>" + element.name + "</li>");
                    });

                },
            });
        }

        function eliminar(id) {
            swal({
                    title: 'Estas seguro?',
                    text: "Deseas eliminar este usuario!",
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
                        url: "{{route('usuario.eliminar')}}",
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
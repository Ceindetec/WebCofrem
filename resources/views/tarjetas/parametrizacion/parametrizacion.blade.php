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
                <h4 class="header-title m-t-0 m-b-20">Parametrizar </h4>
            </div>
        </div> <!-- end row -->


        <div class="card-box">
            <h4 class="m-t-0">Valor tarjeta</h4>
            <br>
            {{Form::open(['route'=>['tarjeta.parametro.valor'], 'class'=>'form-inline', 'id'=>'parametrovalor'])}}
                <div class="form-group">
                    <label for="valor">Valor: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                        <input type="text" class="form-control dinero" name="valor" value="{{$valorTarjeta==null?'':$valorTarjeta->valor}}" id="valor">
                    </div>
                </div>

                <button type="submit" class="btn btn-custom">Guardar</button>
            {{Form::close()}}
        </div>

        <div class="card-box">
            <h4 class="m-t-0">Procentaje de administracion</h4>
            <br>
            {{Form::open(['route'=>['tarjeta.parametro.administracion'], 'class'=>'form-inline', 'id'=>'parametroadministracion'])}}
            <div class="form-group">
                <label for="valor">Tipo tarjeta: </label>
                {{Form::select("tarjeta_codigo",$tipotarjetas,null,['class'=>'form-control', "tabindex"=>"2",'id'=>'departamento'])}}
            </div>
            <div class="form-group">
                <label for="valor">Administracion: </label>
                <div class="input-group">
                    <input type="number" name="porcentaje" class="form-control" required maxlength="2" data-parsley-type="number"
                           data-parsley-max="100" data-parsley-min="0">
                    <span class="input-group-addon"><i class="fa fa-percent" aria-hidden="true"></i></span>
                </div>
            </div>


            <button type="submit" class="btn btn-custom">Agregar</button>
            <p>&nbsp;</p>
            {{Form::close()}}

            <div class="table-responsive m-b-20">
                <table id="datatableadministracion" class="table table-striped table-bordered" width="100%">
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
            <h4 class="m-t-0">Ceuntas contables</h4>
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

        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive m-b-20">
                    <table id="datatable" class="table table-striped table-bordered" width="100%">
                        <thead>
                        <tr>
                            <th>Nit</th>
                            <th>Razon social</th>
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
    <script src="{{asset('plugins/jQuery-Mask-Plugin/dist/jquery.mask.min.js')}}"></script>

    <script>
        var table;
        $(function () {
            table = $('#datatableadministracion').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": "{!!route('datatable_es')!!}"
                },
                ajax: {
                    url: "{!!route('gridadministraciontarjetas')!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'nit', name: 'nit'},
                    {data: 'razon_social', name: 'razon_social'},
                    {
                        data: 'estado',
                        name: 'estado',
                        render: function (data) {
                            if (data == 'A')
                                return 'Activo';
                            else
                                return 'Inactivo';

                        }
                    },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']]
            });

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
            })

            $("#parametroadministracion").parsley();

            $("#parametroadministracion").submit(function (e) {
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
            })


        });
    </script>
@endsection
@extends('layouts.admin')

@section('styles')
    <link href="{{asset('plugins/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/buttons.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/responsive.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/scroller.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/dataTables.colVis.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/dataTables.bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/datatables/fixedColumns.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">

@endsection

@section('contenido')

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Editar establecimiento
                    <span class="pull-right"><a href="{{route('establecimientos')}}" class="btn btn-custom"
                                                style="position: relative; top: -7px"><i class="ti-arrow-left"></i> Volver</a> </span>
                </h4>
            </div>
        </div> <!-- end row -->

        <div class="card-box">
            <h4 class="m-t-0">Informacion establecimiento <span class="pull-right">Editar <input type="checkbox"
                                                                                                 id="editarcheck"
                                                                                                 data-plugin="switchery"
                                                                                                 data-color="#1bb99a"
                                                                                                 data-size="small"/></span>
            </h4>
            {{Form::model($establecimiento,['route'=>['establecimiento.editarp',$establecimiento->id], 'class'=>'form-horizontal', 'id'=>'editarestablecimientos'])}}

            <div class="form-group">
                <label class="col-md-2 control-label">Nit</label>
                <div class="col-md-10">
                    {{Form::text('nit', null ,['class'=>'form-control', "id"=>"nit", "required", "maxlength"=>"10", "data-parsley-type"=>"number", "disabled"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Razon social</label>
                <div class="col-md-10">
                    {{Form::text('razon_social', null ,['class'=>'form-control', "id"=>"razon_social", "required", "maxlength"=>"40","data-parsley-pattern"=>"^[a-zA-Z0-9]+(\s*[a-zA-Z0-9]*)*[a-zA-Z0-9]+$","disabled" ])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">E-mail</label>
                <div class="col-md-10">
                    {{Form::email('email', null ,['class'=>'form-control', "id"=>"email", "required", "disabled"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Telefono</label>
                <div class="col-md-10">
                    {{Form::text('telefono', null ,['class'=>'form-control',"id"=>"telefono", "required", "data-parsley-type"=>"number", "maxlength"=>"10", "disabled"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Celular</label>
                <div class="col-md-10">
                    {{Form::text('celular', null ,['class'=>'form-control',"id"=>"celular", "required", "data-parsley-type"=>"number", "maxlength"=>"10", "disabled"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Estado</label>
                <div class="col-md-10">
                    {{Form::select("estado",['A'=>'Activo','I'=>'Inactivo'],null,['class'=>'form-control', "tabindex"=>"2",'id'=>'estado', "disabled"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">&nbsp;</label>
                <div class="col-md-10">
                    <input type="submit" class="btn btn-custom waves-effect waves-light" value="Guardar" disabled>
                </div>
            </div>

            {{Form::close()}}
        </div>

        <div class="row">
            <div class="col-sm-12">
                <h5>Acciones</h5>
                <div class="card-box widget-inline">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="widget-inline-box">
                                <a href="{{route('convenio.crear',[$establecimiento->id])}}" data-modal
                                   class="btn btn-custom waves-effect waves-light" data-toggle="modal"
                                   data-target="#modalrol">Agregar convenio</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-box">
            <h4 class="m-t-0">Lista de convenios</h4>

            <div class="table-responsive m-b-20">
                <table id="datatable" class="table table-striped table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th>Numero de convenio</th>
                        <th>Fecha de inicio</th>
                        <th>Fecha de finalización</th>
                        <th>estado</th>
                        <th>Acciones</th>
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
    <script src="{{asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-datepicker/locale/bootstrap-datepicker.es.min.js')}}" charset="UTF-8"></script>
    <script src="{{asset('plugins/jQuery-Mask-Plugin/dist/jquery.mask.min.js')}}"></script>



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
                    url: "{!!route('gridconveniosestablecimiento',['id'=>$establecimiento->id])!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'numero_convenio', name: 'numero_convenio'},
                    {
                        data: 'fecha_inicio',
                        name: 'fecha_inicio',
                        render: function (data) {
                            return moment(data).format('DD/MM/YYYY');
                        }
                    },
                    {
                        data: 'fecha_fin',
                        name: 'fecha_fin',
                        render: function (data) {
                            return moment(data).format('DD/MM/YYYY');
                        }
                    },
                    {
                        data: 'estado',
                        name: 'estado',
                        render: function (data) {
                            if (data == 'A')
                                return 'Activo';
                            else if(data=='I')
                                return 'Inactivo';
                            else
                                return 'Pendiente';
                        }
                    },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']]
            });

            $("#editarestablecimientos").parsley();
            $("#editarestablecimientos").submit(function (e) {
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
                            resetInfo(result.data);
                        } else if (result.estado == false) {
                            swal(
                                'Error!!',
                                result.mensaje,
                                'error'
                            );
                            resetInfo(result.data);

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

            $('#editarcheck').change(function () {
                if ($(this).is(':checked')) {
                    $('#editarestablecimientos input, #editarestablecimientos select').attr('disabled', false)
                } else {
                    $('#editarestablecimientos input, #editarestablecimientos select').attr('disabled', true)
                }
            })
        });

        function resetInfo(data) {
            $('#nit').val(data.nit);
            $('#razon_social').val(data.razon_social);
            $('#email').val(data.email);
            $('#telefono').val(data.telefono);
            $('#celular').val(data.celular);
            $('#celular').val(data.celular);
            $('#estado').val(data.estado);
            setTimeout(function () {
                if ($('#editarcheck').is(':checked')) {
                    $('#editarcheck').trigger('click');
                }
            }, 200);

        }

    </script>
@endsection
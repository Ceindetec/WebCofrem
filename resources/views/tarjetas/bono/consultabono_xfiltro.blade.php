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
                <div style="width:80%"> <h4 class="header-title m-t-0 m-b-20">
                        {{Form::radio('filtro', '1',true,['onclick'=>'mostrar(this)', 'value'=>'1'])}} Por Contrato
                 {{Form::radio('filtro', '2', false, ['onclick'=>'mostrar(this)','value'=>'2'])}} Por Empresa </h4></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Consulta tarjetas bono por contrato</h4>
            </div>
        </div>

        <div id="ocultocontrato" class="row" style="display:block">
            {{Form::open(['class'=>'form-horizontal', 'id'=>'consultarxcontrato', 'target'=>"_blank",'role'=>'form','method'=>'POST'])}}  <!-- 'route'=>['bono.consultaxcontratop'],-->
            <div  class="form-group">
                <label class="col-sm-3 control-label">Número de contrato</label>
                <div class="col-sm-7">
                {{Form::text('numero_contrato', null ,['class'=>'form-control', "required", "tabindex"=>"1",'id'=>'numero_contrato'])}}
                </div><button type="button" id="CrearB" class="btn btn-custom waves-effect waves-light" onclick="consultarc()">Buscar</button>

            </div>
            {{Form::close()}}
        </div>
        <div id="ocultoempresa" class="row" style="display:none">
            {{Form::open(['class'=>'form-horizontal', 'id'=>'consultarxempresa', 'target'=>"_blank",'role'=>'form','method'=>'POST'])}}
            <div  class="form-group">
                <label class="col-sm-3 control-label">Nit</label>
                <div class="col-sm-7">
                    {{Form::text('nit', null ,['class'=>'form-control', "required", "tabindex"=>"1",'id'=>'nit'])}}
                </div><button type="button" id="CrearB" class="btn btn-custom waves-effect waves-light" onclick="consultare()">Buscar</button>

            </div>
            {{Form::close()}}
        </div>
        <div id="tablaoculta" name="tablaoculta" style="display: none;">
        <div class="row">
            <div class="col-sm-12">

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
    <script src="{{asset('plugins/moment/moment.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-datepicker/locale/bootstrap-datepicker.es.min.js')}}" charset="UTF-8"></script>

    <script>
        var table;
        function mostrar(elemento) {
            //alert("el valor es "+elemento.value);
            if(elemento.value=="1") {
                document.getElementById("ocultoempresa").style.display = "none";
                document.getElementById("ocultocontrato").style.display = "block";
                document.getElementById("tablaoculta").style.display = "none";
                //alert("valor1");
            }
            else {
                document.getElementById("ocultoempresa").style.display = "block";
                document.getElementById("ocultocontrato").style.display = "none";
                document.getElementById("tablaoculta").style.display = "none";
                //alert("valor2");
            }
        }
        function consultarc() {
            document.getElementById("tablaoculta").style.display = "block";
            var numcontrato=$('#numero_contrato').val();
            $('#tablaoculta').load('{{route('bono.consultaxcontratop')}}',{numcontrato:numcontrato});
            /*
            table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                "language": {
                    "url": " { !!route('datatable_es')!!}"
                },
                ajax: {
                    url: "{ !!route('bono.gridconsultaxcontrato')!!}",
                    "type": "get"
                },
                columns: [
                    {data: 'numero_tarjeta', name: 'numero_tarjeta'},
                    {
                        data: 'monto_inicial',
                        name: 'monto_inicial',
                        render: function (data) {
                            return '$ '+enmascarar(data);
                        }
                    },
                    {
                        data: 'estado',
                        name: 'estado',
                        render: function (data) {
                            if (data == 'A') {
                                return 'Activo';
                            }
                            else if (data == 'I') {
                                return 'Inactivo';
                            } else {
                                return 'Pendiente'
                            }
                        },
                        searchable: false
                    },
                    {data: 'vencimiento', name: 'vencimiento'},
                    {data: 'numcontrato', name: 'numcontrato'},
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
            });
            */
        }
        function activar(id) {
            swal({
                    title: '¿Estas seguro?',
                    text: "¡Desea activar esta tarjeta!",
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
                        url: "{{route('tarjeta.bono.activar')}}",
                        data: {'id': id},
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
                                table.ajax.reload();
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
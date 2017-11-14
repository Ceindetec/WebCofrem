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
    <link href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet">
@endsection

@section('contenido')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="header-title m-t-0 m-b-20">Reporte de Consumo promedio por datafono </h4>
            </div>
        </div> <!-- end row -->

        <div class="row">
            <div class="col-sm-12">
                <h5>Filtros de búsqueda</h5>
                <div class="card-box widget-inline">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="widget-inline-box">
                                <form class="form-horizontal col-md-12" id="formu1">
                                    <div class="form-group">
                                        <label>Rango de fecha para la consulta</label>
                                        <input class="form-control input-daterange-datepicker" type="text" id="daterange"/>
                                    </div>
                                    <!-- { {Form::select("establecimiento[]",$establecimientos,null,['class'=>'select2 form-control', "tabindex"=>"2",'id'=>'establecimiento[]', "required"=>"required","multiple"=>"multiple"])}} -->
                                    <div class="form-group">
                                        <label>Establecimiento</label>
                                    {{Form::select("establecimientos[]",$establecimientos,null,['class'=>'select2 form-control', "tabindex"=>"2",'id'=>'establecimientos', "required"=>"required","multiple"=>"multiple","data-placeholder"=>"Seleccione ..." ])}}
                                    <!-- <select class="select2 form-control" multiple="multiple" name="establecimientos[]" required="required" data-placeholder="Seleccione ..." style="width: 100%" id="establecimientos" ></select> -->
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-custom" onclick="generarRespuesta()" >Generar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="resultado">

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
    <script src="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment-with-locales.min.js"></script>
    <script>
        $(function () {
            moment.locale('es');
            var start = moment().subtract(29, 'days');
            var end = moment();
            $('.input-daterange-datepicker').daterangepicker({
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-success',
                cancelClass: 'btn-default',
                showDropdowns: true,
                startDate: start,
                endDate: end,
                "locale": {
                    "format": "DD/MM/YYYY",
                    "separator": " - ",
                    "applyLabel": "Aplicar",
                    "cancelLabel": "Cancelar",
                    "fromLabel": "From",
                    "toLabel": "To",
                    "customRangeLabel": "Custom",
                    "daysOfWeek": [
                        "Do",
                        "Lu",
                        "Ma",
                        "Mi",
                        "Ju",
                        "Vi",
                        "Sa"
                    ],
                    "monthNames": [
                        "Enero",
                        "Febrero",
                        "Marzo",
                        "Abril",
                        "Mayo",
                        "Junio",
                        "Julio",
                        "Agosto",
                        "Septiembre",
                        "Octubre",
                        "Noviembre",
                        "Diciembre"
                    ],
                    "firstDay": 1
                }
            });
            $("#formu1").submit(function (e) {
                e.preventDefault();
                generarRespuesta();
            });

        });
        $(function () {
            /* metodo para cargar un multiselelect en el frmulario de crear rol
             */
            $(".select2").select2({
                placeholder: "Seleccione...",
                minimumInputLength: 1,
                ajax: {
                    url: "{{route('selectestablecimientos')}}",
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
                                    text: item.razon_social,
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
        });

        function generarRespuesta() {
            var establecimientos = $('#establecimientos').val();
            var rango = $('#daterange').val();
            /*if(establecimientos==null)
             alert("Debe seleccionar al menos un establecimiento");
             else*/
            $('#resultado').load('{{route('resultadopromedioxdatafono')}}',{rango:rango,establecimientos:establecimientos});
        }

    </script>
@endsection
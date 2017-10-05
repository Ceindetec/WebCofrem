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
                <h4 class="header-title m-t-0 m-b-20">Consulta personalizada de tarjetas bono</h4>
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
        $(function () {
            $("#ocultocontrato").submit(function (e) {
                e.preventDefault();
                consultarc();
                //llamar funcion
            });
            $("#ocultoempresa").submit(function (e) {
                e.preventDefault();
                consultare();
                //llamar funcion
            });
        });
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
            var numcontrato=$('#numero_contrato').val();
            $('#tablaoculta').load('{{route('bono.consultaxcontratop')}}',{numcontrato:numcontrato});
            document.getElementById("tablaoculta").style.display = "block";

        }
        function consultare() {
            var nit=$('#nit').val();
            $('#tablaoculta').load('{{route('bono.consultaxempresap')}}',{nit:nit});
            document.getElementById("tablaoculta").style.display = "block";

        }
    </script>
@endsection
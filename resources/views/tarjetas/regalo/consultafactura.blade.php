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
            <h4 class="header-title m-t-0 m-b-20">Consulta tarjetas regalo por factura</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <h5>Acciones</h5>
            <div class="card-box widget-inline">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="widget-inline-box">
                        {{Form::open(['class'=>'form-inline', 'id'=>'consultarxcontrato', 'target'=>"_blank",'role'=>'form','method'=>'POST'])}}  <!-- 'route'=>['bono.consultaxcontratop'],-->
                            <div  class="form-group">
                                <label class="">Número de factura</label>

                                {{Form::text('factura', null ,['class'=>'form-control', "required", "tabindex"=>"1",'id'=>'factura'])}}

                            </div>
                            <button type="button" id="CrearB" class="btn btn-custom waves-effect waves-light" onclick="consultarc()">Buscar</button>
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        $('#consultarxcontrato').submit(function () {
            consultarc()
            return false;
        })
    });

    function consultarc() {
        var factura=$('#factura').val();
        cargando();
        $('#tablaoculta').load('{{route('reagalo.consultaxfactura')}}',{factura:factura}, function () {
            fincarga();
            document.getElementById("tablaoculta").style.display = "block";
        });

    }
</script>
@endsection
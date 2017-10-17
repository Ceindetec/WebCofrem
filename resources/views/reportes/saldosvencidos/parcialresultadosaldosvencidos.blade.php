<div class="row">
    <div class="col-sm-12">
        <h5>Exportar</h5>
        <div class="card-box widget-inline">
            <div class="row">
                <div class="widget-inline-box text-right">
                    <strong>Exportar: </strong>
                    <div class="btn-group">
                        <a href="{{route('pdfsaldosvencidos',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'tiposervicio'=>$tiposervicio,'resultadob'=>$resultadob,'resultador'=>$resultador])}}"
                           class="btn btn-sm btn-custom" data-toggle="tooltip" title="PDF">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>
                        <a href="{{route('excelsaldosvencidos',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'tiposervicio'=>$tiposervicio,'resultadob'=>$resultadob,'resultador'=>$resultador])}}"
                           class="btn btn-sm btn-custom" data-toggle="tooltip" title="EXCEL">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <h5>Resultado</h5>
        @if($tiposervicio!="R")
            <h5>Tarjetas Bono</h5>
            @if(sizeof($resultadob)>0)
                <div class="card-box widget-inline">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12">
                            <div class="widget-inline-box">
                                <div class="table-responsive m-b-20">
                                    <table id="datatable" class="table table-striped table-bordered" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Numero tarjeta</th>
                                    <th>Monto inicial</th>
                                    <th>Sobrante</th>
                                    <th>Fecha activacion</th>
                                    <th>Fecha vencimiento</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($resultadob as $miresul)
                                    <tr>
                                        <td>{{$miresul["numero_tarjeta"]}}</td>
                                        <td>{{$miresul["monto_inicial"]}}</td>
                                        <td>{{$miresul["sobrante"]}}</td>
                                        <td> {{$miresul["fecha_activacion"]}}</td>
                                        <td>{{$miresul["fecha_vencimiento"]}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(sizeof($resultadob)==0)
            <p align="center">No hay registros</p>
        @endif
        @endif
        @if($tiposervicio!="B")
        <h5>Tarjetas Regalo</h5>
        @if(sizeof($resultador)>0)
        <div class="card-box widget-inline">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="widget-inline-box" >
                        <div class="table-responsive m-b-20">
                            <table id="datatable" class="table table-striped table-bordered" width="100%">
                                <thead>
                                <tr>
                                    <th>Numero tarjeta</th>
                                    <th>Monto incial</th>
                                    <th>Sobrante</th>
                                    <th>Fecha activacion</th>
                                    <th>Fecha vencimiento</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($resultador as $miresul)
                                    <tr>
                                        <td>{{$miresul["numero_tarjeta"]}}</td>
                                        <td>{{$miresul["monto_inicial"]}}</td>
                                        <td>{{$miresul["sobrante"]}}</td>
                                        <td> {{$miresul["fecha_activacion"]}}</td>
                                        <td>{{$miresul["fecha_vencimiento"]}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            @endif
            @if(sizeof($resultador)==0)
                <p align="center">No hay registros</p>
            @endif
        @endif
    </div>
</div>

<script>
    $('[data-toggle="tooltip"]').tooltip();
    $('#datatable').DataTable({
        "language": {
            "url": "{!!route('datatable_es')!!}"
        },
    });
</script>
<div class="row">
    <div class="col-sm-12">
        <h5>Exportar</h5>
        <div class="card-box widget-inline">
            <div class="row">
                <div class="widget-inline-box text-right">
                    <strong>Exportar: </strong>
                    <div class="btn-group">
                        <a href="{{route('pdfmontosusados',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'tiposervicio'=>$tiposervicio,'resultadob'=>$resultadob,'resultador'=>$resultador,'resumen'=>$resumen])}}"
                           class="btn btn-sm btn-custom" data-toggle="tooltip" title="PDF">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>
                        <a href="{{route('excelmontosusados',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'tiposervicio'=>$tiposervicio,'resultadob'=>$resultadob,'resultador'=>$resultador,'resumen'=>$resumen])}}"
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
                                            <th>Número tarjeta</th>
                                            <th>Monto inicial</th>
                                            <th>Monto usado</th>
                                            <th>Saldo</th>
                                            <th>Fecha vencimiento</th>
                                            <th>Estado</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($resultadob as $miresul)
                                            <?php $monto_name = '$ '.number_format( $miresul["monto"], 2, ',', '.');
                                            $gasto_name = '$ '.number_format( $miresul["gasto"], 2, ',', '.');
                                            $saldo_name = '$ '.number_format( $miresul["saldo"], 2, ',', '.');?>
                                            <tr>
                                                <td>{{$miresul["numero_tarjeta"]}}</td>
                                                <td>{{$monto_name}}</td>
                                                <td>{{$gasto_name}}</td>
                                                <td> {{$saldo_name}}</td>
                                                <td>{{$miresul["vencimiento"]}}</td>
                                                <td> {{$miresul["estado"]}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <table id="datatable" class="table table-striped table-bordered" width="100%">
                                        @foreach($resumen as $resum)
                                        <tr><td>Total productos de tarjeta bono: </td><td>{{$resum["totalb"]}}</td></tr>
                                            @endforeach
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
                                    <table id="datatable2" class="table table-striped table-bordered" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Número tarjeta</th>
                                            <th>Monto inicial</th>
                                            <th>Monto usado</th>
                                            <th>Saldo</th>
                                            <th>Fecha vencimiento</th>
                                            <th>Estado</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($resultador as $miresul)
                                            <?php $monto_name = '$ '.number_format( $miresul["monto"], 2, ',', '.');
                                            $gasto_name = '$ '.number_format( $miresul["gasto"], 2, ',', '.');
                                            $saldo_name = '$ '.number_format( $miresul["saldo"], 2, ',', '.');?>
                                            <tr>
                                                <td>{{$miresul["numero_tarjeta"]}}</td>
                                                <td>{{$monto_name}}</td>
                                                <td>{{$gasto_name}}</td>
                                                <td> {{$saldo_name}}</td>
                                                <td>{{$miresul["vencimiento"]}}</td>
                                                <td> {{$miresul["estado"]}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <table id="datatable" class="table table-striped table-bordered" width="100%">
                                        @foreach($resumen as $resum)
                                        <tr><td>Total productos de tarjeta regalo: </td><td>{{$resum["totalr"]}}</td></tr>
                                            @endforeach
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
        <div class="card-box widget-inline">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="widget-inline-box">
        <table id="datatable" class="table table-striped table-bordered" width="100%">
            @foreach($resumen as $resum)
            <tr><td>Total productos encontrados: </td><td>{{$resum["total"]}}</td></tr>
                @endforeach
        </table>
                    </div></div>
        </div></div>

    </div>
</div>

<script>
    $('[data-toggle="tooltip"]').tooltip();
    $('#datatable').DataTable({
        "language": {
            "url": "{!!route('datatable_es')!!}"
        },
    });
    $('#datatable2').DataTable({
        "language": {
            "url": "{!!route('datatable_es')!!}"
        },
    });
</script>
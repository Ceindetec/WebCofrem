<div class="row">
    <div class="col-sm-12">
        <h5>Exportar</h5>
        <div class="card-box widget-inline">
            <div class="row">
                <div class="widget-inline-box text-right">
                    <strong>Exportar: </strong>
                    <div class="btn-group">
                        <a href="{{route('pdfpromedioxdatafono',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'lista_esta'=>$lista_esta,'resultado'=>$resultado,'resumen'=>$resumen])}}"
                           class="btn btn-sm btn-custom" data-toggle="tooltip" title="PDF">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>
                        <a href="{{route('excelpromedioxdatafono',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'lista_esta'=>$lista_esta,'resultado'=>$resultado,'resumen'=>$resumen])}}"
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
        <div class="card-box widget-inline">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="widget-inline-box" >
                        <div class="table-responsive m-b-20">
                            @if(sizeof($establecimientos)>0)
                                @foreach($establecimientos as $establecimiento)
                                    <h5>Establecimiento: {{$establecimiento->razon_social}}</h5>
                                    <?php $haysucursal=0;
                                    $hayterminal=0;?>
                                    @if(sizeof($sucursales)>0)
                                    @foreach($sucursales as $sucursale)
                                        @if($sucursale->establecimiento_id==$establecimiento->id)
                                            <?php $haysucursal++; ?>
                                            <h5>{{$sucursale->nombre}}</h5>
                                            @foreach($terminales as $terminale)
                                                    <?php $cant=0; ?>
                                            @if($sucursale->id==$terminale->sucursal_id)
                                            <?php $hayterminal++; ?>
                                            <h5>Código del datafono:{{$terminale->codigo}}</h5>
                                            @if(sizeof($resultado)>0)
                                                <table id="datatable" class="table table-striped table-bordered" width="100%">
                                                    <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Total</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($resultado as $miresul)
                                                        @if($miresul["establecimiento"]==$establecimiento->id && $miresul["sucursal"]==$sucursale->id && $miresul["terminal"]==$terminale->codigo)
                                                            <?php $cant++;
                                                            $venta_name = '$ '.number_format( $miresul["venta"], 2, ',', '.');?>
                                                            <tr>
                                                                <td>{{$miresul["fecha"]}}</td>
                                                                <td>{{$venta_name}}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                    @if($cant==0)
                                                        <p align="center">No hay registros</p>
                                                    @endif
                                                @foreach ($resumen as $resum)
                                                    @if($resum['terminal']== $terminale->codigo)
                                                        <?php    $prom_name = '$ '.number_format( $resum["promedio"], 2, ',', '.');?>
                                                        <table id="datatable" class="table table-striped table-bordered" width="100%">
                                                            <tr><td>Estado actual: </td><td>{{$resum['estado']}}</td><td>Promedio diario: </td><td>{{$prom_name}}</td></tr>
                                                        </table>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @endif
                                            @endforeach
                                            @if($hayterminal==0)
                                               <p align="center">No existen datafonos</p>
                                            @endif
                                        @endif
                                    @endforeach
                                    @endif
                                    @if($haysucursal==0)
                                        <p align="center">No existen sucursales</p>
                                    @endif
                                    <br>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(sizeof($establecimientos)==0)
        <p align="center">Debe seleccionar al menos un establecimiento</p>
    @endif
</div>
</div>

<script>

</script>
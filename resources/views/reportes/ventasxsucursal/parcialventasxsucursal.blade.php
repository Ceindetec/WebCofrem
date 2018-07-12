<div class="row">
    <div class="col-sm-12">
        <h5>Exportar</h5>
        <div class="card-box widget-inline">
            <div class="row">
                <div class="widget-inline-box text-right">
                    <strong>Exportar: </strong>
                    <div class="btn-group">
                        <a href="{{route('pdfventasxsucursal',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'lista_esta'=>$lista_esta,'resultado'=>$resultado])}}"
                           class="btn btn-sm btn-custom" data-toggle="tooltip" title="PDF">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>
                        <a href="{{route('excelventasxsucursal',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'lista_esta'=>$lista_esta,'resultado'=>$resultado])}}"
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
                                <?php $canttotal = 0;
                                $ventatotal = 0; ?>
                                @foreach($establecimientos as $establecimiento)
                                    <h5>Establecimiento: {{$establecimiento->razon_social}}</h5>
                                    <h5>Nit: {{$establecimiento->nit}}</h5>
                                    <?php $haysucursal=0;
                                    $cantest = 0;
                                    $ventaest = 0; ?>
                                    @foreach($sucursales as $sucursale)
                                        <?php $cant=0; ?>
                                        @if($sucursale->establecimiento_id == $establecimiento->id)
                                            <?php $haysucursal++; ?>
                                            <!-- <h5>{ {$sucursale->nombre}}</h5> -->
                                            @if(sizeof($resultado)>0)
                                                <table id="datatable" class="table table-striped table-bordered" width="100%">
                                                    <thead>
                                                    <tr>
                                                        <th>Sucursal</th>
                                                        <th>Cantidad de transacciones</th>
                                                        <th>Monto</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $subtotal=0; ?>
                                                    @foreach($resultado as $miresul)
                                                        @if($miresul["establecimiento"]==$establecimiento->id && $miresul["sucursal"]==$sucursale->id)
                                                            <?php $cant++; ?>
                                                            <?php $venta_name = '$ '.number_format( $miresul["venta"], 2, ',', '.'); ?>
                                                            <tr>
                                                                <td>{{$sucursale->nombre}}</td>
                                                                <td>{{$miresul["cantidad"]}}</td>
                                                                <td>{{$venta_name}}</td>
                                                                <?php $cantest += $miresul["cantidad"];
                                                                $ventaest += $miresul["venta"]; ?>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    <?php $ventaest_name = '$ '.number_format( $ventaest, 2, ',', '.');
                                                    $canttotal += $cantest;
                                                    $ventatotal += $ventaest;    ?>
                                                    <tr><td align="center"><b>Total: </b></td><td align="center"> {{$cantest}}</td><td align="center"> {{$ventaest_name}}</td></tr>
                                                    </tbody>
                                                </table>
                                            @endif
                                            @if($cant==0)
                                                <p align="center">No hay registros</p>
                                            @endif
                                        @endif
                                    @endforeach
                                    @if($haysucursal==0)
                                        <p align="center">No existen sucursales</p>
                                    @endif
                                    <br>
                                @endforeach
                                <?php $ventatotal_name = '$ '.number_format( $ventatotal, 2, ',', '.'); ?>
                                    <h5>Resumen de los resultados</h5>
                                <table id="datatable" class="table table-striped table-bordered" width="100%">
                                    <tr><th>Total establecimientos</th><th>Total sucursales</th><th>Cantidad de transacciones</th><th>Monto</th></tr>
                                    <tr><td align="center">{{sizeof($establecimientos)}}</td><td align="center">{{sizeof($sucursales)}}</td><td align="center"> {{$canttotal}}</td><td align="center"> {{$ventatotal_name}}</td></tr></table>
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
    /* $('#datatable').DataTable({
     "language": {
     "url": "{ !!route('datatable_es')!!}"
     },
     });*/
</script>
<div class="row">
    <div class="col-sm-12">
        <h5>Exportar</h5>
        <div class="card-box widget-inline">
            <div class="row">
                <div class="widget-inline-box text-right">
                    <strong>Exportar: </strong>
                    <div class="btn-group">
                        <a href="{{route('pdfventasxestablecimiento',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'lista_esta'=>$lista_esta,'resultado'=>$resultado])}}"
                           class="btn btn-sm btn-custom" data-toggle="tooltip" title="PDF">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>
                        <a href="{{route('excelventasxestablecimiento',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'lista_esta'=>$lista_esta,'resultado'=>$resultado])}}"
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
                                    <table id="datatable" class="table table-striped table-bordered" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Establecimiento</th>
                                            <th>Nit</th>
                                            <th>Cantidad de transacciones</th>
                                            <th>Monto</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                @foreach($establecimientos as $establecimiento)
                                        <?php $cant=0; ?>
                                        <!-- <h5>{ {$sucursale->nombre}}</h5> -->
                                            @if(sizeof($resultado)>0)
                                                    <?php $subtotal=0; ?>
                                                    @foreach($resultado as $miresul)
                                                        @if($miresul["establecimiento"]==$establecimiento->id)
                                                            <?php $cant++; ?>
                                                            <?php $venta_name = '$ '.number_format( $miresul["venta"], 2, ',', '.'); ?>
                                                            <tr>
                                                                <td>{{$establecimiento->razon_social}}</td>
                                                                <td>{{$establecimiento->nit}}</td>
                                                                <td>{{$miresul["cantidad"]}}</td>
                                                                <td>{{$venta_name}}</td>
                                                                <?php $canttotal += $miresul["cantidad"];
                                                                $ventatotal += $miresul["venta"]; ?>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                            @endif
                                            @if($cant==0)
                                                <p align="center">No hay registros</p>
                                            @endif
                                    <br>
                                @endforeach
                                        </tbody>
                                    </table>
                                <?php $ventatotal_name = '$ '.number_format( $ventatotal, 2, ',', '.'); ?>
                                <h5>Resumen de los resultados</h5>
                                <table id="datatable" class="table table-striped table-bordered" width="100%">
                                    <tr><th>Total establecimientos</th><th>Cantidad de transacciones</th><th>Monto</th></tr>
                                    <tr><td align="center">{{sizeof($establecimientos)}}</td><td align="center"> {{$canttotal}}</td><td align="center"> {{$ventatotal_name}}</td></tr></table>
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
<div class="row">
    <div class="col-sm-12">
        <h5>Exportar</h5>
        <div class="card-box widget-inline">
            <div class="row">
                <div class="widget-inline-box text-right">
                    <strong>Exportar: </strong>
                    <div class="btn-group">
                        <a href="{{route('pdfventasdiarias',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'lista_esta'=>$lista_esta,'resultado'=>$resultado])}}"
                           class="btn btn-sm btn-custom" data-toggle="tooltip" title="PDF">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>
                        <a href="{{route('excelventasdiarias',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'lista_esta'=>$lista_esta,'resultado'=>$resultado])}}"
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
                                            <?php $haysucursal=0; ?>
                                            @foreach($sucursales as $sucursale)
                                                <?php $cant=0; ?>
                                                @if($sucursale->establecimiento_id==$establecimiento->id)
                                                        <?php $haysucursal++; ?>
                                                <h5>{{$sucursale->nombre}}</h5>
                                                @if(sizeof($resultado)>0)
                                                    <table id="datatable" class="table table-striped table-bordered" width="100%">
                                                    <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Venta</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $subtotal=0; ?>
                                                    @foreach($resultado as $miresul)
                                                        @if($miresul["establecimiento"]==$establecimiento->id && $miresul["sucursal"]==$sucursale->id)
                                                            <?php $cant++; ?>
                                                        <tr>
                                                            <td>{{$miresul["fecha"]}}</td>
                                                            <td>{{$miresul["venta"]}}</td>
                                                            <?php $subtotal+=$miresul["venta"]; ?>
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                    <tr><td align="center"><b>Total: </b></td><td align="center"> {{$subtotal}}</td></tr>
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
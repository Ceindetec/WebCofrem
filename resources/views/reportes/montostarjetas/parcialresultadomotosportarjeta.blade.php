<div class="row">
    <div class="col-sm-12">
        <div class="card-box widget-inline">
            <div class="row">
                <div class="widget-inline-box text-right">
                    <strong>Exportar: </strong>
                    <div class="btn-group">
                        <a href="{{route('reportes.exportarpdfmotosportarjeta', ['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'servicios'=>$tipoServicio])}}"
                           class="btn btn-sm btn-custom" data-toggle="tooltip" title="PDF">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>
                        <a href="{{route('reportes.exportarexcelmontosportarjeta', ['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2'],'servicios'=>$tipoServicio])}}"
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
                    <div class="widget-inline-box">
                        <div class="table-responsive m-b-20">
                            @if(isset($data['regalo']) && count($data['regalo'])>0)
                                <h5>Regalo</h5>
                                @foreach($data['regalo']['detalles'] as $detalles)
                                    @foreach($detalles as $montos)
                                        <div class="col-md-6 p-0">
                                            <b>Fecha: </b><span>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$montos[0]->fecha_activacion)->toDateString()}}</span></div>
                                        <div class="col-md-6 text-right"><b>Monto
                                                Inicial: </b><span>{{$montos[0]->monto_inicial}}</span></div>
                                        <br>
                                        <br>
                                        <table id="datatable" class="table table-striped table-bordered datatable"
                                               width="100%">
                                            <thead>
                                            <tr>
                                                <th>Numero tarjeta</th>
                                                <th>Monto inicial</th>
                                                <th>factura</th>
                                                <th>Usuario</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($montos as $monto)
                                                <tr>
                                                    <td>{{$monto->numero_tarjeta}}</td>
                                                    <td>{{$monto->monto_inicial}}</td>
                                                    <td>{{$monto->factura}}</td>
                                                    <td>{{$monto->getUser->name}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <br>
                                        <br>
                                    @endforeach
                                @endforeach
                            @endif
                            @if(isset($data['bono']) && count($data['bono'])>0)
                                <h5>Bonos empresariales</h5>
                                @foreach($data['bono']['detalles'] as $detalles)
                                    @foreach($detalles as $montos)
                                        <div class="col-md-6 p-0">
                                            <b>Fecha: </b><span>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$montos[0]->fecha_activacion)->toDateString()}}</span></div>
                                        <div class="col-md-6 text-right"><b>Monto
                                                Inicial: </b><span>{{$montos[0]->monto_inicial}}</span></div>
                                        <br>
                                        <br>
                                        <table id="datatable" class="table table-striped table-bordered datatable"
                                               width="100%">
                                            <thead>
                                            <tr>
                                                <th>Numero tarjeta</th>
                                                <th>Monto inicial</th>
                                                <th>factura</th>
                                                <th>Usuario</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($montos as $monto)
                                                <tr>
                                                    <td>{{$monto->numero_tarjeta}}</td>
                                                    <td>{{$monto->monto_inicial}}</td>
                                                    <td>{{$monto->getContrato->n_contrato}}</td>
                                                    <td>{{$monto->getUser->name}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <br>
                                        <br>
                                    @endforeach
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $('[data-toggle="tooltip"]').tooltip();
    $('.datatable').DataTable({
        "language": {
            "url": "{!!route('datatable_es')!!}"
        },
    });
</script>

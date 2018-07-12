<div class="row">
    <div class="col-sm-12">
        <div class="card-box widget-inline">
            <div class="row">
                <div class="widget-inline-box text-right">
                    <strong>Exportar: </strong>
                    <div class="btn-group">
                        <a href="{{route('exportarpdfprimeravez',['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2']])}}"
                           class="btn btn-sm btn-custom" data-toggle="tooltip" title="PDF">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>
                        <a href="{{route('exportarexcelprimeravez', ['fecha1'=>$rango['fecha1'],'fecha2'=>$rango['fecha2']])}}"
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

                            @if(count($transacciones["regalo"]["detproducto"])>0)
                                <h5>Regalo</h5>
                                <table id="datatable" class="table table-striped table-bordered datatable"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th>Numero tarjeta</th>
                                        <th>Monto inicial</th>
                                        <th>factura</th>
                                        <th>No. transaccion</th>
                                        <th>Codigo terminal</th>
                                        <th>Sucursal</th>
                                        <th>Valor</th>
                                        <th>Fecha</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @for($i=0; $i < count($transacciones["regalo"]["detproducto"]); $i++)
                                        <tr>
                                            <td>{{$transacciones["regalo"]["detproducto"][$i]->numero_tarjeta}}</td>
                                            <td>{{$transacciones["regalo"]["detproducto"][$i]->monto_inicial}}</td>
                                            <td>{{$transacciones["regalo"]["detproducto"][$i]->factura}}</td>
                                            <td>{{$transacciones["regalo"]["dettransa"][$i]->numero_transaccion}}</td>
                                            <td>{{$transacciones["regalo"]["dettransa"][$i]->codigo_terminal}}</td>
                                            <td>{{$transacciones["regalo"]["dettransa"][$i]->nombre}}</td>
                                            <td>{{$transacciones["regalo"]["dettransa"][$i]->valor}}</td>
                                            <td>{{$transacciones["regalo"]["dettransa"][$i]->fecha}}</td>
                                        </tr>
                                    @endfor
                                    </tbody>
                                </table>
                                <br>
                                <br>
                            @endif
                                @if(count($transacciones["bono"]["detproducto"])>0)
                                    <h5>Bonos empresariales</h5>
                                    <table id="datatable" class="table table-striped table-bordered datatable"
                                           width="100%">
                                        <thead>
                                        <tr>
                                            <th>Numero tarjeta</th>
                                            <th>Monto inicial</th>
                                            <th>No. Contrato</th>
                                            <th>No. transaccion</th>
                                            <th>Codigo terminal</th>
                                            <th>Sucursal</th>
                                            <th>Valor</th>
                                            <th>Fecha</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @for($i=0; $i < count($transacciones["bono"]["detproducto"]); $i++)
                                            <tr>
                                                <td>{{$transacciones["bono"]["detproducto"][$i]->numero_tarjeta}}</td>
                                                <td>{{$transacciones["bono"]["detproducto"][$i]->monto_inicial}}</td>
                                                <td>{{$transacciones["bono"]["detproducto"][$i]->getContrato->n_contrato}}</td>
                                                <td>{{$transacciones["bono"]["dettransa"][$i]->numero_transaccion}}</td>
                                                <td>{{$transacciones["bono"]["dettransa"][$i]->codigo_terminal}}</td>
                                                <td>{{$transacciones["bono"]["dettransa"][$i]->nombre}}</td>
                                                <td>{{$transacciones["bono"]["dettransa"][$i]->valor}}</td>
                                                <td>{{$transacciones["bono"]["dettransa"][$i]->fecha}}</td>
                                            </tr>
                                        @endfor
                                        </tbody>
                                    </table>
                                    <br>
                                    <br>
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
    $('#datatable').DataTable({
        "language": {
            "url": "{!!route('datatable_es')!!}"
        },
    });
</script>
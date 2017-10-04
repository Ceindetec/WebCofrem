

<div class="row">
    <div class="col-sm-12">
        <h5>Resultado</h5>
        <div class="card-box widget-inline">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="widget-inline-box" >
                        <div class="table-responsive m-b-20">
                            <table id="datatable" class="table table-striped table-bordered" width="100%">
                                <thead>
                                <tr>
                                    <th>Numero tarjeta</th>
                                    <th>numero transaccion</th>
                                    <th>Valor</th>
                                    <th>Sucursal</th>
                                    <th>Terminal</th>
                                    <th>Fecha</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($transacciones as $transacione)
                                    <tr>
                                        <td>{{$transacione->numero_tarjeta}}</td>
                                        <td>{{$transacione->numero_transaccion}}</td>
                                        <td>{{$transacione->valorTransacion[0]->total}}</td>
                                        <td>{{$transacione->getSucursal->nombre}}</td>
                                        <td>{{$transacione->codigo_terminal}}</td>
                                        <td>{{$transacione->fecha}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






<script>
$('#datatable').DataTable({
    "language": {
        "url": "{!!route('datatable_es')!!}"
    },
});
</script>
<div class="row">
    <div class="col-sm-12">
        <h5>Exportar</h5>
        <div class="card-box widget-inline">
            <div class="row">
                <div class="widget-inline-box text-right">
                    <strong>Exportar: </strong>
                    <div class="btn-group">
                        <a href="{{route('pdfsaldotarjeta',['numero_tarjeta'=>$numero_tarjeta,'resultado'=>$resultado])}}"
                           class="btn btn-sm btn-custom" data-toggle="tooltip" title="PDF">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
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
            <h5>Servicios activos de la tarjeta No. {{$numero_tarjeta}}</h5>
            @if(sizeof($resultado)>0)
                <div class="card-box widget-inline">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12">
                            <div class="widget-inline-box">
                                <div class="table-responsive m-b-20">
                                    <table id="datatable" class="table table-striped table-bordered" width="100%">
                                        <thead>
                                        <tr>
                                            <th>Tipo de servicio</th>
                                            <th>Monto inicial</th>
                                            <th>Saldo</th>
                                            <th>Fecha de vencimiento</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($resultado as $miresul)
                                            <tr>
                                                <td>{{$miresul["tipo_servicio"]}}</td>
                                                <td>{{$miresul["monto_inicial"]}}</td>
                                                <td>{{$miresul["saldo"]}}</td>
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
            @if(sizeof($resultado)==0)
                <p align="center">No hay registros</p>
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
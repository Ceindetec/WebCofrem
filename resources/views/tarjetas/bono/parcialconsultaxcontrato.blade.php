<div class="table-responsive m-b-20">
    <table id="datatable" class="table table-striped table-bordered" width="100%">
        <thead>
        <tr>
            <th>Numero tarjeta</th>
            <th>Numero contrato</th>
            <th>Monto</th>
            <th>Estado</th>
        </tr>
        </thead>
        <tbody>
          @foreach($tarjetas as $tarjeta)
            <tr>
                <td>{{$tarjeta->numero_tarjeta}}</td>
                <td>{{$contrato->n_contrato}}</td>
                <td>{{$tarjeta->monto_tar}}</td>
                <td>{{$tarjeta->estado_tar}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $('$datatable').DataTable();
</script>
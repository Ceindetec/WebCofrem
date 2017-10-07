<div class="table-responsive m-b-20">
    <table id="datatable" class="table table-striped table-bordered" width="100%">
        <thead>
        <tr>
            <th>Numero de contrato</th>
            <th>Valor</th>
            <th>Fecha creación</th>
            <th>Accion</th>
        </tr>
        </thead>
        <tbody>
        @foreach($contratos as $contrato)
            <tr>
                <td>{{$contrato->n_contrato}}</td>
                <td>{{$contrato->valor_contrato}}</td>
                <td>{{$contrato->fecha_creacion}}</td>
                <td>
                        <button type="button" id="detalle" class="btn btn-custom waves-effect waves-light" onclick="consultarc({{$contrato->n_contrato}})">Detalle</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
    $('#datatable').DataTable();
    function consultarc(ncontrato) {
        $('#tablaoculta').load('{{route('bono.consultaxcontratop')}}',{numcontrato:ncontrato});
    }
</script>
<div class="card-box widget-inline">
    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="widget-inline-box">
                <button type="button" id="activar" class="btn btn-custom waves-effect waves-light" onclick="activar('{{$contrato->n_contrato}}')">Activar todas</button>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive m-b-20">
    <br>
    <table id="datatable" class="table table-striped table-bordered" width="100%">
        <thead>
        <tr>
            <th>Número tarjeta</th>
            <th>Número contrato</th>
            <th>Monto</th>
            <th>Estado</th>
        </tr>
        </thead>
        <tbody>
          @foreach($detalles as $detalle)
            <tr>
                <td>{{$detalle->numero_tarjeta}}</td>
                <td>{{$contrato->n_contrato}}</td>
                <td>{{$detalle->monto_inicial}}</td>
                <td>{{$detalle->estado}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $('#datatable').DataTable({
        "language": {
            "url": "{!!route('datatable_es')!!}"
        },
    });
    function activar(ncontrato) {
        swal({
                title: '¿Estas seguro?',
                text: "¡Desea activar todas las tarjetas bono asociadas a este contrato!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                buttonsStyling: false
            },
            function () {
                $.ajax({
                    url: "{{route('bono.activarxcontrato')}}",
                    data: {'ncontrato': ncontrato},
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function () {
                        cargando();
                    },
                    success: function (result) {
                        if (result.estado) {
                            swal(
                                {
                                    title: 'Bien!!',
                                    text: result.mensaje,
                                    type: 'success',
                                    confirmButtonColor: '#4fa7f3'
                                }
                            );
                            $('#tablaoculta').load('{{route('bono.consultaxcontratop')}}',{numcontrato:"{{$contrato->n_contrato}}"});
                        } else if (result.estado == false) {
                            swal(
                                'Error!!',
                                result.mensaje,
                                'error'
                            )
                        } else {
                            html = '';
                            for (i = 0; i < result.length; i++) {
                                html += result[i] + '\n\r';
                            }
                            swal(
                                'Error!!',
                                html,
                                'error'
                            )
                        }
                    },
                    error: function (xhr, status) {
                        var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                        swal(
                            'Error!!',
                            message,
                            'error'
                        )
                    },
                    // código a ejecutar sin importar si la petición falló o no
                    complete: function (xhr, status) {
                        fincarga();
                    }
                });
            });
    }
</script>
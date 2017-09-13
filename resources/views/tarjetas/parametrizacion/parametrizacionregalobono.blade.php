<div class="card-box">
    <h4 class="m-t-0">Paga plastico</h4>
    <br>
    {{Form::open(['route'=>['tarjeta.parametro.pagaplastico',$servicio->codigo], 'class'=>'form-inline', 'id'=>'prametropagaplastico'])}}
    <div class="form-group">
        <div class="radio radio-custom">
            <input type="radio" name="pagaplatico" id="radio03" value="1">
            <label for="radio03">
                Si
            </label>
        </div>
        <div class="radio radio-custom">
            <input type="radio" name="pagaplatico" id="radio03" value="0">
            <label for="radio03">
                No
            </label>
        </div>
    </div>
    &nbsp;
    <div class="form-group">
    <button type="submit" class="btn btn-custom">Actualizar</button>
    </div>
    <p>&nbsp;</p>
    {{Form::close()}}

    <div class="table-responsive m-b-20">
        <table id="datatablepagaplastico" class="table table-striped table-bordered" width="100%">
            <thead>
            <tr>
                <th>Paga Plastico</th>
                <th>Estado</th>
                <th>Creacion</th>
            </tr>
            </thead>
        </table>
    </div>
</div>


<div class="card-box">
    <h4 class="m-t-0">Procentaje de administracion</h4>
    <br>
    {{Form::open(['route'=>['tarjeta.parametro.administracion'], 'class'=>'form-inline', 'id'=>'parametroadministracion'])}}
    <div class="form-group">
        <label for="valor">Administracion: </label>
        <div class="input-group">
            <input type="number" name="porcentaje" class="form-control" required maxlength="2"
                   data-parsley-type="number"
                   data-parsley-max="100" data-parsley-min="0">
            <span class="input-group-addon"><i class="fa fa-percent" aria-hidden="true"></i></span>
        </div>
    </div>


    <button type="submit" class="btn btn-custom">Agregar</button>
    <p>&nbsp;</p>
    {{Form::close()}}

    <div class="table-responsive m-b-20">
        <table id="datatableadministracion" class="table table-striped table-bordered" width="100%">
            <thead>
            <tr>
                <th>Paga</th>
                <th>Estado</th>
                <th>Creacion</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div class="card-box">
    <h4 class="m-t-0">Ceuntas contables</h4>
    <div class="table-responsive m-b-20">
        <table id="datatablehistorial" class="table table-striped table-bordered" width="100%">
            <thead>
            <tr>
                <th>id</th>
                <th>Evento</th>
                <th>Antrior valor</th>
                <th>Nuevo valor</th>
                <th>Fecha</th>
                <th>Usuario</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive m-b-20">
            <table id="datatable" class="table table-striped table-bordered" width="100%">
                <thead>
                <tr>
                    <th>Nit</th>
                    <th>Razon social</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


<script>
    var tablePaga;
    $(function () {

        $("#prametropagaplastico").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
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
                    tablePaga.ajax.reload();
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

        tablePaga = $('#datatablepagaplastico').DataTable({
            processing: true,
            serverSide: true,
            "language": {
                "url": "{!!route('datatable_es')!!}"
            },
            ajax: {
                url: "{!!route('gridpagaplastico',['codigo'=>$servicio->codigo])!!}",
                "type": "get"
            },
            columns: [
                {
                    data: 'pagaplastico',
                    name: 'pagaplastico',
                    render: function (data) {
                        if(data==1){
                            return 'Si';
                        }else{
                            return 'No';
                        }
                    }
                },
                {data: 'estado', name: 'estado',
                    render: function(data){
                        if(data=='A')
                            return 'Activo';
                        else
                            return 'Inactivo';
                    }
                },
                {data: 'created_at', name: 'created_at'},
            ],
            "order": [[ 2, "desc" ]]
        });
    })
</script>
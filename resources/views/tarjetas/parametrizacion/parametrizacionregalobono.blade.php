<div class="card-box">
    <h4 class="m-t-0">Paga plastico</h4>
    <br>
    {{Form::open(['route'=>['tarjeta.parametro.pagaplastico',$servicio->codigo], 'class'=>'form-inline', 'id'=>'prametropagaplastico'])}}
    <div class="form-group">
        <div class="radio radio-custom">
            <input type="radio" name="pagaplatico" id="radio03" value="1" checked>
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
                <th>Actualizacion</th>
            </tr>
            </thead>
        </table>
    </div>
</div>


<div class="card-box">
    <h4 class="m-t-0">Procentaje de administracion</h4>
    <br>
    {{Form::open(['route'=>['tarjeta.parametro.administracion', $servicio->codigo], 'class'=>'form-inline', 'id'=>'parametroadministracion'])}}
    <div class="form-group">
        <label for="valor">Administracion: </label>
        <div class="input-group">
            <input type="number" name="porcentaje" class="form-control" required maxlength="2"
                   data-parsley-type="number"
                   data-parsley-max="100" data-parsley-min="0">
            <span class="input-group-addon"><i class="fa fa-percent" aria-hidden="true"></i></span>
        </div>
    </div>

    <div class="form-group">
    <button type="submit" class="btn btn-custom">Agregar</button>
    </div>
    <p>&nbsp;</p>
    {{Form::close()}}

    <div class="table-responsive m-b-20">
        <table id="datatableadministracion" class="table table-striped table-bordered" width="100%">
            <thead>
            <tr>
                <th>Administación</th>
                <th>Estado</th>
                <th>Creacion</th>
                <th>Actualizacion</th>
                <th>accion</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div class="card-box">
    <h4 class="m-t-0">Ceuntas contables</h4>
    <br>
    {{Form::open(['route'=>['tarjeta.parametro.cuentaRB', $servicio->codigo], 'class'=>'form-inline', 'id'=>'parametrocuentacontable'])}}
    <div class="form-group">
        <label for="valor">Cuenta contable: </label>
        <div class="input-group">
            <input type="text" name="cuentacontable" class="form-control" required maxlength="10" data-parsley-type="number" data-parsley-min="0">
        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-custom">Agregar</button>
    </div>
    <p>&nbsp;</p>
    {{Form::close()}}
    <div class="table-responsive m-b-20">
        <table id="datatablecuentaContables" class="table table-striped table-bordered" width="100%">
            <thead>
            <tr>
                <th>Cuenta</th>
                <th>Estado</th>
                <th>Creacion</th>
                <th>Actualizacion</th>
            </tr>
            </thead>
        </table>
    </div>
</div>


<script>
    var tablePaga, tableAdministracion, tablecuentaContableRB;
    $(function () {
        $("#prametropagaplastico").parsley();
        $("#parametroadministracion").parsley();
        $("#parametrocuentacontable").parsley();
        $("form").submit(function (e) {
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
                    tableAdministracion.ajax.reload();
                    tablecuentaContableRB.ajax.reload();
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
                        if(data=='A'){
                            html= '<div class="label-success" > <strong style="color:#fff">Activo</strong></div>';
                            return html;
                        }
                        else
                            return 'Inactivo';
                    }
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
            ],
            "order": [[ 2, "desc" ]]
        });

        tableAdministracion = $('#datatableadministracion').DataTable({
            processing: true,
            serverSide: true,
            "language": {
                "url": "{!!route('datatable_es')!!}"
            },
            ajax: {
                url: "{!!route('gridadministraciontarjetas',['codigo'=>$servicio->codigo])!!}",
                "type": "get"
            },
            columns: [
                {data: 'porcentaje', name: 'porcentaje',},
                {data: 'estado', name: 'estado',
                    render: function(data){
                        if(data=='A'){
                            html= '<div class="label-success" > <strong style="color:#fff">Activo</strong></div>';
                            return html;
                        }
                        else
                            return 'Inactivo';
                    }
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'action', name: 'action'},
            ],
            "order": [[ 2, "desc" ]]
        });

        tablecuentaContableRB = $('#datatablecuentaContables').DataTable({
            processing: true,
            serverSide: true,
            "language": {
                "url": "{!!route('datatable_es')!!}"
            },
            ajax: {
                url: "{!!route('gridcuentascontables',['codigo'=>$servicio->codigo])!!}",
                "type": "get"
            },
            columns: [
                {data: 'cuenta', name: 'cuenta',},
                {data: 'estado', name: 'estado',
                    render: function(data){
                        if(data=='A'){
                            html= '<div class="label-success" > <strong style="color:#fff">Activo</strong></div>';
                            return html;
                        }
                        else
                            return 'Inactivo';
                    }
                },
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
            ],
            "order": [[ 2, "desc" ]]
        });

    });

    function eliminarAdministracion(id) {
        swal({
                title: '¿Estas seguro?',
                text: "Deseas eliminar esta administracion!",
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
                    url: "{{route('tarjeta.parametro.administracion.eliminar')}}",
                    data: {'id': id},
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
                            tableAdministracion.ajax.reload();
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
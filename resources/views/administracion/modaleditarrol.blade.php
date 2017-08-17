<div id="editarrol">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Editar Rol</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <h4 class="m-t-0 header-title"><b>Inforación básica</b></h4>
                {{Form::model($rol,['route'=>['editar.rolp',$rol->id], 'class'=>'form-horizontal', 'id'=>'edirol'])}}
                <div class="form-group">
                    <label class="col-md-2 control-label">Nombre</label>
                    <div class="col-md-10">
                        {{Form::text('name', null ,['class'=>'form-control', "required"=>"true"])}}
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label">Slug</label>
                    <div class="col-md-10">
                        {{Form::text('slug', null ,['class'=>'form-control', "required"=>"true"])}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Descripción</label>
                    <div class="col-md-10">
                        {{Form::textarea('description', null ,['class'=>'form-control', 'rows'=>5, "required"=>"true"])}}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"></label>
                    <div class="col-md-10">
                        <button type="submit" class="btn btn-custom waves-effect waves-light">Guardar</button>
                    </div>
                </div>
                {{Form::close()}}

                <div class="col-md-12">
                    <h4 class="m-t-0 header-title"><b>Editar permisos</b></h4>

                    <div class="row">
                        {{Form::open(['route'=>['rol.agrepermiso',$rol->id], 'class'=>'form-horizontal', 'id'=>'agregarpermisorol'])}}
                        <div class="form-group">
                            <label class="col-md-2 control-label">Permisos</label>
                            <div class="col-md-6">
                                <select class="form-control"  id="selectpermisos" name="permisos[]" multiple data-placeholder="Seleccione ..." style="width: 100%" required>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-custom waves-effect waves-light">Agregar</button>
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                    <br/>

                        <div class="table-responsive m-b-20">
                            <table id="datatablepermisos" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>id</th>
                                    <th>Nombre</th>
                                    <th>Descripcion</th>
                                    <th>Accion</th>
                                </tr>
                                </thead>
                            </table>
                        </div>


                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>

    </div>
</div>

<script>
    var table2;
    $(function () {
        $("#edirol").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            modalBs.modal('hide');
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
                        )
                    } else {
                        swal(
                            'Error!!',
                            result.mensaje,
                            'error'
                        )
                    }
                    table.ajax.reload();
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

        $("#agregarpermisorol").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            //modalBs.modal('hide');
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
                        )
                    } else {
                        swal(
                            'Error!!',
                            result.mensaje,
                            'error'
                        )
                    }
                    table2.ajax.reload();
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

        table2 = $('#datatablepermisos').DataTable({
            processing: true,
            serverSide: true,
            "language": {
                "url": "{!!route('datatable_es')!!}"
            },
            ajax: "{!!route('gridpermisosrol', ['id'=>$rol->id])!!}",

            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            "lengthMenu": [[5, 10, 20], [5, 10, 20]]
        });

        $("#selectpermisos").select2({
            placeholder: "Seleccione...",
            minimumInputLength: 1,
            language: "es",
            ajax: {
                url: "{{route('selectpermisos')}}",
                dataType: 'json',
                type: "GET",
                quietMillis: 50,
                data: function (params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
            },
        });

    })

    function eliminarpermi(idPermiso, idRol) {
        swal({
                title: 'Estas seguro?',
                text: "Deseas eliminar este permiso del rol!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si, eliminar!',
                cancelButtonText: 'No, cancelar!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                buttonsStyling: false
            },
            function () {
                $.ajax({
                    url: "{{route('eliminarpermisorol')}}",
                    data: {'idPermiso': idPermiso, 'idRol': idRol},
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function () {
                        cargando();
                    },
                    success: function (result) {
                        console.log(result);
                        setTimeout(function () {
                            if (result.estado) {
                                swal({
                                    title: 'Bien!!',
                                    text: result.mensaje,
                                    type: 'success',
                                    confirmButtonColor: '#4fa7f3'
                                });
                                table2.ajax.reload();
                            } else {
                                swal(
                                    'Error!!',
                                    result.mensaje,
                                    'error'
                                )
                            }

                        }, 200);
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
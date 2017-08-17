<div id="editarpermiso">
    {{Form::open(['route'=>['usuario.crearp'], 'class'=>'form-horizontal', 'id'=>'crearusuario'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Editar permiso</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label">Nombre</label>
                <div class="col-md-10">
                    {{Form::text('name', null ,['class'=>'form-control', "required"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Email</label>
                <div class="col-md-10">
                    {{Form::text('email', null ,['class'=>'form-control', "required"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Roles</label>
                <div class="col-md-10">
                    <select class="form-control" multiple="multiple" name="roles[]" data-placeholder="Seleccione ..." style="width: 100%" id="selectroles"></select>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-custom waves-effect waves-light">Guardar</button>
    </div>
    {{Form::close()}}
</div>

<script>
    $(function () {
        $("#crearusuario").submit(function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url : form.attr('action'),
                data : form.serialize(),
                type : 'POST',
                dataType : 'json',
                beforeSend: function () {
                    cargando();
                },
                success : function(result) {
                    if(result.estado){
                        swal(
                            {
                                title: 'Bien!!',
                                text: result.mensaje,
                                type: 'success',
                                confirmButtonColor: '#4fa7f3'
                            }
                        )
                        modalBs.modal('hide');
                    }else{
                        swal(
                            'Error!!',
                            result.mensaje,
                            'error'
                        )
                    }
                    table.ajax.reload();
                },
                error : function(xhr, status) {
                    var message = "Error de ejecución: " + xhr.status + " " + xhr.statusText;
                    swal(
                        'Error!!',
                        message,
                        'error'
                    )
                },
                // código a ejecutar sin importar si la petición falló o no
                complete : function(xhr, status) {
                    fincarga();
                }
            });
        })


        $("#selectroles").select2({
            placeholder: "Seleccione...",
            minimumInputLength: 1,
            ajax: {
                url: "{{route('selectroles')}}",
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
            language: "es",
            cache: true
        });

    })


</script>
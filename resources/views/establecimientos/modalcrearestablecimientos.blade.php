<div id="modalcrearestablecimientos">
    {{Form::open(['route'=>['establecimiento.crearp'], 'class'=>'form-horizontal', 'id'=>'crearestablecimientos'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Agregar establecimiento comercial</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label">Nit</label>
                <div class="col-md-10">
                    {{Form::text('nit', null ,['class'=>'form-control', "required", "maxlength"=>"10", "data-parsley-type"=>"number"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Razon social</label>
                <div class="col-md-10">
                    {{Form::text('razon_social', null ,['class'=>'form-control', "required", "maxlength"=>"40", "data-parsley-pattern"=>"^[a-zA-Z0-9]+(\s*[a-zA-Z0-9]*)*[a-zA-Z0-9]+$"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">E-mail</label>
                <div class="col-md-10">
                    {{Form::email('email', null ,['class'=>'form-control', "required"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Telefono</label>
                <div class="col-md-10">
                    {{Form::text('telefono', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10"])}}
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Celular</label>
                <div class="col-md-10">
                    {{Form::text('celular', null ,['class'=>'form-control', "required", "data-parsley-type"=>"number", "maxlength"=>"10"])}}
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
        $("#crearestablecimientos").parsley();
        $("#crearestablecimientos").submit(function (e) {
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
                    }else if(result.estado == false){
                        swal(
                            'Error!!',
                            result.mensaje,
                            'error'
                        )
                    }else{
                        html='';
                        for(i=0; i<result.length;i++){
                            html+=result[i]+'\n\r';
                        }
                        swal(
                            'Error!!',
                            html,
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
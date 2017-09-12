<div id="modalcrearcomvenio">
    {{Form::open(['route'=>['convenio.crearp',$establecimiento_id], 'class'=>'form-horizontal', 'id'=>'crearconvenio'])}}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Agregar convenio</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label">Numero convenio</label>
                <div class="col-md-10">
                    {{Form::text('numero_convenio', null ,['class'=>'form-control', "required", "maxlength"=>"10", "data-parsley-type"=>"number"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Fecha de inicio</label>
                <div class="col-md-10">
                    <div class="input-group">
                        {{Form::text('fecha_inicio', null ,['class'=>'form-control datepicker', "id"=>"fecha_inicio", "required", "placeholder"=>"dd/mm/yyyy", "data-parsley-dateformat"=>"date"  ])}}
                        <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                    </div><!-- input-group -->
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Fecha de finalización</label>
                <div class="col-md-10">
                    <div class="input-group">
                        {{Form::text('fecha_fin', null ,['class'=>'form-control datepicker', "id"=>"fecha_fin","required", "placeholder"=>"dd/mm/yyyy","data-parsley-dateformat"=>"date", "data-parsley-fechainferior"=>"date" ])}}
                        <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                    </div><!-- input-group -->
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-2 control-label">Prorrogable</label>
                <div class="col-md-10">
                    <div class="checkbox checkbox-custom">
                        {{Form::checkbox('prorrogable', '1',true)}}
                        <label>&nbsp;</label>
                    </div>
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
    window.Parsley.addValidator('dateformat', {
        validate: function(value, id) {
            var isValid = moment(value, "DD/MM/YYYY", true).isValid();
            return isValid;
        },
        messages: {
            es: 'proporcione la fecha en formato dd/mm/yyyy',
        }
    });
    window.Parsley.addValidator('fechainferior', {
        validate: function(value, id) {
            var valor = moment(value, "DD/MM/YYYY", true);
            var valor2 = moment($('#fecha_inicio').val(), "DD/MM/YYYY", true);
            if(valor2>valor)
                return false;
        },
        messages: {
            es: 'La fecha de finalizacion no puede ser inferior',
        }
    });

    $(function () {
        
        $('#fecha_inicio').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            clearBtn: true,
            language: 'es'
        }).on('changeDate',function(e){
            $('#fecha_fin').datepicker('update', $(this).val());
            $('#fecha_fin').datepicker('setStartDate', $(this).val());
        });

        $('#fecha_fin').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            clearBtn: true,
            language: 'es'
        });

        $("#crearconvenio").parsley();

        $("#crearconvenio").submit(function (e) {
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
                        resetInfo(result.data);
                        modalBs.modal('hide');
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
        })


    })


</script>
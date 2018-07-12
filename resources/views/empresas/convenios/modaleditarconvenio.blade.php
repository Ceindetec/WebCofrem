<div id="modaleditarconvenio">
{{Form::model($convenio_emp,['route'=>['empresas.convenio.editarp',$convenio_emp->id, $empresa->id],'files'=>'true', 'class'=>'form-horizontal', 'id'=>'editarconvenio', 'target'=>"_blank",'role'=>'form','method'=>'POST'])}}
<!-- { {Form::open(['route'=>['empresas.convenio.crearp',$empresa->id],'files'=>'true', 'class'=>'form-horizontal', 'id'=>'crearconvenio', 'target'=>"_blank",'role'=>'form','method'=>'POST'])}}-->
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Editar convenio de la empresa: {{$empresa->razon_social}}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 control-label">Número convenio</label>
                <div class="col-md-10">
                    {{Form::text('numero_convenio', null ,['class'=>'form-control', "readonly", "maxlength"=>"10", "data-parsley-type"=>"number"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">Fecha de inicio</label>
                <div class="col-md-10">
                    <div class="input-group">
                        {{Form::text('fecha_inicio', null ,['class'=>'form-control datepicker', "id"=>"fecha_inicio", "readonly", "placeholder"=>"dd/mm/yyyy", "data-parsley-dateformat"=>"date"  ])}}
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
                <label class="col-md-2 control-label">Tipo</label>
                <div class="col-md-10">
                    {{Form::select("tipo",['L' => 'Libre Inversión', 'C' => 'Cupo rotativo', 'A' => 'Los dos anteriores'],null,['class'=>'form-control ', "tabindex"=>"4",'id'=>'tarjeta_codigo', "required"])}}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Seleccionar archivo</label>
                <div class="col-sm-10">
                    {{Form::file('archivo',['class'=>'filestyle', "data-buttontext"=>"Buscar archivo", "tabindex"=>"5",'id'=>'archivo','accept'=>'.pdf'])}}
                </div>
                @if($convenio_emp->pdf != null)
                    <label>Seleccione solo si desea cambiar el actual</label>
                    <div class="col-md-2">
                        <a href="{{url($convenio_emp->pdf)}}" target="_blank">Ver archivo actual</a>
                    </div>
                @endif
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
            es: 'La fecha de finalización no puede ser inferior',
        }
    });

    $(function () {

        $("#archivo").filestyle({
            buttonText: "Buscar archivo",
        });

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

        $("#editarconvenio").parsley();

        $("#editarconvenio").submit(function (e) {
            e.preventDefault();
            var form = $(this);

            var formData = new FormData(form[0]);
            formData.append( 'archivo', $( '#archivo' )[0].files[0] );

            $.ajax({
                type: "POST",
                context: document.body,
                url: '{{route("empresas.convenio.editarp", $convenio_emp->id)}}',
                processData: false,
                contentType: false,
                data: formData,
                beforeSend: function () {
                    cargando();
                },
                success: function (data) {
                    if (data.estado) {
                        swal(
                            {
                                title: 'Bien!!',
                                text: data.mensaje,
                                type: 'success',
                                confirmButtonColor: '#4fa7f3'
                            }
                        );
                        form.reset();
                        modalBs.modal('hide');
                    } else {
                        swal(
                            'Error!!',
                            data.mensaje,
                            'error'
                        )
                    }
                    table.ajax.reload();
                },
                error: function (xhr, status) {
                },
                // código a ejecutar sin importar si la petición falló o no
                complete: function (xhr, status) {
                    fincarga();
                }
            });
        })
    })
</script>
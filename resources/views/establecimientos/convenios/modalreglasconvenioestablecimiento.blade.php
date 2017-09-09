<div id="modalreglasconvenio">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Parametros convenio No {{$convenio->numero_convenio}}</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            {{Form::open(['route'=>['actualizar.plazo',$convenio->id], 'class'=>'form-horizontal', 'id'=>'actualizarplazo'])}}
            <div class="form-group">
                <label class="col-md-2 control-label">Plazo de pago</label>
                <div class="col-md-8">
                    <input type="number" name="dias" class="form-control" value="{{$plazo==null?'':$plazo->dias}}"
                           required maxlength="2" data-parsley-type="number" data-parsley-max="10" data-parsley-min="5">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-custom waves-effect waves-light">Guardar</button>
                </div>
            </div>
            {{Form::close()}}

            {{Form::open(['route'=>['actualizar.frecuencia',$convenio->id], 'class'=>'form-horizontal', 'id'=>'actualizarfrecuencia'])}}
            <div class="form-group">
                <label class="col-md-2 control-label">Frecuencia de corte</label>
                <div class="col-md-8">
                    {{Form::select("frecuencia_corte",[''=>'Seleccione..','S'=>'SEMANAL','Q'=>'QUINCENAL','M'=>'MENSUAL'],null,['class'=>'form-control frecuencia_corte', "tabindex"=>"2"])}}
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-custom waves-effect waves-light">Guardar</button>
                </div>
            </div>
            {{Form::close()}}


            <div class="col-md-12">
                <h4 class="m-t-0 header-title"><b>Rangos</b></h4>
                {{Form::open(['route'=>['convenio.nuevorango',$convenio->id], 'class'=>'form-horizontal', 'id'=>'nuevorango'])}}
                <div class="form-group">

                    <label class="col-md-2 control-label">Valor minimo</label>
                    <div class="col-md-10">
                        <div class="input-group m-t-10">
                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                            <input type="text" id="valor_min" name="valor_min" class="form-control dinero" maxlength="20" placeholder="" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label">Valor maximo</label>
                    <div class="col-md-10">
                        <div class="input-group m-t-10">
                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                            <input type="text" id="valor_max" name="valor_max" data-parsley-valorminimo="" maxlength="20" class="form-control dinero" placeholder="" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                        <label class="col-md-2 control-label">Plazo de pago</label>
                    <div class="col-md-10">
                        <input type="number" name="dias" class="form-control" required maxlength="2" data-parsley-type="number"
                               data-parsley-max="10" data-parsley-min="5">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label">administracion</label>
                    <div class="col-md-10">
                        <div class="input-group m-t-10">
                            <input type="number" name="porcentaje" class="form-control" required maxlength="2" data-parsley-type="number"
                                   data-parsley-max="100" data-parsley-min="0">
                            <span class="input-group-addon"><i class="fa fa-percent" aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">&nbsp;</label>
                    <div class="col-md-10">
                        <button type="submit" class="btn btn-custom waves-effect waves-light">Agregar</button>
                    </div>
                </div>
                {{Form::close()}}
                <p>&nbsp;</p>
                <div class="table-responsive m-b-20">
                    <table id="datatablerangos" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>V. inicial</th>
                            <th>V. final</th>
                            <th>P. pago</th>
                            <th>%</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                    </table>
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
    var valfrecuencia = '{{$frecuencia==null?'y':$frecuencia->frecuencia_corte}}';

    window.Parsley.addValidator('valorminimo', {
        validateString: function(value) {
            var valor_max = value;
            var valor_min = $('#valor_min').val();
            while(valor_max.indexOf('.')!=-1){
                valor_max = valor_max.replace('.','');
            }
            while(valor_min.indexOf('.')!=-1){
                valor_min = valor_min.replace('.','');
            }
            valor_max = parseFloat(valor_max.replace(',','.'),2);
            valor_min = parseFloat(valor_min.replace(',','.'),2);
            return valor_max>valor_min;
        },
        messages: {
            es: 'El valor maximo debe ser superior al valor minimo',
        }
    });

    $(function () {

        $('.dinero').mask('000.000.000.000,00', {reverse: true});

        table2 = $('#datatablerangos').DataTable({
            processing: true,
            serverSide: true,
            "language": {
                "url": "{!!route('datatable_es')!!}"
            },
            ajax: {
                url: "{!!route('gridrangosconvenio',['id'=>$convenio->id])!!}",
                "type": "get"
            },
            columns: [
                {
                    data: 'valor_min',
                    name: 'valor_min',
                    render: function (data) {
                        return "$ "+enmascarar(data);
                    }
                },
                {
                    data: 'valor_max',
                    name: 'valor_max',
                    render: function (data) {
                        return "$ "+enmascarar(data);
                    }
                },
                {data: 'dias', name: 'dias'},
                {data: 'porcentaje', name: 'porcentaje'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            "columnDefs": [
                { className: "dt-right", "targets": [0,1,2,3] },
            ],
            order: [[1, 'asc']]
        });

        $("#actualizarplazo").parsley();
        $("#actualizarfrecuencia").parsley();
        $("#nuevorango").parsley();

        $("#actualizarplazo").submit(function (e) {
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

        $("#actualizarfrecuencia").submit(function (e) {
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

        $("#nuevorango").submit(function (e) {
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

        if(valfrecuencia!='y')
            $('.frecuencia_corte > option[value=' + valfrecuencia + ']').attr('selected', 'selected');

    })

function eliminarRango(id) {
    swal({
            title: '¿Estas seguro?',
            text: "¡Desea eliminar este rango!",
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
                url: "{{route('convenio.ramgo.eliminar')}}",
                data: {'id': id},
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    cargando();
                },
                success: function (result) {
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
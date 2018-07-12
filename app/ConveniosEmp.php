<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class ConveniosEmp extends Model
{
    //
    protected $table = "convenios_emps";
    public static $ESTADO_CONVENIO_ACTIVO = "A";
    public static $ESTADO_CONVENIO_INACTIVO = "I";
    public static $ESTADO_CONVENIO_PENDIENTE = "P";
    protected $fillable = [
        'numero_convenio', 'fecha_inicio', 'fecha_fin','pdf','tipo', 'estado', 'empresa_id'
    ];
}

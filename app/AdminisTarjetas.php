<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class AdminisTarjetas extends Model implements AuditableContract
{
    //
    use Auditable;

    public static $ESTADO_INACTIVO = "I";
    public static $ESTADO_ACTIVO = "A";

    protected $fillable = ['tarjeta_codigo','porcentaje'];

    public function getTipoTarjeta(){
        return $this->belongsTo('creditocofrem\Servicios','servicio_codigo','codigo');
    }
}

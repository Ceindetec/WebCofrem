<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Terminales extends Model implements AuditableContract
{
    //
    public static $ESTADO_TERMINAL_ACTIVA = "A";
    public static $ESTADO_TERMINAL_INACTIVA = "I";
    use Auditable;

    public function getSucursal(){
        return $this->belongsTo('creditocofrem\Sucursales','sucursal_id','id');
    }
}

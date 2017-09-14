<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Terminales extends Model implements AuditableContract
{
    //
    use Auditable;

    public function getSucursal(){
        return $this->belongsTo('creditocofrem\Sucursales','sucursal_id','id');
    }
}

<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Establecimientos extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [
        'nit', 'razon_social', 'email', 'telefono','celular',
    ];


    public function convenios()
    {
        return $this->hasMany('creditocofrem\ConveniosEsta','establecimiento_id','id');
    }

}

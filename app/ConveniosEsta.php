<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ConveniosEsta extends Model implements AuditableContract
{
    use Auditable;
    protected $fillable = [
        'numero_convenio', 'fecha_inicio', 'fecha_fin'
    ];

    public function getEstablecimiento()
    {
        return $this->belongsTo('creditocofrem\Establecimientos','establecimiento_id','id');
    }
}

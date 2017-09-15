<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Empresas extends Model implements AuditableContract
{
    //
    use Auditable;
    protected $fillable = [
        'nit', 'razon_social', 'representante_legal', 'municipio_codigo', 'email', 'telefono','celular','direccion','tipo',
    ];


    public function getMunicipio()
    {
        return $this->belongsTo('creditocofrem\municipios','municipio_codigo','codigo');
    }
}

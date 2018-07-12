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
        'nit', 'razon_social', 'representante_legal', 'municipio_codigo', 'email', 'telefono','celular','direccion','tipo','tipo_documento'
    ];


    public function getMunicipio()
    {
        return $this->belongsTo('creditocofrem\Municipios','municipio_codigo','codigo');
    }

    public function getEstablecimiento()
    {
        return $this->belongsTo('creditocofrem\Establecimientos', 'establecimiento_id','id');
    }
    public function getTipoDocumento()
    {
        return $this->belongsTo('creditocofrem\TipoDocumento','tipo_documento','tip_codi');
    }
}// se relaciona es con el modelo municipios

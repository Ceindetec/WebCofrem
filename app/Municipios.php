<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Municipios extends Model
{

    protected $primaryKey = 'codigo';
    public function getDepartamento()
    {
        return $this->belongsTo('creditocofrem\Departamentos', 'departamento_codigo','codigo');
    }
}

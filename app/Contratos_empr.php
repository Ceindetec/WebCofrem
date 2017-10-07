<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Contratos_empr extends Model
{

    protected $table = 'contratos_emprs';

    protected $fillable = [
        'n_contrato', 'valor_contrato', 'valor_impuesto', 'fecha', 'empresa_id','n_tarjetas','forma_pago','pdf', 'cons_mensual', 'dias_consumo', 'adminis_tarjeta_id','fecha_creacion',
    ];

    public function getAdministracion()
    {
        return $this->belongsTo('creditocofrem\administarjetas','adminis_tarjeta_id','id');
    }

    public function getEmpresa()
    {
        return $this->belongsTo('creditocofrem\empresas','empresa_id','id');
    }
}

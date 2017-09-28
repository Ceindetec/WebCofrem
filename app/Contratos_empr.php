<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Contratos_empr extends Model
{

    protected $table = 'contratos_emprs';

    protected $fillable = [
        'nit', 'n_contrato', 'valor_contrato', 'valor_impuesto', 'fecha', 'empresa_id','n_tarjetas','forma de pago','pdf', 'cons_mensual', 'dias_consumo', ''
    ];
}

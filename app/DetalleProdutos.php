<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class DetalleProdutos extends Model
{
    public static $ESTADO_INACTIVO = "I";
    public static $ESTADO_ACTIVO = "A";
    protected $fillable = [
        'numero_tarjeta', 'fecha_cracion', 'monto_inicial', 'contrato_emprs_id', 'user_id', 'estado',
    ];
}

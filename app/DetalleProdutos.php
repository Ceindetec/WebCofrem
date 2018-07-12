<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class DetalleProdutos extends Model implements AuditableContract
{
    use Auditable;
    public static $ESTADO_INACTIVO = "I";
    public static $ESTADO_ACTIVO = "A";
    public static $ESTADO_ANULADO = "N";
    protected $fillable = [
        'numero_tarjeta',
        'fecha_cracion',
        'monto_inicial',
        'contrato_emprs_id',
        'user_id',
        'estado',
        'fecha_activacion',
        'fecha_vencimiento',
        'factura',
    ];

    public function getEstadoAttribute($value)
    {
        $result = "";
        switch ($value) {
            case "A":
                $result = "Activo";
                break;
            case "I":
                $result = "Inactiva";
                break;
            default:
                $result = "Anulado";

        }

        return $result;
    }


    public function getUser()
    {
        return $this->belongsTo('creditocofrem\User', 'user_id', 'id');
    }

    public function getContrato()
    {
        return $this->belongsTo('creditocofrem\Contratos_empr', 'contratos_emprs_id', 'id');
    }

    public function tarjeta()
    {
        return $this->belongsTo(Tarjetas::class, "numero_tarjeta", "numero_tarjeta");
    }


}

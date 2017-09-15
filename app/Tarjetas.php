<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;

class Tarjetas extends Model
{
    public static $numero_tarjeta = "9999999";
    public static $ESTADO_TARJETA_CREADA = "C";
    public static $ESTADO_TARJETA_ACTIVA = "A";
    public static $ESTADO_TARJETA_INACTIVA = "I";
    public static $CODIGO_SERVICIO_REGALO = "R";
    public static $CODIGO_SERVICIO_AFILIADO = "A";
    public static $CODIGO_SERVICIO_BONO = "B";


    public static $TEXT_RESULT_MONTO_SUPERADO = "monto superado";
    public static $TEXT_RESULT_FACTURA_Y_NUMTARJETA_EXISTEN = "ya existe un registro de esta factura para esta tarjeta";

    public static  $TEXT_CREACION_TARJETA_INGRESO_INVENTARIO = "Creación de la tarjeta - ingreso al inventario";
    //
    protected $fillable = [
        'numero_tarjeta', 'tarjeta_codigo', 'cambioclave', 'password','persona_id','estado'
    ];

    public function getTarjetaServicios()
    {
        return $this->hasMany('creditocofrem\TarjetaServicios','numero_tarjeta','numero_tarjeta');
    }
}

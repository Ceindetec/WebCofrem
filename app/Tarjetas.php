<?php

namespace creditocofrem;

use Illuminate\Database\Eloquent\Model;
use creditocofrem\DetalleProdutos;

class Tarjetas extends Model
{
    public static $numero_tarjeta = "9999999";
    public static $ESTADO_TARJETA_CREADA = "C";
    public static $ESTADO_TARJETA_ACTIVA = "A";
    public static $ESTADO_TARJETA_INACTIVA = "I";
    public static $ESTADO_TARJETA_ANULADA = "N";
    public static $CODIGO_SERVICIO_REGALO = "R";
    public static $CODIGO_SERVICIO_AFILIADO = "A";
    public static $CODIGO_SERVICIO_BONO = "B";

    public static $TEXT_RESULT_MONTO_SUPERADO = "monto superado";
    public static $TEXT_RESULT_FACTURA_Y_NUMTARJETA_EXISTEN = "ya existe un registro de esta factura para esta tarjeta";
    public static $TEXT_RESULT_FACTURA_Y_NUMTARJETA_EXISTENE_BLOQUE = "ya existe un registro de esta factura para esta tarjeta ";
    public static $TEXT_RESULT_FACTURA_Y_NUMTARJETA_EXISTENEN_BLOQUE = "ya existen registros de esta factura asosiados a estas tarjetas: ";
    public static $TEXT_CREACION_TARJETA_INGRESO_INVENTARIO = "Creación de la tarjeta - ingreso al inventario";
    public static $TEXT_BLOQUE_TARJETAS_YA_EXITEN = "Las siguientes tarjetas ya están registradas: ";
    public static $TEXT_BLOQUE_TARJETA_YA_EXISTE = "La siguiente tarjeta ya está registrada: ";

    public static $TEXT_SERVICIO_TARJETA_REGALO_SIN_PARAMETRIZACION = "Falta la parametrización del servicio Tarjeta regalo";
    public static $TEXT_SIN_VALOR_PLASTICO = "Falta asignarle un valor al plático de la tarjeta (parametrización)";

    public static $TEXT_DEFAULT_MOTIVO_ACTIVACION_TARJETA = 'Activacion del servicio';

    //
    protected $fillable = [
        'numero_tarjeta', 'cambioclave', 'password','persona_id','estado',
    ];

    public function getTarjetaServicios()
    {
        return $this->hasMany('creditocofrem\TarjetaServicios','numero_tarjeta','numero_tarjeta');
    }

    public function getDetalleProductoRegalo(){
        return $this->hasMany('creditocofrem\DetalleProdutos','numero_tarjeta','numero_tarjeta')->where('factura','<>',null);
    }
}

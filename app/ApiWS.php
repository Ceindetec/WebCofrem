<?php
/**
 * Created by PhpStorm.
 * User: luisp
 * Date: 13/10/2017
 * Time: 10:46 AM
 */

namespace creditocofrem;

class ApiWS{

    /**
     * General de la aplicacion
     */
    public static $TEXT_TRANSACCION_EXITOSA = 'Transacción exitosa';
    public static $TEXT_ERROR_OPERACION = 'Error de operación';
    public static $TEXT_VALIDACION_EXITOSA = 'Validacion exitosa';
    public static $TEXT_TERMINAL_NO_EXISTE = 'Terminal no Existe';
    public static $TEXT_ERROR_EJECUCION = 'Error de ejecución';
    public static $TEXT_PASSWORD_CORRECTO = 'Contraseña Correcta';
    public static $TEXT_PASSWORD_INCORECTO = 'Contraseña Incorrecta';
    public static $TEXT_PASSWORD_DEBE_SER_NUM = 'La contraseña debe ser numérica';

    public static $TEXT_TARJETA_INACTIVA = 'Tarjeta Inactiva';
    public static $TEXT_TARJETA_NO_VALIDA = 'Tarjeta no validad';

    public static $TEXT_DOCUMENTIO_INCORRECTO = 'El número de identificación no corresponde a la tarjeta';

    public static $TEXT_TERMINAL_INACTIVA = 'La terminal está inactiva';
    public static $TEXT_TRANSACCION_INSUFICIENTE = 'Saldo insuficiente';

    public static $CODIGO_CAMBIO_CLAVE = 0;
    public static $CODIGO_TARJETA_INACTIVA = 1;
    public static $CODIGO_TARJETA_NO_VALIDA = 2;
    public static $CODIGO_TERMINAL_NO_EXISTE = 3;
    public static $CODIGO_PASSWORD_INCORECTO = 4;
    public static $CODIGO_DOCUMENTIO_INCORRECTO = 5;
    public static $CODIGO_PASSWORD_DEBE_SER_NUM = 6;
    public static $CODIGO_ERROR_EJECUCION = 7;
    public static $CODIGO_TERMINAL_INACTIVA = 8;
    public static $CODIGO_TRANSACCION_INSUFICIENTE = 9;

    /**
     * Modulo de Test de Comunicacion
     */
    public static $TEXT_COMUNICACION_TEST_EXITOSA = 'Comunicación exitosa';
    public static $TEXT_COMUNICACION_TEST_ERROR = 'Error de comunicación';

    /**
     * modulo de GetServicios
     */
    public static $TEXT_CAMBIO_CLAVE = 'Debe realizar cambio de clave';







}
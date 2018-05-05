<?php

namespace creditocofrem\Http\Controllers;


use creditocofrem\DetalleProdutos;
use creditocofrem\Personas;
use creditocofrem\Tarjetas;
use creditocofrem\TarjetaServicios;
use creditocofrem\Terminales;
use Facades\creditocofrem\Encript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use Facades\creditocofrem\AESCrypt;
use creditocofrem\ApiWS;
use creditocofrem\Transaccion;
use creditocofrem\DetalleTransaccion;
use creditocofrem\HEstadoTransaccion;
use creditocofrem\Htarjetas;
use Carbon\Carbon;
use creditocofrem\Duplicado;
use creditocofrem\DuplicadoProductos;

class WebApiController extends Controller
{
    //
    /**
     * valida que las terminales esten cominicadas
     * @param Request $request
     * - codigo
     * @return mixed
     */
    public function comunicacion(Request $request)
    {
        if ($request->codigo == '02') {
            $result['estado'] = true;
            $result['mensaje'] = ApiWS::$TEXT_COMUNICACION_TEST_EXITOSA;
        } else {
            $result['estado'] = false;
            $result['mensaje'] = ApiWS::$TEXT_COMUNICACION_TEST_ERROR;
        }
        $resultFinal['resultado'] = $result;
        return ($resultFinal);
    }

    /**
     * valida la terminal
     * @param Request $request
     * -codigo
     * @return array
     */
    public function validarTerminal(Request $request)
    {
        $result = [];
        try {
            $terminal = Terminales::where('codigo', $request->codigo)->first();
            if (count($terminal) > 0) {
                $sucursal = $terminal->getSucursal;
                $establecimiento = $sucursal->getEstablecimiento;
                $data['nit'] = $establecimiento->nit;
                $data['razon_social'] = $establecimiento->razon_social;
                $data['sucursal_id'] = $sucursal->id;
                $data['nombre_sucursal'] = $sucursal->nombre;
                $data['ciudad'] = $sucursal->getMunicipio->descripcion;
                $data['direccion'] = $sucursal->direccion;
                $data['estado_sucursal'] = $sucursal->estado;
                $data['estado_terminal'] = $terminal->estado;
                $data['codigo_terminal'] = $terminal->codigo;
                $data['ip1'] = "190.159.199.209";
                $result['estado'] = TRUE;
                $result['mensaje'] = ApiWS::$TEXT_VALIDACION_EXITOSA;
                $result['data'] = $data;
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_TERMINAL_NO_EXISTE;
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = ApiWS::$TEXT_ERROR_OPERACION;
        }
        return ['resultado' => $result];
    }

    /**
     * asigana los datos faltantes de la terminal
     * @param Request $request
     * -codigo
     * -uuid
     * -mac
     * -imei
     * @return array
     */
    public function asignaID(Request $request)
    {
        $result = [];
        DB::beginTransaction();
        try {
            $terminal = Terminales::where('codigo', $request->codigo)->first();
            if (count($terminal) > 0) {
                $terminal->imei = $request->imei;
                $terminal->uid = $request->uuid;
                $terminal->mac = $request->mac;
                $terminal->save();
                $result['estado'] = TRUE;
                $result['mensaje'] = ApiWS::$TEXT_VALIDACION_EXITOSA;
                DB::commit();
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_TERMINAL_NO_EXISTE;
                DB::rollBack();
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = ApiWS::$TEXT_ERROR_OPERACION;
            DB::rollBack();
        }
        return ['resultado' => $result];
    }

    /**
     * permite validar la clave de la terminal, esto para caso de configuracion del dispositivo
     * @param Request $request
     * -codigo
     * -password
     * @return array
     * -retirna array con codigos de error, y mensaje
     */
    public function validarClaveTerminal(Request $request)
    {
        $result = [];
        try {
            $teminal = Terminales::where('codigo', $request->codigo)->first();
            if (count($teminal) > 0) {
                $contrasena = Encript::decryption($teminal->password);
                if ($request->password == $contrasena) {
                    $result['estado'] = TRUE;
                    $result['mensaje'] = ApiWS::$TEXT_PASSWORD_CORRECTO;
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = ApiWS::$TEXT_PASSWORD_INCORECTO;
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_TERMINAL_NO_EXISTE;
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = ApiWS::$TEXT_ERROR_EJECUCION;
            $result['codigo'] = ApiWS::$CODIGO_ERROR_EJECUCION;
        }
        return ['resultado' => $result];
    }

    /**
     * valida que la clave de la sucursal, esot para casos administrativos (anulacion etc)
     * @param Request $request
     * -codigo
     * -password
     * @return array
     */
    public function validarClaveSucursal(Request $request)
    {
        $result = [];
        try {
            $teminal = Terminales::where('codigo', $request->codigo)->first();
            if (count($teminal) > 0) {
                $sucursal = $teminal->getSucursal;
                $contrasena = Encript::decryption($sucursal->password);
                if ($request->password == $contrasena) {
                    $result['estado'] = TRUE;
                    $result['mensaje'] = ApiWS::$TEXT_PASSWORD_CORRECTO;
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = ApiWS::$TEXT_PASSWORD_INCORECTO;
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_TERMINAL_NO_EXISTE;
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = ApiWS::$TEXT_ERROR_EJECUCION;
            $result['codigo'] = ApiWS::$CODIGO_ERROR_EJECUCION;
        }
        return ['resultado' => $result];
    }

    /**
     * Metodo encargado de retornas los servicios activos asociados a una tarjeta
     * @param \Illuminate\Http\Request $request
     * - codigo
     * - numero_tarjeta
     * - identificacion
     *
     * @return array
     */
    public function getServicios(Request $request)
    {
        $result = [];
        try {
            $terminal = Terminales::where('codigo', $request->codigo)->first();
            if (count($terminal) > 0) {
                $tarjeta = Tarjetas::where('numero_tarjeta', $request->numero_tarjeta)->first();
                if (count($tarjeta) > 0) {
                    if ($tarjeta->estado == Tarjetas::$ESTADO_TARJETA_ACTIVA) {
                        if ($tarjeta->cambioclave == 1) {
                            if ($tarjeta->persona_id != NULL) {
                                $persona = Personas::where('identificacion', $request->identificacion)->first();
                                if (count($persona) > 0) {
                                    $result = $this->retornaServicios($tarjeta, $request);
                                } else {
                                    $result['estado'] = FALSE;
                                    $result['mensaje'] = ApiWS::$TEXT_DOCUMENTIO_INCORRECTO;
                                    $result['codigo'] = ApiWS::$CODIGO_DOCUMENTIO_INCORRECTO;
                                }
                            } else {
                                $result = $this->retornaServicios($tarjeta, $request);
                            }
                        } else {
                            $result['estado'] = FALSE;
                            $result['mensaje'] = ApiWS::$TEXT_CAMBIO_CLAVE;
                            $result['codigo'] = ApiWS::$CODIGO_CAMBIO_CLAVE;
                        }
                    } else {
                        $result['estado'] = FALSE;
                        $result['mensaje'] = ApiWS::$TEXT_TARJETA_INACTIVA;
                        $result['codigo'] = ApiWS::$CODIGO_TARJETA_INACTIVA;
                    }
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = ApiWS::$TEXT_TARJETA_NO_VALIDA;
                    $result['codigo'] = ApiWS::$CODIGO_TARJETA_NO_VALIDA;
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_TERMINAL_NO_EXISTE;
                $result['codigo'] = ApiWS::$CODIGO_TERMINAL_NO_EXISTE;
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = $exception->getMessage();//ApiWS::$TEXT_ERROR_EJECUCION;
            $result['codigo'] = ApiWS::$CODIGO_ERROR_EJECUCION;

        }
        return ['resultado' => $result];
    }

    /**
     * retorna los servicios de la tarjeta
     * @param $tarjeta
     * @param $request
     * @return array
     */
    private function retornaServicios($tarjeta, $request)
    {
        $result = [];
        /*$servicios = TarjetaServicios::join('servicios', 'tarjeta_servicios.servicio_codigo', 'servicios.codigo')
            ->where('numero_tarjeta', $tarjeta->numero_tarjeta)
            ->where('estado', TarjetaServicios::$ESTADO_ACTIVO)
            ->select('servicios.descripcion', 'servicios.codigo as codigo_servicio')
            ->get();
        $result['estado'] = TRUE;
        $result['mensaje'] = ApiWS::$TEXT_TRANSACCION_EXITOSA;
        $result['servicios'] = $servicios;*/


        $numero_tarjeta = $request->numero_tarjeta;
        $resultado = array();
        $listado = [];
        array_push($listado, $numero_tarjeta);
        $respuesta = $this->consultarDuplicados($numero_tarjeta, $listado);
        if ($respuesta != null)
            $listado = $respuesta;
        $detalles = DetalleProdutos::wherein('numero_tarjeta', $listado)
            ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
            ->get();
        foreach ($detalles as $detalle) {
            $gasto = 0;
            $listadod = [];
            array_push($listadod, $detalle->id);
            $respuesta = $this->consultarDuplicadoProductos($detalle->id, $listadod);
            if ($respuesta != null)
                $listadod = $respuesta;
            $dtransacciones = DetalleTransaccion::wherein('detalle_producto_id', $listadod)->get();//$detalle->id
            foreach ($dtransacciones as $dtransaccione) {
                $htransaccion = DB::table('h_estado_transacciones')->where('transaccion_id', $dtransaccione->transaccion_id)->orderBy('id', 'desc')->first();
                if ($htransaccion->estado == HEstadoTransaccion::$ESTADO_ACTIVO)
                    $gasto += $dtransaccione->valor;
            }
            $sobrante = $detalle->monto_inicial - $gasto;
            $sobrante = number_format($sobrante, 2, ',', '.');
            if ($detalle->contrato_emprs_id == null) {
                $codigo_servicio = Tarjetas::$CODIGO_SERVICIO_REGALO;
                $servicio = 'Regalo';
            } else {
                $codigo_servicio = Tarjetas::$CODIGO_SERVICIO_BONO;
                $servicio = 'Bono empresarial';
            }
            $resultado[] = array(
                'codigo_servicio' => $codigo_servicio,
                'descripcion' => $servicio,
                'saldo' => $sobrante,
            );
        }

        $result['estado'] = TRUE;
        $result['mensaje'] = ApiWS::$TEXT_TRANSACCION_EXITOSA;
        $result['servicios'] = $resultado;
        return $result;
    }

    /**
     * metodo usado para validar la clave de la tarjeta
     * @param Request $request
     * -numero_tarjeta
     * -password
     * @return array
     */
    public function consultarClaveTarjeta(Request $request)
    {
        $result = [];
        try {
            $tarjeta = Tarjetas::where('numero_tarjeta', $request->numero_tarjeta)->first();
            if (count($tarjeta) > 0) {
                if ($tarjeta->estado == Tarjetas::$ESTADO_TARJETA_ACTIVA) {
                    if ($tarjeta->persona_id != NULL) {
                        $persona = Personas::where('identificacion', $request->identificacion)->first();
                        if (count($persona) > 0) {
                            if ($request->password == Encript::decryption($tarjeta->password)) {
                                $result['estado'] = TRUE;
                                $result['mensaje'] = ApiWS::$TEXT_VALIDACION_EXITOSA;
                            } else {
                                $result['estado'] = FALSE;
                                $result['mensaje'] = ApiWS::$TEXT_PASSWORD_INCORECTO;
                                $result['codigo'] = ApiWS::$CODIGO_PASSWORD_INCORECTO;
                            }
                        } else {
                            $result['estado'] = FALSE;
                            $result['mensaje'] = ApiWS::$TEXT_DOCUMENTIO_INCORRECTO;
                            $result['codigo'] = ApiWS::$CODIGO_DOCUMENTIO_INCORRECTO;
                        }
                    } else {
                        if ($request->password == Encript::decryption($tarjeta->password)) {
                            $result['estado'] = TRUE;
                            $result['mensaje'] = ApiWS::$TEXT_VALIDACION_EXITOSA;
                        } else {
                            $result['estado'] = FALSE;
                            $result['mensaje'] = ApiWS::$TEXT_PASSWORD_INCORECTO;
                            $result['codigo'] = ApiWS::$CODIGO_PASSWORD_INCORECTO;
                        }
                    }
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = ApiWS::$TEXT_TARJETA_INACTIVA;
                    $result['codigo'] = ApiWS::$CODIGO_TARJETA_INACTIVA;
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_TARJETA_NO_VALIDA;
                $result['codigo'] = ApiWS::$CODIGO_TARJETA_NO_VALIDA;
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = ApiWS::$TEXT_ERROR_EJECUCION;
            $result['codigo'] = ApiWS::$CODIGO_ERROR_EJECUCION;
        }
        return ['resultado' => $result];
    }

    /**
     * permite actualizar el la contraseña de la tarjeta
     * @param Request $request
     * -numero_tarjeta
     * -password
     * -nuevo_password
     * @return array
     */
    public function actulizarClaveTarjeta(Request $request)
    {
        $result = [];
        try {
            $tarjeta = Tarjetas::where('numero_tarjeta', $request->numero_tarjeta)->first();
            if (count($tarjeta) > 0) {
                if ($tarjeta->estado == Tarjetas::$ESTADO_TARJETA_ACTIVA) {
                    if ($request->password == Encript::decryption($tarjeta->password)) {
                        if (is_numeric($request->nuevo_password)) {
                            $tarjeta->password = Encript::encryption(trim($request->nuevo_password));
                            $tarjeta->cambioclave = 1;
                            $tarjeta->save();
                            $result['estado'] = TRUE;
                            $result['mensaje'] = ApiWS::$TEXT_TRANSACCION_EXITOSA;
                        } else {
                            $result['estado'] = FALSE;
                            $result['mensaje'] = ApiWS::$TEXT_PASSWORD_DEBE_SER_NUM;
                            $result['codigo'] = ApiWS::$CODIGO_PASSWORD_DEBE_SER_NUM;
                        }
                    } else {
                        $result['estado'] = FALSE;
                        $result['mensaje'] = ApiWS::$TEXT_PASSWORD_INCORECTO;
                        $result['codigo'] = ApiWS::$CODIGO_PASSWORD_INCORECTO;
                    }
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = ApiWS::$TEXT_TARJETA_INACTIVA;
                    $result['codigo'] = ApiWS::$CODIGO_TARJETA_INACTIVA;
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_TARJETA_NO_VALIDA;
                $result['codigo'] = ApiWS::$CODIGO_TARJETA_NO_VALIDA;
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = ApiWS::$TEXT_ERROR_EJECUCION;
            $result['codigo'] = ApiWS::$CODIGO_ERROR_EJECUCION;
        }
        return ['resultado' => $result];
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * -codigo
     * -numero_tarjeta
     * -valor
     * -password
     * -servicios
     *
     * @return array
     */
    public function consumo(Request $request)
    {
        $result = [];
        \DB::beginTransaction();
        try {
            $valorConsumir = $request->valor;
            $terminal = Terminales::where('codigo', $request->codigo)->first();
            if (count($terminal) > 0) {
                if ($terminal->estado == Terminales::$TERMINAL_ESTADO_ACTIVA) {
                    $tarjeta = Tarjetas::where('numero_tarjeta', $request->numero_tarjeta)->first();
                    if ($tarjeta != NULL) {
                        if ($tarjeta->estado == Tarjetas::$ESTADO_TARJETA_ACTIVA) {
                            if ($request->password == Encript::decryption($tarjeta->password)) {
                                $servicios = explode(',', $request->servicios);
                                $newTransaccion = new Transaccion();
                                $ultimaTransaccion = Transaccion::select([\DB::raw('max(numero_transaccion) as numero')])->first();
                                if ($ultimaTransaccion->numero == null) {
                                    $numero = '0000000001';
                                } else {
                                    $numero = intval($ultimaTransaccion->numero);
                                    $numero++;
                                    $largo = strlen($numero);
                                    for ($i = 0; $i < (10 - $largo); $i++) {
                                        $numero = "0" . $numero;
                                    }
                                }
                                $newTransaccion->numero_transaccion = $numero;
                                $newTransaccion->numero_tarjeta = $request->numero_tarjeta;
                                $newTransaccion->codigo_terminal = $request->codigo;
                                $newTransaccion->tipo = Transaccion::$TIPO_CONSUMO;
                                $newTransaccion->fecha = Carbon::now();
                                $newTransaccion->sucursal_id = $terminal->getSucursal->id;
                                $newTransaccion->save();
                                $newEstadoTransacion = new HEstadoTransaccion();
                                $newEstadoTransacion->transaccion_id = $newTransaccion->id;
                                $newEstadoTransacion->estado = HEstadoTransaccion::$ESTADO_ACTIVO;
                                $newEstadoTransacion->fecha = Carbon::now();
                                $newEstadoTransacion->save();
                                foreach ($servicios as $servicio) {
                                    if ($valorConsumir > 0) {
                                        if ($servicio == Tarjetas::$CODIGO_SERVICIO_REGALO) {
                                            $detalleProdutos = DetalleProdutos::where('numero_tarjeta', $request->numero_tarjeta)
                                                ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
                                                ->where('factura', '<>', NULL)
                                                ->whereDate('fecha_vencimiento', '>', Carbon::now())
                                                ->orderBy('fecha_vencimiento', 'asc')
                                                ->get();
                                            foreach ($detalleProdutos as $detalleProduto) {
                                                $transaciones = Transaccion::join('h_estado_transacciones', 'transacciones.id', 'h_estado_transacciones.transaccion_id')
                                                    ->join('detalle_transacciones', 'transacciones.id', 'detalle_transacciones.transaccion_id')
                                                    ->where('transacciones.numero_tarjeta', $request->numero_tarjeta)
                                                    ->where('detalle_transacciones.detalle_producto_id', $detalleProduto->id)
                                                    ->where('h_estado_transacciones.estado', 'A')
                                                    ->groupBy('transacciones.numero_tarjeta')
                                                    ->select(\DB::raw('SUM(valor) as total'))
                                                    ->get();
                                                $porconsumir = $detalleProduto->monto_inicial - $transaciones[0]->total;
                                                if ($porconsumir > 0) {
                                                    $consumir = $porconsumir - $valorConsumir;
                                                    if ($consumir < 0) {
                                                        $newDetalle = new DetalleTransaccion();
                                                        $newDetalle->transaccion_id = $newTransaccion->id;
                                                        $newDetalle->detalle_producto_id = $detalleProduto->id;
                                                        $newDetalle->valor = $porconsumir;
                                                        $newDetalle->descripcion = DetalleTransaccion::$DESCRIPCION_CONSUMO;
                                                        $newDetalle->save();
                                                        $detalleProduto->estado = DetalleProdutos::$ESTADO_INACTIVO;
                                                        $detalleProduto->save();
                                                        $historico = new Htarjetas();
                                                        $historico->estado = 'I';
                                                        $historico->motivo = 'Consumido el servicio';
                                                        $historico->fecha = Carbon::now();
                                                        $historico->user_id = '1';
                                                        $historico->tarjetas_id = $tarjeta->id;
                                                        $historico->servicio_codigo = $servicio;
                                                        $historico->save();
                                                        $valorConsumir = $valorConsumir - $porconsumir;
                                                    } else {
                                                        $newDetalle = new DetalleTransaccion();
                                                        $newDetalle->transaccion_id = $newTransaccion->id;
                                                        $newDetalle->detalle_producto_id = $detalleProduto->id;
                                                        $newDetalle->valor = $valorConsumir;
                                                        $newDetalle->descripcion = DetalleTransaccion::$DESCRIPCION_CONSUMO;
                                                        $newDetalle->save();
                                                        $valorConsumir = 0;
                                                    }
                                                }
                                            }
                                        } else if ($servicio == Tarjetas::$CODIGO_SERVICIO_BONO) {
                                            $detalleProdutos = DetalleProdutos::where('numero_tarjeta', $request->numero_tarjeta)
                                                ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
                                                ->where('CONTRATO_EMPRS_ID', '<>', NULL)
                                                ->whereDate('fecha_vencimiento', '>', Carbon::now())
                                                ->orderBy('fecha_vencimiento', 'asc')
                                                ->get();
                                            foreach ($detalleProdutos as $detalleProduto) {
                                                $transaciones = Transaccion::join('h_estado_transacciones', 'transacciones.id', 'h_estado_transacciones.transaccion_id')
                                                    ->join('detalle_transacciones', 'transacciones.id', 'detalle_transacciones.transaccion_id')
                                                    ->where('transacciones.numero_tarjeta', $request->numero_tarjeta)
                                                    ->where('detalle_transacciones.detalle_producto_id', $detalleProduto->id)
                                                    ->where('h_estado_transacciones.estado', 'A')
                                                    ->groupBy('transacciones.numero_tarjeta')
                                                    ->select(\DB::raw('SUM(valor) as total'))
                                                    ->get();
                                                $porconsumir = $detalleProduto->monto_inicial - $transaciones[0]->total;
                                                if ($porconsumir > 0) {
                                                    $consumir = $porconsumir - $valorConsumir;
                                                    if ($consumir < 0) {
                                                        $newDetalle = new DetalleTransaccion();
                                                        $newDetalle->transaccion_id = $newTransaccion->id;
                                                        $newDetalle->detalle_producto_id = $detalleProduto->id;
                                                        $newDetalle->valor = $porconsumir;
                                                        $newDetalle->descripcion = DetalleTransaccion::$DESCRIPCION_CONSUMO;
                                                        $newDetalle->save();
                                                        $detalleProduto->estado = DetalleProdutos::$ESTADO_INACTIVO;
                                                        $detalleProduto->save();
                                                        $historico = new Htarjetas();
                                                        $historico->estado = 'I';
                                                        $historico->motivo = 'Consumido el servicio';
                                                        $historico->fecha = Carbon::now();
                                                        $historico->user_id = '1';
                                                        $historico->tarjetas_id = $tarjeta->id;
                                                        $historico->servicio_codigo = $servicio;
                                                        $historico->save();
                                                        $valorConsumir = $valorConsumir - $porconsumir;
                                                    } else {
                                                        $newDetalle = new DetalleTransaccion();
                                                        $newDetalle->transaccion_id = $newTransaccion->id;
                                                        $newDetalle->detalle_producto_id = $detalleProduto->id;
                                                        $newDetalle->valor = $valorConsumir;
                                                        $newDetalle->descripcion = DetalleTransaccion::$DESCRIPCION_CONSUMO;
                                                        $newDetalle->save();
                                                        $valorConsumir = 0;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                if ($valorConsumir == 0) {
                                    \DB::commit();
                                    $dt = Carbon::now();
                                    $result['estado'] = TRUE;
                                    $result['numero_transaccion'] = $newTransaccion->numero_transaccion;
                                    $result['fecha'] = $dt->toDateString();
                                    $result['hora'] = $dt->toTimeString();
                                    $result['codigoTerminal'] = $terminal->codigo;
                                    $result['numeroTarjeta'] = $tarjeta->numero_tarjeta;
                                    $result['tipoTransaccion'] = Transaccion::$TIPO_CONSUMO;
                                    $result['valor'] = $request->valor;
                                    $result['detalleServicio'] = $request->servicios;
                                    if ($tarjeta->persona_id != NULL) {
                                        $persona = Personas::find($tarjeta->persona_id);
                                        $result['cedula'] = $persona->identificacion;
                                        $result['nombres'] = $persona->nombres . " " . $persona->apellidos;
                                    } else {
                                        $result['nombres'] = "";
                                        $result['cedula'] = "";
                                    }
                                    $result['mensaje'] = 'Transaccion exitosa';
                                } else {
                                    \DB::rollback();
                                    $result['estado'] = FALSE;
                                    $result['mensaje'] = ApiWS::$TEXT_TRANSACCION_INSUFICIENTE;
                                    $result['codigo'] = ApiWS::$CODIGO_TRANSACCION_INSUFICIENTE;
                                }
                            } else {
                                $result['estado'] = FALSE;
                                $result['mensaje'] = ApiWS::$TEXT_PASSWORD_INCORECTO;
                                $result['codigo'] = ApiWS::$CODIGO_PASSWORD_INCORECTO;
                            }
                        } else {
                            $result['estado'] = FALSE;
                            $result['mensaje'] = ApiWS::$TEXT_TARJETA_INACTIVA;
                            $result['codigo'] = ApiWS::$CODIGO_TARJETA_INACTIVA;
                        }
                    } else {
                        $result['estado'] = FALSE;
                        $result['mensaje'] = ApiWS::$TEXT_TARJETA_NO_VALIDA;
                        $result['codigo'] = ApiWS::$CODIGO_TARJETA_NO_VALIDA;
                    }
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = ApiWS::$TEXT_TERMINAL_INACTIVA;
                    $result['codigo'] = ApiWS::$CODIGO_TERMINAL_INACTIVA;
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_TERMINAL_NO_EXISTE;
                $result['codigo'] = ApiWS::$CODIGO_TERMINAL_NO_EXISTE;
            }
        } catch (\Exception $exception) {
            \DB::rollback();
            $result['estado'] = FALSE;
            $result['mensaje'] = ApiWS::$TEXT_ERROR_EJECUCION;
            $result['codigo'] = ApiWS::$CODIGO_ERROR_EJECUCION;
        }
        return ['resultado' => $result];
    }

    /**
     * metodo que permite traer el saldo de la tarjeta para los servicios que tenga disponible
     * @param Request $request
     * -codigo
     * -numero_tarjeta
     * -password
     * @return array
     */
    public function saldoTarjeta(Request $request)
    {
        $result = [];
        try {
            $terminal = Terminales::where('codigo', $request->codigo)->first();
            if ($terminal != NULL) {
                if ($terminal->estado == Terminales::$TERMINAL_ESTADO_ACTIVA) {
                    $tarjeta = Tarjetas::where('numero_tarjeta', $request->numero_tarjeta)->first();
                    if ($tarjeta != NULL) {
                        if ($tarjeta->estado == Tarjetas::$ESTADO_TARJETA_ACTIVA) {
                            if ($request->password == Encript::decryption($tarjeta->password)) {
                                $numero_tarjeta = $request->numero_tarjeta;
                                $resultado = array();
                                $listado = [];
                                array_push($listado, $numero_tarjeta);
                                $respuesta = $this->consultarDuplicados($numero_tarjeta, $listado);
                                if ($respuesta != null)
                                    $listado = $respuesta;
                                $detalles = DetalleProdutos::wherein('numero_tarjeta', $listado)
                                    ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
                                    ->get();
                                foreach ($detalles as $detalle) {
                                    $gasto = 0;
                                    $listadod = [];
                                    array_push($listadod, $detalle->id);
                                    $respuesta = $this->consultarDuplicadoProductos($detalle->id, $listadod);
                                    if ($respuesta != null)
                                        $listadod = $respuesta;
                                    $dtransacciones = DetalleTransaccion::wherein('detalle_producto_id', $listadod)->get();//$detalle->id
                                    foreach ($dtransacciones as $dtransaccione) {
                                        $htransaccion = DB::table('h_estado_transacciones')->where('transaccion_id', $dtransaccione->transaccion_id)->orderBy('id', 'desc')->first();
                                        if ($htransaccion->estado == HEstadoTransaccion::$ESTADO_ACTIVO)
                                            $gasto += $dtransaccione->valor;
                                    }
                                    $sobrante = $detalle->monto_inicial - $gasto;
                                    $sobrante = number_format($sobrante, 2, ',', '.');
                                    if ($detalle->contrato_emprs_id == null) {
                                        $codigo_servicio = Tarjetas::$CODIGO_SERVICIO_REGALO;
                                        $servicio = 'Regalo';
                                    } else {
                                        $codigo_servicio = Tarjetas::$CODIGO_SERVICIO_BONO;
                                        $servicio = 'Bono empresarial';
                                    }
                                    $resultado[] = array(
                                        'codigo_servicio' => $codigo_servicio,
                                        'servicio' => $servicio,
                                        'saldo' => $sobrante,
                                    );
                                }
                                $result['estado'] = TRUE;
                                $result['Saldos'] = $resultado;
                            } else {
                                $result['estado'] = FALSE;
                                $result['mensaje'] = ApiWS::$TEXT_PASSWORD_INCORECTO;
                                $result['codigo'] = ApiWS::$CODIGO_PASSWORD_INCORECTO;
                            }
                        } else {
                            $result['estado'] = FALSE;
                            $result['mensaje'] = ApiWS::$TEXT_TARJETA_INACTIVA;
                            $result['codigo'] = ApiWS::$CODIGO_TARJETA_INACTIVA;
                        }
                    } else {
                        $result['estado'] = FALSE;
                        $result['mensaje'] = ApiWS::$TEXT_TARJETA_NO_VALIDA;
                        $result['codigo'] = ApiWS::$CODIGO_TARJETA_NO_VALIDA;
                    }
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = ApiWS::$TEXT_TERMINAL_INACTIVA;
                    $result['codigo'] = ApiWS::$CODIGO_TERMINAL_INACTIVA;
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_TERMINAL_NO_EXISTE;
                $result['codigo'] = ApiWS::$CODIGO_TERMINAL_NO_EXISTE;
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = ApiWS::$TEXT_ERROR_EJECUCION;
            $result['codigo'] = ApiWS::$CODIGO_ERROR_EJECUCION;
        }

        return ['resultado' => $result];
    }

    /**
     * metodo para anular una transaccion
     * @param Request $request
     * -codigo
     * -numero_tarjeta
     * -numero_transaccion
     * -identificacion
     * -password
     * @return array
     */
    public function anulacion(Request $request)
    {
        $result = [];
        try {
            $terminal = Terminales::where('codigo', $request->codigo)->first();
            if ($terminal != NULL) {
                if ($terminal->estado == Terminales::$ESTADO_TERMINAL_ACTIVA) {
                    $tarjeta = Tarjetas::where('numero_tarjeta', $request->numero_tarjeta)->first();
                    if ($tarjeta != NULL) {
                        if ($tarjeta->estado == Tarjetas::$ESTADO_TARJETA_ACTIVA) {
                            if ($request->password == Encript::decryption($tarjeta->password)) {
                                if ($tarjeta->persona_id != NULL) {
                                    $persona = Personas::find($tarjeta->persona_id);
                                    if ($persona->documento == $request->documento) {
                                        $nombre = $persona->nombres . " " . $persona->apellidos;
                                        $result = $this->anulaTransaccion($request, $terminal->codigo, $tarjeta->numero_tarjeta, $persona->identificacion, $nombre);
                                    } else {
                                        $result['estado'] = FALSE;
                                        $result['mensaje'] = ApiWS::$TEXT_DOCUMENTIO_INCORRECTO;
                                        $result['codigo'] = ApiWS::$CODIGO_DOCUMENTIO_INCORRECTO;
                                    }
                                } else {
                                    $result = $this->anulaTransaccion($request, $terminal->codigo, $tarjeta->numero_tarjeta, "", "");
                                }
                            } else {
                                $result['estado'] = FALSE;
                                $result['mensaje'] = ApiWS::$TEXT_PASSWORD_INCORECTO;
                                $result['codigo'] = ApiWS::$CODIGO_PASSWORD_INCORECTO;
                            }
                        } else {
                            $result['estado'] = FALSE;
                            $result['mensaje'] = ApiWS::$TEXT_TARJETA_INACTIVA;
                            $result['codigo'] = ApiWS::$CODIGO_TARJETA_INACTIVA;
                        }
                    } else {
                        $result['estado'] = FALSE;
                        $result['mensaje'] = ApiWS::$TEXT_TARJETA_NO_VALIDA;
                        $result['codigo'] = ApiWS::$CODIGO_TARJETA_NO_VALIDA;
                    }
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = ApiWS::$TEXT_TERMINAL_INACTIVA;
                    $result['codigo'] = ApiWS::$CODIGO_TERMINAL_INACTIVA;
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_TERMINAL_NO_EXISTE;
                $result['codigo'] = ApiWS::$CODIGO_TERMINAL_NO_EXISTE;
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = ApiWS::$TEXT_ERROR_EJECUCION;
            $result['codigo'] = ApiWS::$CODIGO_ERROR_EJECUCION;
        }
        return ['resultado' => $result];
    }

    /**
     * metodo complementrario a la anulacion
     * @param $request
     * @return array
     */
    private function anulaTransaccion($request, $codigo, $numero_tarjeta, $cedula, $nombres)
    {
        $result = [];
        try {
            $transaccion = Transaccion::where('numero_transaccion', $request->numero_transaccion)->first();
            if ($transaccion != NULL) {
                if ($transaccion->codigo_terminal == $request->codigo) {
                    $fecha_hoy = Carbon::now()->format('Y-m-d');
                    if (Carbon::createFromFormat('Y-m-d H:i:s', $transaccion->fecha)->toDateString() == $fecha_hoy) {
                        $hestado = new HEstadoTransaccion();
                        $hestado->transaccion_id = $transaccion->id;
                        $hestado->estado = HEstadoTransaccion::$ESTADO_INACTIVO;
                        $hestado->fecha = Carbon::now();
                        $hestado->save();
                        $dt = Carbon::now();
                        $result['estado'] = TRUE;
                        $result['numero_transaccion'] = $transaccion->numero_transaccion;
                        $result['fecha'] = $dt->toDateString();
                        $result['hora'] = $dt->toTimeString();
                        $result['codigoTerminal'] = $codigo;
                        $result['numeroTarjeta'] = $numero_tarjeta;
                        $result['tipoTransaccion'] = Transaccion::$TIPO_CONSUMO;
                        $consumido = DetalleTransaccion::where('transaccion_id', $transaccion->id)
                            ->select([DB::raw('SUM(valor) as total')])
                            ->groupBy('transaccion_id')->first();
                        $result['valor'] = $consumido->total;// ----> esta dalo lo queme se nesecita el valor de la transaccion
                        $result['detalleServicio'] = "R"; /// ---->> aun no se necesita para nasda pero nose si mas adelante si
                        $result['nombres'] = $nombres;
                        $result['cedula'] = $cedula;
                        $result['mensaje'] = 'Transaccion anulada';
                    } else {
                        $result['estado'] = FALSE;
                        $result['mensaje'] = ApiWS::$TEXT_FECHA_INVALIDA;
                        $result['codigo'] = ApiWS::$CODIGO_FECHA_INVALIDA;
                    }
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = ApiWS::$TEXT_NUMERO_TRANSACCION_NO_CORRESPONDE;
                    $result['codigo'] = ApiWS::$CODIGO_NUMERO_TRANSACCION_NO_CORRESPONDE;
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_NUMERO_TRANSACCION_INVALIDO;
                $result['codigo'] = ApiWS::$CODIGO_NUMERO_TRANSACCION_INVALIDO;
            }
        } catch (\Exception $exception) {
            $result = [];
            $result['estado'] = FALSE;
            $result['mensaje'] = ApiWS::$TEXT_ERROR_EJECUCION;
            $result['codigo'] = ApiWS::$CODIGO_ERROR_EJECUCION;
        }
        return $result;
    }


    /*
     * Funcion RECURSIVA que retorna listado DE productos DUPLICADO
     * la lista contiene los numero de tarjeta asociados
     */
    private function consultarDuplicadoProductos($numproducto, $listado)
    {
        //buscar si el producto tiene duppicado, si SI, agregar al array listado.
        $duplicado = DuplicadoProductos::where('newproducto', $numproducto)->first();
        if ($duplicado != null) {
            array_push($listado, $duplicado->oldproducto);
            $resultado = $this->consultarDuplicados($duplicado->oldproducto, $listado);
            if ($resultado != null)
                $listado = $resultado;
            return $listado;
        } else
            return null;
    }

    private function consultarDuplicados($numtarjeta, $listado)
    {
        //buscar si la tarjeta tiene duppicado, si SI, agregar al array listado.
        $duplicado = Duplicado::where('newtarjeta', $numtarjeta)->first();
        if ($duplicado != null) {
            array_push($listado, $duplicado->oldtarjeta);
            $resultado = $this->consultarDuplicados($duplicado->oldtarjeta, $listado);
            if ($resultado != null)
                $listado = $resultado;
            return $listado;
        } else
            return null;
    }


}

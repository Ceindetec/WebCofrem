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
                $data['ip1'] = "192.168.0.20";
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
        $servicios = TarjetaServicios::join('servicios', 'tarjeta_servicios.servicio_codigo', 'servicios.codigo')
            ->where('numero_tarjeta', $tarjeta->numero_tarjeta)
            ->where('estado', TarjetaServicios::$ESTADO_ACTIVO)
            ->select('servicios.descripcion', 'servicios.codigo')
            ->get();
        $result['estado'] = TRUE;
        $result['mensaje'] = ApiWS::$TEXT_TRANSACCION_EXITOSA;
        $result['servicios'] = $servicios;
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


    public function consumo(Request $request)
    {
        $result = [];
        try {
            $terminal = Terminales::where('codigo', $request->codigo)->first();
            if (count($terminal) > 0) {
                if ($terminal->estado == Terminales::$TERMINAL_ESTADO_ACTIVA) {
                    $tarjeta = Tarjetas::where('numero_tarjeta', $request->numero_tarjeta)->first();
                    if ($tarjeta != NULL) {
                        if ($tarjeta->estado == Tarjetas::$ESTADO_TARJETA_ACTIVA) {
                            if ($request->password == Encript::decryption($tarjeta->password)) {
                                $servicios = explode(',', $request->servicios);
                                foreach ($servicios as $servicio) {
                                    if ($servicio == Tarjetas::$CODIGO_SERVICIO_REGALO) {
                                        $detalleProdutos = DetalleProdutos::where('numero_tarjeta', $request->numero_tarjeta)
                                            ->where('estado', DetalleProdutos::$ESTADO_ACTIVO)
                                            ->where('factura', '<>', NULL)
                                            ->orderBy('fecha_vencimiento', 'asc')
                                            ->get();

                                        foreach ($detalleProdutos as $detalleProduto){
                                            $transaciones = Transaccion::join('h_estado_transacciones', 'transacciones.id', 'h_estado_transacciones.transaccion_id')
                                                ->join('detalle_transacciones','transacciones.id','detalle_transacciones.transaccion_id')
                                                ->where('transacciones.numero_tarjeta', $request->numero_tarjeta)
                                                ->where('detalle_transacciones.detalle_producto_id',$detalleProduto->id)
                                                ->where('h_estado_transacciones.estado', 'A')
                                                ->get();
                                        }


                                    }
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
                    $result['estoado'] = FALSE;
                    $result['mensaje'] = ApiWS::$TEXT_TERMINAL_INACTIVA;
                    $result['codigo'] = ApiWS::$CODIGO_TERMINAL_INACTIVA;
                }
            } else {
                $result['estoado'] = FALSE;
                $result['mensaje'] = ApiWS::$TEXT_TERMINAL_NO_EXISTE;
                $result['codigo'] = ApiWS::$CODIGO_TERMINAL_NO_EXISTE;
            }
        } catch (\Exception $exception) {
            $result['estoado'] = FALSE;
            $result['mensaje'] = ApiWS::$TEXT_TERMINAL_NO_EXISTE;
            $result['codigo'] = ApiWS::$CODIGO_TERMINAL_NO_EXISTE . ' ' . $exception->getMessage();
        }
        return ['resultado' => $result];
    }
}

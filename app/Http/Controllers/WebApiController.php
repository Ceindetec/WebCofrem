<?php

namespace creditocofrem\Http\Controllers;


use creditocofrem\Personas;
use creditocofrem\Tarjetas;
use creditocofrem\TarjetaServicios;
use creditocofrem\Terminales;
use Facades\creditocofrem\Encript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Facades\creditocofrem\AESCrypt;
use creditocofrem\ApiWS;

class WebApiController extends Controller
{
    //

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
//                $terminal->imei = $request->imei;
//                $terminal->uid = $request->uuid;
//                $terminal->mac = $request->mac;
//                $request->save();
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

    public function getServicios(Request $request)
    {
        $result = [];
        try {
            $terminal = Terminales::where('codigo', $request->codigo)->first();
            if (count($terminal) > 0) {
                $tarjeta = Tarjetas::where('numero_tarjeta', $request->numero_tarjeta)->first();
                if (count($tarjeta) > 0) {
                    if($tarjeta->estado == Tarjetas::$ESTADO_TARJETA_ACTIVA){
                        if($tarjeta->cambioclave == 1){
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
                        }else{
                            $result['estado']= FALSE;
                            $result['mensaje'] = ApiWS::$TEXT_CAMBIO_CLAVE;
                            $result['codigo'] = ApiWS::$CODIGO_CAMBIO_CLAVE;
                        }
                    }else{
                        $result['estado']= FALSE;
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
            $result['mensaje'] = ApiWS::$TEXT_ERROR_EJECUCION;
            $result['codigo'] = ApiWS::$CODIGO_ERROR_EJECUCION;

        }
        return ['resultado' => $result];
    }


    public function retornaServicios($tarjeta, $request)
    {
        $result = [];
        $servicios = TarjetaServicios::where('numero_tarjeta', $tarjeta->numero_tarjeta)
            ->where('estado', TarjetaServicios::$ESTADO_ACTIVO)
            ->select('servicio_codigo')
            ->get();
        $result['estado'] = TRUE;
        $result['mensaje'] = ApiWS::$TEXT_TRANSACCION_EXITOSA;
        $result['data'] = $servicios;
        return $result;
    }

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
                } else{
                    $result['estado']= FALSE;
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
                } else{
                    $result['estado']= FALSE;
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


}

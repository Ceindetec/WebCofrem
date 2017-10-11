<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\Personas;
use creditocofrem\Tarjetas;
use creditocofrem\TarjetaServicios;
use creditocofrem\Terminales;
use Facades\creditocofrem\Encript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebApiController extends Controller
{
    //

    public function comunicacion(Request $request)
    {
        if ($request->codigo == '02') {
            $result['estado'] = true;
            $result['mensaje'] = 'Comunicacion exitosa';
        } else {
            $result['estado'] = false;
            $result['mensaje'] = 'Error de comunicacion';
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
                $result['mensaje'] = 'Validacion exitosa';
                $result['data'] = $data;
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = 'Terminal no Existe';
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = 'Error de operacion';
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
                $result['mensaje'] = 'Validacion exitosa';
                DB::commit();
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = 'Terminal no Existe';
                DB::rollBack();
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = 'Error de operacion';
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
                    $result['mensaje'] = 'contraseña validad';
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = 'Contraseña invalidad';
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = 'Codigo de terminal invalido';
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = 'Error de ejecucion ' . $exception->getMessage();
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
                    $result['mensaje'] = 'contraseña validad';
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = 'Contraseña invalidad';
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = 'Codigo de terminal invalido';
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = 'Error de ejecucion ' . $exception->getMessage();
        }
        return ['resultado' => $result];
    }

    public function getServicios(Request $request)
    {
        $result = [];
        try {
            $terminal = Terminales::where('codigo', $request->codigo)->first();
            if (count($terminal) > 0) {
                $tarjeta = Tarjetas::where('numero_tarjeta', $request->numero_tarjeta)->where('estado', Tarjetas::$ESTADO_TARJETA_ACTIVA)->first();
                if (count($tarjeta) > 0) {
                    if ($tarjeta->persona_id != NULL) {
                        $persona = Personas::where('identificacion', $request->identificacion)->first();
                        if (count($persona) > 0) {
                            $result = $this->retornaServicios($tarjeta, $request);
                        } else {
                            $result['estado'] = FALSE;
                            $result['mensaje'] = 'El numero de identificacion no corresponde a la tarjeta';
                        }
                    } else {
                        $result = $this->retornaServicios($tarjeta, $request);
                    }
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = 'Tarjeta no validad';
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = 'codigo de terminal invalido';
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = 'Error de ejecucion';
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
        $result['mensaje'] = 'Retornando servicios';
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
                                $result['mensaje'] = 'Datos Correctos';
                            } else {
                                $result['estado'] = FALSE;
                                $result['mensaje'] = 'Clave de la tarjeta invalidad';
                            }
                        } else {
                            $result['estado'] = FALSE;
                            $result['mensaje'] = 'Numero de indentificacion invalido';
                        }
                    } else {
                        if ($request->password == Encript::decryption($tarjeta->password)) {
                            $result['estado'] = TRUE;
                            $result['mensaje'] = 'Datos Correctos';
                        } else {
                            $result['estado'] = FALSE;
                            $result['mensaje'] = 'Clave de la tarjeta invalidad';
                        }
                    }
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = 'Tarjeta inactiva';
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = 'Tarjeta Invalidad';
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = 'Error en la operacion';
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
                            $result['mensaje'] = 'Cambio realizado';
                        } else {
                            $result['estado'] = FALSE;
                            $result['mensaje'] = 'La clave debe ser numerica';
                        }
                    } else {
                        $result['estado'] = FALSE;
                        $result['mensaje'] = 'Clave de la tarjeta invalidad';
                    }
                } else {
                    $result['estado'] = FALSE;
                    $result['mensaje'] = 'Tarjeta inactiva';
                }
            } else {
                $result['estado'] = FALSE;
                $result['mensaje'] = 'Tarjeta Invalidad';
            }
        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = 'Error en la operacion';
        }
        return ['resultado' => $result];
    }
}

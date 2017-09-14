<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\DetalleProdutos;
use creditocofrem\Tarjetas;
use creditocofrem\Htarjetas;
use creditocofrem\TarjetaServicios;
use Facades\creditocofrem\Encript;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TarjetasRegaloController extends Controller {


    /**
     * metodo encargado de mostrar la vista principal para crear las Tarjetas
     * Regalo
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function crearTarjetaRegalo() {
        return view("tarjetas.regalo.individualmente");
    }

    /**
     * Metodo encargado de autocompletar las tarjetas buscadas por el número de
     * tarjeta
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function autoCompleNumTarjeta(Request $request) {

        $tarjetas = Tarjetas::where("numero_tarjeta", "like", "%" . $request->numero_tarjeta . "%")->get();
        if (count($tarjetas) == 0) {
            $data["query"] = "Unit";
            $data["suggestions"] = [];
        } else {
            $arrayTarjetas = [];
            foreach ($tarjetas as $producto) {
                $arrayTarjetas[] = ["value" => $producto->numero_tarjeta,
                                    "data" => $producto->id,];
            }
            $data["suggestions"] = $arrayTarjetas;
            $data["query"] = "Unit";
        }
        return $data;
    }

    /**
     * Metodo para agregar las Tarjetas Retgalo
     *
     * @param Request $request
     *
     * @return array
     */
    public function addTarjetaRegalo(Request $request) {

        //quitamos la mascara de pesos al monto
        $monto = str_replace(".", "", $request->monto);

        //consultar todos los detalles producto que existan relacionados con esta factura para validar el monto valido
        $detalles_producto = DetalleProdutos::where("factura", $request->numero_factura)->get();

        //validamos que no existan registros en detalle_producto relacionando con una factura varias veces a la misma tarjeta
        if ($detalles_producto->where("numero_tarjeta", $request->numero_tarjeta)->count() == 0) {

            //TODO: en este punto es necesario consultar la factura para saber el monto

            // array que simula el modelo de la factura que llegaria del WS
            $factura = ["numero_factura" => "02", "monto" => "200000"];

            $monto_inicial = 0;
            foreach ($detalles_producto as $detalle_producto) {
                $monto_inicial += $detalle_producto->monto_inicial;
            }

            $monto_inicial += $monto;

            //TODO: reemplazar el monto del array por el monto de la factura del WS
            //validamos si los montos que estan en los detalle_producto masl el monto de la tarjeta que se esta tratando de crear es permitido
            if ($monto_inicial < $factura["monto"]) {

                //buscamos si el munero de tarjeta existe en nuestros registros
                $tarjeta = Tarjetas::where("numero_tarjeta", $request->numero_tarjeta)->first();

                if ($tarjeta == NULL) {
                    // transaccion para la creacion de la tarjetas
                    DB::beginTransaction();
                    try {

                        $data = $this->crearTarjeta($request->numero_tarjeta);

                        if ($data['estado'] == TRUE) {
                            $tarjeta = $data['tarjeta'];

                            $data = $this->crearTarjetaServicio($tarjeta->numero_tarjeta,
                                                                Tarjetas::$ESTADO_TARJETA_INACTIVA,
                                                                Tarjetas::$CODIGO_SERVICIO_REGALO);

                            $data = $this->crearHtarjeta($tarjeta->id, Tarjetas::$ESTADO_TARJETA_CREADA,
                                                         Tarjetas::$CODIGO_SERVICIO_REGALO);

                            $data = $this->crearDetalleProducto($tarjeta->numero_tarjeta, $monto,
                                                                $request->numero_factura);
                        }
                        DB::commit();

                    } catch (\Exception $e) {
                        DB::rollBack();
                        $data = ["estado" => FALSE,
                                 "mensaje" => "error:" . $e->getMessage()];
                    }

                } else {
                    // Transaccion para la modificacion o creacion del servicio
                    DB::beginTransaction();
                    try {

                        $tarjeta_servicio = TarjetaServicios::where("numero_tarjeta", $tarjeta->numero_tarjeta)
                          ->where("servicio_codigo", Tarjetas::$CODIGO_SERVICIO_REGALO)
                          ->first();
                        
                        if($tarjeta_servicio == NULL){
                            $data = $this->crearTarjetaServicio($tarjeta->numero_tarjeta,
                                                                Tarjetas::$ESTADO_TARJETA_INACTIVA,
                                                                Tarjetas::$CODIGO_SERVICIO_REGALO);
                        }

                        $data = $this->crearDetalleProducto($tarjeta->numero_tarjeta, $monto,
                                                            $request->numero_factura);
                        DB::commit();

                    } catch (\Exception $e) {
                        DB::rollBack();
                        $data = ["estado" => FALSE,
                                 "mensaje" => "error:" . $e->getMessage()];
                    }

                }

            } else {
                $data = ["estado" => FALSE,
                         "mensaje" => Tarjetas::$TEXT_RESULT_MONTO_SUPERADO];

            }

            //            $data["estado"] = TRUE;
            //            $data["mensaje"] = TRUE;
        } else {
            $data = ["estado" => FALSE,
                     "mensaje" => Tarjetas::$TEXT_RESULT_FACTURA_Y_NUMTARJETA_EXISTEN];
        }

        return $data;
    }


    /**
     * Metodo encargado de la creacion de las tarjetas
     *
     * @param $numero_tarjeta
     *
     * @return mixed
     */
    public function crearTarjeta($numero_tarjeta) {

        try {
            $tarjeta = new Tarjetas();

            //completamos el numero de la tarjeta con CEROS para completar los 6 digitos
            $num_tarjeta = $numero_tarjeta;
            while (strlen($num_tarjeta) < 6) {
                $num_tarjeta = "0" . $num_tarjeta;
            }
            $tarjeta->numero_tarjeta = $num_tarjeta;

            //obtenemos los ultimos 4 digitos del nuemro de la tarjeta para ususrlos como contraseña
            $ultimos = substr($tarjeta->numero_tarjeta, -4);

            $tarjeta->password = Encript::encryption($ultimos);
            $tarjeta->estado = Tarjetas::$ESTADO_TARJETA_CREADA;
            $tarjeta->save();

            $data['estado'] = TRUE;
            $data['tarjeta'] = $tarjeta;

        } catch (\Exception $e) {
            $data['estado'] = FALSE;
            $data['mensaje'] = 'No fue posible crear la tarjeta ' . $e->getMessage();
            DB::rollBack();
        }
        return $data;
    }

    /**
     * Metodo encargado de asociar un servicio a una tarjeta
     *
     * @param $numero_tarjeta
     * @param $name_estado
     * @param $servicio_codigo
     *
     * @return mixed
     */
    public function crearTarjetaServicio($numero_tarjeta, $name_estado, $servicio_codigo) {

        try {
            $tarjetaser = new TarjetaServicios();

            $tarjetaser->servicio_codigo = $servicio_codigo;
            $tarjetaser->estado = $name_estado;
            $tarjetaser->numero_tarjeta = $numero_tarjeta;

            $tarjetaser->save();
            $result['estado'] = TRUE;

        } catch (\Exception $exception) {
            $result['estado'] = FALSE;
            $result['mensaje'] = 'No fue posible crear el servicio de la tarjeta' . $exception->getMessage();
            DB::rollBack();
        }
        return $result;
    }

    /**
     * Metodo encargado de insertar los registros del historico de las tarjetas
     *
     * @param $tarjeta_id
     * @param $name_estado
     * @param $servicio_codigo
     *
     * @return array
     */
    public function crearHtarjeta($tarjeta_id, $name_estado, $servicio_codigo) {

        try {
            $htarjetas = new Htarjetas();

            switch ($name_estado) {
                case Tarjetas::$ESTADO_TARJETA_CREADA:
                    $htarjetas->motivo = Tarjetas::$TEXT_CREACION_TARJETA_INGRESO_INVENTARIO;
                    break;
                default:
                    $htarjetas->motivo = "";
            }

            $htarjetas->estado = $name_estado;
            $htarjetas->fecha = Carbon::now();
            $htarjetas->tarjetas_id = $tarjeta_id;
            $htarjetas->user_id = Auth::User()->id;
            $htarjetas->servicio_codigo = $servicio_codigo;

            $htarjetas->save();
            $data['estado'] = TRUE;

        } catch (\Exception $exception) {
            $data['estado'] = FALSE;
            $data['mensaje'] = 'No fue posible crear el historial de tarjeta' . $exception->getMessage();//. $exception->getMessage()
            DB::rollBack();
        }
        return $data;
    }


    public function crearDetalleProducto($numero_tarjeta, $monto, $factura) {
        try {
            $detalle_producto = new DetalleProdutos();

            $detalle_producto->numero_tarjeta = $numero_tarjeta;
            $detalle_producto->fecha_cracion = Carbon::now();
            $detalle_producto->monto_inicial = $monto;
            $detalle_producto->factura = $factura;
            $detalle_producto->user_id = Auth::User()->id;
            $detalle_producto->estado = DetalleProdutos::$ESTADO_INACTIVO;

            $detalle_producto->save();

            $data['estado'] = TRUE;

        } catch (\Exception $exception) {
            $data['estado'] = FALSE;
            $data['mensaje'] = 'No fue posible crear el detalle_producto para esta tarjeta' . $exception->getMessage();//. $exception->getMessage()
            DB::rollBack();
        }
        return $data;
    }

}

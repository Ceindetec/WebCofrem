<?php

namespace creditocofrem\Http\Controllers;

use Caffeinated\Shinobi\Facades\Shinobi;
use creditocofrem\AdminisTarjetas;
use creditocofrem\DetalleProdutos;
use creditocofrem\DetalleTransaccion;
use creditocofrem\HEstadoTransaccion;
use creditocofrem\PagaPlastico;
use creditocofrem\Tarjetas;
use creditocofrem\Htarjetas;
use creditocofrem\TarjetaServicios;
use creditocofrem\Transaccion;
use creditocofrem\ValorTarjeta;
use Facades\creditocofrem\Encript;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;


class TarjetasRegaloController extends Controller
{

    /**
     * metodo encargado de mostrar la vista principal para crear las Tarjetas
     * Regalo
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function crearTarjetaRegalo()
    {
        return view("tarjetas.regalo.individualmente");
    }

    /**
     * metodo encargado de mostrar la vista principal para crear las Tarjetas en Bloque
     * Regalo
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function crearTarjetaBloque()
    {
        return view("tarjetas.regalo.bloque");
    }

    /**
     * Metodo encargado de autocompletar las tarjetas buscadas por el número de
     * tarjeta
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function autoCompleNumTarjeta(Request $request)
    {
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
    public function addTarjetaRegalo(Request $request)
    {

        //quitamos la mascara de pesos al monto
        $monto = str_replace(".", "", $request->monto);

        $banderaPagaPlastico = FALSE;
        $banderaPagoAdmon = FALSE;
        $valorPlatico = 0;
        $valorAdmon = 0;

        $PagaPlstico = $this->validarRegaloPagaPlastico();
        if ($PagaPlstico["estado"]) {

            if ($PagaPlstico["paga"]) {
                $banderaPagaPlastico = TRUE;
                $valorPlatico = $PagaPlstico["valor"];
            }
        } else {
            return $PagaPlstico;
        }

        $PagaAdmon = $this->validarRegaloPagaAdmon();

        if ($PagaAdmon["estado"]) {

            if ($PagaAdmon["paga"]) {
                $banderaPagoAdmon = TRUE;
                $valorAdmon = $PagaAdmon["valor"];
            }
        } else {
            return $PagaAdmon;
        }

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
            if ($monto_inicial <= $factura["monto"]) {


                DB::beginTransaction();
                $data = $this->crearTarjeteDetalleServicio($request->numero_tarjeta, $request->numero_factura, $monto,
                    $banderaPagaPlastico, $valorPlatico, $banderaPagoAdmon, $valorAdmon);

                DB::commit();


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


    public function addTarjetaRegaloBloque(Request $request)
    {

        //quitamos la mascara de pesos al monto
        $monto = str_replace(".", "", $request->monto);

        $banderaPagaPlastico = FALSE;
        $banderaPagoAdmon = FALSE;
        $valorPlatico = 0;
        $valorAdmon = 0;

        $PagaPlstico = $this->validarRegaloPagaPlastico();
        if ($PagaPlstico["estado"]) {

            if ($PagaPlstico["paga"]) {
                $banderaPagaPlastico = TRUE;
                $valorPlatico = $PagaPlstico["valor"];
            }
        } else {
            return $PagaPlstico;
        }

        $PagaAdmon = $this->validarRegaloPagaAdmon();

        if ($PagaAdmon["estado"]) {

            if ($PagaAdmon["paga"]) {
                $banderaPagoAdmon = TRUE;
                $valorAdmon = $PagaAdmon["valor"];
            }
        } else {
            return $PagaAdmon;
        }


        $data = [];

        $numTarjetaInicial = $request->numero_tarjeta_inicial;

        $arrayNumTarjetas = [];
        for ($i = $numTarjetaInicial; $i < $numTarjetaInicial + $request->cantidad; $i++) {
            $arrayNumTarjetas[] = $this->completarCeros($i, 6);
        }

        //consultar todos los detalles producto que existan relacionados con esta factura para validar el monto valido
        $detalles_producto = DetalleProdutos::where("factura", $request->numero_factura)->get();

        $montos_inicial = 0;
        foreach ($detalles_producto as $detalle_producto) {
            $montos_inicial += $detalle_producto->monto_inicial;
        }

        $montoTotalBloque = $request->cantidad * $monto;

        $montoInicialTotal = $montoTotalBloque + $montos_inicial;

        //TODO: en este punto es necesario consultar la factura para saber el monto

        // array que simula el modelo de la factura que llegaria del WS
        $factura = ["numero_factura" => "02", "monto" => "200000"];

        if ($montoInicialTotal <= $factura["monto"]) {
            $collectionDetallesProducto = DetalleProdutos::where("factura", $request->numero_factura)
                ->whereIn("numero_tarjeta", $arrayNumTarjetas)
                ->get();

            if ($collectionDetallesProducto->count() == 0) {


                DB::beginTransaction();
                foreach ($arrayNumTarjetas as $numTarjeta) {

                    $data = $this->crearTarjeteDetalleServicio($numTarjeta, $request->numero_factura, $monto,
                        $banderaPagaPlastico, $valorPlatico, $banderaPagoAdmon, $valorAdmon);

                    if ($data["estado"] == FALSE) {
                        break;
                    }
                }
                DB::commit();

            } else {
                $data["estado"] = FALSE;
                $data["mensaje"] = (($collectionDetallesProducto->count() > 1) ? Tarjetas::$TEXT_RESULT_FACTURA_Y_NUMTARJETA_EXISTENEN_BLOQUE : Tarjetas::$TEXT_RESULT_FACTURA_Y_NUMTARJETA_EXISTENE_BLOQUE) . $collectionDetallesProducto->implode('numero_tarjeta',
                        ', ');
            }
        } else {
            $data = ["estado" => FALSE,
                "mensaje" => Tarjetas::$TEXT_RESULT_MONTO_SUPERADO];
        }

        return $data;
    }

    /**
     * metodo encargado de colocar ceros la inicia del numero para completar la cantidad de caracteres necesrios
     *
     * @param $numero
     * @param $digitos
     *
     * @return string
     */
    private function completarCeros($numero, $digitos)
    {

        $num_tarjeta = $numero;
        while (strlen($num_tarjeta) < $digitos) {
            $num_tarjeta = "0" . $num_tarjeta;
        }
        return $num_tarjeta;
    }

    private function validarRegaloPagaPlastico()
    {

        $pagaPlastico = PagaPlastico::where("servicio_codigo", Tarjetas::$CODIGO_SERVICIO_REGALO)
            ->orderBy("id", "DESC")
            ->first();

        if ($pagaPlastico != NULL) {

            if ($pagaPlastico->pagaplastico == 1) {

                $valorTarjeta = ValorTarjeta::orderBy("id", "DESC")->first();

                if ($valorTarjeta != NULL) {
                    $data["estado"] = TRUE;
                    $data["paga"] = TRUE;
                    $data["valor"] = $valorTarjeta->valor;
                } else {
                    $data["estado"] = FALSE;
                    $data["mensaje"] = Tarjetas::$TEXT_SIN_VALOR_PLASTICO;
                }

            } else {
                $data["estado"] = TRUE;
                $data["paga"] = FALSE;
            }

        } else {
            $data["estado"] = FALSE;
            $data["mensaje"] = Tarjetas::$TEXT_SERVICIO_TARJETA_REGALO_SIN_PARAMETRIZACION;
        }
        return $data;
    }

    private function validarRegaloPagaAdmon()
    {

        $porcentajeAdmon = AdminisTarjetas::where("servicio_codigo", Tarjetas::$CODIGO_SERVICIO_REGALO)
            ->orderBy("id", "DESC")
            ->first();

        if ($porcentajeAdmon != NULL) {

            if ($porcentajeAdmon->estado == Tarjetas::$ESTADO_TARJETA_ACTIVA) {

                $data["estado"] = TRUE;
                $data["paga"] = TRUE;
                $data["valor"] = $porcentajeAdmon->porcentaje;

            } else {
                $data["estado"] = TRUE;
                $data["paga"] = FALSE;
            }

        } else {
            $data["estado"] = FALSE;
            $data["mensaje"] = Tarjetas::$TEXT_SERVICIO_TARJETA_REGALO_SIN_PARAMETRIZACION;
        }
        return $data;
    }

    private function transaccionAdminstrativa($numero_tarjeta, $valor, $destalle_producto_id, $descripcion)
    {
        $transaccionAnterior = Transaccion::orderBy("id", "DESC")->first();

        if ($transaccionAnterior != NULL) {
            $numeroTransaccion = intval($transaccionAnterior->numero_transaccion) + 1;
        } else {
            $numeroTransaccion = 1;
        }


        try {
            $transaccion = new Transaccion();
            $transaccion->numero_transaccion = $this->completarCeros($numeroTransaccion, 10);
            $transaccion->numero_tarjeta = $numero_tarjeta;
            $transaccion->tipo = Transaccion::$TIPO_ADMINISTRATIVO;
            //$transaccion->valor = $valor; se elimino ese campo de la base de datos
            $transaccion->fecha = Carbon::now();

            $transaccion->save();

            $hEstado = new HEstadoTransaccion();
            $hEstado->transaccion_id = $transaccion->id;
            $hEstado->estado = HEstadoTransaccion::$ESTADO_ACTIVO;
            $hEstado->fecha = Carbon::now();

            $hEstado->save();

            $detalleTransaccion = new DetalleTransaccion();
            $detalleTransaccion->transaccion_id = $transaccion->id;
            $detalleTransaccion->detalle_producto_id = $destalle_producto_id;
            $detalleTransaccion->valor = $valor;
            $detalleTransaccion->descripcion = $descripcion;

            $detalleTransaccion->save();

            $data['estado'] = TRUE;


        } catch (\Exception $e) {
            $data['estado'] = FALSE;
            $data['mensaje'] = 'No fue posible crear la Transaccion ' . $e->getMessage();
            DB::rollBack();
        }

        return $data;

    }

    /**
     * @param $numero_tarjeta
     * @param $numero_factura
     * @param $monto
     *
     * @return array|mixed
     */
    protected function crearTarjeteDetalleServicio($numero_tarjeta,
                                                   $numero_factura,
                                                   $monto,
                                                   $pagaPlastico,
                                                   $valorPlastico,
                                                   $pagoAdmon,
                                                   $valorAdmon)
    {

        //buscamos si el munero de tarjeta existe en nuestros registros
        $tarjeta = Tarjetas::where("numero_tarjeta", $numero_tarjeta)->first();

        if ($tarjeta == NULL) {
            // transaccion para la creacion de la tarjetas
            $data = $this->crearTarjeta($numero_tarjeta);

            if ($data['estado'] == TRUE) {
                $tarjeta = $data['tarjeta'];

                $data = $this->crearTarjetaServicio($tarjeta->numero_tarjeta, Tarjetas::$ESTADO_TARJETA_INACTIVA,
                    Tarjetas::$CODIGO_SERVICIO_REGALO);

                $data = $this->crearHtarjeta($tarjeta->id, Tarjetas::$ESTADO_TARJETA_CREADA,
                    Tarjetas::$CODIGO_SERVICIO_REGALO);

                $data = $this->crearDetalleProducto($tarjeta->numero_tarjeta, $monto, $numero_factura);

                $num_tarjeta = $tarjeta->numero_tarjeta;
                $detalle_producto_id = $data['id'];

            }

        } else {

            $tarjeta_servicio = TarjetaServicios::where("numero_tarjeta", $tarjeta->numero_tarjeta)
                ->where("servicio_codigo", Tarjetas::$CODIGO_SERVICIO_REGALO)
                ->first();

            if ($tarjeta_servicio == NULL) {
                $data = $this->crearTarjetaServicio($tarjeta->numero_tarjeta, Tarjetas::$ESTADO_TARJETA_INACTIVA,
                    Tarjetas::$CODIGO_SERVICIO_REGALO);
            }

            $data = $this->crearDetalleProducto($tarjeta->numero_tarjeta, $monto, $numero_factura);

            $num_tarjeta = $tarjeta->numero_tarjeta;
            $detalle_producto_id = $data['id'];

        }


        if ($data['estado'] == TRUE) {
            if ($pagaPlastico) {
                $data = $this->transaccionAdminstrativa($num_tarjeta, $valorPlastico, $detalle_producto_id, DetalleTransaccion::$DESCRIPCION_PLASTICO);
            }
            if ($pagoAdmon) {
                $valorAdministracion = ($monto * $valorAdmon) / 100;
                $data = $this->transaccionAdminstrativa($num_tarjeta, $valorAdministracion, $detalle_producto_id, DetalleTransaccion::$DESCRIPCION_ADMINISTRACION);
            }
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
    public function crearTarjeta($numero_tarjeta)
    {

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
    public function crearTarjetaServicio($numero_tarjeta, $name_estado, $servicio_codigo)
    {

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
    public function crearHtarjeta($tarjeta_id, $name_estado, $servicio_codigo)
    {

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

    /**
     * Metodo encargado de registrar el detalle del producto a una tarjeta
     *
     * @param $numero_tarjeta
     * @param $monto
     * @param $factura
     *
     * @return array
     */
    public function crearDetalleProducto($numero_tarjeta, $monto, $factura)
    {
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
            $data['id'] = $detalle_producto->id;

        } catch (\Exception $exception) {
            $data['estado'] = FALSE;
            $data['mensaje'] = 'No fue posible crear el detalle_producto para esta tarjeta' . $exception->getMessage();//. $exception->getMessage()
            DB::rollBack();
        }
        return $data;
    }

    /**
     * metodo que trae la vista para la consulta de servicios de tarjeta regalo creadas en el sistema
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function consultaTarjetasRegalo()
    {
        return view('tarjetas.regalo.consultaregalo');
    }

    /**
     * devuelve los datos para mostrar en la grid de los servicios de tarjeta regalo que hay en el sistema
     * @return mixed
     */
    public function gridConsulaTarjetaRegalo()
    {
        $tarjetas = Tarjetas::join('tarjeta_servicios', 'tarjetas.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->join('detalle_produtos', 'tarjetas.numero_tarjeta', 'detalle_produtos.numero_tarjeta')
            ->where('tarjeta_servicios.servicio_codigo', Tarjetas::$CODIGO_SERVICIO_REGALO)
            ->where('tarjeta_servicios.estado','<>',TarjetaServicios::$ESTADO_ANULADA)
            ->select(['detalle_produtos.monto_inicial', 'detalle_produtos.factura as fa', 'detalle_produtos.id as deta_id', 'detalle_produtos.estado as estadopro','tarjetas.*'])
            ->get();

        return Datatables::of($tarjetas)
            ->addColumn('action', function ($tarjetas) {
                $acciones = "";
                $acciones .= '<div class="btn-group">';
                $acciones .= '<a data-modal href="'.route('gestionarTarjeta',$tarjetas->deta_id).'" type="button" class="btn btn-custom btn-xs">Gestionar</a>';
                if(Shinobi::can('editar.monto.regalo')){
                    $acciones .= '<a data-modal href="' . route('regalo.editar', $tarjetas->deta_id) . '" type="button" class="btn btn-custom btn-xs">Editar</a>';
                }
                if ($tarjetas->estadopro == 'I') {
                    $acciones .= '<button type="button" class="btn btn-custom btn-xs" onclick="activar(' . $tarjetas->deta_id . ')">Activar</button>';
                }
                $acciones .= '</div>';
                return $acciones;
            })
            ->make(true);
    }


    //TODO: ES IMPORTANTE ESTO VA SOLO CON PERSONAS QUE TENGA PERMISO PARA HACERLO

    /**
     * trae la vista del modal para editar una tarjeta regalo
     * @param $id id del detalle producto de la tarjeta regalo a editar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewEditarRegalo($id)
    {
        $detalle = DetalleProdutos::find($id);
        return view('tarjetas.regalo.modaleditarregalo', compact('detalle'));
    }

    /**
     * metodo que permite editar una tarjeta regalo, aunque solo permite editar su monto
     * @param Request $request
     * @param $id
     * @return array
     */
    public function editarRegalo(Request $request, $id)
    {
        $result = [];
        DB::beginTransaction();
        try {
            $detalle = DetalleProdutos::find($id);
            $detalle->monto_inicial = str_replace(".", "", $request->monto_inicial);
            $detalle->save();
            $detalle_trasacion = DetalleTransaccion::where('detalle_producto_id', $detalle->id)->where('descripcion', DetalleTransaccion::$DESCRIPCION_ADMINISTRACION)->first();
            if ($detalle_trasacion != NULL) {
                $administracion = AdminisTarjetas::where('servicio_codigo', Tarjetas::$CODIGO_SERVICIO_REGALO)
                    ->where('estado', AdminisTarjetas::$ESTADO_ACTIVO)
                    ->first();
                $valorAdministracion = $detalle->monto_inicial * ($administracion->porcentace / 100);
                $detalle_trasacion->valor = $valorAdministracion;
                $detalle_trasacion->save();
            }
            DB::commit();
            $result['estado'] = TRUE;
            $result['mensaje'] = 'Actualizado satisfactoriamente';
        } catch (\Exception $exception) {
            DB::rollBack();
            $result['estado'] = TRUE;
            $result['mensaje'] = 'No fue posible realizar la actualizacion ' . $exception->getMessage();
        }
        return $result;
    }


    /**
     * metodo que permite activar una tarjeta regalo en el sistema
     * @param Request $request
     * @return array
     */
    public function activarTarjetaRegalo(Request $request)
    {
        $result = [];
        DB::beginTransaction();
        try {
            $detalle = DetalleProdutos::find($request->id);
            $detalle->fecha_activacion = Carbon::now();
            $detalle->fecha_vencimiento = Carbon::now()->addYear();
            $detalle->estado = DetalleProdutos::$ESTADO_ACTIVO;
            $detalle->save();
            $tarjeta_servicio = TarjetaServicios::where('numero_tarjeta', $detalle->numero_tarjeta)->first();
            $tarjeta_servicio->estado = TarjetaServicios::$ESTADO_ACTIVO;
            $tarjeta_servicio->save();
            $tarjeta = Tarjetas::where('numero_tarjeta',$detalle->numero_tarjeta)->first();
            $tarjeta->estado = Tarjetas::$ESTADO_TARJETA_ACTIVA;
            $tarjeta->save();
            $htarjetas = new Htarjetas();
            $htarjetas->motivo = Tarjetas::$TEXT_DEFAULT_MOTIVO_ACTIVACION_TARJETA;
            $htarjetas->estado = Tarjetas::$ESTADO_TARJETA_ACTIVA;
            $htarjetas->fecha = Carbon::now();
            $htarjetas->servicio_codigo = Tarjetas::$CODIGO_SERVICIO_REGALO;
            $htarjetas->user_id = Auth::User()->id;
            $htarjetas->tarjetas_id = $tarjeta->id;
            $htarjetas->save();
            DB::commit();
            $result['estado'] = TRUE;
            $result['mensaje'] = 'La tarjeta ha sido activada satisfactoriamente.';
        } catch (\Exception $exception) {
            DB::rollBack();
            $result['estado'] = FALSE;
            $result['mensaje'] = 'No fue posible activar la tarjeta '.$exception->getMessage();
        }
        return $result;
    }
}

<?php

namespace creditocofrem\Http\Controllers;

use Carbon\Carbon;
use creditocofrem\DetalleProdutos;
use creditocofrem\Duplicado;
use creditocofrem\DuplicadoProductos;
use creditocofrem\Htarjetas;
use creditocofrem\Motivo;
use creditocofrem\Tarjetas;
use creditocofrem\Servicios;
use creditocofrem\TarjetaServicios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Facades\creditocofrem\Encript;
use Illuminate\Support\Facades\DB;

class TarjetasController extends Controller
{
    //se listan las tarjetas registradas
    public function index()
    {
        $totalRegalo = Tarjetas::join('tarjeta_servicios', 'tarjetas.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->where('tarjeta_servicios.servicio_codigo', 'R')
            ->where('tarjetas.persona_id', null)
            ->get();
        $totalRegaloSin = Tarjetas::join('tarjeta_servicios', 'tarjetas.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->where('tarjeta_servicios.servicio_codigo', 'R')
            ->where('tarjetas.estado', 'C')
            ->where('tarjetas.persona_id', null)
            ->get();
        $totalRegaloAsin = Tarjetas::join('tarjeta_servicios', 'tarjetas.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->where('tarjeta_servicios.servicio_codigo', 'R')
            ->where('tarjetas.estado', '<>', 'C')
            ->where('tarjetas.persona_id', null)
            ->get();
        $totalBono = Tarjetas::join('tarjeta_servicios', 'tarjetas.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->leftJoin('personas', 'tarjetas.persona_id', 'personas.id')
            ->where('tarjeta_servicios.servicio_codigo', 'B')
            ->where('tarjetas.persona_id', null)
            ->orWhere('personas.tipo_persona', 'T')
            ->get();
        $totalBonoSin = Tarjetas::join('tarjeta_servicios', 'tarjetas.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->leftJoin('personas', 'tarjetas.persona_id', 'personas.id')
            ->where('tarjeta_servicios.servicio_codigo', 'B')
            ->where('tarjetas.estado', 'C')
            ->where('tarjetas.persona_id', null)
            ->orWhere('personas.tipo_persona', 'T')
            ->get();
        $totalBonoAsin = Tarjetas::join('tarjeta_servicios', 'tarjetas.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->leftJoin('personas', 'tarjetas.persona_id', 'personas.id')
            ->where('tarjeta_servicios.servicio_codigo', 'B')
            ->where('tarjetas.estado', '<>', 'C')
            ->where('tarjetas.persona_id', null)
            ->orWhere('personas.tipo_persona', 'T')
            ->get();
        $totalCupo = Tarjetas::join('tarjeta_servicios', 'tarjetas.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->leftJoin('personas', 'tarjetas.persona_id', 'personas.id')
            ->where('tarjeta_servicios.servicio_codigo', 'A')
            ->where('tarjetas.persona_id', null)
            ->orWhere('personas.tipo_persona', 'A')
            ->get();
        $totalCupoSin = Tarjetas::join('tarjeta_servicios', 'tarjetas.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->leftJoin('personas', 'tarjetas.persona_id', 'personas.id')
            ->where('tarjeta_servicios.servicio_codigo', 'A')
            ->where('tarjetas.estado', 'C')
            ->where('tarjetas.persona_id', null)
            ->orWhere('personas.tipo_persona', 'A')
            ->get();
        $totalCupoAsin = Tarjetas::join('tarjeta_servicios', 'tarjetas.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->leftJoin('personas', 'tarjetas.persona_id', 'personas.id')
            ->where('tarjeta_servicios.servicio_codigo', 'A')
            ->where('tarjetas.estado', '<>', 'C')
            ->where('tarjetas.persona_id', null)
            ->orWhere('personas.tipo_persona', 'A')
            ->get();
        return view('tarjetas.listatarjetas',
            compact('totalRegalo',
                'totalRegaloSin',
                'totalRegaloAsin',
                'totalBono',
                'totalBonoSin',
                'totalBonoAsin',
                'totalCupo',
                'totalCupoSin',
                'totalCupoAsin'
            ));
    }

    //carga la grid
    public function gridTarjetas()
    {
        $tarjetas = Tarjetas::all();
        //$servicios = TarjetaServicios::all();

        return Datatables::of($tarjetas)
            ->addColumn('servicios', function ($tarjetas) {
                $tar_ser = '';
                foreach ($tarjetas->getTarjetaServicios as $servicio) {
                    if ($tar_ser != "")
                        $tar_ser .= ", ";
                    $tar_ser .= $servicio->getServicio->descripcion;
                }
                return $tar_ser;
            })
            ->addColumn('action', function ($tarjetas) {
                $acciones = '<a href="' . route("tarjetas.editar", ["id" => $tarjetas->id]) . '" data-modal="modal-lg" class="btn btn-xs btn-custom" ><i class="glyphicon glyphicon-edit"></i> Editar</a>';
                //  $acciones =  '<button class="btn btn-xs btn-danger" onclick="eliminar(' . $tarjetas->id . ')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button>';
                //  $acciones="--";
                return $acciones;
            })
            ->make(true);
    }

    //vista modal para crear tarjetas
    public function viewCrearTarjeta()
    {
        $servicios = Servicios::pluck('descripcion', 'codigo');
        return view('tarjetas.modalcreartarjetas', compact(['servicios']));
    }

    //metodo para agregar nueva tarjeta: insertar en bd
    public function crearTarjeta(Request $request)
    {
        $result = [];
        \DB::beginTransaction();
        try {
            //dd($request->all());
            $tarjetas = new Tarjetas($request->all());
            //dd($tarjetas);
            // $tarjetas->create($request->all());
            $codigo = $tarjetas->numero_tarjeta;
            while (strlen($codigo) < 6) {
                $codigo = "0" . $codigo;
            }
            $tarjetas->numero_tarjeta = $codigo;
            $validator = \Validator::make(['numero_tarjeta' => $codigo], [
                'numero_tarjeta' => 'required|unique:tarjetas'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $ultimos = substr($tarjetas->numero_tarjeta, -4);
            //$tarjetas->password = bcrypt($ultimos);
            $tarjetas->password = Encript::encryption($ultimos);
            $tarjetas->estado = 'C';
            $tarjetas->save();

//            dd($tarjetas);

            $result['estado'] = true;
            $result['mensaje'] = 'La tarjeta ha sido creada satisfactoriamente';
            //$paso = 1;
            //crearHtarjeta($tarjetas);//insertar la historia
            $result = $this->crearHtarjeta($tarjetas, 'C', $request->servicio_codigo);
            $result = $this->crearTarjetaSer($tarjetas, 'I', $request->servicio_codigo);
            \DB::commit();
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la tarjeta ' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }

    //metodo para insertar la historia de la tarjeta, por cambio de estado: insertar en bd
    public static function crearHtarjeta($tarjetas, $name_estado, $servicio_codigo)
    {
        $result = [];
        //   \DB::beginTransaction();
        try {
            //dd($request->all());
            $htarjetas = new Htarjetas();
            //dd($tarjetas);
            // $tarjetas->create($request->all());
            $valor_motivo = '';
            if ($name_estado == Tarjetas::$ESTADO_TARJETA_CREADA)
                $valor_motivo = Tarjetas::$MOTIVO_TARJETA_CREADA;
            if ($name_estado == Tarjetas::$ESTADO_TARJETA_ACTIVA)
                $valor_motivo = Tarjetas::$MOTIVO_TARJETA_ACTIVA;
            if ($name_estado == "M")
            {
                $tarjetaser = TarjetaServicios::where("numero_tarjeta",$tarjetas->numero_tarjeta)->first();
                $name_estado=$tarjetaser->estado;
                $valor_motivo = Tarjetas::$MOTIVO_TARJETA_CAMBIO;
            }

            $htarjetas->motivo = $valor_motivo;
            $htarjetas->estado = $name_estado;
            $htarjetas->fecha = Carbon::now();
            $htarjetas->tarjetas_id = $tarjetas->id;
            $htarjetas->user_id = Auth::User()->id;
            $htarjetas->servicio_codigo = $servicio_codigo;
            // dd($htarjetas);
            //acceder a id usuario
            //consultar id de tarjetas
            $htarjetas->save();
            $result['estado'] = true;
            $result['mensaje'] = 'El historial de tarjeta ha sido creado satisfactoriamente';
            //       \DB::commit();

        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear el historial de tarjeta' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }

    //metodo para insertar la historia de la tarjeta, por cambio de estado: insertar en bd
    public static function crearTarjetaSer($tarjetas, $name_estado, $servicio_codigo)
    {
        $result = [];
        // \DB::beginTransaction();
        try {
            //dd($request->all());
            $tarjetaser = new TarjetaServicios();
            //dd($tarjetas);
            // $tarjetas->create($request->all());

            $tarjetaser->servicio_codigo = $servicio_codigo;
            $tarjetaser->estado = $name_estado;
            $tarjetaser->numero_tarjeta = $tarjetas->numero_tarjeta;
//             dd($tarjetaser);
//            acceder a id usuario
//            consultar id de tarjetas
            $tarjetaser->save();
            $result['estado'] = true;
            $result['mensaje'] = 'El servicio de la tarjeta ha sido creado satisfactoriamente';
            // \DB::commit();

        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear el servicio de la tarjeta' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }

    //metodo para llamar a la vista modal para editar la info de la tarjeta
    public function viewEditarTarjeta(Request $request)
    {
        $tarjeta = Tarjetas::find($request->id);
        $tar_ser = '';
        foreach ($tarjeta->getTarjetaServicios as $servicio) {
            if ($tar_ser != "")
                $tar_ser .= ", ";
            $tar_ser .= $servicio->getServicio->descripcion;
        }
        //return $tar_ser;
        return view('tarjetas.modaleditartarjeta', compact('tarjeta','tar_ser'));
    }

    //metodo para editar sólo el campo tipo, de la tarjeta
    public function editarTarjeta(Request $request, $id)
    {
        $result = [];
        \DB::beginTransaction();
        try {
            //dd($request->all());
            $tarjeta = Tarjetas::find($id);
            $numtarjeta_old=$tarjeta->numero_tarjeta;
            //debe actualizar el valor en todas las tablas donde este ese numero de tarjeta
            $dproducto = DetalleProdutos::where('numero_tarjeta', $tarjeta->numero_tarjeta)->first();
            //dd($dproducto);
            if ($dproducto == null) {
                $newtarjeta = new Tarjetas();
                $codigo = $request->numero_tarjeta;
                while (strlen($codigo) < 6) {
                    $codigo = "0" . $codigo;
                }
                $newtarjeta->numero_tarjeta = $codigo;
                $newtarjeta->password = $tarjeta->password;
                $newtarjeta->cambioclave = $tarjeta->cambioclave;
                $newtarjeta->estado = $tarjeta->estado;
                $validator = \Validator::make(['numero_tarjeta' => $codigo], [
                    'numero_tarjeta' => 'required|unique:tarjetas'
                ]);
                if ($validator->fails()) {
                    $result['estado'] = false;
                    $result['mensaje'] = 'Error.'.$validator->errors()->all();
                    return result;
                    //return $validator->errors()->all();
                }
               //dd($newtarjeta);
                $newtarjeta->save();
                $result['estado'] = true;
                $result['mensaje'] = 'El número de la tarjeta ha sido actualizado satisfactoriamente.';
                TarjetaServicios::where("numero_tarjeta",$numtarjeta_old)->update(["numero_tarjeta"=>$codigo]);
                $result['estado'] = true;
                $result['mensaje'] = 'El registro de tarjeta servicio ha sido actualizado satisfactoriamente.';
                //$result['data'] = $tarjeta;
                //$result = $this->crearHtarjeta($tarjeta, 'M', $request->servicio_codigo);
                Htarjetas::where("tarjetas_id",$tarjeta->id)->update(["tarjetas_id"=>$newtarjeta->id]);
                Tarjetas::where('numero_tarjeta',$numtarjeta_old)->delete();
                $result['estado'] = true;
                $result['mensaje'] = 'La tarjeta ha sido actualizada satisfactoriamente.';
                \DB::commit();
            }
            else
            {
                $result['estado'] = false;
                $result['mensaje'] = 'No es posible cambiar el numero, la tarjeta ya tiene productos asociados.';
                //$result['data'] = $tarjeta;
                \DB::rollBack();
            }
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible editar la tarjeta. ' . $exception->getMessage();
            \DB::rollBack();
        }
        return $result;
    }

    //metodo para llamar a la vista modal para crear tarjetas en bloque - masivo
    public function viewCrearTarjetaBloque(Request $request)
    {
        // $tipo_tarjetas = TipoTarjetas::pluck('descripcion', 'codigo');
        //return view('tarjetas.modalcreartarjetabloque', compact(['tipo_tarjetas']));
        $servicios = Servicios::pluck('descripcion', 'codigo');
        return view('tarjetas.modalcreartarjetabloque', compact(['servicios']));
    }

    //metodo para agregar  tarjetas en bloque: insertar en bd de forma masiva
    public function crearTarjetaBloque(Request $request)
    {
        $result = [];
        \DB::beginTransaction();
        try {

            //dd($request->all());
            //dd($tarjetas);
            // $tarjetas->create($request->all());
            $total = $request->cantidad;
            $primer_num = $request->numero_primer_tarjeta;
            $cont = 0;
            for ($i = $primer_num; $i < ($primer_num + $total); $i++) {
                $tarjetas = new Tarjetas();
                $codigo = $i;
                while (strlen($codigo) < 6) {
                    $codigo = "0" . $codigo;
                }
                $validator = \Validator::make(['numero_tarjeta' => $codigo], [
                    'numero_tarjeta' => 'required|unique:tarjetas'
                ]);
                if ($validator->fails()) {
                    return $validator->errors()->all();
                }
                $tarjetas->estado = 'C';
                // $tarjetas->tarjeta_codigo = $request->tarjeta_codigo;
                $tarjetas->numero_tarjeta = $codigo;
                $ultimos = substr($codigo, -4);
                //$tarjetas->password = bcrypt($ultimos);
                $tarjetas->password = Encript::encryption($ultimos);
                $tarjetas->save();
                $cont++;
                //$paso = 1;
                //crearHtarjeta($tarjetas);//insertar la historia
                $result['estado'] = true;
                $result['mensaje'] = 'la tarjeta' . $codigo . ' ha sido creada satisfactoriamente';
                $result = $this->crearHtarjeta($tarjetas, 'C', $request->servicio_codigo);
                $result = $this->crearTarjetaSer($tarjetas, 'I', $request->servicio_codigo);
            }
            if ($cont == $total) {
                $result['estado'] = true;
                $result['mensaje'] = $total . ' tarjetas han sido creadas satisfactoriamente';
                \DB::commit();
            }

        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la tarjeta ' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }

    /**
     * metodo que trae la vista de gestiononar un detalle producto
     * @param $id id del detalle producto a gestionar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function gestionarTarjeta($id)
    {
        $detalleproducto = DetalleProdutos::find($id);
        return view('tarjetas.modalgestionartarjeta', compact('detalleproducto'));
    }

    /**
     * metodo que trae la vista para duplicar tarjetas
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewDuplicarTarjeta()
    {
        return view('tarjetas.duplicadotarjeta');
    }

    /**
     * trae la informacion de todas las tarjetas en la grid, para luego duplicar la que se necesite
     * @return mixed
     */
    public function gridTarjetasDuplicar()
    {
        $tarjetas = Tarjetas::where('estado', Tarjetas::$ESTADO_TARJETA_ACTIVA)->get();
        return Datatables::of($tarjetas)
            ->addColumn('servicios', function ($tarjetas) {
                $tar_ser = '';
                foreach ($tarjetas->getTarjetaServicios as $servicio) {
                    if ($tar_ser != "")
                        $tar_ser .= ", ";
                    $tar_ser .= $servicio->getServicio->descripcion;
                }
                return $tar_ser;
            })
            ->addColumn('action', function ($tarjetas) {
                return '<a href="' . route('tarjetas.modalduplicar', $tarjetas->id) . '" data-modal class="btn btn-xs btn-custom">Duplicar</a>';
            })
            ->make(true);
    }

    /**
     * trae la vista para el modal del formulario para duplicar una tarjeta
     * @param $id id de la tarjeta que se quiere sacar un duplicado
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewModalDuplicarTarjeta($id)
    {
        $tarjeta = Tarjetas::find($id);
        $motivos = Motivo::where('tipo', Motivo::$MOTIVO_TIPO_DUPLICADO)->pluck('motivo', 'codigo');
        return view('tarjetas.modalduplicartarjeta', compact('tarjeta', 'motivos'));
    }

    /**
     * metodo para el autocomplete del formulario en el campo numero de tarjeta
     * @param Request $request
     * @return mixed
     */
    public function autoCompleteTarjetaDuplicado(Request $request)
    {

        $tarjetas = Tarjetas::where("numero_tarjeta", "like", "%" . $request->numero_tarjeta . "%")->where('estado','C')->get();
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
     * metodo que procesa los datos del formulario para duplicar una tarjeta
     * @param Request $request datos referente a la nueva tarjeta
     * @param $id id de la tarjeta que se quire duplicar
     * @return array
     */
    public function duplicarTarjeta(Request $request, $id){
        $result = [];
        DB::beginTransaction();
        $exiteTarjeta = Tarjetas::where('numero_tarjeta',$request->numero_tarjeta)->where('estado',Tarjetas::$ESTADO_TARJETA_CREADA)->first();
        if(count($exiteTarjeta)==0){
            $result['estado'] = FALSE;
            $result['mensaje'] = 'El numero de tarjeta no existe en el inventario';
            return $result;
        }
        try{
            $oltarjeta = Tarjetas::find($id);
            $motivo = Motivo::where('codigo',$request->motivo)->first();
            $duplicado = new Duplicado();
            $duplicado->oldtarjeta = $oltarjeta->numero_tarjeta;
            $duplicado->newtarjeta = $request->numero_tarjeta;
            $duplicado->fecha = Carbon::now();
            $duplicado->save();
            $servicios = TarjetaServicios::where('numero_tarjeta',$oltarjeta->numero_tarjeta)->get();
            foreach ($servicios as $servicio){
                $servicioNewTarjeta = TarjetaServicios::where('numero_tarjeta',$request->numero_tarjeta)->where('servicio_codigo',$servicio->servicio_codigo)->first();
                if(count($servicioNewTarjeta)==0){
                    $newServicio = new TarjetaServicios();
                    $newServicio->numero_tarjeta = $request->numero_tarjeta;
                    $newServicio->servicio_codigo = $servicio->servicio_codigo;
                    $newServicio->estado = $servicio->estado;
                    $newServicio->save();
                }else{
                    $servicioNewTarjeta->estado = $servicio->estado;
                    $servicioNewTarjeta->save();
                }
            }
            TarjetaServicios::where('numero_tarjeta',$oltarjeta->numero_tarjeta)->update(['estado'=>TarjetaServicios::$ESTADO_ANULADA]);
            $detallesProductos = DetalleProdutos::where('numero_tarjeta',$oltarjeta->numero_tarjeta)->get();
            foreach ($detallesProductos as $detallesProducto){
                $newDetalleProducto = new DetalleProdutos($detallesProducto->toArray());
                $newDetalleProducto->numero_tarjeta = $request->numero_tarjeta;
                $newDetalleProducto->save();
                $newDuplicadoDetalle = new DuplicadoProductos();
                $newDuplicadoDetalle->oldproducto = $detallesProducto->id;
                $newDuplicadoDetalle->newproducto = $newDetalleProducto->id;
                $newDuplicadoDetalle->fecha = Carbon::now();
                $newDuplicadoDetalle->save();
            }
            DetalleProdutos::where('numero_tarjeta',$oltarjeta->numero_tarjeta)->update(['estado'=>DetalleProdutos::$ESTADO_ANULADO]);
            $duplicadoTarjeta = Tarjetas::where('numero_tarjeta',$request->numero_tarjeta)->first();
            $duplicadoTarjeta->estado = $oltarjeta->estado;
            $duplicadoTarjeta->save();
            $oltarjeta->estado = Tarjetas::$ESTADO_TARJETA_ANULADA;
            $oltarjeta->save();
            $htarjeta = new Htarjetas();
            $htarjeta->motivo = $motivo->motivo;
            $htarjeta->tarjetas_id = $oltarjeta->id;
            $htarjeta->user_id = Auth::user()->id;
            $htarjeta->nota = $request->nota;
            $htarjeta->estado = Tarjetas::$ESTADO_TARJETA_ANULADA;
            $htarjeta->fecha = Carbon::now();
            $htarjeta->save();
            $htarjeta = new Htarjetas();
            $htarjeta->motivo = $motivo->motivo;
            $htarjeta->tarjetas_id = $oltarjeta->id;
            $htarjeta->user_id = Auth::user()->id;
            $htarjeta->nota = $request->nota;
            $htarjeta->estado = $duplicadoTarjeta->estado;
            $htarjeta->fecha = Carbon::now();
            $htarjeta->save();
            DB::commit();
            $result['estado'] = TRUE;
            $result['mensaje'] = 'Duplicado de tarjeta creado satisfactoriamente';
        }catch (\Exception $exception){
            DB::rollBack();
            $result['estado'] = FALSE;
            $result['mensaje'] = 'Error durante la operacion '.$exception->getMessage();
        }
        return $result;
    }

}

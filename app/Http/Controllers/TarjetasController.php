<?php

namespace creditocofrem\Http\Controllers;

use Carbon\Carbon;
use creditocofrem\DetalleProdutos;
use creditocofrem\Htarjetas;
use creditocofrem\Motivo;
use creditocofrem\Tarjetas;
use creditocofrem\Servicios;
use creditocofrem\TarjetaServicios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use Facades\creditocofrem\Encript;

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
            if ($name_estado == 'C')
                $valor_motivo = 'Creación de la tarjeta - ingreso al inventario';
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
        return view('tarjetas.modaleditartarjeta', compact('tarjeta'));
    }

    //metodo para editar sólo el campo tipo, de la tarjeta
    public function editarTarjeta(Request $request, $id)
    {
        $result = [];
        \DB::beginTransaction();
        try {
            //dd($request->all());
            $tarjeta = Tarjetas::find($id);
            $tarjeta->tipo = $request->tipo;
            $tarjeta->save();
            $result['estado'] = true;
            $result['mensaje'] = 'La tarjeta ha sido actualizada satisfactoriamente.';
            $result['data'] = $tarjeta;
            \DB::commit();
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
                return '<a href="' . route('tarjetas.modalduplicar',$tarjetas->id) . '" data-modal class="btn btn-xs btn-custom">Duplicar</a>';
            })
            ->make(true);
    }

    public function viewModalDuplicarTarjeta($id)
    {
        $tarjeta = Tarjetas::find($id);
        $motivos = Motivo::where('tipo', Motivo::$MOTIVO_TIPO_DUPLICADO)->pluck('motivo','codigo');
        return view('tarjetas.modalduplicartarjeta', compact('tarjeta', 'motivos'));
    }

}

<?php

namespace creditocofrem\Http\Controllers;

use Carbon\Carbon;
use creditocofrem\Htarjetas;
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
        return view('tarjetas.listatarjetas');
    }

    //carga la grid
    public function gridTarjetas()
    {
        $tarjetas = Tarjetas::all();

        return Datatables::of($tarjetas)
            ->addColumn('servicios',function ($tarjetas){
                $tar_ser='';
                foreach ($tarjetas->getTarjetaServicios as $servicio)
                {
                    if($tar_ser!="")
                        $tar_ser.=", ";
                    $tar_ser.=$servicio->getServicio->descripcion;
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
    public function crearHtarjeta($tarjetas, $name_estado, $servicio_codigo)
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
    public function crearTarjetaSer($tarjetas, $name_estado, $servicio_codigo)
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
        //$tarjeta = Tarjetas::find($request->id);
        //return view('tarjetas.modalcreartarjetabloque', compact('tarjeta'));
        $tipo_tarjetas = TipoTarjetas::pluck('descripcion', 'codigo');
        return view('tarjetas.modalcreartarjetabloque', compact(['tipo_tarjetas']));
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
                $tarjetas->tarjeta_codigo = $request->tarjeta_codigo;
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
                $this->crearHtarjeta($tarjetas, 'C');
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

}

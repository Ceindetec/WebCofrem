<?php

namespace creditocofrem\Http\Controllers;

use Carbon\Carbon;
use creditocofrem\Htarjetas;
use creditocofrem\Tarjetas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;

class TarjetasController extends Controller
{
    //se listan las tarjetas registradas
    public function index(){
        return view('tarjetas.listatarjetas');
    }

    //carga la grid
    public function gridTarjetas(){
        $tarjetas = Tarjetas::all();
        return Datatables::of($tarjetas)
            ->addColumn('action', function ($tarjetas) {
                $acciones = '<a href="' . route("tarjetas.editar", ["id" => $tarjetas->id]) . '" data-modal="modal-lg" class="btn btn-xs btn-custom" ><i class="glyphicon glyphicon-edit"></i> Editar</a>';
              //  $acciones =  '<button class="btn btn-xs btn-danger" onclick="eliminar(' . $tarjetas->id . ')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button>';
              //  $acciones="--";
                return $acciones;
            })
            ->make(true);
    }
    //vista modal para crear tarjetas
    public function viewCrearTarjeta(){
        return view('tarjetas.modalcreartarjetas');
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
            $ultimos = substr($tarjetas->numero_tarjeta, -4);
            $tarjetas->password = bcrypt($ultimos);
            $tarjetas->save();
            $result['estado'] = true;
            $result['mensaje'] = 'La tarjeta ha sido creada satisfactoriamente';
            //$paso = 1;
            //crearHtarjeta($tarjetas);//insertar la historia
            $this->crearHtarjeta($tarjetas,'C');
            \DB::commit();
        }
        catch (\Exception $exception)
        {
        $result['estado'] = false;
        $result['mensaje'] = 'No fue posible crear la tarjeta '. $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }
    //metodo para insertar la historia de la tarjeta, por cambio de estado: insertar en bd
    public function crearHtarjeta($tarjetas,$name_estado)
    {
        $result = [];
        \DB::beginTransaction();
        try {
            //dd($request->all());
            $htarjetas = new Htarjetas();
            //dd($tarjetas);
            // $tarjetas->create($request->all());
            $valor_motivo='';
            if($name_estado=='C')
                $valor_motivo='Creación de la tarjeta - ingreso al inventario';
            $htarjetas->motivo = $valor_motivo;
            $htarjetas->estado =$name_estado;
            $htarjetas->fecha =Carbon::now();
            $htarjetas->hora = Carbon::now();
            $htarjetas->tarjetas_id =$tarjetas->id;
            $htarjetas->user_id =Auth::User()->id;
           // dd($htarjetas);
            //acceder a id usuario
            //consultar id de tarjetas
            $htarjetas->save();
            $result['estado'] = true;
            $result['mensaje'] = 'El historial de tarjeta ha sido creado satisfactoriamente';
            \DB::commit();

        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear el historial de tarjeta'. $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
       // return $result;
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
        $result= [];
        \DB::beginTransaction();
        try{
        //dd($request->all());
            $tarjeta = Tarjetas::find($id);
            $tarjeta->tipo=$request->tipo;
            $tarjeta->save();
            $result['estado'] = true;
            $result['mensaje'] = 'La tarjeta ha sido actualizada satisfactoriamente.';
            $result['data'] = $tarjeta;
            \DB::commit();
        }catch (\Exception $exception){
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible editar la tarjeta. '.$exception->getMessage();
            \DB::rollBack();
        }
        return $result;
    }
    //metodo para llamar a la vista modal para crear tarjetas en bloque - masivo
    public function viewCrearTarjetaBloque(Request $request)
    {
        $tarjeta = Tarjetas::find($request->id);
        return view('tarjetas.modalcreartarjetabloque', compact('tarjeta'));
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
            $total=$request->cantidad;
            $primer_num=$request->numero_primer_tarjeta;
            $cont=0;
            for($i=$primer_num;$i<($primer_num+$total);$i++)
            {
                $tarjetas = new Tarjetas();
                $tarjetas->tipo=$request->tipo;
                $tarjetas->numero_tarjeta =$i;
                $ultimos = substr($i, -4);
                $tarjetas->password = bcrypt($ultimos);
                $tarjetas->save();
                $cont++;
                //$paso = 1;
                //crearHtarjeta($tarjetas);//insertar la historia
                $result['estado'] = true;
                $result['mensaje'] = 'la tarjeta'.$i.' ha sido creada satisfactoriamente';
                $this->crearHtarjeta($tarjetas,'C');
                \DB::commit();
            }
            if($cont==$total)
            {
                $result['estado'] = true;
                $result['mensaje'] = $total.' tarjetas han sido creadas satisfactoriamente';
                \DB::commit();
            }

        }
        catch (\Exception $exception)
        {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la tarjeta '. $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }

}

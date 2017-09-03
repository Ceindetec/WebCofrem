<?php

namespace creditocofrem\Http\Controllers;

use Carbon\Carbon;
use creditocofrem\ConveniosEsta;
use creditocofrem\Establecimientos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ConveniosEstablecimientosController extends Controller
{
    /**
     * carga el data table con la lista de cnvenios existente para un establecimiento
     * @param Request $request id del establecimiento
     * @return mixed
     */
    public function gridConveniosEstablecimiento(Request $request){
        $convenios = ConveniosEsta::where('establecimiento_id',$request->id)->get();
        return Datatables::of($convenios)
            ->addColumn('action', function ($convenios) {
                $acciones = '<div class="btn-group">';
                $acciones = $acciones.'<a href="' . route("establecimiento.editar", ["id" => $convenios->id]) . '" class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Edit</a>';
                $acciones = $acciones.'<a class="btn btn-xs btn-primary" href="'.route("listsucursales", [$convenios->id]).'"><i class="ti-layers-alt"></i> Sucursales</a>';
                $acciones = $acciones.'</div>';
                return $acciones;
            })
            ->make(true);
    }

    /**
     * trae la vista del modal que permite agregar un nuevo convenio a un establecimiento
     * @param $id id correspondiente al establecimiento al que se le quiere agregar un convenio.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCrearConvenio($id){
        $establecimiento_id = $id;
        return view('establecimientos.convenios.modalcrearconvenio', compact('establecimiento_id'));
    }

    public function crearConvenio(Request $request, $id){
        $result=[];
        DB::beginTransaction();
        try{
            $convenioEsta = new ConveniosEsta($request->all());
            $convenioEsta->establecimiento_id = $id;
            if($convenioEsta->fecha_inicio <= Carbon::now()->format('d/m/Y')){
                $convenioEsta->estado = 'A';
                $establecimiento = Establecimientos::find($id);
                $establecimiento->estado = 'A';
                $establecimiento->save();
            }
            else{
                $convenioEsta->estado = 'I';
            }
            $convenioEsta->fecha_inicio = Carbon::createFromFormat('d/m/Y',$convenioEsta->fecha_inicio);
            $convenioEsta->fecha_fin = Carbon::createFromFormat('d/m/Y',$convenioEsta->fecha_fin);
            //dd($convenioEsta);
            $convenioEsta->save();
            $result['estado']=true;
            $result['mensaje'] = 'Convenio agregado satisfactoriamente';
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            $result['estado']=false;
            $result['mensaje'] = 'No fue posible crear el convenio '.$exception->getMessage();
        }
        return $result;
    }
}

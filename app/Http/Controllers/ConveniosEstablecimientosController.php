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
                $acciones = $acciones.'<a href="' . route("coveniosesta.reglas", ["id" => $convenios->id]) . '" data-modal="modal-lg" class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Reglas</a>';
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

    /**
     * metodoq ue permite crear un convenio a un determinado estableciminto comercial
     * @param Request $request datos correspondientes al convenios
     * @param $id id referente al establecimiento al que se le quiere crear el convenio
     * @return array
     */
    public function crearConvenio(Request $request, $id){
        $result=[];
        DB::beginTransaction();
        try{
            $validator = \Validator::make($request->all(), [
                'numero_convenio' => 'required|unique:convenios_estas',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $convenioEsta = new ConveniosEsta($request->all());
            $convenioEsta->establecimiento_id = $id;
            $establecimiento = Establecimientos::find($id);
            if($convenioEsta->fecha_inicio <= Carbon::now()->format('d/m/Y')){
                $convenioEsta->estado = 'A';
                $establecimiento->estado = 'A';
                $establecimiento->save();
            }
            else{
                $convenioEsta->estado = 'P';
            }
            $convenioEsta->fecha_inicio = Carbon::createFromFormat('d/m/Y',$convenioEsta->fecha_inicio);
            $convenioEsta->fecha_fin = Carbon::createFromFormat('d/m/Y',$convenioEsta->fecha_fin);
            //dd($convenioEsta);
            $convenioEsta->save();
            $result['estado']=true;
            $result['mensaje'] = 'Convenio agregado satisfactoriamente';
            $result['data'] = $establecimiento;
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            $result['estado']=false;
            $result['mensaje'] = 'No fue posible crear el convenio '.$exception->getMessage();
        }
        return $result;
    }


    public function viewReglasConvenioEstablecimiento($id){
        $convenio_id = $id;
        return view('establecimientos.convenios.modalreglasconvenioestablecimiento', compact('convenio_id'));
    }
}

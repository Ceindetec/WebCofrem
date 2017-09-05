<?php

namespace creditocofrem\Http\Controllers;

use Carbon\Carbon;
use creditocofrem\Auditoria;
use creditocofrem\ConveniosEsta;
use creditocofrem\Establecimientos;
use creditocofrem\FerecuenciaConvEs;
use creditocofrem\PlazoConvEs;
use creditocofrem\RangoConvEs;
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
    public function gridConveniosEstablecimiento(Request $request)
    {
        $convenios = ConveniosEsta::where('establecimiento_id', $request->id)->get();
        return Datatables::of($convenios)
            ->addColumn('action', function ($convenios) {
                $acciones = '<div class="btn-group">';
                $acciones = $acciones . '<a href="' . route("coveniosesta.reglas", ["id" => $convenios->id]) . '" data-modal="modal-lg" class="btn btn-xs btn-custom" ><i class="ti-pencil-alt"></i> Reglas</a>';
                $acciones = $acciones . '<a class="btn btn-xs btn-primary" href="' . route("convenios.historial", [$convenios->id]) . '" ><i class="ti-layers-alt"></i> Historial</a>';
                $acciones = $acciones . '</div>';
                return $acciones;
            })
            ->make(true);
    }

    /**
     * trae la vista del modal que permite agregar un nuevo convenio a un establecimiento
     * @param $id id correspondiente al establecimiento al que se le quiere agregar un convenio.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCrearConvenio($id)
    {
        $establecimiento_id = $id;
        return view('establecimientos.convenios.modalcrearconvenio', compact('establecimiento_id'));
    }

    /**
     * metodoq ue permite crear un convenio a un determinado estableciminto comercial
     * @param Request $request datos correspondientes al convenios
     * @param $id id referente al establecimiento al que se le quiere crear el convenio
     * @return array
     */
    public function crearConvenio(Request $request, $id)
    {
        $result = [];
        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'numero_convenio' => 'required|unique:convenios_estas',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $convenioEsta = new ConveniosEsta($request->all());
            $convenioEsta->establecimiento_id = $id;
            $establecimiento = Establecimientos::find($id);
            if ($convenioEsta->fecha_inicio <= Carbon::now()->format('d/m/Y')) {
                $convenioEsta->estado = 'A';
                $establecimiento->estado = 'A';
                $establecimiento->save();
            } else {
                $convenioEsta->estado = 'P';
            }
            $convenioEsta->fecha_inicio = Carbon::createFromFormat('d/m/Y', $convenioEsta->fecha_inicio);
            $convenioEsta->fecha_fin = Carbon::createFromFormat('d/m/Y', $convenioEsta->fecha_fin);
            //dd($convenioEsta);
            $convenioEsta->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Convenio agregado satisfactoriamente';
            $result['data'] = $establecimiento;
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear el convenio ' . $exception->getMessage();
        }
        return $result;
    }

    /**
     * metodo que trae la vista para modificar o crear los parametros a un convenio
     * @param $id id referente al convenio a parametrizar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewReglasConvenioEstablecimiento($id)
    {
        $plazo = PlazoConvEs::where('convenios_esta_id', $id)->first();
        $frecuencia = FerecuenciaConvEs::where('convenios_esta_id', $id)->first();
        $convenio = ConveniosEsta::find($id);
        return view('establecimientos.convenios.modalreglasconvenioestablecimiento', ['plazo' => $plazo, 'frecuencia' => $frecuencia, 'convenio' => $convenio]);
    }

    /**
     * metodo que carga los rangos que se han configurado para un convenio
     * @param $id id correspondiente al convenio
     * @return mixed
     */
    public function gridRangosConvenio($id)
    {
        $rangos = RangoConvEs::where('convenios_esta_id', $id)->where('estado','A')->get();
        return Datatables::of($rangos)
            ->addColumn('action', function ($rangos) {
                $maxId = RangoConvEs::where('convenios_esta_id',$rangos->convenios_esta_id)->where('dias',$rangos->dias)->select([\DB::raw('MAX(id) as id')])->first();
                if($rangos->id == $maxId->id){
                    $acciones = '<div class="btn-group">';
                    $acciones = $acciones . '<button class="btn btn-xs btn-danger" onclick="eliminarRango('.$rangos->id.')" ><i class="fa fa-trash"></i> Eliminar</button>';
                    $acciones = $acciones . '</div>';
                    return $acciones;
                }

            })
            ->make(true);
    }

    /**
     * funcion que permite actualizar el plazo de pago de un convenio
     * @param Request $request datos de los dias de plazon para el pago
     * @param $id id correspondiente al convenio a actualizar
     * @return array
     */
    public function actualizarPlazoPagoConvenio(Request $request, $id)
    {
        $result = [];
        try {
            $plazo = PlazoConvEs::where('convenios_esta_id', $id)->first();
            if ($plazo == null) {
                $plazo = new PlazoConvEs();
                $plazo->dias = $request->dias;
                $plazo->convenios_esta_id = $id;
            }else{
                $plazo->dias = $request->dias;
            }
            $plazo->save();
            $result['estado'] = true;
            $result['mensaje'] = 'El valor del plazo del pago acualizado.';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible actualizar el plazo de pago. '.$exception->getMessage();
        }
        return $result;
    }

    /**
     * funcion encargada de actualizar o crear una frecuencia de corte para un convenio
     * @param Request $request dato de la frecuencia de corte
     * @param $id id correspondiente al convenio a actulizar su frecuencia de corte
     * @return array
     */
    public function actualizarFrecuenciaCorteConvenio(Request $request, $id){
        $result = [];
        try {
            $frecuencia = FerecuenciaConvEs::where('convenios_esta_id', $id)->first();
            if ($frecuencia == null) {
                $frecuencia = new FerecuenciaConvEs();
                $frecuencia->frecuencia_corte = $request->frecuencia_corte;
                $frecuencia->convenios_esta_id = $id;
            }else{
                $frecuencia->frecuencia_corte = $request->frecuencia_corte;
            }
            $frecuencia->save();
            $result['estado'] = true;
            $result['mensaje'] = 'El valor del plazo del pago acualizado.';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible actualizar el plazo de pago. '.$exception->getMessage();
        }
        return $result;
    }

    /**
     * metodo encargado de agregar un nuevo rango de administracion a un convenio
     * @param Request $request datos de los rangos a ingresar
     * @param $id id correspondiente al convenio
     * @return array
     */
    public function nuevoRongoConvenio(Request $request, $id){
        $result = [];
        try{
            $rangos = RangoConvEs::where('convenios_esta_id',$id)->where('estado','A')->get();

            if(count($rangos)>0){
                foreach ($rangos as $rango1){
                    if($rango1->dias == $request->dias){
                        if($rango1->valor_max >= str_replace(",",".",str_replace(".","",$request->valor_min))){
                            $result['estado'] = false;
                            $result['mensaje'] = 'Para este plazo de la factura, exite un rango de precios que se cruza con el que trata de agregar';
                            return $result;
                        }
                    }
                }
            }
            $rango = new RangoConvEs();
            $rango->valor_min =  str_replace(",",".",str_replace(".","",$request->valor_min));
            $rango->valor_max =  str_replace(",",".",str_replace(".","",$request->valor_max));
            $rango->dias = $request->dias;
            $rango->porcentaje = $request->porcentaje;
            $rango->convenios_esta_id = $id;
            $rango->save();
            $result['estado'] = true;
            $result['mensaje'] = 'rango agregado correctamente';
        }catch (\Exception $exception){
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible agregar el nuevo rango';
        }

        return $result;
    }

    /**
     * metodo que elimina un rago de administracion para un convenio
     * @param Request $request trae la id corrrespodiente al rango a eliminar
     * @return array
     */
    public function eliminarRangoConvenio(Request $request){
        $result = [];
        try{
            $rango = RangoConvEs::find($request->id);
            $rango->estado='I';
            $rango->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Rango eliminado satisfactoriamente.';
        }catch (\Exception $exception){
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible elimnar el rango.';
        }
        return $result;
    }

    public function historialConvenioEstablecimiento($id){
        $convenio = ConveniosEsta::find($id);
        return view('establecimientos.convenios.historicoconvenio', compact('convenio'));
    }

    public function gridHitorialRangos($id){
        $rangos = RangoConvEs::where('convenios_esta_id',$id)->distinct()->select('id')->pluck('id');
        $auditoria = Auditoria::where('auditable_type','creditocofrem\RangoConvEs')
            ->join('users','users.id','audits.user_id')
            ->whereIn('auditable_id',$rangos->all())
            ->select(['audits.*','users.name'])
            ->get();
        return Datatables::of($auditoria)->make(true);
    }

    public function gridFrecuencia($id){
        $rangos = FerecuenciaConvEs::where('convenios_esta_id',$id)->distinct()->select('id')->pluck('id');
        $auditoria = Auditoria::where('auditable_type','creditocofrem\FerecuenciaConvEs')
            ->join('users','users.id','audits.user_id')
            ->whereIn('auditable_id',$rangos->all())
            ->select(['audits.*','users.name'])
        ->get();
        return Datatables::of($auditoria)->make(true);
    }

    public function gridPlazos($id){
        $rangos = PlazoConvEs::where('convenios_esta_id',$id)->distinct()->select('id')->pluck('id');
        $auditoria = Auditoria::where('auditable_type','creditocofrem\PlazoConvEs')
            ->join('users','users.id','audits.user_id')
            ->whereIn('auditable_id',$rangos->all())
            ->select(['audits.*','users.name'])
        ->get();
        return Datatables::of($auditoria)->make(true);
    }



}

<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\Contratos_empr;
use Illuminate\Http\Request;
use Carbon\Carbon;
use creditocofrem\Htarjetas;
use creditocofrem\Tarjetas;
use creditocofrem\Servicios;
use creditocofrem\TarjetaServicios;
use creditocofrem\DetalleProdutos;
use creditocofrem\Personas;
use Facades\creditocofrem\Encript;
use Yajra\Datatables\Datatables;
use creditocofrem\Http\Controllers\TarjetasController;

class TarjetasBonoController extends Controller
{
    //Abre el formulario para la creacion individual de tarjetas bono
    public function viewCrearTarjetaIndividual()
    {
       // $servicios = Servicios::pluck('descripcion', 'codigo');
        return view('tarjetas.bono.individualmente');
    }
    //funcion crear tarjeta de bono empresarial Individual
    public function crearTarjetaIndividual(Request $request)
    {
        /*Condiciones:
         * Si el contrato existe, registra los valores, si NO, No debe dejar, el usuario debe agregar primero el contrato.
         * si  el numero de tarjeta existe, omite tarjetas, htarjetas y tarjeta_servicios. Si NO, la inserta en esas tablas
         * Si  el documento de la persona existe, lo asocia, Si NO, inserta primero la persona (docuemtno, nombres  apellidos, tipo= tercero)
        *TABLAS INVOLUCRADAS:
         * tarjetas
         * htarjetas
         * tarjeta_servicios
         * personas
         * detalle_produtos
         * transacciones
         * detalle_transacciones
         * hestado_transacciones
        */
        $result = [];
        \DB::beginTransaction();
        try {
            //consulta si el contrato existe, SINO, NO permite nada
            $contrato = Contratos_empr::where("n_contrato", $request->numero_contrato)->first();
            if ($contrato != null) {
                //if ($contrato->n_contrato == $request->numero_contrato) //solo si existe el contrato con una empresa
                //{
                $num_tarjeta = $request->numero_tarjeta;
                while (strlen($num_tarjeta) < 6) {
                    $num_tarjeta = "0" . $num_tarjeta;
                }
                $tarjeta = Tarjetas::where("numero_tarjeta", $num_tarjeta)->first();
                if ($tarjeta->numero_tarjeta != $num_tarjeta) //no existe la tarjeta
                {
                    $tarjeta = new Tarjetas($request->all());
                    $tarjeta->numero_tarjeta = $num_tarjeta;
                    $result = $this->crearTarjeta($tarjeta, 'C', 'B');
                }
                //consulta si existe la persona, SiNO, la inserta.
                $persona = Personas::where("identificacion", $request->identificacion)->first();
                if ($persona->identificacion != $request->identificacion) //no existe la tarjeta
                {
                    $persona = new Personas($request->all());
                    $result = $this->crearPersona($persona);
                }
                //insertar el detalle del producto
                $detalle = new DetalleProdutos($request->all());
                $result = $this->crearDetalleProd($detalle, $request->monto, $contrato->id, 'I');
                \DB::commit();
                // }
            }
            else
                {
                    $result['estado'] = false;
                    $result['mensaje'] = 'No es posible crear la tarjeta bono, el contrato No Existe';//. $exception->getMessage()
                    \DB::rollBack();
                }

        }catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la tarjeta bono ' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }
    //metodo para insertar la historia de la tarjeta, por cambio de estado: insertar en bd
    public function crearTarjeta($tarjetas, $name_estado, $servicio_codigo)
    {
        $result = [];
        // \DB::beginTransaction();
        try {
            $validator = \Validator::make(['numero_tarjeta' => $tarjetas->numero_tarjeta], [
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
            $result['estado'] = true;
            $result['mensaje'] = 'La tarjeta ha sido creada satisfactoriamente';
            $result = TarjetasController::crearHtarjetas($tarjetas, 'C', 'B');
            $result = TarjetasController::crearTarjetaSer($tarjetas, 'I', 'B');
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la tarjeta' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }
    public function crearPersona($persona)
    {
        $result = [];
        // \DB::beginTransaction();
        try {
            $persona->save();
            $result['estado'] = true;
            $result['mensaje'] = 'La persona ha sido ingresada satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la persona' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }
    public function crearDetalleProd($detalle,$monto,$contrato_id,$estado)
    {
        $result = [];
        // \DB::beginTransaction();
        try {
            $detalle->fecha_cracion=Carbon::now();
            $detalle->monto_inicial=$monto;
            $detalle->contrato_emprs_id=$contrato_id;
            $detalle->user_id= Auth::User()->id;
            $detalle->estado=$estado;
            $detalle->save();
            $result['estado'] = true;
            $result['mensaje'] = 'El detalle del producto ha sido creado satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear el detalle del producto' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }

    public function autoCompleNumContrato(Request $request) {

        $contratos = Contratos_empr::where("id", "like", "%" . $request->numero_contrato . "%")->get();
        if (count($contratos) == 0) {
            $data["query"] = "Unit";
            $data["suggestions"] = [];
        } else {
            $arrayContratos = [];
            foreach ($contratos as $contrato) {
                $arrayContratos[] = ["value" => $contrato->numero_contrato,
                    "data" => $contrato->id,];
            }
            $data["suggestions"] = $arrayContratos;
            $data["query"] = "Unit";
        }
        return $data;
    }
    public function getNombre(Request $request) {
        $persona = personas::where("identificacion",$request->identificacion)->first();
        return $persona;
    }
}

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
use creditocofrem\Transaccion;
use creditocofrem\HEstadoTransaccion;
use creditocofrem\DetalleTransaccion;
use creditocofrem\AdminisTarjetas;
use creditocofrem\ValorTarjeta;
use creditocofrem\PagaPlastico;

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
            $contrato = Contratos_empr::where("n_contrato", $request->numero_contrato)->first();
            //dd($contrato);
            if($contrato != null) {
                //if ($contrato->n_contrato == $request->numero_contrato) //solo si existe el contrato con una empresa
                //{
                $num_tarjeta = $request->numero_tarjeta;
                while (strlen($num_tarjeta) < 6) {
                    $num_tarjeta = "0" . $num_tarjeta;
                }
                $tarjeta = Tarjetas::where("numero_tarjeta", $num_tarjeta)->first();
                if($tarjeta==null) //no existe la tarjeta
                {
                    $result = $this->crearTarjeta($num_tarjeta, 'C', 'B');
                }
                //consulta si existe la persona, SiNO, la inserta.
                $persona = Personas::where("identificacion", $request->identificacion)->first();
                if($persona==null) //no existe la persona
                {
                    $result = $this->crearPersona($request->identificacion,$request->nombres,$request->apellidos);
                    //dd($result['mensaje']);
                }
                $monto=str_replace(".","",$request->monto);
                //insertar el detalle del producto
                //dd($detalle);
                $result = $this->crearDetalleProd($num_tarjeta, $monto, $contrato->id, 'I');
                if($result['estado']==true) {
                    //insertar la transaccion
                    $detalle_id=$result['detalle_id'];
                    $result = $this->transacciones($num_tarjeta, 'B', $detalle_id,$monto);// num_tarjeta,servicio_codigo=B,detalle_id,monto
                    //si es correcta la transaccion:
                    if($result['estado']==true) {
                        $result['estado'] = true;
                        $result['mensaje'] = 'La tarjeta bono ha sido creada';//. $exception->getMessage()
                        \DB::commit();
                    }
                }
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
    //Metodo que consulta si se deben pagar costos de la tarjeta y administracion para la tarjeta, si es asi inserta
    public function transacciones($num_tarjeta, $tipo_servicio, $detalle_id, $monto)
    {
         $result = [];
          try {
              //Consultar si paga plastico
              $id_transaccion="";
              $paga = PagaPlastico::where("pagaplastico", '1')->where("estado", 'A')->where("servicio_codigo", $tipo_servicio)->first();
              if ($paga!=null) {//si paga plastico
                  $vtarjeta = ValorTarjeta::where("estado", 'A')->first();
                  //$vtarjeta->valor;
                  //crear transaccion
                  $transaccion = new Transaccion();
                  $transaccion_ant = Transaccion::max('numero_transaccion'); //consulta maximo valor de transaccion
                  //dd($transaccion_ant);
                  if($transaccion_ant==null)//no hay transacciones
                  {
                      $next="0000000001";
                  }
                  else{
                      $next=($transaccion_ant)+1;
                      while (strlen($next) < 10) {
                          $next = "0" . $next;
                      }
                  }
                 // dd($next);
                  $transaccion->numero_transaccion=$next;
                  $transaccion->numero_tarjeta = $num_tarjeta;
                  $transaccion->tipo='A';
                  $transaccion->fecha=Carbon::now();
                 // dd($transaccion);
                  $transaccion->save();
                  $id_transaccion=$transaccion->id;

                  //crear htransaccion
                  $htransaccion = new HEstadoTransaccion();
                  $htransaccion->transaccion_id= $transaccion->id;
                  $htransaccion->estado='A';
                  $htransaccion->fecha=Carbon::now();
                  //dd($htransaccion);
                  $htransaccion->save();

                  //crear detalle_transaccion
                  $detallet= new DetalleTransaccion();
                  $detallet->transaccion_id=$transaccion->id;
                  $detallet->detalle_producto_id=$detalle_id;
                  $detallet->valor=$vtarjeta->valor;
                  $detallet->descripcion='P';

                  $detallet->save();

              }
              //Consultar si paga administracion de tarjeta
              $administra = AdminisTarjetas::where("estado", 'A')->where("servicio_codigo", $tipo_servicio)->first();
              if ($administra!=null) {//si paga plastico
                  if($id_transaccion=="")//no ha creado la transaccion
                  {
                      //crear transaccion
                      $transaccion = new Transaccion();
                      $transaccion_ant = Transaccion::max('numero_transaccion'); //consulta maximo valor de transaccion
                      //dd($transaccion_ant);
                      if($transaccion_ant==null)//no hay transacciones
                      {
                          $next="0000000001";
                      }
                      else{
                          $next=($transaccion_ant)+1;
                          while (strlen($next) < 10) {
                              $next = "0" . $next;
                          }
                      }
                      $transaccion->numero_transaccion=$next;
                      $transaccion->numero_tarjeta = $num_tarjeta;
                      $transaccion->tipo='A';
                      $transaccion->fecha=Carbon::now();
                      $transaccion->save();
                      $id_transaccion=$transaccion->id;
                      //crear htransaccion
                      $htransaccion = new HEstadoTransaccion();
                      $htransaccion->transaccion_id= $transaccion->id;
                      $htransaccion->estado='A';
                      $htransaccion->fecha=Carbon::now();
                      $htransaccion->save();
                  }

                  //crear detalle_transaccion
                  $detallet= new DetalleTransaccion();
                  $detallet->transaccion_id=$id_transaccion;
                  $detallet->detalle_producto_id=$detalle_id;
                  $porcentaje=$administra->porcentaje;
                  $valor=($monto*$porcentaje/100);
                  $detallet->valor=$valor;
                  $detallet->descripcion='A';
                  //dd($detallet);
                  $detallet->save();
              }
              $result['estado'] = true;
              $result['mensaje'] = 'Las transacciones han sido creadas';//. $exception->getMessage()
          }catch (\Exception $exception) {
                $result['estado'] = false;
                $result['mensaje'] = 'No fue posible registrar las transacciones' . $exception->getMessage();//. $exception->getMessage()
                \DB::rollBack();
          }
        return $result;
    }
    //metodo para insertar la historia de la tarjeta, por cambio de estado: insertar en bd
    public function crearTarjeta($num_tarjeta, $name_estado, $servicio_codigo)
    {
        $result = [];
        try {
            $tarjetas = new Tarjetas();
            $tarjetas->numero_tarjeta = $num_tarjeta;
            $validator = \Validator::make(['numero_tarjeta' => $num_tarjeta], [
                'numero_tarjeta' => 'required|unique:tarjetas'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $ultimos = substr($tarjetas->numero_tarjeta, -4);
            //$tarjetas->password = bcrypt($ultimos);
            $tarjetas->password = Encript::encryption($ultimos);
            $tarjetas->estado = $name_estado;
            //dd($tarjetas);
            $tarjetas->save();
            $result['estado'] = true;
            $result['mensaje'] = 'La tarjeta ha sido creada satisfactoriamente';
            $result = TarjetasController::crearHtarjeta($tarjetas, $name_estado, $servicio_codigo);
            $result = TarjetasController::crearTarjetaSer($tarjetas, 'I', $servicio_codigo);
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la tarjeta' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }
    public function crearPersona($iden,$nom,$ape)
    {
        $result = [];
        // \DB::beginTransaction();
        try {
            $persona = new Personas();
            $persona->identificacion = $iden;
            $persona->nombres = $nom;
            $persona->apellidos = $ape;
           // dd($persona);
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
    public function crearDetalleProd($num_tarjeta,$monto,$contrato_id,$estado)
    {
        $result = [];
        // \DB::beginTransaction();
        try {
            $detalle = DetalleProdutos::where("numero_tarjeta", $num_tarjeta)->where("contrato_emprs_id", $contrato_id)->first();
            if ($detalle!=null) {
                $result['estado'] = false;
                $result['mensaje'] = 'Ya existe un detalle de producto asociado al numero de tarjeta y al contrato';//. $exception->getMessage()
                \DB::rollBack();

            } else {
                $detalle = new DetalleProdutos();
                $detalle->numero_tarjeta = $num_tarjeta;
                $detalle->fecha_cracion = Carbon::now();
                $detalle->monto_inicial = $monto;
                $detalle->contrato_emprs_id = $contrato_id;
                $detalle->user_id = \Auth::User()->id;
                $detalle->estado = $estado;
                $detalle->save();
                $result['estado'] = true;
                $result['mensaje'] = 'El detalle del producto ha sido creado satisfactoriamente';
                $result['detalle_id'] = $detalle->id;
            }
        }catch (\Exception $exception) {
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
                $arrayContratos[] = ["value" => $contrato->n_contrato,
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

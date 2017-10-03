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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
    //Abre el formulario para la creacion en bloque de tarjetas bono
    public function viewCrearTarjetaBloque()
    {
        // $servicios = Servicios::pluck('descripcion', 'codigo');
        return view('tarjetas.bono.crearbloque');
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
                    $result = $this->crearTarjeta($num_tarjeta, Tarjetas::$ESTADO_TARJETA_CREADA, Tarjetas::$CODIGO_SERVICIO_BONO);
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
                $result = $this->crearDetalleProd($num_tarjeta, $monto, $contrato->id, Tarjetas::$ESTADO_TARJETA_INACTIVA);
                if($result['estado']==true) {
                    //insertar la transaccion
                    $detalle_id=$result['detalle_id'];
                    $result = $this->transacciones($num_tarjeta, Tarjetas::$CODIGO_SERVICIO_BONO, $detalle_id,$monto);// num_tarjeta,servicio_codigo=B,detalle_id,monto
                    //si es correcta la transaccion:

                    //validar si la suma del monto y cantidad de tarjetas, es igual o menor a la estipulada en el contrato, sino hacer rollback
                    $valor_contrato_original=$contrato->valor_contrato-$contrato->valor_impuesto;
                    $cantidadt_contrato_original=$contrato->n_tarjetas;
                    //dd($valor_contrato_original." -- ".$cantidadt_contrato_original);
                    $detalle1 = DetalleProdutos::where("contrato_emprs_id", $contrato->id)->get();
                    $cantidadt_contrato_nuevo=count($detalle1);
                    $valor_contrato_nuevo=0;
                    foreach ($detalle1 as $det)
                    {
                        $valor_contrato_nuevo+=$det->monto_inicial;
                    }
                    //  dd("cantidad orgi/nuevo: ".$cantidadt_contrato_original."/".$cantidadt_contrato_nuevo." y monto nuevo: ".$valor_contrato_original."/".$valor_contrato_nuevo);
                    if($cantidadt_contrato_original<$cantidadt_contrato_nuevo && $valor_contrato_original<$valor_contrato_nuevo) {
                        $result['estado'] = false;
                        $result['mensaje'] = 'Las tarjetas superan la cantidad y/o el monto que fue contratado inicialmente';//. $exception->getMessage()
                    }
                    if($result['estado']==true) {
                        $result['estado'] = true;
                        $result['mensaje'] = 'La tarjeta bono ha sido creada';//. $exception->getMessage()
                        \DB::commit();
                    }
                    else {
                        //$result['estado'] = false;
                        //$result['mensaje'] = 'Las tarjetas bono no pudieron ser creadas';//. $exception->getMessage()
                        \DB::rollBack();
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
                  $transaccion->tipo=Transaccion::$TIPO_ADMINISTRATIVO;
                  $transaccion->fecha=Carbon::now();
                 // dd($transaccion);
                  $transaccion->save();
                  $id_transaccion=$transaccion->id;

                  //crear htransaccion
                  $htransaccion = new HEstadoTransaccion();
                  $htransaccion->transaccion_id= $transaccion->id;
                  $htransaccion->estado=HEstadoTransaccion::$ESTADO_ACTIVO;
                  $htransaccion->fecha=Carbon::now();
                  //dd($htransaccion);
                  $htransaccion->save();

                  //crear detalle_transaccion
                  $detallet= new DetalleTransaccion();
                  $detallet->transaccion_id=$transaccion->id;
                  $detallet->detalle_producto_id=$detalle_id;
                  $detallet->valor=$vtarjeta->valor;
                  $detallet->descripcion=DetalleTransaccion::$DESCRIPCION_PLASTICO;

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
                      $htransaccion->estado=Transaccion::$TIPO_ADMINISTRATIVO;
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
                  $detallet->descripcion=DetalleTransaccion::$DESCRIPCION_ADMINISTRACION;
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
            $result = TarjetasController::crearTarjetaSer($tarjetas, Tarjetas::$ESTADO_TARJETA_INACTIVA, $servicio_codigo);
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

        $contratos = Contratos_empr::where("n_contrato", "like", "%" . $request->numero_contrato. "%")->get();
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
    //funcion crear tarjeta de bono empresarial Individual
    public function crearTarjetaBloque(Request $request)
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
         * PROCESO
         * valida extension del archivo: txt
         * sube archivo
         * lee archivo (linea por linea): formato= ientificacion;nombres;apellidos;monto
         *  validar cada dato (segun deba ser: numerico, alfabetico etc)
         * al final: validar que la cantidad de tarjetas asociadas al contrato, no pase el limite inicial, ni el monto total.
        */
        $result = [];
        \DB::beginTransaction();
        try {
            //dd($request->all());
            //dd("funcion crear en bloque");
            $contrato = Contratos_empr::where("n_contrato", $request->numero_contrato)->first();
            //dd($contrato);
            if($contrato != null) {
                $primer_num = $request->numero_tarjeta_inicial;
                $num_contrato=$request->numero_contrato;
                $total_tarjetas = 0;
                $total_monto = 0;
                $ruta="tmp/";
                $new_name=$ruta.$primer_num."_".$num_contrato.".txt";
                $fichero = $request->file('archivo');
                $nombre_file=$fichero->getClientOriginalName();
                $extensiones = explode(".", $nombre_file);
                $extension=end($extensiones);
                $num_tarjeta_num=$primer_num;
                $anterior=1;
                if($extension=="txt")
                {
                    copy($_FILES['archivo']['tmp_name'], $new_name);
                    $contents = array();
                    $lineas="";
                    foreach(file($new_name) as $line) {
                        if($anterior==1)
                        {
                        $contents[] = $line . PHP_EOL;
                        //$lineas.=$line." -- ";
                        $campos = explode(";", $line);
                        $identificacion = trim($campos[0]);
                        $nombres = trim($campos[1]);
                        $apellidos = trim($campos[2]);
                        $monto = trim($campos[3]);
                        //validar los campos

                        if (is_numeric($identificacion) && (strlen($identificacion) > 2 && strlen($identificacion) < 11)) {
                            $permitidos = '/^[A-Z üÜáéíóúÁÉÍÓÚñÑ]{1,50}$/i';
                            if (preg_match($permitidos, utf8_encode($nombres)) && preg_match($permitidos, utf8_encode($apellidos))) {
                                if (is_numeric($monto)) {
                                    $lineas .= "correcta la linea: " . ($total_tarjetas + 1) . " -- ";
                                    $num_tarjeta = $num_tarjeta_num;
                                    while (strlen($num_tarjeta) < 6) {
                                        $num_tarjeta = "0" . $num_tarjeta;
                                    }

                                    $tarjeta = Tarjetas::where("numero_tarjeta", $num_tarjeta)->first();
                                    if ($tarjeta == null) //no existe la tarjeta
                                    {
                                        $result = $this->crearTarjeta($num_tarjeta, Tarjetas::$ESTADO_TARJETA_CREADA, Tarjetas::$CODIGO_SERVICIO_BONO);
                                    }
                                    //consulta si existe la persona, SiNO, la inserta.
                                    $persona = Personas::where("identificacion", $identificacion)->first();
                                    if ($persona == null) //no existe la persona
                                    {
                                        $result = $this->crearPersona($identificacion, $nombres, $apellidos);
                                        //dd($result['mensaje']);
                                    }
                                    $monto = str_replace(".", "", $monto);
                                    //insertar el detalle del producto
                                    $result = $this->crearDetalleProd($num_tarjeta, $monto, $contrato->id, DetalleProdutos::$ESTADO_INACTIVO);
                                    if ($result['estado'] == true) {
                                        //insertar la transaccion
                                        $detalle_id = $result['detalle_id'];
                                        $result = $this->transacciones($num_tarjeta, Tarjetas::$CODIGO_SERVICIO_BONO, $detalle_id, $monto);// num_tarjeta,servicio_codigo=B,detalle_id,monto
                                        //si es correcta la transaccion:
                                        if ($result['estado'] == true) {
                                            $result['mensaje'] = 'La tarjeta bono ha sido creada';//. $exception->getMessage()
                                        }
                                    }
                                    $num_tarjeta_num++;

                                } else {
                                    $result['estado'] = false;
                                    $result['mensaje'] = 'El valor del monto debe ser numérico ' . $monto;
                                    $lineas .= " Incorrecta la linea: " . ($total_tarjetas + 1) . " --- ";
                                 //   dd(" error en monto " . $monto);
                                    $anterior=0;
                                }
                            } else {
                                $result['estado'] = false;
                                $result['mensaje'] = 'Los nombres y apellidos deben ser alfabeticos ' . $nombres . " " . $apellidos;
                                $lineas .= " Incorrecta la linea: " . ($total_tarjetas + 1) . " --- ";
                                //dd(preg_match($permitidos,$apellidos));
                               // dd(" error en nombres " . utf8_encode($nombres) . " " . utf8_encode($apellidos));
                                $anterior=0;
                            }
                        } else {
                            $result['estado'] = false;
                            $result['mensaje'] = 'El valor de identificacion debe ser numérico con 3 a 10 digitos ' . $identificacion;
                            $lineas .= " Incorrecta la linea: " . ($total_tarjetas + 1) . " --- ";
                           // dd(" error en identificacion " . $identificacion);
                            $anterior=0;
                        }
                        $total_tarjetas++;
                        $total_monto += $monto;
                        }
                    }
                   // dd($lineas);
                    //validar si la suma del monto y cantidad de tarjetas, es igual o menor a la estipulada en el contrato, sino hacer rollback
                    $valor_contrato_original=$contrato->valor_contrato-$contrato->valor_impuesto;
                    $cantidadt_contrato_original=$contrato->n_tarjetas;
                    //dd($valor_contrato_original." -- ".$cantidadt_contrato_original);
                    $detalle1 = DetalleProdutos::where("contrato_emprs_id", $contrato->id)->get();
                    $cantidadt_contrato_nuevo=count($detalle1);
                    $valor_contrato_nuevo=0;
                    foreach ($detalle1 as $det)
                    {
                        $valor_contrato_nuevo+=$det->monto_inicial;
                    }
                    //  dd("cantidad orgi/nuevo: ".$cantidadt_contrato_original."/".$cantidadt_contrato_nuevo." y monto nuevo: ".$valor_contrato_original."/".$valor_contrato_nuevo);
                    if($cantidadt_contrato_original<$cantidadt_contrato_nuevo && $valor_contrato_original<$valor_contrato_nuevo) {
                        $result['estado'] = false;
                        $result['mensaje'] = 'Las tarjetas superan la cantidad y/o el monto que fue contratado inicialmente';//. $exception->getMessage()
                    }
                    //si lo anterior salio bien
                    if ($result['estado'] == true && $anterior == 1) {
                            $result['mensaje'] = 'Han sido creadas ' . $total_tarjetas . " tarjetas bono empresarial";//. $exception->getMessage()
                            \DB::commit();
                    } else {
                            //$result['estado'] = false;
                            //$result['mensaje'] = 'Las tarjetas bono no pudieron ser creadas';//. $exception->getMessage()
                            \DB::rollBack();
                    }
                }
                else
                    $result['mensaje'] = 'El archivo debe tener extensión txt';//. $exception->getMessage()
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
            dd($exception->getMessage());
            \DB::rollBack();
        }
        return $result;
    }
    /**
     *   * metodo que trae la vista para la consulta de servicios de tarjeta bono creadas en el sistema
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function consultaTarjetasBono()
    {
        return view('tarjetas.bono.consultabono');
    }

    /**
     * devuelve los datos para mostrar en la grid de los servicios de tarjeta bono que hay en el sistema
     * @return mixed
     */
    public function gridConsultaTarjetaBono()
    {
        $tarjetas = Tarjetas::join('tarjeta_servicios', 'tarjetas.numero_tarjeta', 'tarjeta_servicios.numero_tarjeta')
            ->join('detalle_produtos', 'tarjetas.numero_tarjeta', 'detalle_produtos.numero_tarjeta')
            ->where('tarjeta_servicios.servicio_codigo', Tarjetas::$CODIGO_SERVICIO_BONO)
            ->where('tarjeta_servicios.estado','<>',TarjetaServicios::$ESTADO_ANULADA)
            ->select(['detalle_produtos.monto_inicial', 'detalle_produtos.contrato_emprs_id as idcontrato', 'detalle_produtos.id as deta_id', 'tarjetas.*'])
            ->get();

        return Datatables::of($tarjetas)
            ->addColumn('action', function ($tarjetas) {
                $acciones = "";
                $acciones .= '<div class="btn-group">';
                $acciones .= '<a data-modal href="'.route('gestionarTarjeta',$tarjetas->deta_id).'" type="button" class="btn btn-custom btn-xs">Gestionar</a>';
                $acciones .= '<a data-modal href="' . route('bono.editar', $tarjetas->deta_id) . '" type="button" class="btn btn-custom btn-xs">Editar</a>';
                if ($tarjetas->estado == Tarjetas::$ESTADO_TARJETA_CREADA) {
                    $acciones .= '<button type="button" class="btn btn-custom btn-xs" onclick="activar(' . $tarjetas->deta_id . ')">Activar</button>';
                }
                $acciones .= '</div>';
                return $acciones;
            })
            ->make(true);
    }


    //TODO: ES IMPORTANTE ESTO VA SOLO CON PERSONAS QUE TENGA PERMISO PARA HACERLO

    /**
     * trae la vista del modal para editar una tarjeta bono
     * @param $id id del detalle producto de la tarjeta bono a editar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewEditarBono($id)
    {
        $detalle = DetalleProdutos::find($id);
        //list($dia,$mes,$ano) = explode("/",$detalle->fecha_vencimiento);
        //$detalle->fecha_vencimiento==$ano."-".$mes."-".$dia;
        //$detalle->fecha_vencimiento=date($detalle->fecha_vencimiento);
       // if($detalle->fecha_vencimiento!=null)
         //   $detalle->fecha_vencimiento=parse($detalle->fecha_vencimiento)->format('D/M/Y');
        return view('tarjetas.bono.modaleditarbono', compact('detalle'));
    }

    /**
     * metodo que permite editar una tarjeta bono, aunque solo permite editar su fecha de vencimiento
     * @param Request $request
     * @param $id
     * @return array
     */
    public function editarBono(Request $request, $id)
    {
        $result = [];
        DB::beginTransaction();
        try {
            $detalle = DetalleProdutos::find($id);
            $detalle->monto_inicial = str_replace(".", "", $request->monto_inicial);
            $fechav=$request->fecha_vencimiento;
            list($dia,$mes,$ano) = explode("/",$fechav);
            $fechav=$ano."/".$mes."/".$dia;
            //dd("la fecha ".$request->fecha_vencimiento." ahora: ".$fechav);
            $detalle->fecha_vencimiento=$fechav;
            $detalle->save();
            $detalle_trasacion = DetalleTransaccion::where('detalle_producto_id', $detalle->id)->where('descripcion', DetalleTransaccion::$DESCRIPCION_ADMINISTRACION)->first();
            if ($detalle_trasacion != NULL) {
                $administracion = AdminisTarjetas::where('servicio_codigo', Tarjetas::$CODIGO_SERVICIO_BONO)
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
     * metodo que permite activar una tarjeta bono en el sistema
     * @param Request $request
     * @return array
     */
    public function activarTarjetaBono(Request $request)
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
            $htarjetas->servicio_codigo = Tarjetas::$CODIGO_SERVICIO_BONO;
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

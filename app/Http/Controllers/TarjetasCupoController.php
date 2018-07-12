<?php

namespace creditocofrem\Http\Controllers;

use creditocofrem\ConveniosEmp;
use Illuminate\Http\Request;
use Caffeinated\Shinobi\Facades\Shinobi;
use creditocofrem\Contratos_empr;
use creditocofrem\Empresas;
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
use Illuminate\Support\Facades\Auth;
use creditocofrem\Departamentos;
use creditocofrem\Municipios;
use SoapClient;

class TarjetasCupoController extends Controller
{
    //Abre el formulario para la creacion individual de tarjetas de cupo rotativo
    public function viewCrearTarjetaIndividual()
    {
        $empresas = Empresas::where('tipo','A')
            ->whereRaw('id IN (SELECT empresa_id FROM convenios_emps WHERE estado = ?)',['A'])
            ->pluck('razon_social', 'id');
        $departamentos = Departamentos::pluck('descripcion', 'codigo');
        return view('tarjetas.rotativo.individualmente', compact(['empresas','departamentos']));
    }
    //funcion crear tarjeta de cupo rotativo Individual
    public function crearTarjetaIndividual(Request $request)
    {
        /*Condiciones:
         * Si la persona existe en SevenAs, trae los datos y asigna Tipo=afiliado, si NO, No debe dejar crear el nuevo producto (la empresa debe tener convenio activo)
         * si  el numero de tarjeta existe, omite tarjetas, htarjetas y tarjeta_servicios. Si NO, la inserta en esas tablas
         * Si  el documento de la persona existe, lo asocia y actualiza la info (con la info de SevenAs), Si NO, inserta primero la persona (docuemtno, nombres  apellidos, tipo= afiliado)
        *TABLAS INVOLUCRADAS:
         * tarjetas  * htarjetas  * tarjeta_servicios * personas * detalle_produtos * transacciones * detalle_transacciones
         * hestado_transacciones
        */
        $result = [];
        \DB::beginTransaction();
        try {
            $convenio = ConveniosEmp::where("empresa_id", $request->empresa)->where("estado","A")->first();
            //validar si el convenio existe y esta activo, si la persona es tipo "A" y si su estado en la empresa es "A".
            if ($convenio != null && $request->tipo == "Afiliado" && $request->estado == "A") {
                $persona = Personas::where("identificacion", $request->identificacion)->first();
                $existe=0;
                if($persona!=null) {
                    $detalle_e = DetalleProdutos::join('tarjetas', 'detalle_produtos.numero_tarjeta', 'tarjetas.numero_tarjeta')
                        ->where('tarjetas.persona_id', $persona->id)
                        ->whereraw('(tarjetas.estado=? OR tarjetas.estado=?)', ['A','C'])
                        ->where('detalle_produtos.convenio_id',$convenio->id)
                        ->select('detalle_produtos.*')
                        ->get();
                    if($detalle_e!=null)
                        $existe=1;
                }
                //si no existe ya un producto de cupo rotativo para esa persona, con esa empresa
                if($existe==0) {
                    $num_tarjeta = $request->numero_tarjeta;
                    while (strlen($num_tarjeta) < 6) {
                        $num_tarjeta = "0" . $num_tarjeta;
                    }
                    $result = $this->crearTarjeta($num_tarjeta, Tarjetas::$ESTADO_TARJETA_CREADA, Tarjetas::$CODIGO_SERVICIO_AFILIADO);
                    //consulta si existe la persona, SiNO, la inserta.
                    $persona = Personas::where("identificacion", $request->identificacion)->first();
                    $newdireccion = $request->vp . " " . $request->nv . " " . $request->n1 . " " . $request->n2 . " " . $request->complemento;
                    $nacimiento = $request->nacimiento;
                    $nacimiento = substr($nacimiento, 0, 10);
                    $porciones = explode("-", $nacimiento);
                    $anon = $porciones[0];
                    $mesn = $porciones[1];
                    $dian = $porciones[2];
                    $nacimiento = $dian . "/" . $mesn . "/" . $anon;
                    $nacimiento = Carbon::createFromFormat("d/m/Y", $nacimiento);
                    $nacimiento = $nacimiento->toDateString();

                    if ($persona == null) //no existe la persona
                    {
                        $result = $this->crearPersona($request->identificacion, $request->nombres, $request->apellidos, $request->email, $request->sexo, $nacimiento, $request->telefono, $request->celular, $request->municipio_codigo, Personas::$TIPO_PERSONA_AFILIADO, $request->latitud, $request->longitud, $newdireccion);
                    } else //se actualizan los datos
                    {
                        $persona->nombres = strtoupper(trim($request->nombres));
                        $persona->apellidos = strtoupper(trim($request->apellidos));
                        $persona->tipo_persona = Personas::$TIPO_PERSONA_AFILIADO;
                        $persona->email = $request->email;
                        $persona->sexo = $request->sexo;
                        $persona->fecha_nacimiento = $nacimiento;
                        $persona->telefono = $request->telefono;
                        $persona->celular = $request->celular;
                        $persona->municipio_codigo = $request->municipio_codigo;
                        $persona->latitud = $request->latitud;
                        $persona->longitud = $request->longitud;
                        $persona->direccion = $newdireccion;
                        $persona->save();
                        $result['estado'] = true;
                        $result['mensaje'] = 'La información de la persona ha sido actualizada satisfactoriamente';
                    }
                    $persona = Personas::where("identificacion", $request->identificacion)->first();
                    if ($persona != null) // persona creada
                    {
                        //dd($persona);
                        $tarjeta = Tarjetas::where("numero_tarjeta", $num_tarjeta)->update(['persona_id' => $persona->id]);

                        $monto = str_replace(".", "", $request->monto);
                        //insertar el detalle del producto
                        $result = $this->crearDetalleProd($num_tarjeta, $monto, $convenio->id, Tarjetas::$ESTADO_TARJETA_INACTIVA);
                        if ($result['estado'] == true) {
                            //insertar la transaccion
                            $detalle_id = $result['detalle_id'];
                            $result = $this->transacciones($num_tarjeta, Tarjetas::$CODIGO_SERVICIO_AFILIADO, $detalle_id, $monto);// num_tarjeta,servicio_codigo=B,detalle_id,monto
                            if ($result['estado'] == true) {
                                $result['mensaje'] = 'La tarjeta de cupo rotativo ha sido creada';//. $exception->getMessage()
                                \DB::commit();
                            } else {
                                //$result['estado'] = false;
                                //$result['mensaje'] = 'Las tarjetas bono no pudieron ser creadas';//. $exception->getMessage()
                                \DB::rollBack();
                            }
                        }
                    } else {
                        $result['estado'] = false;
                        $result['mensaje'] = 'No es posible crear la tarjeta de cupo rotativo, la persona no pudo ser creada';//. $exception->getMessage()
                        \DB::rollBack();
                    }
                }
                else{
                    $result['estado'] = false;
                    $result['mensaje'] = 'El afiliado ya tiene una tarjeta asociada al cupo rotativo con esa empresa';//. $exception->getMessage()
                    \DB::rollBack();
                }

            } else {
                $result['estado'] = false;
                $result['mensaje'] = 'No es posible crear la tarjeta de cupo rotativo, el convenio de la empresa y el estado del trabajador deben estar activos';//. $exception->getMessage()
                \DB::rollBack();
            }
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la tarjeta de cupo rotativo ' . $exception->getMessage();//. $exception->getMessage()
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
            $id_transaccion = "";
            $paga = PagaPlastico::where("pagaplastico", '1')->where("estado", 'A')->where("servicio_codigo", $tipo_servicio)->first();
            if ($paga != null) {//si paga plastico
                $vtarjeta = ValorTarjeta::where("estado", 'A')->first();
                //crear transaccion
                $transaccion = new Transaccion();
                $transaccion_ant = Transaccion::max('numero_transaccion'); //consulta maximo valor de transaccion
                if ($transaccion_ant == null)//no hay transacciones
                {
                    $next = "0000000001";
                } else {
                    $next = ($transaccion_ant) + 1;
                    while (strlen($next) < 10) {
                        $next = "0" . $next;
                    }
                }
                $transaccion->numero_transaccion = $next;
                $transaccion->numero_tarjeta = $num_tarjeta;
                $transaccion->tipo = Transaccion::$TIPO_ADMINISTRATIVO;
                $transaccion->fecha = Carbon::now();
                $transaccion->save();
                $id_transaccion = $transaccion->id;
                //crear htransaccion
                $htransaccion = new HEstadoTransaccion();
                $htransaccion->transaccion_id = $transaccion->id;
                $htransaccion->estado = HEstadoTransaccion::$ESTADO_ACTIVO;
                $htransaccion->fecha = Carbon::now();
                $htransaccion->save();
                //crear detalle_transaccion
                $detallet = new DetalleTransaccion();
                $detallet->transaccion_id = $transaccion->id;
                $detallet->detalle_producto_id = $detalle_id;
                $detallet->valor = $vtarjeta->valor;
                $detallet->descripcion = DetalleTransaccion::$DESCRIPCION_PLASTICO;
                $detallet->save();
            }
            $result['estado'] = true;
            $result['mensaje'] = 'La transacción ha sido creada';//. $exception->getMessage()
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible registrar la transacción' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }
    //metodo para crear la tarjeta ( si no existe) con su historia y servicio asociado.
    public function crearTarjeta($num_tarjeta, $name_estado, $servicio_codigo)
    {
        $result = [];
        try {
            $tarjeta = Tarjetas::where("numero_tarjeta", $num_tarjeta)->first();
            $result['estado'] = true;
            $result['mensaje'] = 'El servicio de la tarjeta ya existia';
            if ($tarjeta == null) //no existe la tarjeta
            {
                $tarjetas = new Tarjetas();
                $tarjetas->numero_tarjeta = $num_tarjeta;
                $validator = \Validator::make(['numero_tarjeta' => $num_tarjeta], [
                    'numero_tarjeta' => 'required|unique:tarjetas'
                ]);
                if ($validator->fails()) {
                    return $validator->errors()->all();
                }
                $ultimos = substr($tarjetas->numero_tarjeta, -4);
                $tarjetas->password = Encript::encryption($ultimos);
                $tarjetas->estado = $name_estado;
                $tarjetas->save();
                $tarjeta = $tarjetas;
                $result['estado'] = true;
                $result['mensaje'] = 'La tarjeta ha sido creada satisfactoriamente';
            }
            $tservicio = TarjetaServicios::where("numero_tarjeta", $num_tarjeta)->where("servicio_codigo", $servicio_codigo)->first();

            if ($tservicio == null)//no existe
            {
                $result = TarjetasController::crearHtarjeta($tarjeta, $name_estado, $servicio_codigo);
                $result = TarjetasController::crearTarjetaSer($tarjeta, Tarjetas::$ESTADO_TARJETA_INACTIVA, $servicio_codigo);
                $result['estado'] = true;
                $result['mensaje'] = 'El servicio de la tarjeta ha sido creado satisfactoriamente';
            }
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la tarjeta' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }
    public function crearPersona($iden, $nom, $ape, $email, $sexo, $nacimiento, $telefono, $celular, $municipio, $tipo, $latitud, $longitud, $dir)
    {
        $result = [];
        try {
            $persona = new Personas();
            $persona->identificacion = $iden;
            $persona->nombres = strtoupper(trim($nom));
            $persona->apellidos = strtoupper(trim($ape));
            $persona->tipo_persona = $tipo;
            $persona->email = $email;
            $persona->sexo = $sexo;
            $persona->fecha_nacimiento = $nacimiento;
            $persona->telefono = $telefono;
            $persona->celular = $celular;
            $persona->municipio_codigo = $municipio;
            $persona->latitud = $latitud;
            $persona->longitud = $longitud;
            $persona->direccion = $dir;
            $persona->save();
            $result['estado'] = true;
            $result['mensaje'] = 'La persona ha sido ingresada satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear la persona' . $exception->getMessage();//. $exception->getMessage()
            dd($result['mensaje']);
            \DB::rollBack();
        }
        return $result;
    }

    public function crearDetalleProd($num_tarjeta, $monto, $convenio_id, $estado)
    {
        // (SOLO DEBE HABER UN CONVENIO ACTIVO)
        $result = [];
        try {
            $detalle = DetalleProdutos::where("numero_tarjeta", $num_tarjeta)->where("convenio_id", $convenio_id)->first();
            if ($detalle != null) {
                $result['estado'] = false;
                $result['mensaje'] = 'Ya existe un detalle de producto asociado al numero de tarjeta y al convenio';//. $exception->getMessage()
                \DB::rollBack();
            } else {
                $detalle = new DetalleProdutos();
                $detalle->numero_tarjeta = $num_tarjeta;
                $detalle->fecha_cracion = Carbon::now();
                $detalle->monto_inicial = $monto;
                $detalle->convenio_id = $convenio_id;
                $detalle->user_id = \Auth::User()->id;
                $detalle->estado = $estado;
                $detalle->save();
                $result['estado'] = true;
                $result['mensaje'] = 'El detalle del producto ha sido creado satisfactoriamente';
                $result['detalle_id'] = $detalle->id;
            }
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible crear el detalle del producto' . $exception->getMessage();//. $exception->getMessage()
            \DB::rollBack();
        }
        return $result;
    }
    public function getNombre(Request $request)
    {
        $persona = personas::where("identificacion", $request->identificacion)->first();
        return $persona;
    }
    /**
     * METODO para consultar datos deL AFILIADO asociado con la empresa aportante con WS de SevenAs
     * @return mixed returna una objeto de tipo persona, con los datos consultados (si existe) o vacia (si no la encontro)
     */
    public function consultarTrabajador(Request $request)
    {
        $empresa = Empresas::where('id',$request->empresa)->first();
        $persona = new Personas();
        $url = "http://192.168.0.188/WebServices4/WConsultasCajas.asmx?wsdl";
        try {
            $client = new SoapClient($url, array("trace" => 1, "exception" => 0));
            $result = $client->ConsultaTrabajadorPorAportante( [ "emp_codi" => 406, "tip_codi" => $empresa->tipo_documento, "apo_coda" => $empresa->nit, "tip_codt" => 2, "afi_docu" => $request->identificacion ] );// 2  "41676254"
            if(isset($result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado))
            {
                //dd(" el tamanio del vector es ".count($result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado));
                $tami=count($result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado);
                $posi=$tami-1;
                //si solo hay un resultado (solo una inscripcion del trabajador a esa empresa
                if($tami==1) {
                    //dd("existe");
                    $persona->categoria = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_cate;
                    $persona->apellidos = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_ape1;
                    $persona->apellidos .= " ";
                    $persona->apellidos .= $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_ape2;
                    $persona->nombres = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_nom1;
                    $persona->nombres .= " ";
                    $persona->nombres .= $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_nom2;
                    $municipio_name = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Mun_nomb;
                    $depto_name = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Dep_nomb;
                    $depto_name = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Dep_nomb;
                    $persona->direccion = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Loc_nomb;
                    $persona->direccion .= " Barrio ";
                    $persona->direccion .= $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Bar_nomb;
                    $persona->direccion .= " ";
                    $persona->direccion .= $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_dire;
                    $persona->email = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_mail;
                    $persona->sexo = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_gene;
                    $persona->fecha_nacimiento = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_fecn;
                    $persona->telefono = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_tele;
                    if(isset($result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_celu))
                        $persona->celular = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Afi_celu;
                    else
                        $persona->celular = "0";
                    $persona->estado = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Tra_esta;// si trae D no esta activo, debe ser A
                    $persona->salario = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado->Tra_salb;
                    $persona->tipo = "A";

                    //buscar departamento
                    $depto = Departamentos::where("descripcion", $depto_name)->first();
                    //dd($depto);
                    if ($depto != null) {
                        $muni = Municipios::where("descripcion", $municipio_name)->where("departamento_codigo", $depto->codigo)->first();
                        $persona->municipio_codigo = (string)$muni->codigo;
                        $persona->departamento_codigo = $depto->codigo;
                    } else {
                        /* $muni = Municipios::where("descripcion", "VILLAVICENCIO")->where("departamento_codigo", $depto->codigo);
                         $empresa->municipio_codigo = $muni->codigo;
                         $empresa->departamento_codigo = $muni->departamento_codigo;*/
                    }
                }
                else //si  hay varios resultados se toma el ultimo(varias inscripciones del trabajador a esa empresa - historial)
                {
                    //dd($result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Tra_esta);
                    $persona->categoria = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_cate;
                    $persona->apellidos = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_ape1;
                    $persona->apellidos .= " ";
                    $persona->apellidos .= $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_ape2;
                    $persona->nombres = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_nom1;
                    $persona->nombres .= " ";
                    $persona->nombres .= $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_nom2;
                    $municipio_name = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Mun_nomb;
                    $depto_name = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Dep_nomb;
                    $depto_name = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Dep_nomb;
                    $persona->direccion = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Loc_nomb;
                    $persona->direccion .= " Barrio ";
                    $persona->direccion .= $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Bar_nomb;
                    $persona->direccion .= " ";
                    $persona->direccion .= $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_dire;
                    $persona->email = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_mail;
                    $persona->sexo = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_gene;
                    $persona->fecha_nacimiento = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_fecn;
                    $persona->telefono = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_tele;
                    if(isset($result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_celu))
                        $persona->celular = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Afi_celu;
                    else
                        $persona->celular = "0";
                    $persona->estado = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Tra_esta;// si trae D no esta activo, debe ser A
                    $persona->salario = $result->ConsultaTrabajadorPorAportanteResult->Afiliados->TOAfiliado[$posi]->Tra_salb;
                    $persona->tipo = "A";
                    //buscar departamento
                    $depto = Departamentos::where("descripcion", $depto_name)->first();
                    //dd($depto);
                    if ($depto != null) {
                        $muni = Municipios::where("descripcion", $municipio_name)->where("departamento_codigo", $depto->codigo)->first();
                        $persona->municipio_codigo = (string)$muni->codigo;
                        $persona->departamento_codigo = $depto->codigo;
                    } else {
                        /* $muni = Municipios::where("descripcion", "VILLAVICENCIO")->where("departamento_codigo", $depto->codigo);
                         $empresa->municipio_codigo = $muni->codigo;
                         $empresa->departamento_codigo = $muni->departamento_codigo;*/
                    }
                }
            }
            else
            {
                $persona->tipo="T";
            }
        } catch ( SoapFault $e ) {
            echo $e->getMessage();
        }
        return $persona; //->razon_social
    }

}
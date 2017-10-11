<?php

namespace creditocofrem\Http\Controllers;

use Carbon\Carbon;
use creditocofrem\DetalleProdutos;
use creditocofrem\DetalleTransaccion;
use creditocofrem\HEstadoTransaccion;
use creditocofrem\Tarjetas;
use creditocofrem\Transaccion;
use Illuminate\Http\Request;
use function MongoDB\BSON\toJSON;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class ReportesController extends Controller
{
    public function viewReportePrimeraVez()
    {
        return view('reportes.primeravez.primeravez');
    }

    public function resultadoPrimeravez(Request $request)
    {
        $rangos = explode(" - ", $request->rango);
        $transacciones = Transaccion::whereBetween('fecha', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
            ->where('tipo', 'C')
            ->groupBy('numero_tarjeta')
            ->select('numero_tarjeta')
            ->get();
        $arrayTarjetas = [];
        foreach ($transacciones as $transaccione) {
            array_push($arrayTarjetas, $transaccione->numero_tarjeta);
        }
        $transacciones2 = Transaccion::whereDate('fecha', '<', Carbon::createFromFormat("d/m/Y", $rangos[0])->addDays(-1))
            ->whereIn('numero_tarjeta', $arrayTarjetas)
            ->where('tipo', 'C')
            ->groupBy('transacciones.numero_tarjeta')
            ->select('transacciones.numero_tarjeta')
            ->get();
        $arrayTarjetas2 = [];
        foreach ($transacciones2 as $transaccion) {
            array_push($arrayTarjetas2, $transaccion->numero_tarjeta);
        }
        $arrayFinal = [];
        foreach ($arrayTarjetas as $arrayTarjeta) {
            if (!in_array($arrayTarjeta, $arrayTarjetas2)) {
                array_push($arrayFinal, $arrayTarjeta);
            }
        }
        $arrayIds = [];
        foreach ($arrayFinal as $item) {
            $id = Transaccion::where('numero_tarjeta', $item)
                ->where('tipo', 'C')
                ->orderBy('fecha', 'asc')
                ->select('id')
                ->first();
            array_push($arrayIds, $id->id);
        }
        $transacciones = Transaccion::whereIn('id', $arrayIds)
            ->where('tipo', 'C')
            ->get();
        $rango =['fecha1'=> $rangos[0], 'fecha2'=>$rangos[1]];
        return view('reportes.primeravez.resultadoprimeravez', compact('transacciones', 'rango'));
    }

    public function exportarPdfPrimeravez(Request $request)
    {
        //dd($request->all());
        $transacciones = Transaccion::whereBetween('fecha', [Carbon::createFromFormat("d/m/Y", $request->fecha1), Carbon::createFromFormat("d/m/Y", $request->fecha2)])
            ->where('tipo', 'C')
            ->groupBy('numero_tarjeta')
            ->select('numero_tarjeta')
            ->get();
        $arrayTarjetas = [];
        foreach ($transacciones as $transaccione) {
            array_push($arrayTarjetas, $transaccione->numero_tarjeta);
        }
        $transacciones2 = Transaccion::whereDate('fecha', '<', Carbon::createFromFormat("d/m/Y", $request->fecha1)->addDays(-1))
            ->whereIn('numero_tarjeta', $arrayTarjetas)
            ->where('tipo', 'C')
            ->groupBy('transacciones.numero_tarjeta')
            ->select('transacciones.numero_tarjeta')
            ->get();
        $arrayTarjetas2 = [];
        foreach ($transacciones2 as $transaccion) {
            array_push($arrayTarjetas2, $transaccion->numero_tarjeta);
        }
        $arrayFinal = [];
        foreach ($arrayTarjetas as $arrayTarjeta) {
            if (!in_array($arrayTarjeta, $arrayTarjetas2)) {
                array_push($arrayFinal, $arrayTarjeta);
            }
        }
        $arrayIds = [];
        foreach ($arrayFinal as $item) {
            $id = Transaccion::where('numero_tarjeta', $item)
                ->where('tipo', 'C')
                ->orderBy('fecha', 'asc')
                ->select('id')
                ->first();
            array_push($arrayIds, $id->id);
        }
        $transacciones = Transaccion::whereIn('id', $arrayIds)
            ->where('tipo', 'C')
            ->get();
        $data = ['transacciones'=>$transacciones, 'rango'=>$request->fecha1." - ".$request->fecha2];
        $pdf = \PDF::loadView('reportes.primeravez.pdfprimeravez', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('primeravez.pdf');
    }
    /**
     * INICIA REPORTE SALDOS VENCIDOS
     * LLamar a la vista de consulta de saldos vencidos
     */
    public function viewSaldosvencidos()
    {
        return view('reportes.saldosvencidos.saldosvencidos');
    }
    /*
     * Funcion consultar saldos vencidos
     * - segun el tipo: consultar tarjetas bono, regalo o las dos.
     * -
     */
    public function consultarSaldosVencidos(Request $request)
    {
        $rangos = explode(" - ", $request->rango);
        $tiposervicio = $request->tipo;
        $resultadob=array();
        $resultador=array();
        if($tiposervicio=="B" || $tiposervicio=="T")
        {
            $detallesb=DetalleProdutos::join('tarjeta_servicios','detalle_produtos.numero_tarjeta','tarjeta_servicios.numero_tarjeta')
                ->whereBetween('detalle_produtos.fecha_vencimiento', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                ->where('tarjeta_servicios.servicio_codigo',Tarjetas::$CODIGO_SERVICIO_BONO)
                ->select('detalle_produtos.*')
                ->get();
            $listaDetallesb=[];
            foreach ($detallesb as $detalle)
            {
                $gasto=0;
                $dtransacciones=DetalleTransaccion::where('detalle_producto_id',$detalle->id)->get();
                foreach ($dtransacciones as $dtransaccione)
                {
                    $htransaccion=DB::table('h_estado_transacciones')->where('transaccion_id',$dtransaccione->transaccion_id)->orderBy('id', 'desc')->first();
                    if($htransaccion->estado==HEstadoTransaccion::$ESTADO_ACTIVO)
                        $gasto+=$dtransaccione->valor;
                }
                if($gasto<$detalle->monto_inicial) //si hay saldo
                {
                    $sobrante=$detalle->monto_inicial-$gasto;
                    $resultadob[]=array('numero_tarjeta' => $detalle->numero_tarjeta,
                        'monto_inicial' => $detalle->monto_inicial,
                        'sobrante' => $sobrante,
                        'fecha_activacion' => $detalle->fecha_activacion,
                        'fecha_vencimiento' => $detalle->fecha_vencimiento,
                    );
                }
            }
        }
        if($tiposervicio=="R" || $tiposervicio=="T")
        {
            $detallesr=DetalleProdutos::join('tarjeta_servicios','detalle_produtos.numero_tarjeta','tarjeta_servicios.numero_tarjeta')
                ->whereBetween('detalle_produtos.fecha_vencimiento', [Carbon::createFromFormat("d/m/Y", $rangos[0]), Carbon::createFromFormat("d/m/Y", $rangos[1])])
                ->where('tarjeta_servicios.servicio_codigo',Tarjetas::$CODIGO_SERVICIO_REGALO)
                ->select('detalle_produtos.*')
                ->get();
            foreach ($detallesr as $detalle)
            {
                $gasto=0;
                $dtransacciones=DetalleTransaccion::where('detalle_producto_id',$detalle->id)->get();
                foreach ($dtransacciones as $dtransaccione)
                {
                    $htransaccion=DB::table('h_estado_transacciones')->where('transaccion_id',$dtransaccione->transaccion_id)->orderBy('id', 'desc')->first();
                    if($htransaccion->estado==HEstadoTransaccion::$ESTADO_ACTIVO)
                        $gasto+=$dtransaccione->valor;
                }
                if($gasto<$detalle->monto_inicial) //si hay saldo
                {
                    $sobrante=$detalle->monto_inicial-$gasto;
                    $resultador[]=array('numero_tarjeta' => $detalle->numero_tarjeta,
                        'monto_inicial' => $detalle->monto_inicial,
                        'sobrante' => $sobrante,
                        'fecha_activacion' => $detalle->fecha_activacion,
                        'fecha_vencimiento' => $detalle->fecha_vencimiento,
                    );
                }
            }
        }
        $rango =['fecha1'=> $rangos[0], 'fecha2'=>$rangos[1]];
        return view('reportes.saldosvencidos.parcialresultadosaldosvencidos', compact('resultadob', 'resultador', 'rango','tiposervicio'));
    }
    /*
     * FUNCION GENERAR PDF para Saldos Vencidos
     * Exporta en formato pdf, los resultados
     * de tarjetas bono y/o regalo que se vencieron y quedaron con saldo
     */
    public function pdfSaldosVencidos(Request $request)
    {
        $data = ['resultadob'=>$request->resultadob, 'resultador'=>$request->resultador, 'tiposervicio'=>$request->tiposervicio, 'rango'=>$request->fecha1." - ".$request->fecha2];
        $pdf = \PDF::loadView('reportes.saldosvencidos.pdfsaldosvencidos', $data);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('saldosvencidos.pdf');
    }
    /*
     * FUNCION GENERAR EXCEL para saldos vencidos
     * Exporta a excel, los resultados de las tarjetas
     * bono y/o regalo que se vencieron y quedaron con saldo
     */
    public function excelSaldosVencidos(Request $request)
    {
        \Excel::create('ExcelSaldosVencidos', function($excel) use($request) {
            $resultadob = $request->resultadob;
            $resultador = $request->resultador;
            $fecha1= $request->fecha1;
            $fecha2= $request->fecha2;
            $rango=$fecha1." - ".$fecha2;
            $tiposervicio = $request->tiposervicio;

            $excel->sheet('SaldosVencidos', function($sheet) use($resultadob, $resultador, $rango, $tiposervicio) {
                $data = ['resultadob'=>$resultadob, 'resultador'=>$resultador, 'tiposervicio'=>$tiposervicio, 'rango'=>$rango];
                $sheet->loadView('reportes.saldosvencidos.excelsaldosvencidos',$data);
               /* $sheet->row(1, array('REPORTE DE SALDOS VENCIDOS'));
                $sheet->row(1, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $sheet->row(2, array('TARJETAS BONO'));
                $sheet->row(3, array('Numero tarjeta', 'Monto incial', 'Sobrante', 'Fecha activacion', 'Fecha vencimiento'));
                $fila = 4;
                if (sizeof($resultadob) > 0) {
                    foreach ($resultadob as $miresul) {
                        $sheet->row($fila, array($miresul["numero_tarjeta"], $miresul["monto_inicial"], $miresul["sobrante"], $miresul["fecha_activacion"], $miresul["fecha_vencimiento"]));
                        $fila++;
                    }
                }
                else
                    $sheet->row($fila, array('No hay resultados'));
                $sheet->row($fila,array('TARJETAS REGALO'));
                $fila++;
                if (sizeof($resultador) > 0) {
                    $sheet->row($fila, array('Numero tarjeta', 'Monto incial', 'Sobrante', 'Fecha activacion', 'Fecha vencimiento'));
                    $fila++;
                    foreach ($resultador as $miresul) {
                        $sheet->row($fila, array($miresul["numero_tarjeta"], $miresul["monto_inicial"], $miresul["sobrante"], $miresul["fecha_activacion"], $miresul["fecha_vencimiento"]));
                        $fila++;
                    }
                }
                else
                    $sheet->row($fila, array('No hay resultados'));
               */
            });

        })->export('xls');
    }
    /*
     * FINALIZA REPORTES SALDOS VENCIDOS
     */
}

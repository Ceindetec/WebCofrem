<?php

namespace creditocofrem\Http\Controllers;

use Carbon\Carbon;
use creditocofrem\Transaccion;
use Illuminate\Http\Request;
use function MongoDB\BSON\toJSON;

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
        $data = ['transacciones'=>$transacciones];
        $pdf = \PDF::loadView('reportes.primeravez.pdfprimeravez', $data);
        return $pdf->download('primeravez.pdf');
    }
}

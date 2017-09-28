<?php

namespace creditocofrem\Http\Controllers;

use Illuminate\Http\Request;

class ContratosController extends Controller
{
    /**
     * trae la vista donde se listan todas las empresas de la red cofrem
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('empresas.contratos.listacontratos');
    }
}

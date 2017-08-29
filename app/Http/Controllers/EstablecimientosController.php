<?php

namespace creditocofrem\Http\Controllers;

use Illuminate\Http\Request;

class EstablecimientosController extends Controller
{
    public function index(){
        return view('establecimientos.listaestablecimientod');
    }
}

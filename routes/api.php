<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('comunicacion','WebApiController@comunicacion');
Route::post('terminal','WebApiController@validarTerminal');
Route::post('clavesucursal','WebApiController@validarClaveSucursal');
Route::post('claveterminal','WebApiController@validarClaveTerminal');
Route::post('asignaID','WebApiController@asignaID');
Route::post('getservicios','WebApiController@getServicios');
Route::post('consultarclavetarjeta','WebApiController@consultarClaveTarjeta');
Route::post('actulizarclavetarjeta','WebApiController@actulizarClaveTarjeta');






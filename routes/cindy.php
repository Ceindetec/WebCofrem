<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware' => 'auth'], function () {

    Route::group(['middleware' => 'roleshinobi:admin'], function (){

        /*INICIO EMPRESAS*/

        route::get('empresas','EmpresasController@index')->name('empresas');
        route::get('empresas/gridempresas','EmpresasController@gridEmpresas')->name('gridempresas');
        route::get('empresas/crear','EmpresasController@viewCrearEmpresa')->name('empresa.crear');
        route::post('empresas/crear','EmpresasController@crearEmpresa')->name('empresa.crearp');
        /*
        route::get('empresas/editar/{id}','EmpresasController@viewEditarEmpresa')->name('empresa.editar');
        route::post('emmpresas/editar','EmpresasController@editarEmpresa')->name('empresa.editarp');

        */



        /*FINALIZA EMPRESAS*/

    });
});

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nit')->unique();
            $table->string('razon_social');
            $table->string('representante_legal');
            $table->string('municipio_codigo')->index();
            $table->foreign('municipio_codigo')->references('codigo')->on('municipios')->onDelete('cascade');
            $table->string('email')->unique();
            $table->string('telefono');
            $table->string('celular')->nullable();
            $table->string('direccion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresas');
    }
}

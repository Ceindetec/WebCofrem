<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCempresaTpagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cempresa_tpagos', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->Integer('tiempo_pagos_id')->unsigned();
            $table->bigInteger('contrato_empresas_id')->unsigned();
            $table->timestamps();

            $table->foreign('tiempo_pagos_id')->references('id')->on('tiempo_pagos')->onDelete('cascade');
            $table->foreign('contrato_empresas_id')->references('id')->on('contratos_emprs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cempresa_tpagos');
    }
}

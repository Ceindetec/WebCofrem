<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTarjetaServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarjeta_servicios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('numero_tarjeta')->index();
            $table->foreign('numero_tarjeta')->references('numero_tarjeta')->on('tarjetas')->onDelete('cascade');
            $table->string('servicio_codigo')->index();
            $table->foreign('servicio_codigo')->references('codigo')->on('servicios')->onDelete('cascade');
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
        Schema::dropIfExists('tarjeta_servicios');
    }
}

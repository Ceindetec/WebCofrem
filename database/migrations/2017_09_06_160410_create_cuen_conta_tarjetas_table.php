<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCuenContaTarjetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuen_conta_tarjetas', function (Blueprint $table) {
            $table->increments('id')->nochece();
            $table->string('tarjeta_codigo',1)->index();
            $table->foreign('tarjeta_codigo')->references('codigo')->on('tipo_tarjetas')->onDelete('cascade');
            $table->string('municipio_codigo')->index();
            $table->foreign('municipio_codigo')->references('codigo')->on('municipios')->onDelete('cascade');
            $table->integer('cuenta');
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
        Schema::dropIfExists('cuen_conta_tarjetas');
    }
}

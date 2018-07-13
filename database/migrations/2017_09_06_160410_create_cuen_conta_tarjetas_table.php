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
            $table->string('servicio_codigo',10)->index();
            $table->string('municipio_codigo')->index();
            $table->integer('cuenta');
            $table->enum('estado',['A','I'])->default('I');
            $table->timestamps();

            $table->foreign('servicio_codigo')->references('codigo')->on('servicios')->onDelete('cascade');
            $table->foreign('municipio_codigo')->references('codigo')->on('municipios')->onDelete('cascade');
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

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminisTarjetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adminis_tarjetas', function (Blueprint $table) {
            $table->increments('id')->nocache();
            $table->integer('porcentaje');
            $table->enum('estado',['A','I'])->default('A');
            $table->string('servicio_codigo')->index();
            $table->timestamps();

            $table->foreign('servicio_codigo')->references('codigo')->on('servicios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adminis_tarjetas');
    }
}

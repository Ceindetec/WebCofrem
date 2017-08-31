<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHtarjetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('htarjetas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->primary('id');
            $table->string('motivo');
            $table->enum('estado',['A','I','P','C']);//activo, inactivo, pendiente, creado
            $table->date('fecha');
            $table->integer('user_id')->unsigned()-> index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->time('hora');
            $table->biginteger('tarjetas_id')->unsigned()-> index();
            $table->foreign('tarjetas_id')->references('id')->on('tarjetas')->onDelete('cascade');
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
        Schema::dropIfExists('htarjetas');
    }
}

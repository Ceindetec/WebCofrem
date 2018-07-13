<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHEstadoTransaccionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_estado_transacciones', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->biginteger('transaccion_id')->unsigned()-> index();
            $table->enum("estado", ["A","I"]); // A -> Activo , Inactivo
            $table->date("fecha");
            $table->timestamps();

            $table->foreign('transaccion_id')->references('id')->on('transacciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('h_estado_transacciones');
    }
}

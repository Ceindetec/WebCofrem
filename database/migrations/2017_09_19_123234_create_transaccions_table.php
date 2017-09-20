<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransaccionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('transacciones', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->bigInteger('numero_transaccion');
            $table->string('numero_tarjeta');
            $table->string('codigo_terminal');
            $table->bigInteger('sucursal_id')->unsigned();
            $table->enum("tipo", ["A", "C"]);//C : Consumo , A : Administrativo
            $table->integer("valor");
            $table->date("fecha");
            $table->timestamps();
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('transacciones');
    }
}

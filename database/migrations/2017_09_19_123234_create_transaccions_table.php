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
            $table->bigInteger('numero_transaccion',10);
            $table->string('numero_tarjeta');
            $table->string('codigo_terminal')->nullable();
            $table->bigInteger('sucursal_id')->nullable()->unsigned();
            $table->enum("tipo", ["A", "C"]);//C : Consumo , A : Administrativo
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

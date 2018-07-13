<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDuplicadoProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duplicado_productos', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->string('oldproducto');
            $table->string('newproducto');
            $table->date('fecha');
            $table->timestamps();

            $table->foreign('oldproducto')->references('numero_tarjeta')->on('tarjetas')->onDelete('cascade');
            $table->foreign('newproducto')->references('numero_tarjeta')->on('tarjetas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('duplicado_productos');
    }
}

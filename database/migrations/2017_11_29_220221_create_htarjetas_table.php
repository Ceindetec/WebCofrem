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
            $table->bigIncrements('id')->nocache();
            $table->primary('id');
            $table->integer('user_id')->unsigned()-> index();
            $table->biginteger('tarjetas_id')->unsigned()-> index();
            $table->bigInteger('detalle_producto_id')->unsigned()->nullable()->index();
            $table->string('servicio_codigo')->nullable()->index();
            $table->string('motivo');
            $table->enum('estado',['A','I','P','C','N'])->default('C');//activo, inactivo, pendiente, creado, anulado
            $table->date('fecha');
            $table->string('nota')->nullable();
            $table->timestamps();


            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tarjetas_id')->references('id')->on('tarjetas')->onDelete('cascade');
            $table->foreign('servicio_codigo')->references('codigo')->on('servicios')->onDelete('cascade');
            $table->foreign('detalle_producto_id')->references('id')->on('detalle_produtos')->onDelete('cascade');
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

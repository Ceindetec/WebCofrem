<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleTransaccionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_transacciones', function (Blueprint $table) {
            $table->bigIncrements('id')->nocache();
            $table->biginteger('transaccion_id')->unsigned()-> index();
            $table->biginteger('detalle_producto_id')->nullable()->unsigned();
            $table->integer('valor');
            $table->enum("descripcion",["A","P","C"]); // A -> Administracion , P -> Plastico , C -> Consumo
            $table->timestamps();
            $table->foreign('transaccion_id')->references('id')->on('transacciones')->onDelete('cascade');
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
        Schema::dropIfExists('detalle_transacciones');
    }
}

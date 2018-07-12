<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterHtarjetasProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('htarjetas', function (Blueprint $table) {
            $table->bigInteger('detalle_producto_id')->unsigned()->nullable()->index();
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
        //
        Schema::dropIfExists('detalle_producto_id');
    }
}

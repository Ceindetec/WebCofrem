<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RomeveTipoTarjetaTableTarjetas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->dropColumn('tarjeta_codigo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->string('tarjeta_codigo')->index();
            $table->foreign('tarjeta_codigo')->references('codigo')->on('tipo_tarjetas')->onDelete('cascade');
        });
    }
}

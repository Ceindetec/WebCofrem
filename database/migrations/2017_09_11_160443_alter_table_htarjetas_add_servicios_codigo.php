<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableHtarjetasAddServiciosCodigo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('htarjetas', function (Blueprint $table) {
            $table->string('servicio_codigo')->index();
            $table->foreign('servicio_codigo')->references('codigo')->on('servicios')->onDelete('cascade');
            $table->string('nota')->nullable();
            $table->dropColumn('hora');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('htarjetas', function (Blueprint $table) {
            $table->dropColumn('servicio_codigo');
            $table->dropColumn('nota');
            $table->date('hora');
        });
    }
}

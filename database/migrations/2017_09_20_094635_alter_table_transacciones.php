<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransacciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transacciones', function (Blueprint $table) {
            $table->string('numero_transaccion', 10)->change();
            $table->string('codigo_terminal')->nullable()->change();
            $table->bigInteger('sucursal_id')->nullable()->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transacciones', function (Blueprint $table) {
            $table->bigInteger('numero_transaccion')->change();
            $table->string('codigo_terminal')->change();
            $table->bigInteger('sucursal_id')->change();
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCuenContaTarjetaAddEstado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cuen_conta_tarjetas', function (Blueprint $table) {
            $table->string('servicio_codigo',10)->change();
            $table->enum('estado',['A','I'])->default('I');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cuen_conta_tarjetas', function (Blueprint $table) {
            $table->string('servicio_codigo',1)->change();
            $table->dropColumn('estado');
        });
    }
}

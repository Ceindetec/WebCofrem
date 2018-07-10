<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCuenContaTarjetasRenameTarjetaCodigo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cuen_conta_tarjetas', function (Blueprint $table) {
            $table->renameColumn('tarjeta_codigo','servicio_codigo');
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
            $table->renameColumn('servicio_codigo','tarjeta_codigo');
        });
    }
}

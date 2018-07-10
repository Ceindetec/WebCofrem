<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableConveniosEstaAddFechaFin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('convenios_estas', function(Blueprint $table) {
            $table->renameColumn('fecha', 'fecha_inicio');
            $table->date('fecha_fin')->default(\Carbon\Carbon::now());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('convenios_estas', function(Blueprint $table) {
            $table->renameColumn('fecha_inicio', 'fecha');
            $table->dropColumn('fecha_fin');
        });
    }
}

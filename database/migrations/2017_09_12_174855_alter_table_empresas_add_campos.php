<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableEmpresasAddCampos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos_emprs', function(Blueprint $table) {
            $table->date('fecha_creacion');
            $table->boolean('cons_mensual')->defaul(0);
            $table->integer('dias_consumo');
             });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contratos_emprs', function(Blueprint $table) {
            $table->dropColumn('fecha_creacion');
            $table->dropColumn('cons_mensual');
            $table->dropColumn('dias_consumo');

        });
    }
}

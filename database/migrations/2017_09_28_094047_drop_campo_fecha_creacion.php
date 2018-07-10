<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCampoFechaCreacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos_emprs', function (Blueprint $table) {
            $table->dropColumn('fecha_creacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contratos_emprs', function (Blueprint $table) {
            $table->date('fecha_creacion');
        });
    }
}

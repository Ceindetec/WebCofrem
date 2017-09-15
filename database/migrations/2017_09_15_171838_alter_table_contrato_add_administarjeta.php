<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableContratoAddAdministarjeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos_emprs', function (Blueprint $table) {
            $table->integer('adminis_tarjeta_id')->unsigned();
            $table->foreign('adminis_tarjeta_id')->references('id')->on('adminis_tarjetas')->onDelete('cascade');
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
            $table->dropColumn('adminis_tarjeta_id');
        });
    }
}

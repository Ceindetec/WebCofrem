<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateValorContratoContratos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos_emprs', function (Blueprint $table) {
            $table->double('valor_contrato', 8, 2)->required()->after('n_contrato');

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
            $table->dropColumn('valor_contrato');

        });
    }
}

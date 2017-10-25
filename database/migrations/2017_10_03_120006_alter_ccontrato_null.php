<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCcontratoNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos_emprs', function (Blueprint $table) {
            $table->dropColumn('dias_consumo');
        });
        Schema::table('contratos_emprs', function (Blueprint $table) {
            $table->string('dias_consumo',3)->nullable(true);
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
            $table->dropColumn('dias_consumo');
        });
        Schema::table('contratos_emprs', function (Blueprint $table) {
            $table->double('dias_consumo',10)->nullable(false)->change();
        });
    }

}

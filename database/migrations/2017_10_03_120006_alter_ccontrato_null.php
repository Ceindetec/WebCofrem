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
            $table->string('dias_consumo',3)->nullable(true)->change();
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
            $table->double('dias_consumo',10)->nullable(false)->change();
        });
    }

}

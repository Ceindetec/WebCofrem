<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTarjetasBonos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tarjetas_bonos', function(Blueprint $table) {
            $table->double('monto_inicial',15,2)->nullable();
            $table->double('monto_restante',15,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tarjetas_bonos', function(Blueprint $table) {
            $table->dropColumn('monto_inicial');
            $table->dropColumn('monto_restante');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEstadoTableTarjetas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->enum('estado',['A','I','C','N'])->default('C');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->enum('estado',['A','I','C'])->default('C');
        });
    }
}

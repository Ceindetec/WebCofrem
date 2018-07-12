<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEstadoTableHtarjetas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('htarjetas', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
        Schema::table('htarjetas', function (Blueprint $table) {
            $table->enum('estado',['A','I','P','C','N'])->default('C');
            $table->string('servicio_codigo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('htarjetas', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
        Schema::table('htarjetas', function (Blueprint $table) {
            $table->enum('estado',['A','I','P','C'])->default('C');
            $table->string('servicio_codigo')->nullable(false)->change();
        });
    }
}

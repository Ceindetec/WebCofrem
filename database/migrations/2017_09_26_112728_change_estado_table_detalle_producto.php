<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEstadoTableDetalleProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detalle_produtos', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
        Schema::table('detalle_produtos', function (Blueprint $table) {
            $table->enum('estado',['A','I','N'])->default('I');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_produtos', function (Blueprint $table) {
            $table->dropColumn('estado');
        });

        Schema::table('detalle_produtos', function (Blueprint $table) {
            $table->enum('estado',['A','I'])->default('I');
        });
    }
}

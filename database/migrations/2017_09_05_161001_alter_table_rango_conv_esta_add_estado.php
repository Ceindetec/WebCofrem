<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRangoConvEstaAddEstado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rango_conv_es', function(Blueprint $table) {
            $table->enum('estado',['A','I'])->default('A');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rango_conv_es', function(Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
}

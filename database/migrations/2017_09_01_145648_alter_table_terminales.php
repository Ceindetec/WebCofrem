<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTerminales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('terminales', function(Blueprint $table) {
            $table->string('numero_activo');
            $table->string('codigo',15)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('terminales', function(Blueprint $table) {
            $table->dropColumn('numero_activo');
            $table->string('codigo')->change();
        });
    }
}

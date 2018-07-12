<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDetalleProdutos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detalle_produtos', function (Blueprint $table) {
            $table->bigInteger('convenio_id')->unsigned()->nullable();
            $table->foreign('convenio_id')->references('id')->on('convenios_emps')->onDelete('cascade');
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
            $table->dropColumn('convenio_id');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulosEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('modulos_empresas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('modulos_id');
            $table->foreign('modulos_id')->references('id')->on('modulos');
            $table->unsignedInteger('empresas_id');
            $table->foreign('empresas_id')->references('id')->on('empresas');
            $table->boolean('solo_super')->default(0);
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('usuCrea');
            $table->foreign('usuCrea')->references('id')->on('users');
            $table->dateTime('created_at')->default('CURRENT_TIMESTAMP');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('modulos_empresas');
    }
}

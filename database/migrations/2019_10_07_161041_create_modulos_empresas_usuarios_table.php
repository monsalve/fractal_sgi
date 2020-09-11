<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulosEmpresasUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('modulos_empresas_usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('modulos_empresas_id');
            $table->foreign('modulos_empresas_id')->references('id')->on('modulos_empresas');
            $table->unsignedInteger('usuarios_id');
            $table->foreign('usuarios_id')->references('id')->on('users');
            $table->boolean('crear')->default(0);
            $table->boolean('leer')->default(0);
            $table->boolean('actualizar')->default(0);
            $table->boolean('anular')->default(0);
            $table->boolean('imprimir')->default(0);
            $table->unsignedInteger('usu_crea');
            $table->foreign('usu_crea')->references('id')->on('users');
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
        //Schema::dropIfExists('modulos_empresas_usuarios');
    }
}

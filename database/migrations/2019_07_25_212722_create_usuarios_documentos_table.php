<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuariosDocumentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_documentos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('documento', 100);
            $table->string('ruta', 60);
            $table->unsignedInteger('fk_id_empresas');
            $table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_id_usuarios_carpetas');
            $table->foreign('fk_id_usuarios_carpetas')->references('id')->on('usuarios_carpetas');
            $table->unsignedInteger('fk_id_cargos_documentos')->nullable();
            $table->foreign('fk_id_cargos_documentos')->references('id')->on('cargos_documentos');
            $table->unsignedInteger('fk_usuCrea_users');
            $table->foreign('fk_usuCrea_users')->references('id')->on('users');
            $table->unsignedInteger('fk_usuActualiza_users');
            $table->foreign('fk_usuActualiza_users')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios_documentos');
    }
}

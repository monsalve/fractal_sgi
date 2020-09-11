<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegmentosArchivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('segmentos_archivos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('archivo', 50);
            $table->string('codigo', 20);
            $table->string('versionamiento', 2);
            $table->string('ruta', 100);
            $table->text('descripcion')->nullable();
            $table->boolean('gestionable');
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('fk_id_empresas');
            $table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_id_segmentos_carpetas');
            $table->foreign('fk_id_segmentos_carpetas')->references('id')->on('segmentos_carpetas');
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
        Schema::dropIfExists('segmentos_archivos');
    }
}

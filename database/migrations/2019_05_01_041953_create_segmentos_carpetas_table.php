<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegmentosCarpetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('segmentos_carpetas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('carpeta', 50);
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('fk_id_empresas');
            $table->foreign('fk_id_empresas')->references('id')->on('empresas');
            $table->unsignedInteger('fk_id_tipos_carpetas');
            $table->foreign('fk_id_tipos_carpetas')->references('id')->on('tipos_carpetas');
            $table->unsignedInteger('fk_id_segmentos');
            $table->foreign('fk_id_segmentos')->references('id')->on('segmentos');
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
        Schema::dropIfExists('segmentos_carpetas');
    }
}

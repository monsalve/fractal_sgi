<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('modulos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 250);
            $table->string('descripcion', 250)->nullable();
            $table->string('componente', 250)->nullable();
            $table->string('template', 40)->nullable();
            $table->unsignedInteger('menu');
            $table->boolean('tipo');
            $table->string('icono', 30);
            $table->unsignedInteger('padre');
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('usu_crea');
            $table->foreign('usu_crea')->references('id')->on('users');
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('modulos');
    }
}

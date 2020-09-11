<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriasOpcionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias_opciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('categoriaOpcion', 100);
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('fk_id_categorias_archivos');
            $table->foreign('fk_id_categorias_archivos')->references('id')->on('categorias_archivos');
            
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
        Schema::dropIfExists('categorias_opciones');
    }
}

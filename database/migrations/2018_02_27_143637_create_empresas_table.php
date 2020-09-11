<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
            $table->string('logo', 100);
            $table->string('repre_legal', 100);
            $table->string('nit', 50);
            $table->string('direccion', 100);
            $table->string('res_fact_elect', 100);
            $table->string('res_fact_pos', 100);
            $table->string('correo', 100);
            $table->string('celular', 15);
            $table->string('telefono', 15);
            $table->unsignedInteger('usu_crea');
            $table->foreign('usu_crea')->references('id')->on('users');
            $table->unsignedInteger('usu_actualiza');
            $table->foreign('usu_actualiza')->references('id')->on('users');
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
        Schema::dropIfExists('empresas');
    }
}

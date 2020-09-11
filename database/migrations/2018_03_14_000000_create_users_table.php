<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->foreign('id')->references('id')->on('personas')->onDelete('cascade');
            
            $table->string('usuario')->nullable();
            $table->string('nombre_completo', 100);
            $table->string('password')->nullable();
            $table->boolean('super')->default(0);
            $table->boolean('condicion')->default(1);
            $table->boolean('logueable')->default(1);

            $table->unsignedInteger('fk_id_cargos');
            $table->foreign('fk_id_cargos')->references('id')->on('cargos');
            $table->integer('idrol')->unsigned();
            $table->foreign('idrol')->references('id')->on('roles');

            $table->unsignedInteger('fk_usuCrea_users');
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

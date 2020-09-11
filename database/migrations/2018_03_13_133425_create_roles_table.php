<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 30)->unique();
            $table->string('descripcion', 100)->nullable();
            $table->boolean('estado')->default(1);
            $table->unsignedInteger('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->unsignedInteger('usu_crea');
            $table->foreign('usu_crea')->references('id')->on('users');
            $table->unsignedInteger('fk_usuActualiza_users');
            $table->foreign('fk_usuActualiza_users')->references('id')->on('users');
            $table->timestamps();
        });
        /*DB::table('roles')->insert([
            [
                'id'                      => 1
                , 'nombre'                => 'Administrador'
                , 'descripcion'           => 'Administradores de área'
                , 'empersa_id'            => 2
                , 'usu_crea'              => 1
                , 'fk_usuActualiza_users' => 1
            ]
            , [
                'id'                      => 2
                , 'nombre'                => 'Vendedor'
                , 'descripcion'           => 'Vendedor área venta'
                , 'empersa_id'            => 2
                , 'usu_crea'              => 1
                , 'fk_usuActualiza_users' => 1
            ]
            , [
                'id'                      => 3
                , 'nombre'                => 'Almacenero'
                , 'descripcion'           => 'Almacenero área compras'
                , 'empersa_id'            => 2
                , 'usu_crea'              => 1
                , 'fk_usuActualiza_users' => 1
            ]
        ]);*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}

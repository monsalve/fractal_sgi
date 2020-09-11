<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCausasItemsRaicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('causas_items_raics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fk_id_causas_items');
            $table->foreign('fk_id_causas_items')->references('id')->on('causas_novedades_items');
            $table->unsignedInteger('fk_id_raics');
            $table->foreign('fk_id_raics')->references('id')->on('raics');
            $table->unsignedInteger('fk_usuCrea_users');
            $table->foreign('fk_usuCrea_users')->references('id')->on('users');
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
        Schema::dropIfExists('causas_items_raics');
    }
}

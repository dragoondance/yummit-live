<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbTopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_topups', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('balance');
            $table->integer('unique_code');
            $table->string('status', 8)->default('UNPAID')->nullable();
            $table->string('slip_image')->default('https://yummit.aurigaaristo.com/img_not_found.png')->nullable();

            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users');

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
        Schema::dropIfExists('tb_topups');
    }
}

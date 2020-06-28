<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbBalanceHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_balance_histories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->date('date');
            $table->string('description', 100);
            $table->integer('balance');
            $table->string('status', 11)->default('PENDING')->nullable();

            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users');
            $table->unsignedBigInteger('id_topup');
            $table->foreign('id_topup')->references('id')->on('tb_balance_histories');

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
        Schema::dropIfExists('tb_balance_histories');
    }
}

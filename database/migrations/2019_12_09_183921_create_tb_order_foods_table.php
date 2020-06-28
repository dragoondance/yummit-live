<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbOrderFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_order_foods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('quantity');
            $table->string('note', 100)->nullable();

            $table->unsignedBigInteger('id_order');
            $table->foreign('id_order')->references('id')->on('tb_orders');
            $table->unsignedBigInteger('id_food');
            $table->foreign('id_food')->references('id')->on('tb_foods');

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
        Schema::dropIfExists('tb_order_foods');
    }
}

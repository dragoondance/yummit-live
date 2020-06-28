<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_discounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('discount', 3);
            $table->string('is_active', 5);
            $table->time('start_time');
            $table->time('end_time');

            $table->unsignedBigInteger('id_food');
            $table->foreign('id_food')->references('id')->on('tb_foods');
            $table->unsignedBigInteger('id_restaurant');
            $table->foreign('id_restaurant')->references('id')->on('tb_restaurants');

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
        Schema::dropIfExists('tb_discount');
    }
}

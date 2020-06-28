<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbFoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_foods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->integer('price');
            $table->string('description', 100)->nullable();
            $table->string('food_image', 100)->default('https://yummit.aurigaaristo.com/img_not_found.png')->nullable();
            $table->string('is_available', 5);

            $table->unsignedBigInteger('id_food_category')->nullable();
            $table->foreign('id_food_category')->references('id')->on('tb_food_categories');

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
        Schema::dropIfExists('tb_foods');
    }
}

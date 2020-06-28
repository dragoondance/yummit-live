<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_restaurants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('email', 50)->unique();
            $table->string('password');
            $table->string('restaurant_image')->default('https://yummit.aurigaaristo.com/img_not_found.png')->nullable();
            $table->string('restaurant_header')->default('https://yummit.aurigaaristo.com/img_not_found.png')->nullable();
            $table->string('address', 150);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->string('description', 100)->nullable();
            $table->double('rating', 2)->default('0')->nullable();
            $table->integer('balance')->default('0')->nullable();
            $table->longText('fcm_token')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

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
        Schema::dropIfExists('tb_restaurants');
    }
}

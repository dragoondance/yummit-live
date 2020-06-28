<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status', 11)->default('NEW');
            $table->integer('price');
            $table->string('note')->nullable();
            $table->integer('delivery_fee')->nullable();
            $table->time('pickup_time');
            $table->string('order_type', 11);
            $table->string('address', 50);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users');

            $table->unsignedBigInteger('id_restaurant');
            $table->foreign('id_restaurant')->references('id')->on('tb_restaurants');

            $table->unsignedBigInteger('id_voucher')->nullable();
            $table->foreign('id_voucher')->references('id')->on('tb_voucher');

            $table->unsignedBigInteger('id_balance_history')->nullable();
            $table->foreign('id_balance_history')->references('id')->on('tb_balance_histories');

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
        Schema::dropIfExists('tb_orders');
    }
}

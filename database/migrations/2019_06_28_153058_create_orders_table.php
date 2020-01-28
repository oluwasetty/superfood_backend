<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->decimal('cart_subtotal', 9, 2);
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_amount', 9, 2)->nullable();
            $table->decimal('order_total', 9, 2);
            $table->unsignedBigInteger('address_id');
            $table->string('delivery_method')->default("");
            $table->decimal('delivery_charge', 9, 2)->default(0);
            $table->decimal('total_amount', 9, 2);
            $table->string('payment_method');
            $table->string('comment')->nullable();
            $table->string('payment_status')->default('not paid');
            $table->decimal('amount_paid', 9, 2)->default(0);
            $table->string('order_status')->default('pending');
            $table->string('received')->nullable();
            $table->datetime('received_time')->nullable();
            $table->string('completion_status')->default('incomplete');
            $table->datetime('completed_time')->nullable();
            $table->string('pickup_status')->default("not delivered");
            $table->datetime('pickup_time')->nullable();
            $table->string('delivery_status')->default("not delivered");
            $table->datetime('delivery_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

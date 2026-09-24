<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->string('gateway')->default('cod'); // cod, momo, etc.
                $table->string('transaction_code')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('status')->default('pending'); // pending, initiated, paid, failed, cancelled, refund_pending, refunded
                $table->text('payload')->nullable();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('payment_transactions');
    }
};

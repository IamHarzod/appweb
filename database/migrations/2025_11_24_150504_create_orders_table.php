<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // Nếu bạn cho phép khách mua không cần đăng nhập thì dùng ->nullable()
            // Nếu bắt buộc đăng nhập thì xóa ->nullable() đi
            $table->unsignedBigInteger('user_id')->nullable();

            // Thông tin người nhận (Lấy từ Form)
            $table->string('shipping_name');
            $table->string('shipping_email');
            $table->string('shipping_phone');
            $table->string('shipping_address');
            $table->text('notes')->nullable();

            // Thanh toán & Trạng thái
            $table->string('payment_method')->default('COD');
            $table->decimal('total_amount', 15, 2); // Tổng tiền
            $table->string('status')->default('pending'); // Trạng thái đơn

            $table->timestamps();

            // Khóa ngoại (nếu có user_id)
            // Nếu bạn dùng bảng users, hãy giữ dòng dưới. Nếu không thì xóa đi.
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};

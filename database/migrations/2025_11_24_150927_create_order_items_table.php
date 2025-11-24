<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // Liên kết với bảng orders
            $table->unsignedBigInteger('product_id'); // Liên kết với bảng products

            $table->string('product_name'); // Lưu tên SP lúc mua
            $table->integer('quantity');    // Số lượng
            $table->decimal('price', 15, 2); // Giá tiền lúc mua

            $table->timestamps();

            // Tạo khóa ngoại
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            // Giả sử bảng sản phẩm của bạn tên là 'products'
            // $table->foreign('product_id')->references('id')->on('products'); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};

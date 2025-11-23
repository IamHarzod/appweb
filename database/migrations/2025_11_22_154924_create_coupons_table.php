<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Ví dụ: SALES50
            $table->enum('type', ['percent', 'fixed', 'free_ship']); // Loại giảm giá
            $table->decimal('value', 10, 2)->nullable(); // Giá trị (ví dụ: 10 là 10% hoặc 10.000đ)
            $table->integer('quantity')->default(0); // Số lượng mã còn lại
            $table->date('expiry_date')->nullable(); // Hạn sử dụng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

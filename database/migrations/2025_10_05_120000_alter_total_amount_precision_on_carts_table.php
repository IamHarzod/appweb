<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tăng độ chính xác của totalAmount để chứa tổng tiền lớn (VND)
        DB::statement('ALTER TABLE `carts` MODIFY `totalAmount` DECIMAL(15,2) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục về DECIMAL(10,2) như ban đầu (có thể tràn với đơn hàng lớn)
        DB::statement('ALTER TABLE `carts` MODIFY `totalAmount` DECIMAL(10,2) NOT NULL');
    }
};



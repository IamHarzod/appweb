<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_status')) {
                $table->string('shipping_status')->default('pending')->after('status');
            }
            if (!Schema::hasColumn('orders', 'ghn_order_code')) {
                $table->string('ghn_order_code')->nullable()->after('shipping_status');
            }
            if (!Schema::hasColumn('orders', 'shipping_carrier')) {
                $table->string('shipping_carrier')->default('GHN')->nullable()->after('ghn_order_code');
            }
            if (!Schema::hasColumn('orders', 'cod_amount')) {
                $table->decimal('cod_amount', 15, 2)->default(0)->after('shipping_carrier');
            }
            if (!Schema::hasColumn('orders', 'name')) {
                $table->string('name')->nullable()->after('shipping_name');
            }
            if (!Schema::hasColumn('orders', 'phone')) {
                $table->string('phone')->nullable()->after('shipping_phone');
            }
            if (!Schema::hasColumn('orders', 'total_price')) {
                $table->decimal('total_price', 15, 2)->nullable()->after('total_amount');
            }
        });

        // Đồng bộ dữ liệu hiện có
        DB::table('orders')->update([
            'name'            => DB::raw('COALESCE(name, shipping_name)'),
            'phone'           => DB::raw('COALESCE(phone, shipping_phone)'),
            'total_price'     => DB::raw('COALESCE(total_price, total_amount)'),
            'shipping_status' => DB::raw("COALESCE(shipping_status, CASE WHEN status IN ('completed', 'delivered') THEN 'delivered' WHEN status = 'shipping' THEN 'delivering' WHEN status = 'cancelled' THEN 'cancelled' ELSE 'pending' END)"),
            'cod_amount'      => DB::raw("CASE WHEN payment_method = 'COD' THEN total_amount ELSE 0 END"),
        ]);
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $cols = ['shipping_status', 'ghn_order_code', 'shipping_carrier', 'cod_amount', 'name', 'phone', 'total_price'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

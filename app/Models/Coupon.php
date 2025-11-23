<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons'; // Tên bảng trong database

    protected $fillable = [
        'code',
        'type',
        'value',
        'quantity',
        'expiry_date',
    ];

    // Khai báo để Laravel hiểu đây là cột ngày tháng
    protected $casts = [
        'expiry_date' => 'date',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    // CẬP NHẬT LẠI ĐOẠN NÀY
    protected $fillable = [
        'user_id',
        'shipping_name',    // Thêm dòng này
        'shipping_email',   // Thêm dòng này
        'shipping_phone',   // Thêm dòng này
        'shipping_address', // Thêm dòng này
        'notes',            // Thêm dòng này
        'payment_method',   // Thêm dòng này
        'total_amount',     // Thêm dòng này
        'status',           // Thêm dòng này
    ];

    public function orderItems()
    {
        return $this->hasMany(OderItem::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

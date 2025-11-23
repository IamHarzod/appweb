<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $timestamps = true;
    //bảng liên kết
    protected $table = 'orders';
    //khóa chính
    protected $primaryKey = 'id';
    //thuộc tính có thể gán hàng loạt
    protected $fillable = [
        'user_id',
        'unitPrice',
        'quantity',
        'totalPrice',

    ];

    use HasFactory;
    public function oderItems()
    {
        return $this->hasMany(OderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

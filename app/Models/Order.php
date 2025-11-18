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
        'Quantity',
        'totalPrice',

    ];

    use HasFactory;
    public function oderItems()
    {
        return $this->hasMany(OderItem::class);
    }
}

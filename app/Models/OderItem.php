<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OderItemFactory> */
    use HasFactory;
   
    //bảng liên kết
    protected $table = 'oder_items';
    //khóa chính
    protected $primaryKey = 'id';
    //thuộc tính có thể gán hàng loạt
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'UnitPrice',
        'totalPrice',
    ];
}

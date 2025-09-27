<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public $timestamps = true;
    //bảng liên kết
    protected $table = 'carts';
    //khóa chính
    protected $primaryKey = 'id';
    //thuộc tính có thể gán hàng loạt
    protected $fillable = [
        'totalAmount',
    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = true;
    //bảng liên kết
    protected $table = 'products';
    //khóa chính
    protected $primaryKey = 'id';
    //thuộc tính có thể gán hàng loạt
    protected $fillable = [
        'name',
        'description',
        'price',
        'stockQuantity',
        'category_id',
        'discountPercent',
        'imageURL',
        'IsActive',
        'line',
        'style',
        'status',
    ];
}

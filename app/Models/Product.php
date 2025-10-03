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
        'imageURL',
        'discountPercent',
        'description',
        'price',
        'stockQuantity',
        'status',
        'IsActive',
        'category_id',
        'style',
    ];

    // Quan hệ với Category (nếu có bảng categories)
    public function category()
    {
        return $this->belongsTo(\app\Models\Category::class, 'category_id');
    }
}

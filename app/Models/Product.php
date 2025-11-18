<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'id_brand',
    ];

    // Quan hệ với Category (nếu có bảng categories)
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'id_brand', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id'); // chú ý: đúng tên cột
    }

    use HasFactory;
    public function oderItems()
    {
        return $this->hasMany(OderItem::class);
    }
}

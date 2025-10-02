<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = true;

    // Bảng liên kết (theo migration của bạn)
    protected $table = '_category';

    // Khóa chính
    protected $primaryKey = 'id';

    // Thuộc tính có thể gán hàng loạt
    protected $fillable = [
        'name',
        'description',
        'ImageURL',
    ];
}

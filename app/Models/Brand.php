<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    public $timestamps = true;
    //bảng liên kết
    protected $table = 'brand';
    //khóa chính
    protected $primaryKey = 'id';
    //thuộc tính có thể gán hàng loạt
    protected $fillable = [
        'Logo',
        'TenThuongHieu',
        'MoTa',
        'TrangThai',

    ];
}

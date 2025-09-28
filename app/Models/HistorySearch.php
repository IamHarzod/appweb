<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorySearch extends Model
{
    /** @use HasFactory<\Database\Factories\HistorySearchFactory> */
    use HasFactory;
    //bảng liên kết
    protected $table = 'history_searches';
    //khóa chính
    protected $primaryKey = 'id';
    //thuộc tính có thể gán hàng loạt
    protected $fillable = [
        'user_id',
        'search_term',
        'SearchedDate',
        'SearchTerm' ,
    ];     
}

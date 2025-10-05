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
        'user_id'
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với CartItem
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Tính tổng tiền giỏ hàng
    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->cartItems as $item) {
            $total += $item->quantity * $item->product->price;
        }
        return $total;
    }

    // Cập nhật tổng tiền
    public function updateTotal()
    {
        $this->totalAmount = $this->calculateTotal();
        $this->save();
    }
}

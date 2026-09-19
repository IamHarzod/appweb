<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $table = 'payment_transactions';

    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_code',
        'amount',
        'status',
        'payload',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}

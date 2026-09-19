<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'notes',
        'payment_method',
        'total_amount',
        'discount_amount',
        'shipping_fee',
        'status',
        'shipping_status',
        'ghn_order_code',
        'shipping_carrier',
        'cod_amount',
        'name',
        'phone',
        'total_price',
        'address',
        'latitude',
        'longitude',
        'to_district_id',
        'to_ward_code',
        'ghn_total_fee',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($order) {
            // Tự động đồng bộ các cột tương thích
            if (empty($order->name) && !empty($order->shipping_name)) {
                $order->name = $order->shipping_name;
            } elseif (empty($order->shipping_name) && !empty($order->name)) {
                $order->shipping_name = $order->name;
            }

            if (empty($order->phone) && !empty($order->shipping_phone)) {
                $order->phone = $order->shipping_phone;
            } elseif (empty($order->shipping_phone) && !empty($order->phone)) {
                $order->shipping_phone = $order->phone;
            }

            if (empty($order->address) && !empty($order->shipping_address)) {
                $order->address = $order->shipping_address;
            } elseif (empty($order->shipping_address) && !empty($order->address)) {
                $order->shipping_address = $order->address;
            }

            if (($order->ghn_total_fee === null || $order->ghn_total_fee == 0) && !empty($order->shipping_fee)) {
                $order->ghn_total_fee = (int) $order->shipping_fee;
            } elseif (($order->shipping_fee === null || $order->shipping_fee == 0) && !empty($order->ghn_total_fee)) {
                $order->shipping_fee = $order->ghn_total_fee;
            }

            if (($order->total_price === null || $order->total_price == 0) && !empty($order->total_amount)) {
                $order->total_price = $order->total_amount;
            } elseif (($order->total_amount === null || $order->total_amount == 0) && !empty($order->total_price)) {
                $order->total_amount = $order->total_price;
            }

            if (empty($order->shipping_status)) {
                $order->shipping_status = match ($order->status) {
                    'shipping' => 'delivering',
                    'completed', 'delivered' => 'delivered',
                    'cancelled' => 'cancelled',
                    default => 'pending',
                };
            }

            if ($order->cod_amount === null || $order->cod_amount == 0) {
                if (strtoupper($order->payment_method ?? '') === 'COD') {
                    $order->cod_amount = $order->total_amount ?? $order->total_price ?? 0;
                }
            }
        });
    }

    public function items()
    {
        return $this->hasMany(OderItem::class, 'order_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OderItem::class, 'order_id');
    }

    public function oderItems()
    {
        return $this->hasMany(OderItem::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'order_id');
    }

    // Accessors
    public function getNameAttribute($value)
    {
        return $value ?: $this->shipping_name;
    }

    public function getPhoneAttribute($value)
    {
        return $value ?: $this->shipping_phone;
    }

    public function getAddressAttribute($value)
    {
        return $value ?: $this->shipping_address;
    }

    public function getTotalPriceAttribute($value)
    {
        return $value !== null ? $value : $this->total_amount;
    }
}

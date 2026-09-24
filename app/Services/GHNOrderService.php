<?php

namespace App\Services;

use App\Models\Order;

class GHNOrderService
{
    public function __construct(private GHNService $ghn)
    {
    }

    public function create(Order $order, bool $isPaid = false): array
    {
        $items = [];
        $weight = 0;

        $orderItems = $order->items ?? $order->orderItems ?? collect();
        foreach ($orderItems as $item) {
            $itemWeight = (int) ($item->product->weight ?? 200);
            $weight += $itemWeight * (int) $item->quantity;
            $items[] = [
                'name'     => $item->product_name ?? optional($item->product)->name ?? 'Sản phẩm',
                'quantity' => (int) $item->quantity,
                'price'    => (int) $item->price,
                'weight'   => $itemWeight,
            ];
        }

        $codAmount = $isPaid ? 0 : (int) ($order->cod_amount ?? $order->total_price ?? $order->total_amount ?? 0);

        return $this->ghn->createOrder([
            'payment_type_id' => 2, // 2: Người nhận thanh toán phí vận chuyển, 1: Người gửi trả
            'note'            => 'Đơn hàng #' . $order->id,
            'required_note'   => 'KHONGCHOXEMHANG',
            'to_name'         => $order->name ?: $order->shipping_name,
            'to_phone'        => $order->phone ?: $order->shipping_phone,
            'to_address'      => $order->address ?: $order->shipping_address,
            'to_ward_code'    => (string) $order->to_ward_code,
            'to_district_id'  => (int) $order->to_district_id,
            'cod_amount'      => $codAmount,
            'weight'          => $weight > 0 ? $weight : 300,
            'length'          => 15,
            'width'           => 15,
            'height'          => 10,
            'service_type_id' => 2, // Gói chuẩn E-commerce
            'items'           => $items,
        ]);
    }
}

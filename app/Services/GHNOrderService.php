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

        $fromDistrictId = (int) (config('services.ghn.from_district_id') ?: 1482);
        $fromWardCode   = (string) (config('services.ghn.from_ward_code') ?: '11007');
        $fromName       = (string) (config('services.ghn.from_name') ?: env('GHN_FROM_NAME', 'Shop AppWeb'));
        $fromPhone      = (string) (config('services.ghn.from_phone') ?: env('GHN_FROM_PHONE', '0947092026'));
        $fromAddress    = (string) (config('services.ghn.from_address') ?: env('GHN_FROM_ADDRESS', 'Số 114 Đường Nguyễn Đạo An, Phường Minh Khai'));

        return $this->ghn->createOrder([
            'client_order_code' => 'ORDER-' . $order->id,
            'payment_type_id'   => 2, // 2: Người nhận thanh toán phí vận chuyển, 1: Người gửi trả
            'note'              => 'Đơn hàng #' . $order->id,
            'required_note'     => 'KHONGCHOXEMHANG',
            'from_name'         => $fromName,
            'from_phone'        => $fromPhone,
            'from_address'      => $fromAddress,
            'from_district_id'  => $fromDistrictId,
            'from_ward_code'    => $fromWardCode,
            'to_name'           => $order->name ?: $order->shipping_name,
            'to_phone'          => $order->phone ?: $order->shipping_phone,
            'to_address'        => $order->address ?: $order->shipping_address,
            'to_ward_code'      => (string) $order->to_ward_code,
            'to_district_id'    => (int) $order->to_district_id,
            'cod_amount'        => $codAmount,
            'weight'            => $weight > 0 ? $weight : 300,
            'length'            => 15,
            'width'             => 15,
            'height'            => 10,
            'service_type_id'   => 2, // Gói chuẩn E-commerce
            'items'             => $items,
        ]);
    }

    /**
     * Tự động tạo vận đơn GHN cho đơn hàng (hỗ trợ cả COD và Online Paid).
     *
     * @param Order $order
     * @return string|null Mã vận đơn GHN nếu thành công
     */
    public function autoCreateForOrder(Order $order): ?string
    {
        if (!empty($order->ghn_order_code)) {
            return $order->ghn_order_code;
        }

        // Tự động fallback district_id & ward_code từ session nếu thiếu
        if (empty($order->to_district_id) && session('ghn_to_district_id')) {
            $order->to_district_id = (int) session('ghn_to_district_id');
        }
        if (empty($order->to_ward_code) && session('ghn_to_ward_code')) {
            $order->to_ward_code = (string) session('ghn_to_ward_code');
        }

        if (!$order->to_district_id || !$order->to_ward_code) {
            \Illuminate\Support\Facades\Log::warning("Order #{$order->id} lacks district/ward for GHN auto-create");
            return null;
        }

        try {
            $isPaid = in_array(strtoupper($order->payment_method ?? ''), ['MOMO', 'VNPAY', 'PAID']) 
                      || $order->payment_status === 'paid';
            $ghnRes = $this->create($order, $isPaid);

            if (!empty($ghnRes['data']['order_code'])) {
                $order->ghn_order_code = $ghnRes['data']['order_code'];
                $order->shipping_status = 'ready_to_pick';
                if ($order->status === 'pending') {
                    $order->status = 'processing';
                }
                $order->save();

                \Illuminate\Support\Facades\Log::info("GHN waybill auto-created for Order #{$order->id}: {$order->ghn_order_code}");
                return $order->ghn_order_code;
            } else {
                \Illuminate\Support\Facades\Log::warning("GHN auto-create returned error for Order #{$order->id}: " . json_encode($ghnRes, JSON_UNESCAPED_UNICODE));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("GHN auto-create exception for Order #{$order->id}: " . $e->getMessage());
        }

        return null;
    }
}

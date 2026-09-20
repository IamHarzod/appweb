<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OrderService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Xử lý đặt hàng đầy đủ từ giỏ hàng (hỗ trợ cả user đăng nhập và khách vãng lai).
     *
     * @param array $shippingData Thông tin người nhận và địa chỉ
     * @param Request|null $request Request gốc để lấy to_district_id, to_ward_code, v.v.
     * @return Order
     * @throws \Exception
     */
    public function placeOrder(array $shippingData, ?Request $request = null): Order
    {
        // 1. Thu thập dữ liệu giỏ hàng
        $orderItemsData = [];
        $productTotal = 0.0;
        $cartDB = null;

        if (Auth::check()) {
            $cartDB = Cart::with('cartItems.product')
                ->where('user_id', Auth::id())
                ->first();

            if ($cartDB && $cartDB->cartItems->count() > 0) {
                foreach ($cartDB->cartItems as $item) {
                    if ($item->product) {
                        $lineTotal = (float) ($item->product->price * $item->quantity);
                        $orderItemsData[] = [
                            'product_id' => $item->product_id,
                            'name'       => $item->product->name,
                            'price'      => (float) $item->product->price,
                            'quantity'   => (int) $item->quantity,
                            'total'      => $lineTotal,
                        ];
                        $productTotal += $lineTotal;
                    }
                }
            }
        } else {
            $sessionCart = Session::get('cart', []);
            foreach ($sessionCart as $id => $details) {
                $lineTotal = (float) ($details['price'] * $details['quantity']);
                $orderItemsData[] = [
                    'product_id' => $id,
                    'name'       => $details['name'] ?? ('Sản phẩm #' . $id),
                    'price'      => (float) $details['price'],
                    'quantity'   => (int) $details['quantity'],
                    'total'      => $lineTotal,
                ];
                $productTotal += $lineTotal;
            }
        }

        if (empty($orderItemsData)) {
            throw new \Exception('Giỏ hàng trống! Vui lòng chọn sản phẩm trước khi thanh toán.');
        }

        // 2. Tính phí vận chuyển & giảm giá
        $shippingFee = isset($shippingData['shipping_fee']) ? (float) $shippingData['shipping_fee'] : 50000.0;
        $discountAmount = 0.0;
        $couponCode = null;

        if (Session::has('coupon')) {
            $coupon = Session::get('coupon');
            $couponCode = $coupon['code'] ?? null;
            $type = $coupon['type'] ?? '';
            $val = (float) ($coupon['value'] ?? 0);

            if ($type === 'free_ship') {
                $shippingFee = max(0.0, $shippingFee - $val);
            } elseif ($type === 'fixed') {
                $discountAmount = $val;
            } elseif ($type === 'percent') {
                $discountAmount = ($productTotal * $val) / 100.0;
            }
        }

        // Miễn phí vận chuyển cho đơn trên 10 triệu
        if ($productTotal > 10000000) {
            $shippingFee = 0.0;
        }

        $finalTotalAmount = max(0.0, $productTotal + $shippingFee - $discountAmount);

        // 3. Thực thi Database Transaction
        DB::beginTransaction();
        try {
            $phuong = $shippingData['phuong_xa_name'] ?? ($shippingData['phuong_xa'] ?? '');
            $quan = $shippingData['quan_huyen_name'] ?? ($shippingData['quan_huyen'] ?? '');
            $tinh = $shippingData['tinh_thanh_name'] ?? ($shippingData['tinh_thanh'] ?? '');
            $fullAddress = trim(($shippingData['shipping_address'] ?? '') . ', ' . $phuong . ', ' . $quan . ', ' . $tinh, ', ');

            $order = Order::create([
                'user_id'          => Auth::id() ?? null,
                'shipping_name'    => $shippingData['shipping_name'],
                'name'             => $shippingData['shipping_name'],
                'shipping_email'   => $shippingData['shipping_email'] ?? null,
                'shipping_phone'   => $shippingData['shipping_phone'],
                'phone'            => $shippingData['shipping_phone'],
                'shipping_address' => $fullAddress,
                'address'          => $fullAddress,
                'latitude'         => !empty($shippingData['latitude']) ? (float) $shippingData['latitude'] : null,
                'longitude'        => !empty($shippingData['longitude']) ? (float) $shippingData['longitude'] : null,
                'to_district_id'   => !empty($shippingData['to_district_id']) ? (int) $shippingData['to_district_id'] : null,
                'to_ward_code'     => !empty($shippingData['to_ward_code']) ? (string) $shippingData['to_ward_code'] : null,
                'notes'            => $shippingData['ghichu'] ?? ($shippingData['notes'] ?? null),
                'payment_method'   => $shippingData['payment_method'] ?? 'COD',

                'total_amount'     => $finalTotalAmount,
                'total_price'      => $finalTotalAmount,
                'discount_amount'  => $discountAmount,
                'shipping_fee'     => $shippingFee,
                'ghn_total_fee'    => (int) $shippingFee,

                'status'           => 'pending',
                'shipping_status'  => 'pending',
            ]);

            // Trừ tồn kho và tạo OrderItem
            foreach ($orderItemsData as $item) {
                $prod = Product::where('id', $item['product_id'])->lockForUpdate()->first();
                if ($prod) {
                    if ($prod->stockQuantity < $item['quantity']) {
                        throw new \Exception('Sản phẩm "' . $prod->name . '" không đủ số lượng tồn kho (còn: ' . $prod->stockQuantity . ').');
                    }
                    $prod->decrement('stockQuantity', $item['quantity']);
                }

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'],
                    'quantity'     => $item['quantity'],
                    'price'        => $item['price'],
                ]);
            }

            // Trừ lượt sử dụng coupon
            if ($couponCode) {
                Coupon::where('code', $couponCode)->where('quantity', '>', 0)->decrement('quantity', 1);
            }

            // Xóa giỏ hàng
            $this->cartService->clearCart();
            session(['last_placed_order_id' => $order->id]);

            DB::commit();

            // Đẩy đơn tự động sang GHN nếu có thông tin quận huyện
            if ($order->to_district_id && $order->to_ward_code) {
                try {
                    $ghnOrderService = app(GHNOrderService::class);
                    $isPaid = in_array(strtoupper($order->payment_method ?? ''), ['MOMO', 'VNPAY', 'PAID']);
                    $ghnOrderService->create($order, $isPaid);
                } catch (\Throwable $ghnError) {
                    Log::warning('GHN auto-create failed for order #' . $order->id . ': ' . $ghnError->getMessage());
                }
            }

            return $order;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('OrderService::placeOrder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}

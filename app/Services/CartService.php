<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Lấy dữ liệu giỏ hàng hoàn chỉnh (items, subtotal, phí ship, giảm giá, tổng tiền).
     * Dùng chung cho CartController@show_cart và CheckoutController@show_checkout.
     */
    public function getCartData(float $defaultShippingFee = 50000): array
    {
        $shippingFee = $defaultShippingFee;
        $discountAmount = 0.0;
        $subtotal = 0.0;
        $cart = null;
        $cartItems = collect();

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cartItems = CartItem::with('product')->where('cart_id', $cart->id)->get();
                foreach ($cartItems as $item) {
                    if ($item->product) {
                        $subtotal += (float) ($item->product->price * $item->quantity);
                    }
                }
            }
        } else {
            $sessionCart = Session::get('cart', []);
            foreach ($sessionCart as $id => $details) {
                $product = Product::find($id);
                if ($product) {
                    $qty = (int) ($details['quantity'] ?? 1);
                    $cartItems->push((object) [
                        'id'         => $id,
                        'product'    => $product,
                        'product_id' => $id,
                        'quantity'   => $qty,
                        'price'      => (float) $product->price,
                    ]);
                    $subtotal += (float) ($product->price * $qty);
                }
            }
        }

        // Tính toán giảm giá từ coupon
        if (Session::has('coupon')) {
            $coupon = Session::get('coupon');
            $type = $coupon['type'] ?? '';
            $value = (float) ($coupon['value'] ?? 0);

            if ($type === 'free_ship') {
                $shippingFee = max(0.0, $shippingFee - $value);
            } elseif ($type === 'fixed') {
                $discountAmount = $value;
            } elseif ($type === 'percent') {
                $discountAmount = ($subtotal * $value) / 100.0;
            }
        }

        $totalPrice = max(0.0, $subtotal + $shippingFee - $discountAmount);

        return [
            'cart'           => $cart,
            'cartItems'      => $cartItems,
            'subtotal'       => $subtotal,
            'shippingFee'    => $shippingFee,
            'discountAmount' => $discountAmount,
            'totalPrice'     => $totalPrice,
        ];
    }

    /**
     * Kiểm tra và lưu coupon vào Session.
     */
    public function applyCoupon(string $code): array
    {
        $code = trim($code);
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return ['success' => false, 'message' => 'Mã giảm giá sai hoặc không tồn tại!'];
        }

        if ($coupon->quantity <= 0) {
            return ['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng!'];
        }

        if ($coupon->expiry_date && Carbon::now()->gt(Carbon::parse($coupon->expiry_date))) {
            return ['success' => false, 'message' => 'Mã giảm giá đã hết hạn!'];
        }

        $couponData = [
            'id'    => $coupon->id,
            'code'  => $coupon->code,
            'type'  => $coupon->type,
            'value' => (float) $coupon->value,
        ];

        Session::put('coupon', $couponData);

        return ['success' => true, 'message' => 'Áp dụng mã giảm giá thành công!', 'coupon' => $couponData];
    }

    /**
     * Gỡ bỏ coupon khỏi Session.
     */
    public function removeCoupon(): void
    {
        Session::forget('coupon');
    }

    /**
     * Xoá sạch giỏ hàng (DB hoặc Session).
     */
    public function clearCart(): void
    {
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                CartItem::where('cart_id', $cart->id)->delete();
                $cart->totalAmount = 0;
                $cart->save();
            }
        }
        Session::forget('cart');
        Session::forget('coupon');
    }
}

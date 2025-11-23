<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function show_checkout(Request $request)
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Vui lòng đăng nhập để xem giỏ hàng.'], 401);
            }
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để truy cập giỏ hàng.');
        }
        // Lấy giỏ hàng từ DB (ưu tiên), kèm sản phẩm
        $cartModel = Cart::with(['cartItems.product'])
            ->where('user_id', Auth::id())
            ->first();

        // Nếu tồn tại session cart cũ và chưa có DB cart -> có thể migrate
        $legacySessionCart = session('cart', []);
        if (!$cartModel && !empty($legacySessionCart)) {
            $cartModel = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['totalAmount' => 0]
            );
            foreach ($legacySessionCart as $item) {
                if (isset($item['id'], $item['quantity'])) {
                    \App\Models\CartItem::updateOrCreate(
                        ['cart_id' => $cartModel->id, 'product_id' => $item['id']],
                        ['quantity' => (int)$item['quantity']]
                    );
                }
            }
            session()->forget('cart');
            $cartModel->load('cartItems.product');
            $cartModel->updateTotal();
        }

        $cartItems = $cartModel?->cartItems ?? collect();
        $subtotal = $cartItems->sum(fn($ci) => $ci->quantity * ($ci->product->price ?? 0));

        $categories = Category::all();

        return view("client.checkout.checkout_index", [
            'categories' => $categories,
            'subtotal' => $subtotal,
            'cartItems' => $cartItems,
            'cart' => $cartModel,
        ]);
    }
}
<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Services\GHNService;
use App\Services\GHNOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // ==========================================
    // 1. CÁC VIEW HIỂN THỊ ĐƠN HÀNG & THANH TOÁN
    // ==========================================
    public function index()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Giỏ hàng đang trống.');
        }

        $totalPrice = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('client.checkout.checkout_index', compact('cart', 'totalPrice'));
    }

    public function orderHistory()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'paymentTransactions' => function ($query) {
                $query->latest();
            }])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('client.orders.my_orders', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id() && !(Auth::user() && Auth::user()->role === 'admin')) {
            abort(403);
        }

        $order->load('items.product');
        return view('client.orders.show', compact('order'));
    }

    // ==========================================
    // 2. AJAX LOCATION & TÍNH PHÍ GHN
    // ==========================================
    public function getProvinces(GHNService $ghn)
    {
        return response()->json($ghn->getProvinces());
    }

    public function getDistricts(int $provinceId, GHNService $ghn)
    {
        return response()->json($ghn->getDistricts($provinceId));
    }

    public function getWards(int $districtId, GHNService $ghn)
    {
        return response()->json($ghn->getWards($districtId));
    }

    public function getShippingFee(Request $request, GHNService $ghn)
    {
        $cart = session('cart', []);
        $totalWeight = 0;
        foreach ($cart as $item) {
            $totalWeight += ((int)($item['weight'] ?? 200)) * (int)($item['quantity'] ?? 1);
        }

        if ($totalWeight <= 0 && Auth::check()) {
            $dbCart = \App\Models\Cart::with('cartItems.product')->where('user_id', Auth::id())->first();
            if ($dbCart && $dbCart->cartItems) {
                foreach ($dbCart->cartItems as $cItem) {
                    $w = (int) ($cItem->product->weight ?? 200);
                    $totalWeight += $w * (int) $cItem->quantity;
                }
            }
        }

        $res = $ghn->calculateFee([
            'service_type_id'  => 2, // Gói chuẩn E-commerce
            'from_district_id' => (int) config('services.ghn.from_district_id'),
            'to_district_id'   => (int) $request->to_district_id,
            'to_ward_code'     => (string) $request->to_ward_code,
            'weight'           => $totalWeight > 0 ? $totalWeight : 300,
            'length'           => 15,
            'width'            => 15,
            'height'           => 10,
        ]);

        return response()->json($res);
    }
}

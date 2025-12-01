<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\OderItemController;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // Admin: list all orders
    public function index()
    {
        $orders = Order::with('user')->orderByDesc('id')->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    // Authenticated user: list own orders
    public function myOrders()
    {
        $user = Auth::user();
        $orders = Order::with('oderItems')->where('user_id', $user->id)->orderByDesc('id')->paginate(15);
        return view('client.orders.my_orders', compact('orders'));
    }

    // Show single order (ensure ownership or admin)
    public function show($id)
    {
        $order = Order::with(['oderItems.product', 'user'])->findOrFail($id);
        $this->authorize('view', $order);
        return view('client.orders.show', compact('order'));
    }

    // Create order from session cart
    public function storeFromCart(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt hàng.');
        }

        // Lấy giỏ hàng DB
        $cart = \App\Models\Cart::with(['cartItems.product'])
            ->where('user_id', Auth::id())
            ->first();

        // Nếu chưa có cart DB nhưng có session cart cũ -> migrate rồi dùng DB
        $legacySessionCart = session('cart', []);
        if ((!$cart || $cart->cartItems->isEmpty()) && !empty($legacySessionCart)) {
            $cart = \App\Models\Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['totalAmount' => 0]
            );
            foreach ($legacySessionCart as $item) {
                if (isset($item['id'], $item['quantity'])) {
                    \App\Models\CartItem::updateOrCreate(
                        ['cart_id' => $cart->id, 'product_id' => $item['id']],
                        ['quantity' => (int)$item['quantity']]
                    );
                }
            }
            session()->forget('cart');
            $cart->load('cartItems.product');
            $cart->updateTotal();
        }

        if (!$cart || $cart->cartItems->isEmpty()) {
            return back()->with('error', 'Giỏ hàng đang trống.');
        }

        $items = $cart->cartItems;
        $totalQuantity = (int) $items->sum('quantity');
        $totalPrice = (float) $items->sum(fn($i) => $i->quantity * ($i->product->price ?? 0));
        $unitPrice = $totalQuantity > 0 ? $totalPrice / $totalQuantity : 0;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'    => Auth::id(),
                'unitPrice'  => $unitPrice,
                'quantity'   => $totalQuantity,
                'totalPrice' => $totalPrice,
            ]);

            foreach ($items as $cartItem) {
                $product = $cartItem->product;
                if (!$product) {
                    continue;
                }
                OderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => (int) $cartItem->quantity,
                    'UnitPrice'  => (float) $product->price,
                    'totalPrice' => (float) $product->price * (int) $cartItem->quantity,
                ]);
            }
            // Xóa cart items sau khi tạo order
            \App\Models\CartItem::where('cart_id', $cart->id)->delete();
            $cart->updateTotal();

            DB::commit();
            // Xóa session cart legacy nếu còn
            session()->forget('cart');
            return redirect()->route('orders.my')->with('success', 'Đặt hàng thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
        }
    }

    // Delete order (admin or owner)
    public function destroy($id)
    {
        try {
            // 1. Tìm đơn hàng
            $order = Order::findOrFail($id);

            // 2. Xóa chi tiết đơn hàng trước (QUAN TRỌNG)
            // Trong Model Order.php của bạn tên hàm là 'orderItems'
            // Nên ở đây BẮT BUỘC phải gọi là 'orderItems()'
            $order->orderItems()->delete();

            // 3. Xóa đơn hàng chính
            $order->delete();

            // 4. Trả về true để hàm JS DeleteData hiểu là thành công và reload trang
            return true;
        } catch (\Throwable $e) {
            // Nếu có lỗi, ghi log hệ thống để kiểm tra (vào storage/logs/laravel.log xem)
            Log::error("Lỗi xóa đơn hàng: " . $e->getMessage());

            // Trả về false để JS hiện thông báo lỗi
            return false;
        }
    }

    public function placeOrder(Request $request)
    {
        // DEBUG: Kiểm tra session ngay đầu
        Log::info('=== START placeOrder ===');
        Log::info('Session has coupon?', ['has' => Session::has('coupon')]);
        if (Session::has('coupon')) {
            Log::info('Coupon in session', Session::get('coupon'));
        }

        // 1. Validate dữ liệu
        $request->validate([
            'shipping_name' => 'required',
            'shipping_phone' => 'required',
            'shipping_address' => 'required',
            'tinh_thanh' => 'required',
            'quan_huyen' => 'required',
            'phuong_xa' => 'required',
        ]);

        // 2. Lấy dữ liệu giỏ hàng (Xử lý cả 2 trường hợp: Login và Chưa Login)
        $subtotal = 0;
        $orderData = [];
        $productTotal = 0; // Đổi tên biến này thành Tiền Hàng cho dễ hiểu
        $cartDB = null;

        if (Auth::check()) {
            // TRƯỜNG HỢP 1: Đã đăng nhập -> Lấy từ Database
            $cartDB = \App\Models\Cart::with('cartItems.product')
                ->where('user_id', Auth::id())
                ->first();

            if ($cartDB && $cartDB->cartItems->count() > 0) {
                foreach ($cartDB->cartItems as $item) {
                    $orderData[] = [
                        'product_id' => $item->product_id,
                        'name' => $item->product->name,
                        'price' => $item->product->price,
                        'quantity' => $item->quantity,
                        'total' => $item->product->price * $item->quantity
                    ];
                    $productTotal += $item->product->price * $item->quantity;
                }
            }
        } else {
            // TRƯỜNG HỢP 2: Khách vãng lai -> Lấy từ Session
            $sessionCart = Session::get('cart', []);
            foreach ($sessionCart as $id => $details) {
                $orderData[] = [
                    'product_id' => $id,
                    'name' => $details['name'],
                    'price' => $details['price'],
                    'quantity' => $details['quantity'],
                    'total' => $details['price'] * $details['quantity']
                ];
                $productTotal += $details['price'] * $details['quantity'];
            }
        }

        // Kiểm tra lại lần cuối xem có hàng không
        if (empty($orderData)) {
            return redirect()->back()->with('error', 'Giỏ hàng trống! Vui lòng chọn sản phẩm.');
        }

        $shippingFee = 50000;
        $discountAmount = 0;
        $couponCode = null;

        // Tính toán discount từ coupon session (giống CheckoutController)
        if (Session::has('coupon')) {
            $coupon = Session::get('coupon');
            $couponCode = $coupon['code'] ?? null;

            Log::info('Coupon applied in placeOrder', [
                'code' => $couponCode,
                'type' => $coupon['type'] ?? null,
                'value' => $coupon['value'] ?? null
            ]);

            if ($coupon['type'] == 'free_ship') {
                $tempShipping = $shippingFee - $coupon['value'];
                $shippingFee = $tempShipping < 0 ? 0 : $tempShipping;
                Log::info('Free ship applied', ['new_shipping_fee' => $shippingFee]);
            } elseif ($coupon['type'] == 'fixed') {
                // Giảm giá cố định (VD: 100.000đ)
                $discountAmount = $coupon['value'];
            } elseif ($coupon['type'] == 'percent') {
                // Giảm giá theo % (VD: 10%)
                $discountAmount = ($productTotal * $coupon['value']) / 100;
            }
        } else {
            Log::info('No coupon in session when placing order');
        }

        // Miễn phí ship nếu đơn hàng > 10 triệu
        if ($productTotal > 10000000) {
            $shippingFee = 0;
        }

        $finalTotalAmount = $productTotal + $shippingFee - $discountAmount;
        if ($finalTotalAmount < 0) $finalTotalAmount = 0;
        DB::beginTransaction();
        try {
            $fullAddress = $request->shipping_address . ', ' . $request->phuong_xa . ', ' . $request->quan_huyen . ', ' . $request->tinh_thanh;

            $order = Order::create([
                'user_id' => Auth::id() ?? null,
                'shipping_name' => $request->shipping_name,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $fullAddress,
                'notes' => $request->ghichu,
                'payment_method' => $request->payment_method ?? 'COD',

                // LƯU CÁC SỐ QUAN TRỌNG
                'total_amount' => $finalTotalAmount,       // Tổng thực trả
                'discount_amount' => $discountAmount,      // Lưu số tiền đã giảm
                'shipping_fee' => $shippingFee,            // Lưu phí vận chuyển

                'status' => 'pending',
            ]);

            foreach ($orderData as $item) {
                OderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // 4. Xóa giỏ hàng sau khi đặt thành công
            if (Auth::check() && $cartDB) {
                \App\Models\CartItem::where('cart_id', $cartDB->id)->delete();
                $cartDB->update(['totalPrice' => 0]);
            } else {
                Session::forget('cart');
            }

            DB::commit();

            return redirect()->route('order.success', ['id' => $order->id])->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    public function showSuccess($id)
    {
        // Lấy đơn hàng kèm theo chi tiết sản phẩm
        $order = Order::with('orderItems')->findOrFail($id);

        // Kiểm tra quyền: Chỉ cho xem nếu là chủ đơn hàng (nếu bạn muốn bảo mật chặt hơn)
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403);
        }
        $categories = \App\Models\Category::all();

        return view('client.checkout.checkout_success', compact('order', 'categories'));
    }

    public function showDetail($id)
    {
        // Lấy đơn hàng kèm theo chi tiết sản phẩm
        // Lưu ý: 'orderItems' là tên function trong Model Order bạn đã cung cấp
        $order = Order::with('orderItems')->findOrFail($id);

        // Trả về một View riêng (Partial View) chỉ chứa nội dung modal
        return view('admin.orders.detail_modal', compact('order'));
    }
}

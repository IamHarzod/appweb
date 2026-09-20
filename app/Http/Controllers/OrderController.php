<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OderItem;
use App\Models\Product;
use App\Services\OrderService;
use App\Http\Requests\PlaceOrderRequest;
use Illuminate\Support\Facades\Session;
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
        $orders = Order::with('orderItems.product')->where('user_id', $user->id)->orderByDesc('id')->paginate(15);
        return view('client.orders.my_orders', compact('orders'));
    }

    // Show single order (ensure ownership or admin)
    public function show($id)
    {
        $order = Order::with(['orderItems.product', 'user'])->findOrFail($id);
        if (Auth::user()->role !== 'admin' && $order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }
        return view('client.orders.show', compact('order'));
    }

    // Customer cancel own order (only allowed when pending)
    public function userCancel(Request $request, $id)
    {
        $order = Order::with('orderItems')->findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Đơn hàng này hiện tại không thể hủy vì đã được xác nhận hoặc đang vận chuyển.');
        }

        DB::beginTransaction();
        try {
            // Hoàn lại tồn kho cho các sản phẩm
            foreach ($order->orderItems as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stockQuantity', (int)$item->quantity);
                }
            }

            $order->status = 'cancelled';
            $order->notes = trim(($order->notes ? $order->notes . ' | ' : '') . 'Khách hàng tự hủy đơn vào ' . now()->format('d/m/Y H:i'));
            $order->save();

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Đã hủy đơn hàng thành công!']);
            }
            return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi khách hủy đơn: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Lỗi khi hủy đơn hàng: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Lỗi khi hủy đơn: ' . $e->getMessage());
        }
    }

    // Show guest order lookup page
    public function showLookupForm(Request $request)
    {
        $order = null;
        $orderId = $request->query('order_id');
        $phone = $request->query('phone');

        if ($orderId && $phone) {
            $order = Order::with(['orderItems.product'])
                ->where('id', $orderId)
                ->where(function ($q) use ($phone) {
                    $q->where('shipping_phone', $phone)
                      ->orWhere('shipping_phone', 'like', '%' . substr($phone, -9));
                })
                ->first();
        }

        return view('client.orders.lookup', compact('order', 'orderId', 'phone'));
    }

    // Process lookup form POST
    public function processLookup(Request $request)
    {
        $request->validate([
            'order_id' => 'required|numeric',
            'phone'    => 'required|string',
        ], [
            'order_id.required' => 'Vui lòng nhập mã đơn hàng.',
            'order_id.numeric'  => 'Mã đơn hàng phải là số.',
            'phone.required'    => 'Vui lòng nhập số điện thoại đặt hàng.',
        ]);

        $order = Order::with(['orderItems.product'])
            ->where('id', $request->order_id)
            ->where(function ($q) use ($request) {
                $q->where('shipping_phone', $request->phone)
                  ->orWhere('shipping_phone', 'like', '%' . substr($request->phone, -9));
            })
            ->first();

        if (!$order) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Không tìm thấy đơn hàng với thông tin đã nhập. Vui lòng kiểm tra lại Mã đơn và Số điện thoại.');
        }

        return view('client.orders.lookup', [
            'order'    => $order,
            'orderId' => $request->order_id,
            'phone'   => $request->phone,
        ]);
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
            $user = Auth::user();
            $order = Order::create([
                'user_id'          => Auth::id(),
                'shipping_name'    => $user->name ?? 'Khách hàng',
                'shipping_email'   => $user->email ?? 'no-email@example.com',
                'shipping_phone'   => $user->phoneNumber ?? '',
                'shipping_address' => 'Địa chỉ mặc định',
                'payment_method'   => 'COD',
                'total_amount'     => $totalPrice,
                'discount_amount'  => 0,
                'shipping_fee'     => 0,
                'status'           => 'pending',
            ]);

            foreach ($items as $cartItem) {
                $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();
                if (!$product) {
                    continue;
                }
                if ($product->stockQuantity < $cartItem->quantity) {
                    throw new \Exception('Sản phẩm "' . $product->name . '" không đủ tồn kho (còn: ' . $product->stockQuantity . ').');
                }
                $product->decrement('stockQuantity', $cartItem->quantity);

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'quantity'     => (int) $cartItem->quantity,
                    'price'        => (float) $product->price,
                ]);
            }
            // Xóa cart items sau khi tạo order
            \App\Models\CartItem::where('cart_id', $cart->id)->delete();
            $cart->updateTotal();

            DB::commit();
            // Xóa session cart legacy nếu còn
            session()->forget('cart');
            session(['last_placed_order_id' => $order->id]);
            return redirect()->route('order.success', ['id' => $order->id])->with('success', 'Đặt hàng thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
        }
    }

    // Update order status (Admin)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipping,completed,cancelled',
        ]);

        try {
            $order = Order::findOrFail($id);
            $order->status = $request->status;
            $order->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái đơn hàng thành công!']);
            }
            return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } catch (\Throwable $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Lỗi cập nhật trạng thái: ' . $e->getMessage());
        }
    }

    // Delete order (admin or owner)
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $order = Order::findOrFail($id);
                $order->orderItems()->delete();
                $order->delete();
            });

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Xóa đơn hàng thành công!']);
            }
            return redirect()->route('admin.orders.index')->with('success', 'Xóa đơn hàng thành công!');
        } catch (\Throwable $e) {
            Log::error("Lỗi xóa đơn hàng: " . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Lỗi khi xóa đơn hàng: ' . $e->getMessage());
        }
    }

    public function placeOrder(PlaceOrderRequest $request, OrderService $orderService)
    {
        Log::info('=== START placeOrder ===', ['user_id' => Auth::id()]);

        try {
            $shippingData = array_merge($request->validated(), [
                'shipping_fee'     => $request->input('shipping_fee'),
                'tinh_thanh_name'   => $request->input('tinh_thanh_name'),
                'quan_huyen_name'  => $request->input('quan_huyen_name'),
                'phuong_xa_name'   => $request->input('phuong_xa_name'),
                'latitude'         => $request->input('latitude'),
                'longitude'        => $request->input('longitude'),
                'to_district_id'   => $request->input('to_district_id'),
                'to_ward_code'     => $request->input('to_ward_code'),
                'ghichu'           => $request->input('ghichu'),
                'payment_method'   => $request->input('payment_method', 'COD'),
            ]);

            $order = $orderService->placeOrder($shippingData, $request);

            return redirect()->route('order.success', ['id' => $order->id])->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }


    public function showSuccess($id)
    {
        // Lấy đơn hàng kèm theo chi tiết sản phẩm
        $order = Order::with('orderItems.product')->findOrFail($id);

        // Kiểm tra quyền chống IDOR: Chỉ cho xem nếu là chủ đơn hàng hoặc khách vừa đặt trong session
        if (Auth::check()) {
            if ($order->user_id && $order->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền xem thông tin đơn hàng này.');
            }
        } else {
            if (session('last_placed_order_id') != $id) {
                abort(403, 'Bạn không có quyền xem thông tin đơn hàng này.');
            }
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

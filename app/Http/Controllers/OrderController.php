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
use App\Services\MoMoService;
use App\Services\VNPayService;

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
            $paymentMethod = $request->input('payment_method', 'COD');
            $shippingData = array_merge($request->validated(), [
                'shipping_name'    => $request->input('shipping_name'),
                'shipping_phone'   => $request->input('shipping_phone'),
                'shipping_email'   => $request->input('shipping_email'),
                'shipping_address' => $request->input('shipping_address'),
                'shipping_fee'     => $request->input('shipping_fee'),
                'tinh_thanh_name'   => $request->input('tinh_thanh_name', $request->input('tinh_thanh')),
                'quan_huyen_name'  => $request->input('quan_huyen_name', $request->input('quan_huyen')),
                'phuong_xa_name'   => $request->input('phuong_xa_name', $request->input('phuong_xa')),
                'latitude'         => $request->input('latitude'),
                'longitude'        => $request->input('longitude'),
                'to_district_id'   => $request->input('to_district_id'),
                'to_ward_code'     => $request->input('to_ward_code'),
                'ghichu'           => $request->input('ghichu'),
                'payment_method'   => $paymentMethod,
            ]);

            $order = $orderService->placeOrder($shippingData, $request);

            // Nếu người dùng chọn thanh toán qua VNPAY -> Redirect trực tiếp sang cổng VNPAY Sandbox
            if ($paymentMethod === 'VNPAY') {
                $vnpayService = new VNPayService();
                $vnpayUrl = $vnpayService->createPaymentUrl($order);
                return redirect()->away($vnpayUrl);
            }

            // Nếu người dùng chọn thanh toán qua MoMo
            if ($paymentMethod === 'MOMO') {
                $momoService = new MoMoService();
                $momoRes = $momoService->createPayment($order);

                if ($momoRes['success'] && !empty($momoRes['payUrl'])) {
                    return redirect()->away($momoRes['payUrl']);
                }

                // Nếu MoMo API lỗi/tài khoản test khóa (vd: lỗi 13), tự động chuyển sang trang MoMo Giả lập để test luồng
                return redirect()->route('momo.mock_pay', ['order_id' => $order->id]);
            }

            return redirect()->route('order.success', ['id' => $order->id])->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    /**
     * Callback Redirect từ MoMo sau khi khách thanh toán xong
     */
    public function momoReturn(Request $request)
    {
        Log::info('MoMo Return parameters:', $request->all());

        $momoService = new MoMoService();
        $isValidSignature = $momoService->verifySignature($request->all());

        $orderIdParam = $request->input('orderId');
        $resultCode = $request->input('resultCode');
        $transId = $request->input('transId');
        $message = $request->input('message');

        // Extract order_id thực tế từ chuỗi orderId (VD: "12_1726000000" -> 12)
        $orderIdParts = explode('_', $orderIdParam ?? '');
        $orderId = $orderIdParts[0] ?? null;

        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đơn hàng thanh toán.');
        }

        if ($resultCode == 0) {
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $transId ?? $orderIdParam,
            ]);

            return redirect()->route('order.success', ['id' => $order->id])
                ->with('success', 'Thanh toán qua Ví MoMo thành công!');
        } else {
            $order->update([
                'payment_status' => 'failed',
            ]);

            $reasonDetail = $this->getMoMoErrorMessage((string)$resultCode, $message);
            $reason = $reasonDetail . ($resultCode !== null ? ' (Mã lỗi: ' . $resultCode . ')' : '');
            session(['order_failure_reason_' . $order->id => $reason]);

            return redirect()->route('order.success', ['id' => $order->id])
                ->with('error', $reason)
                ->with('payment_failed', true);
        }
    }

    /**
     * IPN Webhook ngầm từ MoMo server gửi về
     */
    public function momoIpn(Request $request)
    {
        Log::info('MoMo IPN Callback payload:', $request->all());

        $momoService = new MoMoService();
        $isValidSignature = $momoService->verifySignature($request->all());

        if (!$isValidSignature) {
            Log::warning('MoMo IPN invalid signature!');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $orderIdParam = $request->input('orderId');
        $resultCode = $request->input('resultCode');
        $transId = $request->input('transId');

        $orderIdParts = explode('_', $orderIdParam ?? '');
        $orderId = $orderIdParts[0] ?? null;

        $order = Order::find($orderId);

        if ($order) {
            if ($resultCode == 0) {
                $order->update([
                    'payment_status' => 'paid',
                    'transaction_id' => $transId ?? $orderIdParam,
                ]);
            } else {
                $order->update([
                    'payment_status' => 'failed',
                ]);
            }
        }

        return response()->json(['message' => 'Received'], 200);
    }

    /**
     * Callback Redirect từ VNPay sau khi khách thanh toán xong
     */
    public function vnpayReturn(Request $request)
    {
        Log::info('VNPay Return parameters:', $request->all());

        $vnpayService = new VNPayService();
        $isValidSignature = $vnpayService->verifySignature($request->all());

        $vnp_TxnRef = $request->input('vnp_TxnRef');
        $vnp_ResponseCode = $request->input('vnp_ResponseCode');
        $vnp_TransactionNo = $request->input('vnp_TransactionNo');

        $orderIdParts = explode('_', $vnp_TxnRef ?? '');
        $orderId = $orderIdParts[0] ?? null;

        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đơn hàng thanh toán.');
        }

        if ($isValidSignature && $vnp_ResponseCode == '00') {
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $vnp_TransactionNo ?? $vnp_TxnRef,
            ]);

            return redirect()->route('order.success', ['id' => $order->id])
                ->with('success', 'Thanh toán qua VNPAY thành công!');
        } else {
            $order->update([
                'payment_status' => 'failed',
            ]);

            $reasonDetail = $this->getVNPayErrorMessage($vnp_ResponseCode);
            if (!$isValidSignature) {
                $reasonDetail = 'Chữ ký bảo mật không hợp lệ (Dữ liệu giao dịch có thể đã bị can thiệp).';
            }

            $reason = $reasonDetail . ($vnp_ResponseCode ? ' (Mã lỗi: ' . $vnp_ResponseCode . ')' : '');
            session(['order_failure_reason_' . $order->id => $reason]);

            return redirect()->route('order.success', ['id' => $order->id])
                ->with('error', $reason)
                ->with('payment_failed', true);
        }
    }

    /**
     * IPN Webhook ngầm từ VNPay Server gửi về
     */
    public function vnpayIpn(Request $request)
    {
        Log::info('VNPay IPN parameters:', $request->all());

        $vnpayService = new VNPayService();
        $isValidSignature = $vnpayService->verifySignature($request->all());

        if (!$isValidSignature) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid Checksum'], 200);
        }

        $vnp_TxnRef = $request->input('vnp_TxnRef');
        $vnp_ResponseCode = $request->input('vnp_ResponseCode');
        $vnp_TransactionNo = $request->input('vnp_TransactionNo');

        $orderIdParts = explode('_', $vnp_TxnRef ?? '');
        $orderId = $orderIdParts[0] ?? null;

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order Not Found'], 200);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed'], 200);
        }

        if ($vnp_ResponseCode == '00') {
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $vnp_TransactionNo ?? $vnp_TxnRef,
            ]);
        } else {
            $order->update([
                'payment_status' => 'failed',
            ]);
        }

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success'], 200);
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

    public function momoMockPay($order_id)
    {
        $order = Order::findOrFail($order_id);
        $categories = \App\Models\Category::all();

        return view('client.checkout.momo_mock', compact('order', 'categories'));
    }

    /**
     * Cho phép khách hàng thực hiện thanh toán lại nếu trước đó bị hủy hoặc thất bại
     */
    public function repay($id)
    {
        $order = Order::findOrFail($id);

        // Kiểm tra quyền chống IDOR
        if (Auth::check()) {
            if ($order->user_id && $order->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền thực hiện thanh toán đơn hàng này.');
            }
        } else {
            if (session('last_placed_order_id') != $id) {
                abort(403, 'Bạn không có quyền thực hiện thanh toán đơn hàng này.');
            }
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('order.success', ['id' => $order->id])
                ->with('warning', 'Đơn hàng này đã được thanh toán thành công trước đó.');
        }

        $method = strtoupper($order->payment_method ?? '');

        if ($method === 'VNPAY') {
            $vnpayService = new VNPayService();
            $vnpayUrl = $vnpayService->createPaymentUrl($order);
            return redirect()->away($vnpayUrl);
        }

        if ($method === 'MOMO') {
            $momoService = new MoMoService();
            $momoRes = $momoService->createPayment($order);

            if ($momoRes['success'] && !empty($momoRes['payUrl'])) {
                return redirect()->away($momoRes['payUrl']);
            }

            return redirect()->route('momo.mock_pay', ['order_id' => $order->id]);
        }

        return redirect()->route('order.success', ['id' => $order->id])
            ->with('error', 'Phương thức thanh toán hiện tại không hỗ trợ thanh toán lại trực tuyến.');
    }

    /**
     * Lấy mô tả chi tiết lý do lỗi từ mã phản hồi VNPAY
     */
    protected function getVNPayErrorMessage(?string $code): string
    {
        $errors = [
            '00' => 'Giao dịch thành công.',
            '07' => 'Trừ tiền thành công nhưng giao dịch bị nghi ngờ bất thường.',
            '09' => 'Thẻ/Tài khoản của quý khách chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần.',
            '11' => 'Đã hết hạn chờ thanh toán. Vui lòng thực hiện lại giao dịch.',
            '12' => 'Thẻ/Tài khoản của quý khách đang bị khóa.',
            '13' => 'Quý khách nhập sai mật khẩu xác thực OTP.',
            '24' => 'Khách hàng đã hủy giao dịch thanh toán.',
            '51' => 'Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Tài khoản của quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Khách hàng nhập sai mật khẩu thanh toán quá số lần quy định.',
            '97' => 'Chữ ký bảo mật không hợp lệ (Dữ liệu có thể đã bị can thiệp).',
            '99' => 'Lỗi kết nối hoặc hệ thống ngân hàng đang bận.',
        ];

        if (!$code) {
            return 'Giao dịch thanh toán VNPAY không hoàn tất.';
        }

        return $errors[$code] ?? ('Lỗi giao dịch VNPAY không xác định (Mã lỗi: ' . $code . ').');
    }

    /**
     * Lấy mô tả chi tiết lý do lỗi từ mã phản hồi MoMo
     */
    protected function getMoMoErrorMessage(?string $resultCode, ?string $defaultMessage = null): string
    {
        $errors = [
            '0'    => 'Giao dịch thành công.',
            '1006' => 'Khách hàng đã hủy giao dịch thanh toán.',
            '1001' => 'Tài khoản không đủ số dư để thực hiện giao dịch.',
            '1002' => 'Giao dịch bị từ chối do nhà phát hành tài khoản.',
            '1003' => 'Giao dịch đã hết hạn chờ thanh toán.',
            '1004' => 'Số tiền giao dịch vượt quá hạn mức thanh toán trong ngày.',
            '1005' => 'Mã thanh toán hoặc QR code đã hết hạn.',
            '1007' => 'Tài khoản người dùng đang bị tạm khóa.',
            '49'   => 'Khách hàng đã hủy giao dịch trước khi thanh toán.',
        ];

        if ($resultCode && isset($errors[(string)$resultCode])) {
            return $errors[(string)$resultCode];
        }

        return $defaultMessage ?: 'Giao dịch qua Ví MoMo không thành công hoặc đã bị hủy.';
    }
}

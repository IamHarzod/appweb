<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

use function Symfony\Component\Clock\now;

class CartController extends Controller
{
    public function show_cart()
    {
        $shippingFee = 50000;
        $discountAmount = 0;
        $subtotal = 0;
        $categories = Category::orderBy("id", "desc")->get();
        $cart = null;
        $cartItems = collect();

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cartItems = CartItem::with('product')->where('cart_id', $cart->id)->get();
                foreach ($cartItems as $item) {
                    $subtotal += $item->product->price * $item->quantity;
                }
            }
        }
        if (Session::has('coupon')) {
            $coupon = Session::get('coupon');

            if ($coupon['type'] == 'free_ship') {
                $shippingFee = $shippingFee - $coupon['value'];
                if ($shippingFee < 0) {
                    $shippingFee = 0; //ko dc am
                }
            } elseif ($coupon['type'] == 'fixed') {
                $discountAmount = $coupon['value'];
            } elseif ($coupon['type'] == 'percent') {
                $discountAmount = ($subtotal * $coupon['value']) / 100;
            }
        }
        $totalPrice = $subtotal + $shippingFee - $discountAmount;

        if ($totalPrice < 0) $totalPrice = 0;

        return view("client.cart.show_cart")->with(compact(
            "categories",
            "cart",
            "cartItems",
            "subtotal",
            "shippingFee",
            "discountAmount",
            "totalPrice"
        ));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart(Request $request)
    {
        \Log::info('CartController@addToCart called', [
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'ip' => $request->ip()
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng'
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);

            // Kiểm tra tồn kho
            if ($product->stockQuantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng sản phẩm không đủ trong kho'
                ], 400);
            }

            // Tìm hoặc tạo giỏ hàng cho user
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['totalAmount' => 0]
            );

            // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingItem) {
                // Cập nhật số lượng
                $newQuantity = $existingItem->quantity + $request->quantity;

                // Kiểm tra tồn kho tổng
                if ($product->stockQuantity < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Số lượng sản phẩm vượt quá tồn kho'
                    ], 400);
                }

                $existingItem->quantity = $newQuantity;
                $existingItem->save();
            } else {
                // Thêm mới sản phẩm vào giỏ hàng
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity
                ]);
            }

            // Cập nhật tổng tiền giỏ hàng
            $cart->updateTotal();

            DB::commit();

            \Log::info('Cart item added successfully', [
                'cart_id' => $cart->id,
                'cart_items_count' => $cart->cartItems()->count(),
                'total_amount' => $cart->totalAmount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm sản phẩm vào giỏ hàng thành công',
                'cart_total' => $cart->totalAmount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('CartController@addToCart failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'product_id' => $request->product_id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function updateCartItem(Request $request, $cartItemId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $cartItem = CartItem::with(['cart', 'product'])
                ->where('id', $cartItemId)
                ->whereHas('cart', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->firstOrFail();

            // Kiểm tra tồn kho
            if ($cartItem->product->stockQuantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng sản phẩm không đủ trong kho'
                ], 400);
            }

            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            // Cập nhật tổng tiền giỏ hàng
            $cartItem->cart->updateTotal();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật giỏ hàng thành công',
                'cart_total' => $cartItem->cart->totalAmount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa một sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart($cartItemId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        try {
            DB::beginTransaction();

            $cartItem = CartItem::with('cart')
                ->where('id', $cartItemId)
                ->whereHas('cart', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->firstOrFail();

            $cart = $cartItem->cart;
            $cartItem->delete();

            // Cập nhật tổng tiền giỏ hàng
            $cart->updateTotal();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa sản phẩm khỏi giỏ hàng thành công',
                'cart_total' => $cart->totalAmount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa tất cả sản phẩm trong giỏ hàng
     */
    public function clearCart()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        try {
            DB::beginTransaction();

            $cart = Cart::where('user_id', Auth::id())->first();

            if ($cart) {
                // Xóa tất cả cart items
                CartItem::where('cart_id', $cart->id)->delete();

                // Reset tổng tiền về 0
                $cart->totalAmount = 0;
                $cart->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa tất cả sản phẩm trong giỏ hàng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin giỏ hàng (API)
     */
    public function getCart()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập'
            ], 401);
        }

        $cart = Cart::with(['cartItems.product'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cart_items' => [],
                    'total_amount' => 0,
                    'total_items' => 0
                ]
            ]);
        }

        $cartItems = $cart->cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'product_price' => $item->product->price,
                'product_image' => $item->product->imageURL,
                'quantity' => $item->quantity,
                'subtotal' => $item->quantity * $item->product->price
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'cart_items' => $cartItems,
                'total_amount' => $cart->totalAmount,
                'total_items' => $cartItems->sum('quantity')
            ]
        ]);
    }

    /**
     * Lấy tóm tắt giỏ hàng (public) để hiển thị bộ đếm – không yêu cầu đăng nhập
     */
    public function getCartSummary()
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_items' => 0,
                    'total_amount' => 0,
                ]
            ]);
        }

        $cart = Cart::with(['cartItems'])
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->first();

        if (!$cart) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_items' => 0,
                    'total_amount' => 0,
                ]
            ]);
        }

        $totalItems = $cart->cartItems->sum('quantity');
        return response()->json([
            'success' => true,
            'data' => [
                'total_items' => $totalItems,
                'total_amount' => $cart->totalAmount,
            ]
        ]);
    }

    public function applyCoupon($request)
    {
        $code = $request->input('coupon_code');
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return redirect()->back()->with('error', 'Mã giảm giá sai hoặc không tồn tại!');
        }
        if ($coupon->quantity <= 0) {
            return redirect()->back()->with('error', 'Mã giảm giá đã hết lượt sử dụng!');
        }
        if ($coupon->expiry_date && now() > ($coupon->expiry_date)) {
            return redirect()->back()->with('error', 'Mã giảm giá đã hết hạn');
        }

        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value'  => $coupon->value,
        ]);

        return redirect()->back()->with('success', 'Áp dụng mã giảm giá thành công!');
    }

    public function removeCoupon()
    {
        Session::forget('coupon');
        return redirect()->back()->with('success', 'Đã gỡ bỏ mã giảm giá!');
    }

    public function checkCoupon(Request $request)
    {
        $data = $request->all();
        $coupon = Coupon::where('code', $data['code_input'])->first();

        if (!$coupon) {
            return redirect()->back()->with('error', 'Mã giảm giá sai hoặc không tồn tại');
        }
        if (Carbon::now()->gt(Carbon::parse($coupon->expiry_date))) {
            return redirect()->back()->with('error', 'Mã giảm giá đã hết hạn');
        }
        if ($coupon->quantity <= 0) {
            return redirect()->back()->with('error', 'Mã giảm giá đã hết số lượng!');
        }
        // nếu ok
        Session::put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);

        return redirect()->back()->with('success', 'Áp dụng mã thành công');
    }
}

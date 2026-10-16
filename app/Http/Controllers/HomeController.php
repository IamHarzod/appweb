<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Brand;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function show_home()
    {
        $categories = Category::orderBy("id", "desc")->get();
        $all_product = Product::with(['category', 'brand'])->where('IsActive', 1)->orderBy("id", "desc")->get();
        $our_product = Product::with(['category', 'brand'])->where('IsActive', 1)->orderBy("id", "desc")->limit(8)->get();
        $best_seller_product = Product::with(['category', 'brand'])
            ->withSum('oderItems', 'quantity')
            ->orderBy('oder_items_sum_quantity', 'desc')
            ->limit(6)
            ->get();
        return view("client.home.index_home")->with(compact("categories", "all_product", "our_product", "best_seller_product"));
    }


    public function show_category_home()
    {
        $categories = Category::orderBy('name')
            ->get();

        return view('layout.home_layout', compact('categories'));
    }

    public function show_product_category_home($id)
    {
        $categories = Category::orderBy("id", "asc")->get();
        $currentCategory = Category::find($id);
        $product = Product::with(['category', 'brand', 'reviews'])
            ->where('category_id', $id)
            ->where('IsActive', 1)
            ->orderBy('id', 'desc')
            ->get();
        $brands = Brand::where('TrangThai', 1)->get();
        return view('client.home.product_category', compact('product', 'categories', 'currentCategory', 'brands'));
    }

    public function show_product_detail($id)
    {
        $product = Product::with([
            'category',
            'brand',
            'reviews' => fn($q) => $q->where('is_approved', true)->orderBy('id', 'desc'),
        ])->findOrFail($id);

        $categories = Category::orderBy("id", "desc")->get();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $canReview = false;
        if (Auth::check()) {
            $userId = Auth::id();
            $canReview = OrderItem::whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('status', '!=', 'cancelled');
            })->where('product_id', $id)->exists();
        }

        return view('client.product.detail', compact('product', 'categories', 'relatedProducts', 'canReview'));
    }

    public function store_product_review(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $orderId = $request->input('order_id');
        $userId = Auth::id();

        // 1. Nếu có order_id (quy trình đánh giá trực tiếp sau khi mua hoặc từ chi tiết đơn hàng)
        if ($orderId) {
            $order = \App\Models\Order::where('id', $orderId)->first();
            if (!$order) {
                $msg = 'Không tìm thấy thông tin đơn hàng tương ứng.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 404);
                }
                return redirect()->back()->with('error', $msg);
            }

            // Kiểm tra sản phẩm có trong đơn hàng này không
            $itemInOrder = OrderItem::where('order_id', $order->id)
                ->where('product_id', $product->id)
                ->exists();

            if (!$itemInOrder) {
                $msg = 'Sản phẩm này không thuộc đơn hàng #' . $order->id . '.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return redirect()->back()->with('error', $msg);
            }

            // Kiểm tra quyền: Thành viên sở hữu đơn hàng hoặc khách vừa đặt đơn trong session hiện tại
            $isAuthorized = false;
            if (Auth::check() && ($order->user_id == $userId || Auth::user()->role === 'admin')) {
                $isAuthorized = true;
            } elseif (session('last_placed_order_id') == $order->id) {
                $isAuthorized = true;
            }

            if (!$isAuthorized) {
                $msg = 'Bạn không có quyền đánh giá đơn hàng này.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 403);
                }
                return redirect()->back()->with('error', $msg);
            }

            if ($order->status === 'cancelled') {
                $msg = 'Đơn hàng đã bị hủy, không thể gửi đánh giá.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return redirect()->back()->with('error', $msg);
            }
        } else {
            // 2. Nếu không có order_id (đánh giá từ trang chi tiết sản phẩm công khai)
            if (!Auth::check()) {
                $msg = 'Vui lòng đăng nhập để đánh giá sản phẩm!';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 403);
                }
                return redirect()->back()->with('error', $msg);
            }

            // Nghiệp vụ: Khách hàng đã mua sản phẩm và nhận hàng thành công
            $hasPurchased = OrderItem::whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('status', '!=', 'cancelled')
                  ->where(fn ($order) => $order->whereIn('status', ['completed', 'delivered'])->orWhere('shipping_status', 'delivered'));
            })->where('product_id', $id)->exists();

            if (!$hasPurchased) {
                $msg = 'Bạn chỉ có thể đánh giá sản phẩm sau khi đã mua và nhận hàng thành công!';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 403);
                }
                return redirect()->back()->with('error', $msg);
            }
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
            'author_name' => 'nullable|string|max:100',
        ]);

        if (Auth::check()) {
            $user = Auth::user();
            $authorName = ($validated['author_name'] ?? null) ?: ($user->name ?: ($user->email ? explode('@', $user->email)[0] : 'Thành viên'));
        } else {
            $authorName = ($validated['author_name'] ?? null) ?: ($order->shipping_name ?? 'Khách mua hàng');
        }

        if ($userId) {
            $review = ProductReview::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'user_id'    => $userId,
                ],
                [
                    'author_name'          => $authorName,
                    'rating'               => (int) $validated['rating'],
                    'comment'              => trim($validated['comment']),
                    'is_verified_purchase' => true,
                    'is_approved'          => true,
                ]
            );
        } else {
            $review = ProductReview::create([
                'product_id'           => $product->id,
                'user_id'              => null,
                'author_name'          => $authorName,
                'rating'               => (int) $validated['rating'],
                'comment'              => trim($validated['comment']),
                'is_verified_purchase' => true,
                'is_approved'          => true,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã gửi đánh giá cho sản phẩm!',
                'review'  => [
                    'author_name'          => $review->author_name,
                    'rating'               => $review->rating,
                    'comment'              => $review->comment,
                    'created_at'           => $review->created_at->diffForHumans(),
                    'is_verified_purchase' => true,
                ]
            ]);
        }

        return redirect()->back()->with('success_review', 'Cảm ơn bạn đã gửi đánh giá cho sản phẩm!');
    }
}

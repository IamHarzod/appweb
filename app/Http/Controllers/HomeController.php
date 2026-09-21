<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
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
        $categories = Category::orderBy("id", "desc")->get();
        $product = Product::where('category_id', $id)->orderBy('id', 'desc')->get();
        return view('client.home.product_category', compact('product', 'categories'));
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

        return view('client.product.detail', compact('product', 'categories', 'relatedProducts'));
    }

    public function store_product_review(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
            'author_name' => 'nullable|string|max:100',
        ]);

        $userId = null;
        $authorName = 'Khách hàng';
        $isVerified = false;

        if (Auth::check()) {
            $user = Auth::user();
            $userId = $user->id;
            $authorName = $user->name ?: ($user->email ? explode('@', $user->email)[0] : 'Thành viên');
            $isVerified = OrderItem::whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('product_id', $id)->exists();
        }

        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $userId,
            'author_name' => $authorName,
            'rating' => (int) $validated['rating'],
            'comment' => trim($validated['comment']),
            'is_verified_purchase' => $isVerified,
            'is_approved' => true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã gửi đánh giá cho sản phẩm!',
                'review' => [
                    'author_name' => $review->author_name,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->diffForHumans(),
                    'is_verified_purchase' => $review->is_verified_purchase,
                ]
            ]);
        }

        return redirect()->back()->with('success_review', 'Cảm ơn bạn đã gửi đánh giá cho sản phẩm!');
    }
}

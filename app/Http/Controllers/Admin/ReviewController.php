<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return view('admin.reviews', ['reviews' => ProductReview::with('product')->latest('id')->paginate(20)]);
    }

    public function update(Request $request, ProductReview $review)
    {
        $data = $request->validate(['is_approved' => ['required', 'boolean']]);
        $review->update($data);
        return back()->with('success', 'Đã cập nhật trạng thái đánh giá.');
    }
}

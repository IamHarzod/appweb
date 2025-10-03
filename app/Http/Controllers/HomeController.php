<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function show_home()
    {
        $categories = Category::orderBy("id", "desc")->get();
        return view("layout.home_layout")->with(compact("categories"));
    }

    public function category($id)
    {
        $category = Category::withCount('products')->findOrFail($id);

        // Lấy sản phẩm thuộc danh mục này, có phân trang
        $products = Product::where('category_id', $id)
            ->latest('id')
            ->paginate(12);

        return view('frontend.category', compact('category', 'products'));
    }
}

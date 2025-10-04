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
        $our_product = Product::orderBy("id", "desc")->limit(8)->get();
        $all_product = Product::orderBy("id", "desc")->get();
        return view("client.home.index_home")->with(compact("categories", "all_product", "our_product"));
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

    public function show_category_home()
    {
        // nếu có cột status => chỉ lấy danh mục đang bật
        $categories = Category::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('layout.home_layout', compact('categories')); // view nào chứa khối nav-bar thì truyền vào view đó
    }

    public function show_product_category_home($id)
    {
        $categories = Category::orderBy("id", "desc")->get();
        $product = Product::where('category_id', $id)->orderBy('id', 'desc')->get();
        return view('client.home.product_category', compact('product', 'categories'));
    }
}

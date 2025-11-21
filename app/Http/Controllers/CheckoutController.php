<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CheckoutController extends Controller
{
    public function show_checkout()
    {
        // lấy giỏ hàng từ ss
        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $categories = Category::all();
        return view("client.checkout.checkout_index", compact('categories', 'subtotal'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session; // <--- QUAN TRỌNG: Nhớ thêm dòng này để dùng Session

class CheckoutController extends Controller
{
    public function show_checkout()
    {
        $shippingFee = 50000;
        $discountAmount = 0;
        $subtotal = 0;

        $categories = Category::all();
        $cartItems = [];


        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();

            if ($cart) {

                $cartItems = $cart->items()->with('product')->get();


                foreach ($cartItems as $item) {
                    if ($item->product) {
                        $subtotal += $item->product->price * $item->quantity;
                    }
                }
            }
        }


        if (Session::has('coupon')) {
            $coupon = Session::get('coupon');
            if ($coupon['type'] == 'free_ship') {
                $shippingFee = $shippingFee - $coupon['value'];
                if ($shippingFee < 0) {
                    $shippingFee = 0;
                }
            } elseif ($coupon['type'] == 'fixed') {

                $discountAmount = $coupon['value'];
            } elseif ($coupon['type'] == 'percent') {

                $discountAmount = ($subtotal * $coupon['value']) / 100;
            }
        }

        $totalPrice = $subtotal + $shippingFee - $discountAmount;


        if ($totalPrice < 0) {
            $totalPrice = 0;
        }


        return view("client.checkout.checkout_index", compact(
            'categories',
            'cartItems',
            'subtotal',
            'shippingFee',
            'discountAmount',
            'totalPrice'
        ));
    }

    public function place_oder() {}
}

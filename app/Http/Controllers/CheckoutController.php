<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CartService;

class CheckoutController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function show_checkout()
    {
        $categories = Category::all();
        $cartData = $this->cartService->getCartData(50000);

        return view("client.checkout.checkout_index", array_merge(
            compact('categories'),
            $cartData
        ));
    }
}


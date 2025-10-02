<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show_cart()
    {
        return view("layout.cart_layout");
    }
}

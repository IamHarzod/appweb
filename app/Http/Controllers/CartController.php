<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;


class CartController extends Controller
{
    public function show_cart()
    {
        $categories = Category::orderBy("id", "desc")->get();
        return view("client.cart.show_cart")->with(compact("categories"));;
    }

    // public function show_category_cart()
    // {
    //     $categories = Category::orderBy("id", "desc")->get();
    //     return view('layout.cart_layout')->with(compact("categories"));; // view nào chứa khối nav-bar thì truyền vào view đó
    // }
}

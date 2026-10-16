<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ProfilesController extends Controller
{
    public function show_profile()
    {
        $categories = Category::orderBy("id", "desc")->get();
        $client = Auth::user();
        $orders = Order::with('orderItems.product')
            ->where('user_id', $client->id)
            ->orderByDesc('id')
            ->paginate(5);

        return view('layout.profile_layout', compact('client', 'categories', 'orders'));
    }
}

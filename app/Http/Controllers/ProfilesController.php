<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ProfilesController extends Controller
{
    public function show_profile()
    {
        $categories = Category::orderBy("id", "desc")->get();
        $client = Auth::user();
        return view('layout.profile_layout', compact('client', 'categories'));
    }
}

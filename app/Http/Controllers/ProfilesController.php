<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    public function show_profile() {
        $client = auth()->user();
        return view('layout.profile_layout', compact('client'));
    }
}

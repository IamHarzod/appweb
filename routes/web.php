<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BrandController;

// Client
Route::get('/', [HomeController::class, 'show_home'])->name('home');




// ADMIN
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'show_dasboard'])->name('dashboard');
    Route::get('/logout-admin', [AdminController::class, 'logout_admin'])->name('logout');
});
Route::get('/register-admin', [AdminController::class, 'register_admin']);
Route::get('/admin', [AdminController::class, 'login'])->name('admin');
Route::post('/submit-register-admin', [AdminController::class, 'submit_register']);
Route::post('/submit-login-admin', [AdminController::class, 'submit_login']);


// Brand
Route::get('/show-brand', [BrandController::class, 'show_brand']);
Route::post('/create-brand', [BrandController::class, 'create_brand']);
Route::delete('/brand/{brand}', [BrandController::class, 'destroy'])->name('brand.destroy');

//Product
Route::get('/show-product', [App\Http\Controllers\ProductController::class, 'show_product']);
Route::post('/create-product', [App\Http\Controllers\ProductController::class, 'create_product']);
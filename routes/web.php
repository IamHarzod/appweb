<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BrandController;
use App\Models\Product;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;


// Home
Route::get('/show-home', [HomeController::class, 'show_home'])->name('home');




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
Route::get('/delete-brand/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');
Route::get('/show-create-brand', [BrandController::class, 'showCreate']);
Route::get('/show-edit-brand/{id}', [BrandController::class, 'showEdit']);
// Edit Brand
Route::get('/show-edit-brand/{id}', [BrandController::class, 'showEdit']);
Route::post('/update-brand', [BrandController::class, 'update_brand']);

//Product
Route::get('/show-product', [App\Http\Controllers\ProductController::class, 'show_product']);
Route::post('/create-product', [App\Http\Controllers\ProductController::class, 'create_product']);
Route::get('/delete-product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');

// Category
Route::get('/show-category', [CategoryController::class, 'show_category'])->name('category.index');
Route::post('/create-category', [CategoryController::class, 'create_category'])->name('category.create');
Route::get('/edit-category/{id}', [CategoryController::class, 'edit'])->name('category.edit');
Route::post('/update-category/{id}', [CategoryController::class, 'update'])->name('category.update');
Route::delete('/delete-category/{id}', [CategoryController::class, 'destroy'])->name('delete-category');

// Cart
Route::get('/show-cart', [CartController::class, 'show_cart'])->name('cart');

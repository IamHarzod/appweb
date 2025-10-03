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

// Cart
Route::get('/show-cart', [CartController::class, 'show_cart'])->name('cart');


// ADMIN

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'show_dasboard'])->name('admin.dashboard');
    //LOGOUT AND LOGIN`
});
Route::middleware('auth')->group(function () {
    Route::get('/logout-admin', [AdminController::class, 'logout_admin'])->name('logout');
});
Route::get('/register-admin', [AdminController::class, 'register_admin']);
Route::get('/admin', [AdminController::class, 'login'])->name('admin');
Route::post('/submit-register-admin', [AdminController::class, 'submit_register']);
Route::post('/submit-login-admin', [AdminController::class, 'submit_login']);


// ADMIN FEATURE ROUTES (protected)
Route::middleware(['auth', 'admin'])->group(function () {
    // Brand
    Route::get('/show-brand', [BrandController::class, 'show_brand']);
    Route::post('/create-brand', [BrandController::class, 'create_brand']);
    Route::get('/delete-brand/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');
    Route::get('/show-create-brand', [BrandController::class, 'showCreate']);
    Route::get('/show-edit-brand/{id}', [BrandController::class, 'showEdit']);
    Route::post('/update-brand', [BrandController::class, 'update_brand']);

    // Product
    Route::get('/show-product', [App\Http\Controllers\ProductController::class, 'show_product']);
    Route::get('/show-create-product', [App\Http\Controllers\ProductController::class, 'show_create_product']);
    Route::post('/create-product', [App\Http\Controllers\ProductController::class, 'create_product']);
    Route::get('/delete-product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::get('/show-edit-product/{id}', [ProductController::class, 'show_edit'])->name('product.show_edit');
    Route::put('/update-product/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::post('/update-product/{id}', [ProductController::class, 'update']);

    // Category
    Route::post('/create-category', [CategoryController::class, 'create_category'])->name('category.create');
    Route::get('/show-category', [CategoryController::class, 'show_category'])->name('category.index');
    Route::get('/edit-category/{id}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::delete('/delete-category/{id}', [CategoryController::class, 'destroy'])->name('delete-category');
    Route::post('/update-category/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::get('/show-edit-category/{id}', [CategoryController::class, 'show_edit_modal'])->name('category.show_edit_modal');
    // Users management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{id}/role', [AdminController::class, 'update_user_role'])->name('admin.users.role');
});

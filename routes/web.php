<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BrandController;
use App\Models\Product;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CheckoutController;



// Home

Route::get('/', [HomeController::class, 'show_home'])->name('home');
Route::get('/show-category-home', [HomeController::class, 'show_category_home']);
Route::get('/show-product-category-home/{id}', [HomeController::class, 'show_product_category_home']);
Route::get('/product/{id}', [HomeController::class, 'show_product_detail'])->name('product.detail');

// Cart Routes (public)
Route::get('/show-cart', [CartController::class, 'show_cart'])->name('cart');
// Public cart summary for header counter (no auth required)
Route::get('/cart/summary', [CartController::class, 'getCartSummary'])->name('cart.summary');

// Cart Routes (authenticated)
Route::middleware('auth')->group(function () {
    // Cart API endpoints
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::put('/cart/update/{cartItemId}', [CartController::class, 'updateCartItem'])->name('cart.update');
    Route::delete('/cart/remove/{cartItemId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::get('/cart/api', [CartController::class, 'getCart'])->name('cart.api');
});

// Checkout Routes
Route::get('/show-checkout', [CheckoutController::class, 'show_checkout'])->name('checkout.index');
Route::post('/thanh-toan', [CheckoutController::class, 'processOrder'])->name('checkout.process');

// Orders (user scope)
Route::middleware('auth')->group(function () {
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my');
    Route::post('/orders/from-cart', [OrderController::class, 'storeFromCart'])->name('orders.store_from_cart');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
});

// Orders (admin scope)
Route::middleware(['auth','admin'])->group(function () {
    Route::get('/admin/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::delete('/admin/orders/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
});

//Profile
Route::middleware('auth')->group(function () {
    Route::get('/show-profile', [ProfilesController::class, 'show_profile'])
        ->name('profile');
});


Route::get('/show-category-cart', [CartController::class, 'show_category_cart']);


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

// Password Reset Routes
Route::get('/password/reset', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [PasswordResetController::class, 'reset'])->name('password.update');

// Test route để xem log reset password (chỉ để test)
Route::get('/test-password-reset/{email}', function ($email) {
    $resetRecord = DB::table('password_reset_tokens')->where('email', $email)->first();
    if ($resetRecord) {
        return response()->json([
            'email' => $resetRecord->email,
            'created_at' => $resetRecord->created_at,
            'token_exists' => true
        ]);
    }
    return response()->json(['message' => 'No reset record found for this email']);
});



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
    Route::get('/show-category', [CategoryController::class, 'show_category'])->name('category.index');
    Route::post('/create-category', [CategoryController::class, 'create_category'])->name('category.create');
    Route::get('/edit-category/{id}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::post('/update-category/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::get('/delete-category/{id}', [CategoryController::class, 'destroy'])->name('delete-category');
    Route::get('/show-edit-category/{id}', [CategoryController::class, 'show_edit_modal'])->name('category.show_edit_modal');
    // Users management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{id}/role', [AdminController::class, 'update_user_role'])->name('admin.users.role');

    //Coupon management
    Route::get('/show-coupon', [CouponController::class, 'index'])->name('coupon.index');
    Route::get('/show-create-coupon', [CouponController::class, 'create'])->name('show_create_coupon');
    Route::get('/edit-coupon/{id}', [CouponController::class, 'edit'])->name('show_coupon_edit');
    Route::post('/save', [CouponController::class, 'store'])->name('coupon.store');
    Route::get('/delete-coupon/{id}', [CouponController::class, 'destroy'])->name('coupon.delete');
    Route::post('/update-coupon/{id}', [CouponController::class, 'update'])->name('coupon.update');
    // Route sử lý coupon
    Route::post('/check-coupon', [CartController::class, 'checkCoupon'])->name('check_coupon');
    Route::get('/remove-coupon', [CartController::class, 'removeCoupon'])->name('remove_coupon');
    // thanh toan ok
    // Route::get('/order-success/{id}', [CheckoutController::class, 'order_success'])->name('order.success');
});

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
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\GHNController;

// GHN API Routes (Location & Fee)
Route::get('/ghn/provinces', [GHNController::class, 'getProvinces'])->name('ghn.provinces');
Route::get('/ghn/districts/{provinceId}', [GHNController::class, 'getDistricts'])->name('ghn.districts');
Route::get('/ghn/wards/{districtId}', [GHNController::class, 'getWards'])->name('ghn.wards');
Route::post('/ghn/calculate-fee', [GHNController::class, 'calculateFee'])->name('ghn.calculate_fee');



// Home
Route::get('/', [HomeController::class, 'show_home'])->name('home');
Route::get('/show-category-home', [HomeController::class, 'show_category_home']);
Route::get('/show-product-category-home/{id}', [HomeController::class, 'show_product_category_home'])->name('home.category.product');
Route::get('/product/{id}', [HomeController::class, 'show_product_detail'])->name('product.detail');
Route::post('/product/{id}/review', [HomeController::class, 'store_product_review'])->name('product.review.store');
Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/autocomplete-ajax', [ProductController::class, 'autocomplete_ajax'])->name('product.autocomplete_ajax');

// Cart Routes (public)
Route::get('/show-cart', [CartController::class, 'show_cart'])->name('cart');
Route::get('/cart/summary', [CartController::class, 'getCartSummary'])->name('cart.summary');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::post('/check-coupon', [CartController::class, 'checkCoupon'])->name('check_coupon');
Route::get('/remove-coupon', [CartController::class, 'removeCoupon'])->name('remove_coupon');

// Cart Routes (authenticated)
Route::middleware('auth')->group(function () {
    // Cart API endpoints
    Route::put('/cart/update/{cartItemId}', [CartController::class, 'updateCartItem'])->name('cart.update');
    Route::delete('/cart/remove/{cartItemId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::get('/cart/api', [CartController::class, 'getCart'])->name('cart.api');
});

// GHN Locations & Shipping Fee Routes
Route::prefix('locations')->name('locations.')->group(function () {
    Route::get('/provinces', [LocationController::class, 'getProvinces'])->name('provinces');
    Route::get('/districts/{provinceId}', [LocationController::class, 'getDistricts'])->name('districts');
    Route::get('/wards/{districtId}', [LocationController::class, 'getWards'])->name('wards');
    Route::post('/calculate-fee', [LocationController::class, 'getShippingFee'])->name('fee');
    Route::post('/reverse-geocode', [LocationController::class, 'reverseGeocode'])->name('reverse_geocode');
});

// Checkout Routes
Route::get('/show-checkout', [CheckoutController::class, 'show_checkout'])->name('checkout.index');
Route::post('/thanh-toan', [OrderController::class, 'placeOrder'])->name('checkout.process');
Route::post('/dat-hang', [OrderController::class, 'placeOrder'])->name('dathang');
Route::get('/dat-hang-thanh-cong/{id}', [OrderController::class, 'showSuccess'])->name('order.success');

// MoMo Payment Callback Routes
Route::get('/momo-return', [OrderController::class, 'momoReturn'])->name('momo.return');
Route::post('/momo-ipn', [OrderController::class, 'momoIpn'])->name('momo.ipn');
Route::get('/momo/mock-pay/{order_id}', [OrderController::class, 'momoMockPay'])->name('momo.mock_pay');

// VNPay Payment Callback Routes
Route::get('/vnpay-return', [OrderController::class, 'vnpayReturn'])->name('vnpay.return');
Route::get('/vnpay-ipn', [OrderController::class, 'vnpayIpn'])->name('vnpay.ipn');
Route::post('/vnpay-ipn', [OrderController::class, 'vnpayIpn']);

// Orders (user scope)
Route::middleware('auth')->group(function () {
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my');
    Route::post('/orders/from-cart', [OrderController::class, 'storeFromCart'])->name('orders.store_from_cart');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'userCancel'])->name('orders.user_cancel');
});

// Tra cứu đơn hàng (công khai cho khách vãng lai và thành viên)
Route::get('/tra-cuu-don-hang', [OrderController::class, 'showLookupForm'])->name('orders.lookup');
Route::post('/tra-cuu-don-hang', [OrderController::class, 'processLookup'])->name('orders.lookup.post');

// Orders (admin scope)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/reports/orders/{format}', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->whereIn('format', ['xlsx', 'pdf'])->name('reports.export');
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'update'])->name('reviews.update');
    Route::resource('orders', AdminOrderController::class)->except(['create', 'store', 'edit', 'update']);
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{id}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/push-ghn', [AdminOrderController::class, 'pushGhn'])->name('orders.push_ghn');
    Route::match(['get', 'delete'], '/orders/delete/{id}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
});

//Profile
Route::middleware('auth')->group(function () {
    Route::get('/show-profile', [ProfilesController::class, 'show_profile'])
        ->name('profile');
});

Route::get('/show-category-cart', [CartController::class, 'show_category_cart']);

// ADMIN & AUTH ROUTES
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/logout-admin', [AdminController::class, 'logout_admin'])->name('logout');
});

Route::get('/register-admin', [AdminController::class, 'register_admin']);
Route::get('/register', [AdminController::class, 'register_admin'])->name('register');
Route::post('/register', [AdminController::class, 'submit_register']);
Route::get('/admin', [AdminController::class, 'login'])->name('admin');
Route::get('/login', [AdminController::class, 'login'])->name('login');
Route::post('/submit-register-admin', [AdminController::class, 'submit_register']);
Route::post('/submit-login-admin', [AdminController::class, 'submit_login']);
Route::post('/login', [AdminController::class, 'submit_login']);

// Password Reset Routes
Route::get('/password/reset', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [PasswordResetController::class, 'reset'])->name('password.update');

// ADMIN FEATURE ROUTES (protected)
Route::middleware(['auth', 'admin'])->group(function () {
    // Brand
    Route::get('/show-brand', [BrandController::class, 'show_brand']);
    Route::post('/create-brand', [BrandController::class, 'create_brand']);
    Route::match(['get', 'delete'], '/delete-brand/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');
    Route::get('/show-create-brand', [BrandController::class, 'showCreate']);
    Route::get('/show-edit-brand/{id}', [BrandController::class, 'showEdit']);
    Route::post('/update-brand', [BrandController::class, 'update_brand']);

    // Product
    Route::get('/show-product', [ProductController::class, 'show_product']);
    Route::get('/show-create-product', [ProductController::class, 'show_create_product']);
    Route::post('/create-product', [ProductController::class, 'create_product']);
    Route::match(['get', 'delete'], '/delete-product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::get('/show-edit-product/{id}', [ProductController::class, 'show_edit'])->name('product.show_edit');
    Route::put('/update-product/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::post('/update-product/{id}', [ProductController::class, 'update']);

    // Category
    Route::get('/show-category', [CategoryController::class, 'show_category'])->name('category.index');
    Route::post('/create-category', [CategoryController::class, 'create_category'])->name('category.create');
    Route::get('/edit-category/{id}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::post('/update-category/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::match(['get', 'delete'], '/delete-category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::get('/show-edit-category/{id}', [CategoryController::class, 'show_edit_modal'])->name('category.show_edit_modal');

    // Users management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{id}/role', [AdminController::class, 'update_user_role'])->name('admin.users.role');
    Route::match(['get', 'delete'], '/admin/users/delete/{id}', [AdminController::class, 'destroy_user'])->name('admin.users.destroy');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroy_user']);

    //Coupon management
    Route::get('/show-coupon', [CouponController::class, 'index'])->name('coupon.index');
    Route::get('/show-create-coupon', [CouponController::class, 'create'])->name('show_create_coupon');
    Route::get('/edit-coupon/{id}', [CouponController::class, 'edit'])->name('show_coupon_edit');
    Route::post('/save', [CouponController::class, 'store'])->name('coupon.store');
    Route::match(['get', 'delete'], '/delete-coupon/{id}', [CouponController::class, 'destroy'])->name('coupon.delete');
    Route::post('/update-coupon/{id}', [CouponController::class, 'update'])->name('coupon.update');
});

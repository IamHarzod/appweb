<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFYING FINDINGS ===\n";

// 1. Check Route 'register' in welcome.blade.php
try {
    route('register');
    echo "[1] route('register'): Exists\n";
} catch (\Throwable $e) {
    echo "[1] route('register'): Crashes with " . get_class($e) . " - " . $e->getMessage() . "\n";
}

// 2. Check CheckoutController@processOrder
try {
    $ctrl = new App\Http\Controllers\CheckoutController();
    if (method_exists($ctrl, 'processOrder')) {
        echo "[2] CheckoutController@processOrder: Exists\n";
    } else {
        echo "[2] CheckoutController@processOrder: DOES NOT EXIST!\n";
    }
} catch (\Throwable $e) {
    echo "[2] CheckoutController error: " . $e->getMessage() . "\n";
}

// 3. Check HomeController@show_category_home column status
try {
    $ctrl = new App\Http\Controllers\HomeController();
    $ctrl->show_category_home();
    echo "[3] HomeController@show_category_home: Success\n";
} catch (\Throwable $e) {
    echo "[3] HomeController@show_category_home: Crashes with " . get_class($e) . " - " . $e->getMessage() . "\n";
}

// 4. Check Category null safety simulation
try {
    $dummyProduct = new App\Models\Product();
    $dummyProduct->name = 'Test';
    $dummyProduct->category_id = 999999;
    // simulating $dummyProduct->category->name
    $cat = $dummyProduct->category;
    if ($cat === null) {
        echo "[4] Dummy product with missing category: \$product->category is NULL. Calling \$product->category->name would throw error.\n";
    }
} catch (\Throwable $e) {
    echo "[4] Category null test error: " . $e->getMessage() . "\n";
}

// 5. Check CartController@checkCoupon with empty request
try {
    $ctrl = new App\Http\Controllers\CartController();
    $req = new Illuminate\Http\Request();
    $ctrl->checkCoupon($req);
    echo "[5] CartController@checkCoupon with empty request: Handled\n";
} catch (\Throwable $e) {
    echo "[5] CartController@checkCoupon with empty request: Crashes with " . get_class($e) . " - " . $e->getMessage() . "\n";
}

echo "=== VERIFICATION COMPLETE ===\n";

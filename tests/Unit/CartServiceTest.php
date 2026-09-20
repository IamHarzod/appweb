<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CartService;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;
    protected Category $category;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = new CartService();

        $this->category = Category::firstOrCreate(['name' => 'Unit Category']);
        $this->brand = Brand::firstOrCreate(
            ['TenThuongHieu' => 'Unit Brand'],
            ['Logo' => 'brand.png', 'MoTa' => 'Unit Brand Description', 'TrangThai' => 1]
        );
    }

    public function test_get_cart_data_returns_defaults_when_cart_is_empty(): void
    {
        Session::forget('cart');
        Session::forget('coupon');

        $data = $this->cartService->getCartData(50000);

        $this->assertNull($data['cart']);
        $this->assertCount(0, $data['cartItems']);
        $this->assertEquals(0.0, $data['subtotal']);
        $this->assertEquals(50000.0, $data['shippingFee']);
        $this->assertEquals(0.0, $data['discountAmount']);
        $this->assertEquals(50000.0, $data['totalPrice']);
    }

    public function test_get_cart_data_calculates_subtotal_from_session(): void
    {
        $product1 = Product::create([
            'name'          => 'Product A',
            'price'         => 100000,
            'stockQuantity' => 10,
            'category_id'   => $this->category->id,
            'id_brand'      => $this->brand->id,
            'image'         => 'a.jpg',
        ]);

        $product2 = Product::create([
            'name'          => 'Product B',
            'price'         => 200000,
            'stockQuantity' => 5,
            'category_id'   => $this->category->id,
            'id_brand'      => $this->brand->id,
            'image'         => 'b.jpg',
        ]);

        Session::put('cart', [
            $product1->id => ['quantity' => 2],
            $product2->id => ['quantity' => 1],
        ]);

        $data = $this->cartService->getCartData(30000);

        // Subtotal = (100000 * 2) + (200000 * 1) = 400000
        $this->assertEquals(400000.0, $data['subtotal']);
        $this->assertEquals(30000.0, $data['shippingFee']);
        $this->assertEquals(0.0, $data['discountAmount']);
        $this->assertEquals(430000.0, $data['totalPrice']);
        $this->assertCount(2, $data['cartItems']);
    }

    public function test_get_cart_data_with_fixed_discount_coupon(): void
    {
        $product = Product::create([
            'name'          => 'Product Fixed',
            'price'         => 500000,
            'stockQuantity' => 10,
            'category_id'   => $this->category->id,
            'id_brand'      => $this->brand->id,
            'image'         => 'fixed.jpg',
        ]);

        Session::put('cart', [$product->id => ['quantity' => 1]]);
        Session::put('coupon', [
            'id'    => 1,
            'code'  => 'GIAM50K',
            'type'  => 'fixed',
            'value' => 50000,
        ]);

        $data = $this->cartService->getCartData(30000);

        // subtotal 500k + shipping 30k - discount 50k = 480k
        $this->assertEquals(500000.0, $data['subtotal']);
        $this->assertEquals(30000.0, $data['shippingFee']);
        $this->assertEquals(50000.0, $data['discountAmount']);
        $this->assertEquals(480000.0, $data['totalPrice']);
    }

    public function test_get_cart_data_with_percent_discount_coupon(): void
    {
        $product = Product::create([
            'name'          => 'Product Percent',
            'price'         => 1000000,
            'stockQuantity' => 10,
            'category_id'   => $this->category->id,
            'id_brand'      => $this->brand->id,
            'image'         => 'percent.jpg',
        ]);

        Session::put('cart', [$product->id => ['quantity' => 1]]);
        Session::put('coupon', [
            'id'    => 2,
            'code'  => 'GIAM10PT',
            'type'  => 'percent',
            'value' => 10, // 10%
        ]);

        $data = $this->cartService->getCartData(40000);

        // subtotal 1,000,000 * 10% = 100,000
        // total = 1,000,000 + 40,000 - 100,000 = 940,000
        $this->assertEquals(1000000.0, $data['subtotal']);
        $this->assertEquals(40000.0, $data['shippingFee']);
        $this->assertEquals(100000.0, $data['discountAmount']);
        $this->assertEquals(940000.0, $data['totalPrice']);
    }

    public function test_get_cart_data_with_free_ship_coupon(): void
    {
        $product = Product::create([
            'name'          => 'Product Freeship',
            'price'         => 300000,
            'stockQuantity' => 10,
            'category_id'   => $this->category->id,
            'id_brand'      => $this->brand->id,
            'image'         => 'ship.jpg',
        ]);

        Session::put('cart', [$product->id => ['quantity' => 1]]);
        Session::put('coupon', [
            'id'    => 3,
            'code'  => 'FREESHIP30',
            'type'  => 'free_ship',
            'value' => 30000,
        ]);

        $data = $this->cartService->getCartData(30000);

        // shippingFee reduced by 30k -> 0
        $this->assertEquals(300000.0, $data['subtotal']);
        $this->assertEquals(0.0, $data['shippingFee']);
        $this->assertEquals(0.0, $data['discountAmount']);
        $this->assertEquals(300000.0, $data['totalPrice']);
    }

    public function test_get_cart_data_total_price_never_negative(): void
    {
        $product = Product::create([
            'name'          => 'Cheap Item',
            'price'         => 20000,
            'stockQuantity' => 5,
            'category_id'   => $this->category->id,
            'id_brand'      => $this->brand->id,
            'image'         => 'cheap.jpg',
        ]);

        Session::put('cart', [$product->id => ['quantity' => 1]]);
        Session::put('coupon', [
            'id'    => 4,
            'code'  => 'MEGA',
            'type'  => 'fixed',
            'value' => 500000, // much larger than subtotal + shipping
        ]);

        $data = $this->cartService->getCartData(10000);

        // subtotal 20k + shipping 10k - 500k -> clamped to 0
        $this->assertEquals(0.0, $data['totalPrice']);
    }

    public function test_get_cart_data_for_authenticated_user_with_database_cart(): void
    {
        $user = User::create([
            'name'        => 'Cart User',
            'email'       => 'cart_user@example.com',
            'phoneNumber' => '0987654321',
            'password'    => bcrypt('password'),
            'role'        => 'user',
            'IsActive'    => 1,
        ]);

        $product = Product::create([
            'name'          => 'DB Cart Product',
            'price'         => 150000,
            'stockQuantity' => 20,
            'category_id'   => $this->category->id,
            'id_brand'      => $this->brand->id,
            'image'         => 'db.jpg',
        ]);

        $cart = Cart::create(['user_id' => $user->id, 'totalAmount' => 300000]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'price'      => 150000,
        ]);

        Auth::login($user);

        $data = $this->cartService->getCartData(25000);

        $this->assertNotNull($data['cart']);
        $this->assertEquals($cart->id, $data['cart']->id);
        $this->assertCount(1, $data['cartItems']);
        $this->assertEquals(300000.0, $data['subtotal']);
        $this->assertEquals(25000.0, $data['shippingFee']);
        $this->assertEquals(325000.0, $data['totalPrice']);
    }

    public function test_apply_coupon_fails_when_coupon_does_not_exist(): void
    {
        $result = $this->cartService->applyCoupon('NONEXISTENT_CODE');

        $this->assertFalse($result['success']);
        $this->assertEquals('Mã giảm giá sai hoặc không tồn tại!', $result['message']);
        $this->assertFalse(Session::has('coupon'));
    }

    public function test_apply_coupon_fails_when_quantity_is_zero(): void
    {
        Coupon::create([
            'code'        => 'OUTOFSTOCK',
            'type'        => 'fixed',
            'value'       => 20000,
            'quantity'    => 0,
            'expiry_date' => Carbon::now()->addDays(5),
        ]);

        $result = $this->cartService->applyCoupon('OUTOFSTOCK');

        $this->assertFalse($result['success']);
        $this->assertEquals('Mã giảm giá đã hết lượt sử dụng!', $result['message']);
        $this->assertFalse(Session::has('coupon'));
    }

    public function test_apply_coupon_fails_when_expired(): void
    {
        Coupon::create([
            'code'        => 'EXPIRED',
            'type'        => 'percent',
            'value'       => 15,
            'quantity'    => 10,
            'expiry_date' => Carbon::now()->subDay(), // yesterday
        ]);

        $result = $this->cartService->applyCoupon('EXPIRED');

        $this->assertFalse($result['success']);
        $this->assertEquals('Mã giảm giá đã hết hạn!', $result['message']);
        $this->assertFalse(Session::has('coupon'));
    }

    public function test_apply_coupon_succeeds_for_valid_coupon(): void
    {
        $coupon = Coupon::create([
            'code'        => 'VALIDCODE',
            'type'        => 'fixed',
            'value'       => 50000,
            'quantity'    => 5,
            'expiry_date' => Carbon::now()->addDays(10),
        ]);

        $result = $this->cartService->applyCoupon('VALIDCODE');

        $this->assertTrue($result['success']);
        $this->assertEquals('Áp dụng mã giảm giá thành công!', $result['message']);
        $this->assertTrue(Session::has('coupon'));
        $this->assertEquals('VALIDCODE', Session::get('coupon')['code']);
        $this->assertEquals(50000.0, Session::get('coupon')['value']);
    }

    public function test_remove_coupon_clears_session(): void
    {
        Session::put('coupon', ['code' => 'TEST', 'value' => 10000]);
        $this->assertTrue(Session::has('coupon'));

        $this->cartService->removeCoupon();

        $this->assertFalse(Session::has('coupon'));
    }

    public function test_clear_cart_resets_database_and_session(): void
    {
        $user = User::create([
            'name'        => 'Clear Cart User',
            'email'       => 'clear@example.com',
            'phoneNumber' => '0987654321',
            'password'    => bcrypt('password'),
            'role'        => 'user',
            'IsActive'    => 1,
        ]);

        $product = Product::create([
            'name'          => 'Product To Clear',
            'price'         => 100000,
            'stockQuantity' => 5,
            'category_id'   => $this->category->id,
            'id_brand'      => $this->brand->id,
            'image'         => 'clear.jpg',
        ]);

        $cart = Cart::create(['user_id' => $user->id, 'totalAmount' => 100000]);
        CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 1,
            'price'      => 100000,
        ]);

        Session::put('cart', [$product->id => ['quantity' => 1]]);
        Session::put('coupon', ['code' => 'COUPON']);

        Auth::login($user);

        $this->cartService->clearCart();

        $this->assertDatabaseMissing('cart_items', ['cart_id' => $cart->id]);
        $this->assertEquals(0, $cart->fresh()->totalAmount);
        $this->assertFalse(Session::has('cart'));
        $this->assertFalse(Session::has('coupon'));
    }
}

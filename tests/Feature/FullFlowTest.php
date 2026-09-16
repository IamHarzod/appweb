<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FullFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleData()
    {
        $category = Category::create([
            'name' => 'Điện Thoại',
            'description' => 'Danh mục điện thoại di động',
            'ImageURL' => null,
        ]);

        $brand = Brand::create([
            'TenThuongHieu' => 'Apple',
            'Logo' => 'apple.png',
            'MoTa' => 'Thương hiệu Apple',
            'TrangThai' => 1,
        ]);

        $product = Product::create([
            'name' => 'iPhone 15 Pro Max',
            'price' => 30000000,
            'stockQuantity' => 10,
            'discountPercent' => 5,
            'description' => 'Điện thoại cao cấp',
            'status' => 1,
            'IsActive' => 1,
            'style' => 'Flagship',
            'imageURL' => 'iphone15.jpg',
            'id_brand' => $brand->id,
            'category_id' => $category->id,
        ]);

        return compact('category', 'brand', 'product');
    }

    public function test_01_home_page_and_navigation(): void
    {
        $data = $this->createSampleData();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('iPhone 15 Pro Max');
        $response->assertSee('Điện Thoại');
    }

    public function test_02_category_and_product_detail_views(): void
    {
        $data = $this->createSampleData();

        // Trang sản phẩm theo danh mục
        $catResponse = $this->get('/show-product-category-home/' . $data['category']->id);
        $catResponse->assertStatus(200);
        $catResponse->assertSee('iPhone 15 Pro Max');

        // Trang chi tiết sản phẩm
        $detailResponse = $this->get('/product/' . $data['product']->id);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('iPhone 15 Pro Max');
        $detailResponse->assertSee('Apple');
    }

    public function test_03_search_and_autocomplete_ajax(): void
    {
        $data = $this->createSampleData();

        // Tìm kiếm trang kết quả
        $searchResponse = $this->get('/search?keyword=iPhone');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('iPhone 15 Pro Max');

        // Gợi ý tìm kiếm nhanh Ajax
        $ajaxResponse = $this->get('/autocomplete-ajax?query=iPhone');
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertSee('iPhone 15 Pro Max');
    }

    public function test_04_cart_api_and_cart_page(): void
    {
        $data = $this->createSampleData();
        $user = User::factory()->create(['role' => 'user']);

        // Chưa đăng nhập thì Cart Summary trả về 0
        $this->get('/cart/summary')->assertJson(['data' => ['total_items' => 0]]);

        // Đăng nhập và thêm sản phẩm vào giỏ
        $addResponse = $this->actingAs($user)->postJson('/cart/add', [
            'product_id' => $data['product']->id,
            'quantity' => 2,
        ]);
        $addResponse->assertStatus(200);
        $addResponse->assertJson(['success' => true]);

        // Cart Summary sau khi thêm
        $summaryResponse = $this->actingAs($user)->get('/cart/summary');
        $summaryResponse->assertJson(['data' => ['total_items' => 2]]);

        // Xem trang giỏ hàng
        $cartPage = $this->actingAs($user)->get('/show-cart');
        $cartPage->assertStatus(200);
        $cartPage->assertSee('iPhone 15 Pro Max');

        // Cập nhật số lượng
        $cartItem = CartItem::first();
        $updateResponse = $this->actingAs($user)->putJson('/cart/update/' . $cartItem->id, [
            'quantity' => 3,
        ]);
        $updateResponse->assertStatus(200);
        $this->assertEquals(3, CartItem::first()->quantity);

        // Xóa sản phẩm khỏi giỏ
        $removeResponse = $this->actingAs($user)->deleteJson('/cart/remove/' . $cartItem->id);
        $removeResponse->assertStatus(200);
        $this->assertNull(CartItem::first());
    }

    public function test_05_coupon_lifecycle(): void
    {
        $coupon = Coupon::create([
            'code' => 'SALE10',
            'type' => 'percent',
            'value' => 10,
            'quantity' => 5,
            'expiry_date' => now()->addDays(30)->toDateString(),
        ]);

        // Kiểm tra áp dụng mã coupon hợp lệ
        $response = $this->post('/check-coupon', ['code_input' => 'SALE10']);
        $response->assertSessionHas('coupon');
        $this->assertEquals('SALE10', session('coupon')['code']);

        // Gỡ bỏ mã coupon
        $removeResponse = $this->get('/remove-coupon');
        $this->assertFalse(session()->has('coupon'));
    }

    public function test_06_checkout_and_place_order_flow(): void
    {
        $data = $this->createSampleData();
        $user = User::factory()->create(['role' => 'user']);

        // 1. Thêm vào giỏ hàng
        $this->actingAs($user)->postJson('/cart/add', [
            'product_id' => $data['product']->id,
            'quantity' => 2,
        ]);

        // 2. Tạo và áp mã giảm giá
        Coupon::create([
            'code' => 'DISCOUNT50K',
            'type' => 'fixed',
            'value' => 50000,
            'quantity' => 10,
            'expiry_date' => now()->addDays(10)->toDateString(),
        ]);
        $this->actingAs($user)->post('/check-coupon', ['code_input' => 'DISCOUNT50K']);

        // 3. Xem trang thanh toán
        $checkoutPage = $this->actingAs($user)->get('/show-checkout');
        $checkoutPage->assertStatus(200);

        // 4. Tiến hành Đặt hàng
        $orderData = [
            'shipping_name' => 'Nguyễn Văn Test',
            'shipping_phone' => '0987654321',
            'shipping_email' => 'test@example.com',
            'shipping_address' => 'Số 123 Đường ABC',
            'tinh_thanh' => 'Hà Nội',
            'quan_huyen' => 'Cầu Giấy',
            'phuong_xa' => 'Dịch Vọng',
            'payment_method' => 'COD',
            'ghichu' => 'Giao hàng giờ hành chính',
        ];

        $initialStock = $data['product']->stockQuantity; // 10
        $placeOrderResponse = $this->actingAs($user)->post('/dat-hang', $orderData);

        // Xác minh chuyển hướng sang trang đặt hàng thành công
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $placeOrderResponse->assertRedirect(route('order.success', ['id' => $order->id]));

        // Kiểm tra xem trang thành công
        $successPage = $this->actingAs($user)->get(route('order.success', ['id' => $order->id]));
        $successPage->assertStatus(200);
        $successPage->assertSee('Đặt hàng thành công');

        // Xác minh tồn kho bị trừ: 10 - 2 = 8
        $this->assertEquals($initialStock - 2, $data['product']->fresh()->stockQuantity);

        // Xác minh số lượng coupon bị trừ: 10 - 1 = 9
        $this->assertEquals(9, Coupon::where('code', 'DISCOUNT50K')->first()->quantity);

        // Xác minh session coupon đã được giải phóng
        $this->assertFalse(session()->has('coupon'));
    }

    public function test_07_customer_order_management_and_authorization(): void
    {
        $data = $this->createSampleData();
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);

        $order = Order::create([
            'user_id' => $user1->id,
            'shipping_name' => 'User 1',
            'shipping_email' => 'user1@test.com',
            'shipping_phone' => '0912345678',
            'shipping_address' => 'Hà Nội',
            'total_amount' => 500000,
            'status' => 'pending',
            'payment_method' => 'COD',
        ]);

        // User 1 xem danh sách đơn hàng của mình
        $myOrdersResponse = $this->actingAs($user1)->get('/my-orders');
        $myOrdersResponse->assertStatus(200);
        $myOrdersResponse->assertSee('#' . $order->id);

        // User 1 xem chi tiết đơn hàng của mình
        $orderDetailResponse = $this->actingAs($user1)->get('/orders/' . $order->id);
        $orderDetailResponse->assertStatus(200);
        $orderDetailResponse->assertSee('Chi tiết đơn hàng #' . $order->id);

        // User 2 truy cập đơn hàng của User 1 -> Bị từ chối 403
        $forbiddenResponse = $this->actingAs($user2)->get('/orders/' . $order->id);
        $forbiddenResponse->assertStatus(403);
    }

    public function test_08_admin_authentication_and_route_guards(): void
    {
        $user = User::factory()->create(['role' => 'user', 'password' => 'password123']);
        $admin = User::factory()->create(['role' => 'admin', 'password' => 'password123']);

        // Khách chưa đăng nhập vào admin dashboard -> chuyển hướng về login
        $this->get('/admin/dashboard')->assertRedirect(route('login'));

        // User thường vào admin dashboard -> bị chặn chuyển về trang chủ
        $this->actingAs($user)->get('/admin/dashboard')->assertRedirect('/');

        // Admin vào admin dashboard -> thành công 200
        $this->actingAs($admin)->get('/admin/dashboard')->assertStatus(200);
    }

    public function test_09_admin_crud_operations(): void
    {
        $data = $this->createSampleData();
        $admin = User::factory()->create(['role' => 'admin']);

        // 1. Quản lý Sản phẩm
        $this->actingAs($admin)->get('/show-product')->assertStatus(200);
        $this->actingAs($admin)->get('/show-create-product')->assertStatus(200);

        // 2. Quản lý Danh mục
        $this->actingAs($admin)->get('/show-category')->assertStatus(200);
        $this->actingAs($admin)->post('/create-category', [
            'name' => 'Laptop & Tablet',
            'description' => 'Máy tính xách tay',
        ])->assertRedirect('/show-category');
        $this->assertDatabaseHas('_category', ['name' => 'Laptop & Tablet']);

        // 3. Quản lý Thương hiệu
        $this->actingAs($admin)->get('/show-brand')->assertStatus(200);
        $this->actingAs($admin)->get('/show-create-brand')->assertStatus(200);

        // 4. Quản lý Mã giảm giá
        $this->actingAs($admin)->get('/show-coupon')->assertStatus(200);
        $this->actingAs($admin)->post('/save', [
            'code' => 'ADMINCODE2026',
            'type' => 'percent',
            'value' => 15,
            'quantity' => 20,
            'expiry_date' => now()->addDays(60)->toDateString(),
        ])->assertSessionHas('success');
        $createdCoupon = Coupon::where('code', 'ADMINCODE2026')->first();
        $this->assertNotNull($createdCoupon);

        // Xóa mã giảm giá bằng Ajax DeleteData
        $deleteCouponResponse = $this->actingAs($admin)->get('/delete-coupon/' . $createdCoupon->id, ['X-Requested-With' => 'XMLHttpRequest']);
        $deleteCouponResponse->assertSuccessful();
        $this->assertDatabaseMissing('coupons', ['id' => $createdCoupon->id]);

        // 5. Quản lý Đơn hàng
        $this->actingAs($admin)->get('/admin/orders')->assertStatus(200);

        // 6. Quản lý Người dùng
        $targetUser = User::factory()->create(['role' => 'user']);
        $this->actingAs($admin)->get('/admin/users')->assertStatus(200);

        // Đổi quyền người dùng
        $this->actingAs($admin)->post('/admin/users/' . $targetUser->id . '/role', ['role' => 'admin'])
            ->assertRedirect(route('admin.users'));
        $this->assertEquals('admin', $targetUser->fresh()->role);

        // Xóa người dùng
        $this->actingAs($admin)->get('/admin/users/delete/' . $targetUser->id)
            ->assertRedirect(route('admin.users'));
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);

        // Admin không thể tự xóa chính mình
        $selfDeleteResponse = $this->actingAs($admin)->get('/admin/users/delete/' . $admin->id);
        $selfDeleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
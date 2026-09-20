<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class AdminCatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'        => 'Admin Catalog',
            'email'       => 'admin_catalog@example.com',
            'phoneNumber' => '0987654321',
            'password'    => bcrypt('password'),
            'role'        => 'admin',
            'IsActive'    => 1,
        ]);

        $this->customer = User::create([
            'name'        => 'Customer Catalog',
            'email'       => 'customer_catalog@example.com',
            'phoneNumber' => '0912345678',
            'password'    => bcrypt('password'),
            'role'        => 'user',
            'IsActive'    => 1,
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles as $filePath) {
            if (File::exists($filePath)) {
                @File::delete($filePath);
            }
        }
        parent::tearDown();
    }

    // --- AUTHORIZATION TESTS ---

    public function test_guests_cannot_access_catalog_management(): void
    {
        $this->get('/show-category')->assertRedirect(route('login'));
        $this->get('/show-brand')->assertRedirect(route('login'));
        $this->get('/show-coupon')->assertRedirect(route('login'));
    }

    public function test_normal_users_cannot_access_catalog_management(): void
    {
        $this->actingAs($this->customer);

        $this->get('/show-category')
            ->assertRedirect('/')
            ->assertSessionHas('error', 'You do not have admin access.');

        $this->get('/show-brand')
            ->assertRedirect('/')
            ->assertSessionHas('error', 'You do not have admin access.');

        $this->get('/show-coupon')
            ->assertRedirect('/')
            ->assertSessionHas('error', 'You do not have admin access.');
    }

    // --- CATEGORY CRUD TESTS ---

    public function test_admin_can_view_categories_list(): void
    {
        Category::create(['name' => 'Laptops', 'description' => 'Laptop category']);

        $response = $this->actingAs($this->admin)->get('/show-category');

        $response->assertStatus(200);
        $response->assertViewIs('admin.category.show_category');
        $response->assertSee('Laptops');
    }

    public function test_admin_can_create_category_without_image(): void
    {
        $response = $this->actingAs($this->admin)->post('/create-category', [
            'name'        => 'Smartphones',
            'description' => 'Smartphones and accessories',
        ]);

        $response->assertRedirect('/show-category');
        $response->assertSessionHas('success', 'Thêm danh mục thành công!');
        $this->assertDatabaseHas('_category', [
            'name'        => 'Smartphones',
            'description' => 'Smartphones and accessories',
        ]);
    }

    public function test_admin_can_create_category_with_image(): void
    {
        $file = UploadedFile::fake()->create('cat_image.jpg', 50, 'image/jpeg');

        $response = $this->actingAs($this->admin)->post('/create-category', [
            'name'        => 'Audio Devices',
            'description' => 'Headphones and speakers',
            'ImageURL'    => $file,
        ]);

        $response->assertRedirect('/show-category');
        $category = Category::where('name', 'Audio Devices')->first();
        $this->assertNotNull($category);
        $this->assertNotNull($category->ImageURL);

        $savedPath = public_path('uploads/categories/' . $category->ImageURL);
        $this->createdFiles[] = $savedPath;
        $this->assertFileExists($savedPath);
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::create(['name' => 'Old Name', 'description' => 'Old Description']);

        $response = $this->actingAs($this->admin)->post("/update-category/{$category->id}", [
            'name'        => 'Updated Category Name',
            'description' => 'Updated Category Description',
        ]);

        $response->assertRedirect('/show-category');
        $response->assertSessionHas('success', 'Cập nhật danh mục thành công!');
        $this->assertDatabaseHas('_category', [
            'id'          => $category->id,
            'name'        => 'Updated Category Name',
            'description' => 'Updated Category Description',
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $category = Category::create(['name' => 'Category To Delete']);

        $response = $this->actingAs($this->admin)->delete("/delete-category/{$category->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('_category', ['id' => $category->id]);
    }

    // --- BRAND CRUD TESTS ---

    public function test_admin_can_view_brands_list(): void
    {
        Brand::create([
            'TenThuongHieu' => 'Sony Test',
            'Logo'          => 'sony.png',
            'MoTa'          => 'Sony Electronics',
            'TrangThai'     => 1,
        ]);

        $response = $this->actingAs($this->admin)->get('/show-brand');

        $response->assertStatus(200);
        $response->assertViewIs('admin.brand.show_brand');
        $response->assertSee('Sony Test');
    }

    public function test_admin_can_create_brand_with_logo(): void
    {
        $logo = UploadedFile::fake()->create('brand_logo.png', 50, 'image/png');

        $response = $this->actingAs($this->admin)->post('/create-brand', [
            'TenThuongHieu' => 'Samsung Brand',
            'Logo'          => $logo,
            'MoTa'          => 'Samsung Electronics Co.',
            'TrangThai'     => 1,
        ]);

        $response->assertRedirect('/show-brand');
        $response->assertSessionHas('success', 'Thêm thương hiệu thành công!');

        $brand = Brand::where('TenThuongHieu', 'Samsung Brand')->first();
        $this->assertNotNull($brand);
        $this->assertEquals(1, $brand->TrangThai);

        $savedPath = public_path('uploads/brands/' . $brand->Logo);
        $this->createdFiles[] = $savedPath;
        $this->assertFileExists($savedPath);
    }

    public function test_admin_can_update_brand(): void
    {
        $brand = Brand::create([
            'TenThuongHieu' => 'Original Brand',
            'Logo'          => 'orig.png',
            'MoTa'          => 'Original Description',
            'TrangThai'     => 1,
        ]);

        $response = $this->actingAs($this->admin)->post('/update-brand', [
            'id'            => $brand->id,
            'TenThuongHieu' => 'Renamed Brand',
            'MoTa'          => 'New Brand Description',
            'TrangThai'     => 0,
        ]);

        $response->assertRedirect('/show-brand');
        $response->assertSessionHas('success', 'Cập nhật thương hiệu thành công!');

        $this->assertDatabaseHas('brand', [
            'id'            => $brand->id,
            'TenThuongHieu' => 'Renamed Brand',
            'MoTa'          => 'New Brand Description',
            'TrangThai'     => 0,
        ]);
    }

    public function test_admin_can_delete_brand(): void
    {
        $brand = Brand::create([
            'TenThuongHieu' => 'Brand To Delete',
            'Logo'          => 'delete.png',
            'MoTa'          => 'Will be deleted',
            'TrangThai'     => 1,
        ]);

        $response = $this->actingAs($this->admin)->delete("/delete-brand/{$brand->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('brand', ['id' => $brand->id]);
    }

    // --- COUPON CRUD TESTS ---

    public function test_admin_can_view_coupons_list(): void
    {
        Coupon::create([
            'code'        => 'SUMMER2026',
            'type'        => 'percent',
            'value'       => 20,
            'quantity'    => 50,
            'expiry_date' => '2026-12-31',
        ]);

        $response = $this->actingAs($this->admin)->get('/show-coupon');

        $response->assertStatus(200);
        $response->assertViewIs('admin.coupon.show_coupon');
        $response->assertSee('SUMMER2026');
    }

    public function test_admin_can_create_coupon(): void
    {
        $response = $this->actingAs($this->admin)->post('/save', [
            'code'        => 'NEWYEAR50',
            'type'        => 'fixed',
            'value'       => 50000,
            'quantity'    => 100,
            'expiry_date' => '2026-12-31',
        ]);

        $response->assertSessionHas('success', 'Thêm mã giảm giá thành công!');
        $this->assertDatabaseHas('coupons', [
            'code'     => 'NEWYEAR50',
            'type'     => 'fixed',
            'value'    => 50000,
            'quantity' => 100,
        ]);
    }

    public function test_admin_can_update_coupon(): void
    {
        $coupon = Coupon::create([
            'code'        => 'OLDCOUPON',
            'type'        => 'fixed',
            'value'       => 20000,
            'quantity'    => 10,
            'expiry_date' => '2026-06-30',
        ]);

        $response = $this->actingAs($this->admin)->post("/update-coupon/{$coupon->id}", [
            'code'        => 'UPDATEDCOUPON',
            'type'        => 'percent',
            'value'       => 25,
            'quantity'    => 30,
            'expiry_date' => '2026-09-30',
        ]);

        $response->assertSessionHas('success', 'Cập nhật mã giảm giá thành công!');
        $this->assertDatabaseHas('coupons', [
            'id'       => $coupon->id,
            'code'     => 'UPDATEDCOUPON',
            'type'     => 'percent',
            'value'    => 25,
            'quantity' => 30,
        ]);
    }

    public function test_admin_can_delete_coupon(): void
    {
        $coupon = Coupon::create([
            'code'        => 'DELETECOUPON',
            'type'        => 'fixed',
            'value'       => 10000,
            'quantity'    => 5,
            'expiry_date' => '2026-05-01',
        ]);

        $response = $this->actingAs($this->admin)->delete("/delete-coupon/{$coupon->id}");

        $response->assertSessionHas('success', 'Đã xoá mã giảm giá!');
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }
}

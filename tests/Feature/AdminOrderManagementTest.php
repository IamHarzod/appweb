<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminOrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_order@example.com'],
            ['name' => 'Admin Test', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $category = Category::firstOrCreate(['name' => 'Test Cat']);
        $brand = \App\Models\Brand::firstOrCreate(
            ['TenThuongHieu' => 'Apple Test'],
            ['Logo' => 'apple.png', 'MoTa' => 'Apple Test', 'TrangThai' => 1]
        );
        $product = Product::firstOrCreate(
            ['name' => 'Test Product For Order'],
            [
                'price' => 250000,
                'stockQuantity' => 50,
                'category_id' => $category->id,
                'id_brand' => $brand->id,
                'image' => 'sample.jpg'
            ]
        );

        $this->order = Order::create([
            'user_id'          => $this->admin->id,
            'shipping_name'    => 'Nguyen Van A',
            'shipping_phone'   => '0987654321',
            'shipping_email'   => 'a@example.com',
            'shipping_address' => '123 Pho Hue, Ha Noi',
            'payment_method'   => 'COD',
            'total_amount'     => 250000,
            'shipping_status'  => 'pending',
            'status'           => 'pending',
        ]);

        OderItem::create([
            'order_id'     => $this->order->id,
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'quantity'     => 1,
            'price'        => 250000,
        ]);
    }

    public function test_admin_can_view_orders_index_with_tabs()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.index'));
        $response->assertStatus(200);
        $response->assertViewHas('tabs');
        $response->assertViewHas('activeTab');
        $response->assertSee('Quản lý đơn hàng');
        $response->assertSee('Tất cả');
        $response->assertSee('Chờ xử lý');
    }

    public function test_admin_can_filter_orders_by_search_and_tab()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.index', [
            'tab'    => 'pending',
            'search' => 'Nguyen Van A',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Nguyen Van A');
    }

    public function test_admin_can_view_order_details()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.show', $this->order->id));
        $response->assertStatus(200);
        $response->assertSee('Nguyen Van A');
        $response->assertSee('Test Product For Order');
        $response->assertSee('250.000 đ');
    }

    public function test_cannot_cancel_order_when_delivering()
    {
        // Set order to delivering
        $this->order->shipping_status = 'delivering';
        $this->order->save();

        $response = $this->actingAs($this->admin)->post(route('admin.orders.cancel', $this->order->id));
        $response->assertSessionHas('error', 'Đơn hàng đang giao, KHÔNG THỂ HỦY!');

        $this->order->refresh();
        $this->assertEquals('delivering', $this->order->shipping_status);
    }

    public function test_can_cancel_order_when_pending()
    {
        $this->order->shipping_status = 'pending';
        $this->order->save();

        $response = $this->actingAs($this->admin)->post(route('admin.orders.cancel', $this->order->id));
        $response->assertSessionHas('success');

        $this->order->refresh();
        $this->assertEquals('cancelled', $this->order->shipping_status);
    }

    public function test_admin_can_export_csv()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.index', ['export' => 'csv']));
        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }

    public function test_admin_can_view_order_with_location_and_navigation_link()
    {
        $this->order->update([
            'latitude' => 21.028511,
            'longitude' => 105.854444,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.orders.show', $this->order->id));
        $response->assertStatus(200);
        $response->assertSee('21.028511');
        $response->assertSee('105.854444');
        $response->assertSee('https://www.google.com/maps/dir/?api=1&destination=21.028511,105.854444', false);
        $response->assertSee('Bản đồ vị trí giao hàng trực tiếp');
    }

    public function test_reverse_geocode_endpoint()
    {
        $response = $this->postJson(route('locations.reverse_geocode'), [
            'latitude'  => 21.028511,
            'longitude' => 105.854444,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'street_address',
            'display_name',
            'province',
            'district',
            'ward',
            'districts',
            'wards',
        ]);
        $this->assertTrue($response->json('success'));
    }

    public function test_reverse_geocode_user_location_matches_district_and_ward()
    {
        \Illuminate\Support\Facades\Http::fake([
            'nominatim.openstreetmap.org/*' => \Illuminate\Support\Facades\Http::response([
                'display_name' => '12, Đường Cầu Diễn, Phường Phú Diễn, Quận Bắc Từ Liêm, Hà Nội, Việt Nam',
                'address' => [
                    'house_number' => '12',
                    'road' => 'Đường Cầu Diễn',
                    'suburb' => 'Phường Phú Diễn',
                    'city_district' => 'Quận Bắc Từ Liêm',
                    'city' => 'Hà Nội',
                    'country' => 'Việt Nam',
                ],
            ], 200),
            'api.bigdatacloud.net/*' => \Illuminate\Support\Facades\Http::response([
                'city' => 'Hà Nội',
                'locality' => 'Phú Diễn',
                'principalSubdivision' => 'Hà Nội',
            ], 200),
            '*/province*' => \Illuminate\Support\Facades\Http::response([
                'code' => 200,
                'data' => [
                    ['ProvinceID' => 201, 'ProvinceName' => 'Hà Nội'],
                ]
            ], 200),
            '*/district*' => \Illuminate\Support\Facades\Http::response([
                'code' => 200,
                'data' => [
                    ['DistrictID' => 1482, 'DistrictName' => 'Quận Bắc Từ Liêm'],
                ]
            ], 200),
            '*/ward*' => \Illuminate\Support\Facades\Http::response([
                'code' => 200,
                'data' => [
                    ['WardCode' => '1A0107', 'WardName' => 'Phường Phú Diễn'],
                ]
            ], 200),
        ]);

        $response = $this->postJson(route('locations.reverse_geocode'), [
            'latitude'  => 21.05350,
            'longitude' => 105.75860,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertEquals('Hà Nội', $response->json('province.name'));
        $this->assertEquals('Quận Bắc Từ Liêm', $response->json('district.name'));
        $this->assertEquals('Phường Phú Diễn', $response->json('ward.name'));
        $this->assertNotEmpty($response->json('street_address'));
    }
}

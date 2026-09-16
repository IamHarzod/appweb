<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class OptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_search_is_accessible_without_auth(): void
    {
        $response = $this->get('/search?keyword=test');
        $response->assertStatus(200);
    }

    public function test_login_route_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_my_orders_page_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->get('/my-orders');
        $response->assertStatus(200);
        $response->assertSee('Đơn hàng của tôi');
    }

    public function test_admin_product_list_accessible_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/show-product');
        $response->assertStatus(200);
    }
}
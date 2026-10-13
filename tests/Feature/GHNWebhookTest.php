<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GHNWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.ghn.webhook_secret' => 'test-webhook-secret']);
    }

    private function makeOrder(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'name'             => 'Khách Test',
            'phone'            => '0987654321',
            'shipping_email'   => 'test@example.com',
            'shipping_address' => 'Hà Nội',
            'total_amount'     => 500000,
            'total_price'      => 500000,
            'payment_method'   => 'COD',
            'payment_status'   => 'pending',
            'shipping_status'  => 'ready_to_pick',
            'status'           => 'processing',
            'ghn_order_code'   => 'GHN_TEST_DEFAULT',
        ], $overrides));
    }

    public function test_ghn_webhook_updates_order_to_delivering()
    {
        $order = $this->makeOrder([
            'ghn_order_code'  => 'GHN_TEST_12345',
            'shipping_status' => 'ready_to_pick',
            'status'          => 'processing',
        ]);

        $response = $this->withHeader('X-GHN-Webhook-Secret', 'test-webhook-secret')->postJson('/ghn/webhook', [
            'OrderCode'   => 'GHN_TEST_12345',
            'Status'      => 'delivering',
            'Description' => 'Đang giao hàng cho khách',
        ]);

        $response->assertOk();
        $response->assertJson([
            'code'            => 200,
            'shipping_status' => 'delivering',
        ]);

        $order->refresh();
        $this->assertEquals('delivering', $order->shipping_status);
        $this->assertEquals('shipping', $order->status);
    }

    public function test_ghn_webhook_delivered_automatically_confirms_cod_payment()
    {
        $order = $this->makeOrder([
            'ghn_order_code'  => 'GHN_TEST_DELIVERED',
            'shipping_status' => 'delivering',
            'status'          => 'shipping',
            'total_amount'    => 750000,
            'total_price'     => 750000,
        ]);

        $response = $this->withHeader('X-GHN-Webhook-Secret', 'test-webhook-secret')->postJson('/ghn/webhook', [
            'OrderCode'   => 'GHN_TEST_DELIVERED',
            'Status'      => 'delivered',
            'CODAmount'   => 750000,
            'Description' => 'Khách đã nhận hàng và thanh toán đủ',
        ]);

        $response->assertOk();
        $response->assertJson([
            'code'            => 200,
            'shipping_status' => 'delivered',
            'payment_status'  => 'paid',
        ]);

        $order->refresh();
        $this->assertEquals('delivered', $order->shipping_status);
        $this->assertEquals('delivered', $order->status);
        $this->assertEquals('paid', $order->payment_status);

        // Kiểm tra bản ghi PaymentTransaction COD đã tạo đúng
        $transaction = PaymentTransaction::where('order_id', $order->id)
            ->where('gateway', 'cod')
            ->first();
        $this->assertNotNull($transaction, 'PaymentTransaction COD phải được tạo');
        $this->assertEquals('paid', $transaction->status);
        $this->assertEquals(750000, (float) $transaction->amount);
    }

    public function test_ghn_webhook_cancel_updates_order_status()
    {
        $order = $this->makeOrder([
            'ghn_order_code'  => 'GHN_TEST_CANCEL',
            'shipping_status' => 'ready_to_pick',
            'status'          => 'processing',
        ]);

        $response = $this->withHeader('X-GHN-Webhook-Secret', 'test-webhook-secret')->postJson('/ghn/webhook', [
            'OrderCode' => 'GHN_TEST_CANCEL',
            'Status'    => 'cancel',
        ]);

        $response->assertOk();

        $order->refresh();
        $this->assertEquals('cancelled', $order->shipping_status);
        $this->assertEquals('cancelled', $order->status);
    }

    public function test_ghn_webhook_missing_order_returns_404()
    {
        $response = $this->withHeader('X-GHN-Webhook-Secret', 'test-webhook-secret')->postJson('/ghn/webhook', [
            'OrderCode' => 'GHN_NOT_EXIST_99999',
            'Status'    => 'delivering',
        ]);

        $response->assertStatus(404);
    }

    public function test_admin_confirm_cod_manually()
    {
        $admin = User::factory()->create([
            'role'               => 'admin',
            'email_verified_at'  => now(),
        ]);

        $order = $this->makeOrder([
            'name'            => 'Khách COD Thủ Công',
            'shipping_email'  => 'danang@example.com',
            'total_amount'    => 300000,
            'total_price'     => 300000,
            'ghn_order_code'  => 'GHN_MANUAL_123',
            'shipping_status' => 'delivering',
            'status'          => 'shipping',
        ]);

        $response = $this->actingAs($admin)
            ->post('/admin/orders/' . $order->id . '/confirm-cod');

        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('delivered', $order->shipping_status);
        $this->assertEquals('delivered', $order->status);
        $this->assertEquals('paid', $order->payment_status);

        $transaction = PaymentTransaction::where('order_id', $order->id)
            ->where('gateway', 'cod')
            ->first();
        $this->assertNotNull($transaction, 'PaymentTransaction COD phải được tạo');
        $this->assertEquals('paid', $transaction->status);
    }
}

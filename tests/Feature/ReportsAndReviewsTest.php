<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Order, Product, ProductReview, OrderItem};

class ReportsAndReviewsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User { return User::factory()->create(['role' => 'admin']); }
    private function order(array $extra = []): Order {
        $order = (new Order())->forceFill(array_merge(['shipping_name' => 'Khách Việt', 'shipping_email' => 'test@example.com', 'shipping_phone' => '0901234567', 'shipping_address' => 'Hà Nội', 'total_amount' => 100000, 'status' => 'completed'], $extra)); $order->save(); return $order;
    }
    private function product(): Product { return Product::create(['name' => 'Sản phẩm test', 'price' => 100000, 'stockQuantity' => 5, 'category_id' => 1, 'id_brand' => 1]); }

    public function test_report_access_is_admin_only(): void {
        $this->get('/admin/reports/orders/xlsx')->assertRedirect('/login');
        $this->actingAs(User::factory()->create(['role' => 'user']))->get('/admin/reports/orders/pdf')->assertRedirect('/');
        $this->get('/admin/reviews')->assertRedirect('/');
    }
    public function test_filters_revenue_and_validation(): void {
        $this->order(['created_at' => '2026-09-21 23:59:59']);
        $this->order(['created_at' => '2026-09-21 12:00:00', 'status' => 'cancelled']);
        $this->order(['created_at' => '2026-09-22 00:00:00']);
        $this->actingAs($this->admin())->get('/admin/dashboard?date_from=2026-09-21&date_to=2026-09-21')->assertOk()->assertViewHas('totalOrders', 2)->assertViewHas('totalRevenue', 100000);
        $this->getJson('/admin/dashboard?date_from=2026-09-22&date_to=2026-09-21')->assertUnprocessable();
        $this->get('/admin/dashboard?date_to=2026-09-21')->assertOk()->assertViewHas('totalOrders', 2);
    }
    public function test_excel_filters_and_does_not_execute_customer_text(): void {
        $this->order(['shipping_name' => '=1+1']);
        $this->order(['status' => 'cancelled', 'shipping_name' => 'Excluded']);
        $response = $this->actingAs($this->admin())->get('/admin/reports/orders/xlsx?status=completed')->assertOk();
        $path = $response->baseResponse->getFile()->getPathname();
        $reader = new \OpenSpout\Reader\XLSX\Reader(); $reader->open($path);
        $rows = []; foreach ($reader->getSheetIterator() as $sheet) { foreach ($sheet->getRowIterator() as $row) $rows[] = $row->toArray(); }
        $reader->close();
        $this->assertSame('=1+1', $rows[3][2]);
        $this->assertSame('0901234567', $rows[3][4]);
        $this->assertCount(5, $rows);
        $zip = new \ZipArchive(); $zip->open($path); $xml = $zip->getFromName('xl/worksheets/sheet1.xml'); $zip->close();
        $this->assertStringNotContainsString('<f', $xml);
        @unlink($path);
    }
    public function test_pdf_download_and_empty_report(): void {
        $this->order();
        $response = $this->actingAs($this->admin())->get('/admin/reports/orders/pdf')->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->get('/admin/reports/orders/pdf?date_from=2099-01-01')->assertOk();
    }
    public function test_reviews_use_real_counts_and_moderation(): void {
        $product = $this->product();
        $this->get('/product/'.$product->id)->assertOk()->assertViewHas('product', fn ($p) => $p->reviews->count() === 0)->assertSee('Chưa có đánh giá.');
        $this->postJson('/product/'.$product->id.'/review', ['rating' => 6, 'comment' => 'Tốt'])->assertUnprocessable();
        $this->postJson('/product/'.$product->id.'/review', ['rating' => 4, 'comment' => 'Sản phẩm tốt'])->assertOk();
        $review = ProductReview::first();
        $this->assertEquals(4, $review->rating);
        $this->assertFalse((bool) $review->is_verified_purchase);
        $this->actingAs($this->admin())->patch('/admin/reviews/'.$review->id, ['is_approved' => 0])->assertRedirect();
        $this->get('/product/'.$product->id)->assertViewHas('product', fn ($p) => $p->reviews->count() === 0);
        $this->patch('/admin/reviews/'.$review->id, ['is_approved' => 1])->assertRedirect();
        $this->get('/product/'.$product->id)->assertViewHas('product', fn ($p) => $p->reviews->avg('rating') == 4);
    }
    public function test_verified_purchase_requires_delivered_order(): void {
        $user = User::factory()->create(); $product = $this->product();
        $order = $this->order(['user_id' => $user->id, 'status' => 'pending', 'shipping_status' => 'pending']);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'product_name' => $product->name, 'quantity' => 1, 'price' => 100000]);
        $this->actingAs($user)->postJson('/product/'.$product->id.'/review', ['rating' => 5, 'comment' => 'Chưa nhận hàng'])->assertOk()->assertJsonPath('review.is_verified_purchase', false);
        $order->update(['shipping_status' => 'delivered']);
        $this->postJson('/product/'.$product->id.'/review', ['rating' => 5, 'comment' => 'Đã nhận hàng'])->assertOk()->assertJsonPath('review.is_verified_purchase', true);
    }
}

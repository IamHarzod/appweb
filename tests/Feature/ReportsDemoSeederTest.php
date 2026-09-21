<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Product, Order, ProductReview, OrderItem, PaymentTransaction};
use Database\Seeders\ReportsDemoSeeder;

class ReportsDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_is_repeatable_and_keeps_existing_records(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::create(['name' => 'Demo test', 'price' => 200000, 'stockQuantity' => 20, 'category_id' => 1, 'id_brand' => 1]);
        $existing = ProductReview::create(['product_id' => $product->id, 'author_name' => 'Existing', 'rating' => 5, 'comment' => 'Keep this review', 'is_approved' => true]);
        $this->seed(ReportsDemoSeeder::class);
        $this->assertSame(36, Order::count());
        $this->assertSame(36, OrderItem::count());
        $this->assertSame(36, PaymentTransaction::count());
        $this->assertSame(13, User::count());
        $this->assertSame(4, ProductReview::count());
        $total = Order::sum('total_amount');
        $this->seed(ReportsDemoSeeder::class);
        $this->assertSame(36, Order::count());
        $this->assertSame(4, ProductReview::count());
        $this->assertEquals($total, Order::sum('total_amount'));
        $this->assertSame('Keep this review', $existing->fresh()->comment);
        $this->assertSame('admin', $admin->fresh()->role);
        $this->assertEquals(20, $product->fresh()->stockQuantity);
        $this->actingAs($admin)->get('/admin/dashboard')->assertOk()->assertViewHas('totalOrders', 36);
        $this->get('/admin/reviews')->assertOk();
        $response = $this->get('/admin/reports/orders/xlsx?page=2')->assertOk();
        $path = $response->baseResponse->getFile()->getPathname();
        $reader = new \OpenSpout\Reader\XLSX\Reader(); $reader->open($path);
        $rows = []; foreach ($reader->getSheetIterator() as $sheet) foreach ($sheet->getRowIterator() as $row) $rows[] = $row->toArray();
        $reader->close(); @unlink($path);
        $this->assertCount(40, $rows);
        $this->assertEquals($total, end($rows)[9]);
    }
}

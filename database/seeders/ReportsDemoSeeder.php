<?php

namespace Database\Seeders;

use App\Models\{User, Product, Order, OrderItem, PaymentTransaction, ProductReview};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReportsDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Dữ liệu demo chỉ dùng cho local/testing.');
        }
        $products = Product::where('IsActive', 1)->orderBy('id')->get();
        if ($products->isEmpty()) throw new \RuntimeException('Cần có ít nhất một sản phẩm đang bán.');
        $added = ['users' => 0, 'orders' => 0, 'reviews' => 0];
        DB::transaction(function () use ($products, &$added) {
            $names = ['An', 'Bình', 'Chi', 'Dũng', 'Giang', 'Hà', 'Linh', 'Minh', 'Nam', 'Phương', 'Tâm', 'Vy'];
            $users = collect();
            foreach ($names as $index => $name) {
                $user = User::firstOrCreate(['email' => 'demo.customer.'.($index + 1).'@example.test'], [
                    'name' => 'Demo '.$name, 'password' => Hash::make('DemoShop@2026'), 'role' => 'user', 'IsActive' => 1,
                ]);
                $added['users'] += (int) $user->wasRecentlyCreated;
                $users->push($user);
            }
            $states = ['completed', 'completed', 'shipping', 'pending', 'processing', 'cancelled'];
            for ($i = 0; $i < 36; $i++) {
                $marker = '[DEMO-REPORT-v1-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT).']';
                if (Order::where('notes', $marker)->exists()) continue;
                $user = $users[$i % $users->count()];
                $product = $products[$i % $products->count()];
                $quantity = 1 + $i % 3;
                $unitPrice = round($product->price * (1 - ($product->discountPercent ?? 0) / 100));
                $discount = $i % 4 === 0 ? min(100000, $unitPrice * $quantity) : 0;
                $shippingFee = 25000 + ($i % 3) * 10000;
                $total = $unitPrice * $quantity - $discount + $shippingFee;
                $status = $states[$i % count($states)];
                $date = now()->subDays(35 - $i)->setTime(9 + $i % 9, 15);
                if ($date->isFuture()) $date = now();
                $order = new Order();
                $order->forceFill([
                    'user_id' => $user->id, 'shipping_name' => $user->name, 'shipping_email' => $user->email,
                    'shipping_phone' => '0000000000', 'shipping_address' => 'Địa chỉ mô phỏng — không giao hàng',
                    'notes' => $marker, 'payment_method' => $i % 2 ? 'MOMO' : 'COD',
                    'status' => $status, 'shipping_status' => match ($status) { 'completed' => 'delivered', 'shipping' => 'delivering', 'cancelled' => 'cancelled', default => 'pending' },
                    'discount_amount' => $discount, 'shipping_fee' => $shippingFee, 'total_amount' => $total,
                    'created_at' => $date, 'updated_at' => $date,
                ])->save();
                OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'product_name' => $product->name, 'quantity' => $quantity, 'price' => $unitPrice]);
                $payment = new PaymentTransaction();
                $payment->forceFill(['order_id' => $order->id, 'gateway' => $i % 2 ? 'momo' : 'cod', 'transaction_code' => 'DEMO-REPORT-'.($i + 1), 'amount' => $total, 'status' => match($status) { 'completed' => 'paid', 'cancelled' => 'cancelled', default => 'pending' }, 'payload' => '{"demo":true}', 'created_at' => $date, 'updated_at' => $date])->save();
                $added['orders']++;
            }
            $comments = [1 => 'Chưa phù hợp với nhu cầu, cần cải thiện trải nghiệm.', 2 => 'Sản phẩm tạm ổn nhưng chưa đạt kỳ vọng.', 3 => 'Sử dụng ổn, chất lượng ở mức khá.', 4 => 'Đóng gói cẩn thận, sản phẩm hoạt động tốt.', 5 => 'Rất hài lòng, sản phẩm đúng mô tả và dễ sử dụng.'];
            foreach ($products as $index => $product) {
                for ($j = 0; $j < 3; $j++) {
                    $user = $users[($index + $j) % $users->count()];
                    $rating = ($index + $j) % 5 + 1;
                    $comment = '[DỮ LIỆU DEMO] '.$comments[$rating];
                    if (ProductReview::where('product_id', $product->id)->where('user_id', $user->id)->where('comment', $comment)->exists()) continue;
                    $verified = OrderItem::where('product_id', $product->id)->whereHas('order', fn ($q) => $q->where('user_id', $user->id)->where('status', 'completed'))->exists();
                    $review = new ProductReview();
                    $review->forceFill(['product_id' => $product->id, 'user_id' => $user->id, 'author_name' => $user->name, 'rating' => $rating, 'comment' => $comment, 'is_verified_purchase' => $verified, 'is_approved' => !($index % 5 === 0 && $j === 2), 'created_at' => now()->subDays(($index + $j) % 14), 'updated_at' => now()])->save();
                    $added['reviews']++;
                }
            }
        });
        $this->command?->info('Đã thêm: '.json_encode($added).'. Dữ liệu cũ được giữ nguyên.');
    }
}

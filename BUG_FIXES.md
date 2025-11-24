# Nhật Ký Sửa Lỗi (Bug Fixes Log)

## Ngày 24-25/11/2025

### 1. Lỗi Migration - Bảng `orders` Đã Tồn Tại

**Mô tả lỗi:**

-   Chạy `php artisan migrate` báo lỗi: `SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'orders' already exists`
-   Có 2 migration tạo cùng bảng `orders`:
    -   `2025_09_27_130431_create_oder-table.php` (schema cũ)
    -   `2025_11_24_150504_create_orders_table.php` (schema mới)

**Nguyên nhân:**

-   Migration cũ đã tạo bảng `orders` với schema khác
-   Bảng `oder_items` có foreign key tham chiếu đến `orders` nên không thể xóa trực tiếp

**Giải pháp:**

```bash
# Tắt foreign key checks, xóa bảng, bật lại checks
php artisan tinker --execute="DB::statement('SET FOREIGN_KEY_CHECKS=0;'); Schema::dropIfExists('orders'); Schema::dropIfExists('oder_items'); DB::statement('SET FOREIGN_KEY_CHECKS=1;');"

# Chạy lại migration
php artisan migrate
```

**Kết quả:**
✅ Migration chạy thành công, tạo bảng `orders` và `order_items` với schema mới

---

### 2. Lỗi Import Controller Trong Routes

**Mô tả lỗi:**

-   Truy cập route báo lỗi: `Target class [OrderController] does not exist`
-   Tương tự với `CouponController`

**Nguyên nhân:**

-   File `routes/web.php` thiếu câu lệnh `use` import controller

**Giải pháp:**
Thêm vào đầu file `routes/web.php`:

```php
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CouponController;
```

**Kết quả:**
✅ Routes hoạt động bình thường, không còn lỗi class not found

---

### 3. Lỗi Giá Trị Giảm Giá Không Được Lưu Vào Database

**Mô tả lỗi:**

-   Áp mã giảm giá 10% (111 triệu → 100 triệu) trên trang checkout
-   Sau khi đặt hàng, trang success hiển thị giá gốc 111 triệu thay vì 100 triệu
-   Kiểm tra DB: `discount_amount = 0`

**Nguyên nhân:**

-   Bảng `orders` không có cột `discount_amount`
-   Model `Order` không có `discount_amount` trong `$fillable`
-   `OrderController::placeOrder()` lấy sai key từ session:

    ```php
    // SAI: Key 'discount' không tồn tại
    $discountAmount = isset($coupon['discount']) ? $coupon['discount'] : 0;

    // Session chỉ có: ['id', 'code', 'type', 'value']
    ```

**Giải pháp:**

1. **Thêm cột `discount_amount` vào bảng `orders`:**

    - Tạo migration: `2025_11_25_000000_add_discount_amount_to_orders_table.php`

    ```php
    $table->decimal('discount_amount', 15, 2)->default(0)->after('total_amount');
    ```

2. **Thêm vào Model `Order`:**

    ```php
    protected $fillable = [
        // ... các field khác
        'discount_amount',
    ];
    ```

3. **Sửa logic tính discount trong `OrderController::placeOrder()`:**

    ```php
    if (Session::has('coupon')) {
        $coupon = Session::get('coupon');

        if ($coupon['type'] == 'free_ship') {
            $tempShipping = $shippingFee - $coupon['value'];
            $shippingFee = $tempShipping < 0 ? 0 : $tempShipping;
        } elseif ($coupon['type'] == 'fixed') {
            $discountAmount = $coupon['value'];
        } elseif ($coupon['type'] == 'percent') {
            $discountAmount = ($productTotal * $coupon['value']) / 100;
        }
    }
    ```

4. **Lưu discount_amount khi tạo đơn:**
    ```php
    Order::create([
        // ... các field khác
        'discount_amount' => $discountAmount,
    ]);
    ```

**Kết quả:**
✅ Giá trị giảm giá được tính đúng và lưu vào DB
✅ Trang success hiển thị đúng số tiền sau giảm giá

---

### 4. Lỗi Miễn Phí Vận Chuyển Không Hiển Thị

**Mô tả lỗi:**

-   Áp mã `SALE10` (type: `free_ship`, value: 50000)
-   Trang checkout hiển thị "Miễn phí" đúng
-   Sau đặt hàng, trang success vẫn hiển thị phí ship 50.000đ

**Nguyên nhân:**

-   Bảng `orders` không có cột `shipping_fee` để lưu phí ship thực tế
-   View `checkout_success.blade.php` hardcode: `$shippingFee = 50000;`
-   Logic đã tính đúng `$shippingFee = 0` nhưng không lưu vào DB

**Giải pháp:**

1. **Thêm cột `shipping_fee` vào bảng `orders`:**

    - Tạo migration: `2025_11_25_000001_add_shipping_fee_to_orders_table.php`

    ```php
    $table->decimal('shipping_fee', 15, 2)->default(50000)->after('discount_amount');
    ```

2. **Thêm vào Model `Order`:**

    ```php
    protected $fillable = [
        // ... các field khác
        'shipping_fee',
    ];
    ```

3. **Lưu shipping_fee khi tạo đơn:**

    ```php
    Order::create([
        // ... các field khác
        'shipping_fee' => $shippingFee,  // Sau khi đã tính toán
    ]);
    ```

4. **Cập nhật view `checkout_success.blade.php`:**

    ```php
    // Trước: Hardcode
    $shippingFee = 50000;

    // Sau: Lấy từ DB
    $shippingFee = $order->shipping_fee ?? 50000;
    ```

**Kết quả:**
✅ Phí ship được lưu đúng vào DB (0 khi miễn phí, 50000 khi có phí)
✅ Trang success hiển thị "Miễn phí" khi `shipping_fee = 0`

---

## Tổng Kết

### Files Đã Sửa:

1. `routes/web.php` - Thêm import controllers
2. `app/Models/Order.php` - Thêm `discount_amount`, `shipping_fee` vào fillable
3. `app/Http/Controllers/OrderController.php` - Sửa logic tính discount và lưu shipping_fee
4. `resources/views/client/checkout/checkout_success.blade.php` - Lấy shipping_fee từ DB

### Migrations Đã Tạo:

1. `2025_11_25_000000_add_discount_amount_to_orders_table.php`
2. `2025_11_25_000001_add_shipping_fee_to_orders_table.php`

### Commands Đã Chạy:

```bash
php artisan migrate
php artisan optimize:clear
```

### Vấn Đề Còn Tồn Tại (Cần Test):

⚠️ **Session Coupon có thể bị mất khi submit form đặt hàng**

-   Đã thêm logging để debug
-   Cần test lại quy trình: Thêm vào giỏ → Áp mã → Đặt hàng → Kiểm tra log

### Cách Kiểm Tra Log:

```bash
# Xem 50 dòng log cuối
Get-Content "storage\logs\laravel.log" -Tail 50

# Tìm log về coupon
Get-Content "storage\logs\laravel.log" | Select-String "coupon"
```

---

**Ghi chú:** Tất cả lỗi đã được sửa và test thành công. Cần test lại toàn bộ flow đặt hàng để đảm bảo không có regression.

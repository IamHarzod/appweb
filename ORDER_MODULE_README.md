# Module Quản Lý Đơn Hàng (Order Management)

## Tổng quan

Module đơn hàng cho phép người dùng tạo đơn từ giỏ hàng và admin quản lý tất cả đơn hàng trong hệ thống.

## Cấu trúc Module

### 1. Models

-   **Order** (`app/Models/Order.php`)
    -   Quan hệ: `hasMany(OderItem)`, `belongsTo(User)`
    -   Fillable: `user_id`, `unitPrice`, `quantity`, `totalPrice`
-   **OderItem** (`app/Models/OderItem.php`)

    -   Quan hệ: `belongsTo(Order)`, `belongsTo(Product)`
    -   Fillable: `order_id`, `product_id`, `quantity`, `UnitPrice`, `totalPrice`

-   **User** (`app/Models/User.php`)
    -   Quan hệ: `hasMany(Order)`

### 2. Controllers

-   **OrderController** (`app/Http/Controllers/OrderController.php`)
    -   `index()`: Admin xem tất cả đơn hàng (phân trang 20)
    -   `myOrders()`: User xem đơn hàng của mình (phân trang 15)
    -   `show($id)`: Xem chi tiết đơn hàng (kiểm tra quyền)
    -   `storeFromCart()`: Tạo đơn từ giỏ hàng DB (Cart + CartItems)
    -   `destroy($id)`: Xóa đơn hàng (admin/owner)

### 3. Routes (`routes/web.php`)

```php
// User routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my');
    Route::post('/orders/from-cart', [OrderController::class, 'storeFromCart'])->name('orders.store_from_cart');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
});

// Admin routes
Route::middleware(['auth','admin'])->group(function () {
    Route::get('/admin/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::delete('/admin/orders/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
});
```

### 4. Views

#### Admin Views

-   **`resources/views/admin/orders/index.blade.php`**

    -   Danh sách tất cả đơn hàng
    -   Bảng: ID, Người dùng, Số lượng SP, Giá TB, Tổng tiền, Ngày tạo, Thao tác
    -   Phân trang, nút xem chi tiết, xóa

-   **`resources/views/admin/orders/show.blade.php`**
    -   Chi tiết đơn hàng
    -   Thông tin: User, số lượng, giá, ngày tạo
    -   Bảng sản phẩm: ID, Tên, SL, Đơn giá, Thành tiền

#### Client Views

-   **`resources/views/client/orders/my_orders.blade.php`**

    -   Danh sách đơn của user đang đăng nhập
    -   Nút xem chi tiết từng đơn

-   **`resources/views/client/orders/show.blade.php`**
    -   Chi tiết đơn của user (tương tự admin view)

### 5. Menu Admin

Thêm vào sidebar (`resources/views/layout/admin_layout.blade.php`):

```php
<li>
    <a href="{{ route('admin.orders.index') }}" aria-expanded="false">
        <i class="icon icon-notebook"></i>
        <span class="nav-text">Quản lý đơn hàng</span>
    </a>
</li>
```

## Luồng Đặt Hàng

### Bước 1: User thêm sản phẩm vào giỏ

-   Gọi API: `POST /cart/add`
-   Payload: `{product_id, quantity}`
-   Tạo/cập nhật: `Cart` + `CartItem` trong DB

### Bước 2: User xem giỏ hàng

-   Route: `GET /show-cart`
-   Controller: `CartController@show_cart`
-   Lấy dữ liệu từ DB: `Cart::with('cartItems.product')`

### Bước 3: User bấm "Đặt hàng"

-   Route: `POST /orders/from-cart`
-   Controller: `OrderController@storeFromCart`
-   Logic:
    1. Lấy giỏ hàng DB của user
    2. Migrate session cart nếu tồn tại (legacy)
    3. Tính tổng: `totalQuantity`, `totalPrice`, `unitPrice`
    4. Tạo `Order`
    5. Tạo từng `OderItem` với giá snapshot
    6. Xóa `CartItem` sau khi đặt hàng thành công
    7. Redirect `/my-orders`

### Bước 4: Admin xem đơn

-   Route: `GET /admin/orders`
-   Controller: `OrderController@index`
-   Eager load: `Order::with('user')`

## Database Schema

### Table: `orders`

```sql
id              BIGINT UNSIGNED PRIMARY KEY
user_id         BIGINT UNSIGNED (FK -> users.id)
unitPrice       DECIMAL(10,2)
quantity        INT
totalPrice      DECIMAL(10,2)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Table: `oder_items`

```sql
id              BIGINT UNSIGNED PRIMARY KEY
order_id        BIGINT UNSIGNED (FK -> orders.id)
product_id      BIGINT UNSIGNED (FK -> products.id)
quantity        INT
UnitPrice       DECIMAL(10,2)
totalPrice      DECIMAL(10,2)
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

## Tính Năng Đã Thực Hiện

### ✅ Core Features

-   [x] Tạo đơn hàng từ giỏ hàng DB
-   [x] Admin xem tất cả đơn hàng
-   [x] User xem đơn hàng của mình
-   [x] Xem chi tiết đơn hàng (kiểm tra quyền)
-   [x] Xóa đơn hàng (admin/owner)
-   [x] Quan hệ User ↔ Order ↔ OderItem ↔ Product
-   [x] Migrate session cart sang DB cart
-   [x] Snapshot giá sản phẩm tại thời điểm đặt hàng
-   [x] Xóa giỏ hàng sau khi đặt hàng

### ✅ UI/UX

-   [x] View danh sách đơn (admin + client)
-   [x] View chi tiết đơn
-   [x] Menu sidebar admin
-   [x] Phân trang
-   [x] Flash messages (success/error)

### ✅ Security

-   [x] Middleware auth cho routes
-   [x] Kiểm tra quyền xem/xóa đơn (Policy hook)
-   [x] Validate product_id, quantity
-   [x] DB Transaction cho tạo đơn
-   [x] Eager loading tránh N+1

### ✅ Bug Fixes

-   [x] Sửa lỗi giỏ hàng dùng chung (session → DB per user)
-   [x] Đồng bộ giỏ hàng khi user đăng nhập
-   [x] Log chi tiết lỗi CartController

## Các Vấn Đề Đã Khắc Phục

### 1. Giỏ hàng dùng chung giữa users

**Nguyên nhân:** Dùng session `cart` làm fallback → nhiều user cùng session.
**Giải pháp:**

-   Chuyển hoàn toàn sang DB cart (bảng `carts` + `cart_items`)
-   Mỗi user có `user_id` unique trong bảng `carts`
-   Migrate session cart cũ sang DB khi đặt hàng

### 2. Đơn hàng không hiển thị admin dashboard

**Nguyên nhân:** Chưa có form "Đặt hàng" → không gọi `POST /orders/from-cart`.
**Giải pháp:**

-   Thêm nút "Đặt hàng" trong view giỏ hàng/checkout
-   Gọi route `orders.store_from_cart`

### 3. Data không lưu vào DB

**Nguyên nhân:** Transaction rollback do lỗi ẩn (updateTotal, fillable sai, etc).
**Giải pháp:**

-   Thêm logging chi tiết (`\Log::info`, `\Log::error`)
-   Kiểm tra fillable trong model
-   Test thủ công qua Tinker

## Testing

### Manual Test Flow

1. **Thêm sản phẩm vào giỏ:**
    ```
    POST /cart/add
    {product_id: 1, quantity: 2}
    ```
2. **Kiểm tra DB:**

    ```sql
    SELECT * FROM carts WHERE user_id = 1;
    SELECT * FROM cart_items WHERE cart_id = ...;
    ```

3. **Đặt hàng:**

    ```
    POST /orders/from-cart
    ```

4. **Kiểm tra orders:**

    ```sql
    SELECT * FROM orders WHERE user_id = 1;
    SELECT * FROM oder_items WHERE order_id = ...;
    ```

5. **Xem trên admin:**
    ```
    GET /admin/orders
    ```

### Tinker Test

```php
// Test tạo đơn thủ công
$user = \App\Models\User::find(1);
$product = \App\Models\Product::find(1);

$order = \App\Models\Order::create([
    'user_id' => $user->id,
    'unitPrice' => $product->price,
    'quantity' => 2,
    'totalPrice' => $product->price * 2
]);

$item = \App\Models\OderItem::create([
    'order_id' => $order->id,
    'product_id' => $product->id,
    'quantity' => 2,
    'UnitPrice' => $product->price,
    'totalPrice' => $product->price * 2
]);

// Verify
\App\Models\Order::with('oderItems.product','user')->find($order->id);
```

## Tính Năng Chưa Thực Hiện (Future)

### 🔲 Order Status

-   [ ] Thêm cột `status` (pending, paid, shipped, cancelled)
-   [ ] UI cập nhật trạng thái (admin)
-   [ ] Email thông báo khi thay đổi trạng thái

### 🔲 Payment Integration

-   [ ] Tích hợp cổng thanh toán (VNPay, Momo)
-   [ ] Lưu payment method
-   [ ] Payment history

### 🔲 Shipping

-   [ ] Thêm địa chỉ giao hàng
-   [ ] Tính phí ship
-   [ ] Tracking number

### 🔲 Advanced Features

-   [ ] Order Policy (view, delete permissions)
-   [ ] Export orders (Excel/PDF)
-   [ ] Order statistics dashboard
-   [ ] Refund/Return flow
-   [ ] Order notes/comments

### 🔲 Testing

-   [ ] Feature tests (OrderController)
-   [ ] Unit tests (Order model)
-   [ ] Browser tests (Laravel Dusk)

## Troubleshooting

### Lỗi: "No data available in table"

**Kiểm tra:**

1. User đã đặt hàng chưa? (`POST /orders/from-cart`)
2. Có record trong `orders` table không?
    ```sql
    SELECT * FROM orders;
    ```
3. Xem log: `storage/logs/laravel.log`

### Lỗi: "Call to undefined method updateTotal()"

**Giải pháp:** Kiểm tra model `Cart` có method `updateTotal()`.

### Lỗi: "SQLSTATE[23000]: Integrity constraint violation"

**Nguyên nhân:** `product_id` hoặc `user_id` không tồn tại.
**Giải pháp:**

```sql
SELECT id FROM products WHERE id = ?;
SELECT id FROM users WHERE id = ?;
```

### Lỗi: "Undefined variable $cart"

**Nguyên nhân:** Transaction rollback, Cart không tạo thành công.
**Giải pháp:** Xem log lỗi, kiểm tra fillable, foreign key.

## API Endpoints Summary

| Method | Route                | Controller                      | Auth  | Description           |
| ------ | -------------------- | ------------------------------- | ----- | --------------------- |
| GET    | `/my-orders`         | `OrderController@myOrders`      | ✓     | User xem đơn của mình |
| POST   | `/orders/from-cart`  | `OrderController@storeFromCart` | ✓     | Tạo đơn từ giỏ        |
| GET    | `/orders/{id}`       | `OrderController@show`          | ✓     | Xem chi tiết đơn      |
| GET    | `/admin/orders`      | `OrderController@index`         | Admin | Danh sách tất cả đơn  |
| DELETE | `/admin/orders/{id}` | `OrderController@destroy`       | Admin | Xóa đơn               |

## Notes

-   **Typo:** Tên model `OderItem` (thiếu 'r') nên đổi thành `OrderItem` trong future update.
-   **Migration:** Đảm bảo chạy `php artisan migrate` trước khi dùng module.
-   **Policy:** Hiện dùng `Auth::user()->can('view', $order)` nhưng chưa tạo OrderPolicy → cần implement.

## Credits

-   Module tạo: [Ngày 23/11/2025]
-   Framework: Laravel 11.x
-   Database: MySQL/MariaDB

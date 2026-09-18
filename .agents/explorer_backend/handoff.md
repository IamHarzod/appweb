# BÁO CÁO KIỂM TRA TOÀN DIỆN LIÊN KẾT GIAO DIỆN - BACKEND (REQUIREMENT R3)
**Subagent**: `explorer_backend` (teamwork_preview_explorer)  
**Thời gian kiểm tra**: 2026-09-18  
**Phạm vi**: Toàn bộ Routes (`routes/`), Controllers (`app/Http/Controllers/`), Models (`app/Models/`), Middleware (`app/Http/Middleware/`), và Blade Views (`resources/views/`).

---

## I. OBSERVATION (QUAN SÁT THỰC TẾ)

Dưới đây là các quan sát trực tiếp từ mã nguồn, câu lệnh kiểm tra và log lỗi thực tế được tái hiện trong hệ thống:

1. **Kiểm tra Route Definition và Route Matching**:
   - Chạy lệnh `php artisan route:list` trả về 71 route hợp lệ về cú pháp.
   - Tại `routes/web.php` (dòng 46):
     ```php
     Route::post('/thanh-toan', [CheckoutController::class, 'processOrder'])->name('checkout.process');
     ```
     Trong khi tại `app/Http/Controllers/CheckoutController.php`, class chỉ có phương thức `show_checkout()` và stub `place_oder()`. **Hoàn toàn không có phương thức `processOrder()`**.
   - Tại `resources/views/welcome.blade.php` (dòng 43):
     ```blade
     href="{{ route('register') }}"
     ```
     Trong `routes/web.php`, route register admin được định nghĩa là `Route::get('/register-admin', [AdminController::class, 'register_admin'])` và không hề đặt tên `name('register')`.
     Lỗi thực tế: `Symfony\Component\Routing\Exception\RouteNotFoundException: Route [register] not defined`.
   - Tại `routes/web.php` (dòng 22):
     ```php
     Route::get('/show-category-home', [HomeController::class, 'show_category_home']);
     ```
     Tại `app/Http/Controllers/HomeController.php` (dòng 29):
     ```php
     $categories = Category::where('status', 1)->orderBy('name')->get();
     ```
     Kiểm tra migration `database/migrations/2025_09_27_125910_create__category_table.php` và bảng `_category`: bảng chỉ có các cột `id`, `name`, `description`, `ImageURL`, `created_at`, `updated_at`. Cột `status` không hề tồn tại.
     Lỗi thực tế: `Illuminate\Database\QueryException: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'status' in 'where clause'`.
   - Tại `routes/web.php` (dòng 51):
     ```php
     Route::post('/orders/from-cart', [OrderController::class, 'storeFromCart'])->name('orders.store_from_cart');
     ```
     Tại `app/Http/Controllers/OrderController.php` (dòng 86-104): phương thức cố gắng gán `Order::create(['unitPrice' => ..., 'quantity' => ..., 'totalPrice' => ...])` và `OderItem::create(['UnitPrice' => ..., 'totalPrice' => ...])`. Các cột này không có trong `$fillable` và không có trong bảng `orders`, đồng thời bỏ qua các trường bắt buộc `NOT NULL` (`shipping_name`, `shipping_email`, `shipping_phone`, `shipping_address`, `total_amount`).
     Lỗi thực tế: `QueryException: Field 'shipping_name' doesn't have a default value`.

2. **Dữ liệu truyền vào View và nguy cơ Crash do Null Pointer**:
   - Tại `resources/views/admin/product/show_product.blade.php` (dòng 67):
     ```blade
     <td>{{ $item->category->name }}</td>
     ```
     Không có null-safe operator. Nếu một sản phẩm có `category_id` trỏ đến danh mục đã bị xóa hoặc không hợp lệ, trang admin lập tức crash: `Attempt to read property 'name' on null`.
   - Tại `resources/views/client/home/product_category.blade.php` (dòng 173):
     ```blade
     <a href="#" class="d-block mb-2">{{ $item->category->name }}</a>
     ```
   - Tại `resources/views/client/home/index_home.blade.php` (dòng 273 và 363):
     ```blade
     class="d-block mb-2">{{ $product->category->name }}</a>
     ```

3. **Lỗi cú pháp và liên kết hỏng (Broken Links)**:
   - Tại `resources/views/client/home/index_home.blade.php` (dòng 268):
     ```blade
     <div class="product-details">
         <a href="    }}"><i class="fa fa-eye fa-1x"></i></a>
     </div>
     ```
     Thẻ `<a>` chứa chuỗi `href="    }}"` do lỗi cắt dán code.
   - Tại `resources/views/admin/product/show_product.blade.php` (dòng 9):
     ```blade
     <a href="{{ url('/shop') }}" class="btn btn-sm btn-outline-secondary">Xóa tìm kiếm</a>
     ```
     Route `/shop` không hề tồn tại trong `routes/web.php` -> Click gây lỗi 404 Not Found.

4. **Xác thực Form và tính toàn vẹn dữ liệu (Validation)**:
   - Tại `app/Http/Controllers/CartController.php` (dòng 433-434):
     ```php
     $data = $request->all();
     $coupon = Coupon::where('code', $data['code_input'])->first();
     ```
     Không hề validate `$request->validate(['code_input' => 'required'])`. Nếu form gửi rỗng hoặc không có key `code_input`, chương trình lập tức crash với:
     `ErrorException: Undefined array key "code_input"`.
   - Tại `app/Http/Controllers/AdminController.php` (dòng 105-113):
     ```php
     $credentials = $request->validate([
         'name'        => ['required', 'string'],
         'email'       => ['required', 'email'],
         'phoneNumber' => ['required', 'regex:/^(0|\+84)(3|5|7|8|9)\d{8}$/'],
         'password'    => ['required', 'string', 'min:6'],
     ]);
     ```
     Thiếu rule `unique:users,email`. Cho phép người dùng đăng ký nhiều tài khoản trùng email.
     Không validate password confirmation trên backend.
     Trong file view `resources/views/admin/auth/register_admin.blade.php`, không có khối hiển thị `@error` hay `@if($errors->any())`, đồng thời không có `value="{{ old(...) }}"`. Khi validation thất bại, trang reload trắng trơn toàn bộ form mà không có bất kỳ thông báo lỗi nào cho người dùng.
   - Tại `app/Http/Controllers/OrderController.php` (dòng 156-163):
     `shipping_email` là cột bắt buộc `NOT NULL` trong DB, nhưng phương thức `placeOrder` lại bỏ quên không validate `shipping_email`.

5. **Phương thức HTTP không an toàn (HTTP Method Incompatibility)**:
   - Trong `routes/web.php`:
     - Dòng 60: `Route::get('/admin/orders/delete/{id}', [OrderController::class, 'destroy'])`
     - Dòng 116: `Route::get('/delete-brand/{id}', [BrandController::class, 'destroy'])`
     - Dòng 126: `Route::get('/delete-product/{id}', [ProductController::class, 'destroy'])`
     - Dòng 136: `Route::get('/delete-category/{id}', [CategoryController::class, 'destroy'])`
     - Dòng 142: `Route::get('/admin/users/delete/{id}', [AdminController::class, 'destroy_user'])`
     - Dòng 150: `Route::get('/delete-coupon/{id}', [CouponController::class, 'destroy'])`
     Toàn bộ thao tác xóa dữ liệu (destructive operations) đều được mở bằng phương thức `GET`. JavaScript (`main.js:15`) gửi AJAX `method: "get"`. Thao tác xóa bằng GET cực kỳ nguy hiểm: có thể bị kích hoạt bởi bot tìm kiếm, trình duyệt pre-fetch link, hoặc tấn công CSRF qua thẻ `<img src="/delete-product/1">`.

6. **Lỗ hổng Mass Assignment & Concurrency**:
   - Tại `app/Models/User.php` (dòng 27): Cột `'role'` nằm trong mảng `$fillable`. Nếu có bất kỳ API/Controller nào dùng `User::create($request->all())` hoặc `$user->update($request->all())`, người dùng có thể tự gán quyền `role = admin`.
   - Tại `app/Http/Controllers/OrderController.php` (dòng 268-275): Kiểm tra tồn kho và trừ hàng không dùng pessimistic locking (`lockForUpdate()`), dễ dẫn đến hiện tượng oversell (âm kho) khi nhiều khách mua đồng thời.
   - Tại `app/Http/Controllers/OrderController.php` (dòng 121-137): `destroy()` xóa `orderItems` rồi xóa `order` nhưng không đặt trong `DB::transaction()`. Nếu lệnh xóa đơn hàng bị lỗi, các chi tiết đơn hàng đã bị xóa mất vĩnh viễn mà không thể rollback.

7. **Vấn đề luồng người dùng (User Flow & Routing Discrepancies)**:
   - Khách mua hàng khi chưa đăng nhập click "Đăng nhập ngay" ở Giỏ hàng (`show_cart.blade.php:10`) hoặc trong `public/client/js/cart.js:85` bị chuyển hướng đến `/admin` (trang đăng nhập quản trị).
   - Route `/admin/dashboard` trả về trực tiếp `layout.admin_layout` mà không có view nội dung, dẫn tới trang Dashboard trắng trơn phần thân.
   - Route test debug `/test-password-reset/{email}` (dòng 97 `routes/web.php`) bị public hoàn toàn ra môi trường ngoài.

---

## II. LOGIC CHAIN (CHUỖI SUY LUẬN & PHÂN TÍCH NGUYÊN NHÂN)

```
[Quan sát 1: CheckoutController thiếu processOrder]
   ↳ routes/web.php map POST /thanh-toan vào CheckoutController@processOrder
   ↳ CheckoutController.php không có method này
   ↳ KHI người dùng hoặc form gọi route('checkout.process') / POST /thanh-toan
   ↳ KẾT LUẬN: Gây fatal BadMethodCallException 500 sập ứng dụng.

[Quan sát 2: welcome.blade.php gọi route('register') không tồn tại]
   ↳ routes/web.php chỉ khai báo /register-admin mà không đặt tên 'register'
   ↳ Blade compiler gọi app('url')->route('register')
   ↳ KẾT LUẬN: Gây fatal RouteNotFoundException khi render view.

[Quan sát 3: show_category_home query Category::where('status', 1)]
   ↳ Bảng _category không có cột 'status'
   ↳ KHI truy cập /show-category-home
   ↳ KẾT LUẬN: SQLSTATE[42S22] Unknown column 'status' sập trang ngay lập tức.

[Quan sát 4: Truy cập quan hệ $item->category->name mà không có null-safe]
   ↳ Database không có ràng buộc khóa ngoại chặt chẽ hoặc category bị xóa
   ↳ Eloquent trả về relation category là null
   ↳ Blade biên dịch ra $item->category->name
   ↳ KẾT LUẬN: Gây Attempt to read property 'name' on null làm sập danh sách sản phẩm.

[Quan sát 5: CartController@checkCoupon lấy trực tiếp $data['code_input']]
   ↳ Không qua $request->validate(['code_input' => 'required'])
   ↳ Khi request rỗng hoặc AJAX thiếu payload
   ↳ KẾT LUẬN: ErrorException Undefined array key "code_input" 500 error.

[Quan sát 6: Tất cả route Xóa đều dùng GET]
   ↳ GET là safe/idempotent method theo chuẩn HTTP RFC 7231
   ↳ Trình duyệt, web crawler hoặc tấn công CSRF thông qua thẻ <img src="..."> đều có thể vô tình hoặc cố ý xóa sạch dữ liệu sản phẩm, đơn hàng, người dùng
   ↳ KẾT LUẬN: Lỗ hổng bảo mật nghiêm trọng (Insecure Direct Object Reference + CSRF via GET).

[Quan sát 7: Kiểm tra tồn kho không lockForUpdate]
   ↳ Quá trình đặt hàng gồm: Đọc tồn kho -> So sánh -> Trừ tồn kho
   ↳ Nếu 2 request chạy đồng thời (race condition)
   ↳ Cả 2 cùng đọc thấy tồn kho còn 1 -> Cả 2 cùng trừ
   ↳ KẾT LUẬN: Tồn kho bị âm, sai lệch sổ sách kho hàng.
```

---

## III. BẢNG TỔNG HỢP VÀ PHÂN LOẠI LỖI CHI TIẾT

| Mã | Phân loại | Tệp tin & Dòng code | Tóm tắt lỗi | Mức độ rủi ro |
|---|---|---|---|---|
| **ISSUE-01** | Critical / Nghiêm trọng | `routes/web.php:46`<br>`app/Http/Controllers/CheckoutController.php` | Method `processOrder` không tồn tại trong Controller | Crash 500 (BadMethodCallException) |
| **ISSUE-02** | Critical / Nghiêm trọng | `resources/views/welcome.blade.php:43` | Gọi named route `route('register')` chưa từng được định nghĩa | Crash 500 (RouteNotFoundException) |
| **ISSUE-03** | Critical / Nghiêm trọng | `app/Http/Controllers/HomeController.php:29` | Query `Category::where('status', 1)` trên cột không tồn tại | Crash 500 (QueryException SQLSTATE 42S22) |
| **ISSUE-04** | Critical / Nghiêm trọng | `app/Http/Controllers/OrderController.php:86-104` | `storeFromCart` ghi vào các cột cũ không tồn tại và thiếu trường bắt buộc | Crash 500 (QueryException 1364) |
| **ISSUE-05** | Critical / Nghiêm trọng | `app/Http/Controllers/CartController.php:433-434` | Truy cập `$data['code_input']` không qua validate | Crash 500 (ErrorException Undefined key) |
| **ISSUE-06** | Critical / Nghiêm trọng | `admin/product/show_product.blade.php:67`<br>`client/home/product_category.blade.php:173`<br>`client/home/index_home.blade.php:273, 363` | Truy cập `$item->category->name` không có toán tử null-safe (`?->` hoặc `??`) | Crash 500 (Attempt to read property on null) |
| **ISSUE-07** | Functional / Nghiệp vụ | `routes/web.php:60, 116, 126, 136, 142, 150`<br>`public/admin/js/main.js:15` | Dùng HTTP GET cho toàn bộ hành vi xóa dữ liệu Admin | Lỗ hổng bảo mật CSRF & mất mát dữ liệu |
| **ISSUE-08** | Functional / Nghiệp vụ | `app/Http/Controllers/AdminController.php:105-113`<br>`resources/views/admin/auth/register_admin.blade.php` | Thiếu validate unique email, confirm password, thiếu hiển thị lỗi và mất input | Trải nghiệm người dùng hỏng, dữ liệu trùng lặp |
| **ISSUE-09** | Functional / Nghiệp vụ | `routes/web.php:53`<br>`app/Http/Controllers/OrderController.php:190-202` | Khách vãng lai bị chặn ở middleware `auth` khi đặt hàng mặc dù Controller có code hỗ trợ | Gãy luồng mua hàng vãng lai |
| **ISSUE-10** | Functional / Nghiệp vụ | `app/Http/Controllers/OrderController.php:121-137` | Xóa đơn hàng không có `DB::transaction()` | Rủi ro hỏng toàn vẹn dữ liệu |
| **ISSUE-11** | Functional / Nghiệp vụ | `app/Http/Controllers/OrderController.php:269-275` | Trừ tồn kho không có `lockForUpdate()` | Race condition, âm kho hàng |
| **ISSUE-12** | Functional / Nghiệp vụ | `app/Models/User.php:27` | Cột `role` nằm trong `$fillable` | Lỗ hổng leo thang đặc quyền Mass Assignment |
| **ISSUE-13** | UI/UX / Giao diện | `resources/views/client/home/index_home.blade.php:268` | Lỗi cú pháp link chi tiết sản phẩm: `href="    }}"` | Liên kết hỏng trên trang chủ |
| **ISSUE-14** | UI/UX / Giao diện | `resources/views/admin/product/show_product.blade.php:9` | Nút "Xóa tìm kiếm" trỏ link `url('/shop')` không tồn tại | Lỗi 404 Not Found |
| **ISSUE-15** | UI/UX / Giao diện | `resources/views/client/cart/show_cart.blade.php:10`<br>`public/client/js/cart.js:85` | Khách mua hàng bấm đăng nhập bị đưa vào trang Login Admin `/admin` | Nhầm lẫn luồng UI/UX giữa Khách và Quản trị |
| **ISSUE-16** | UI/UX / Giao diện | `routes/web.php:77`<br>`app/Http/Controllers/AdminController.php:21-24` | Dashboard admin render thẳng layout không có nội dung | Trang Dashboard trống rỗng |
| **ISSUE-17** | Improvements / Đề xuất | `app/Providers/AppServiceProvider.php:25-34` và các Controller | Query Category lặp lại nhiều lần trên cùng một request | Tối ưu hiệu năng Database & View Composer |
| **ISSUE-18** | Improvements / Đề xuất | Toàn bộ các Views và Controllers | Đường dẫn asset hardcode `asset('public/...')` và junction Windows | Vi phạm quy chuẩn web root của Laravel |

---

## IV. NGUYÊN NHÂN VÀ GIẢI PHÁP KHẮC PHỤC CHI TIẾT (COPY-PASTEABLE CODE FIXES)

### 1. [CRITICAL] Khắc phục Route `/thanh-toan` gọi method không tồn tại
- **Vị trí**: `routes/web.php` dòng 46
- **Nguyên nhân**: Route `Route::post('/thanh-toan', [CheckoutController::class, 'processOrder'])` trỏ tới `CheckoutController@processOrder` vốn không tồn tại. Luồng đặt hàng thực tế trong form `checkout_index.blade.php:100` sử dụng route `route('dathang')` trỏ tới `OrderController@placeOrder`.
- **Giải pháp**:
  Trong `routes/web.php`, xóa bỏ hoặc map lại route `/thanh-toan` sang `OrderController::class, 'placeOrder'`:
  ```php
  // Sửa trong routes/web.php dòng 46:
  Route::post('/thanh-toan', [OrderController::class, 'placeOrder'])->name('checkout.process');
  ```

---

### 2. [CRITICAL] Khắc phục gọi named route không tồn tại `route('register')`
- **Vị trí**: `resources/views/welcome.blade.php` dòng 43 và `routes/web.php` dòng 83
- **Nguyên nhân**: `routes/web.php` không có name cho route đăng ký, trong khi Blade template gọi `route('register')`.
- **Giải pháp**:
  Đặt tên cho route đăng ký trong `routes/web.php` dòng 83:
  ```php
  Route::get('/register-admin', [AdminController::class, 'register_admin'])->name('register');
  ```

---

### 3. [CRITICAL] Khắc phục Query cột không tồn tại `status` trong `show_category_home`
- **Vị trí**: `app/Http/Controllers/HomeController.php` dòng 29
- **Nguyên nhân**: Bảng `_category` không có cột `status`.
- **Giải pháp**:
  Sửa trong `app/Http/Controllers/HomeController.php`:
  ```php
  public function show_category_home()
  {
      $categories = Category::orderBy('name')->get();
      return view('layout.home_layout', compact('categories'));
  }
  ```

---

### 4. [CRITICAL] Khắc phục `OrderController@storeFromCart` ghi sai cấu trúc DB
- **Vị trí**: `app/Http/Controllers/OrderController.php` dòng 86-105
- **Nguyên nhân**: Đoạn code legacy cố tình insert các trường không tồn tại trong bảng `orders` và `order_items` (`unitPrice`, `quantity`, `totalPrice`, `UnitPrice`).
- **Giải pháp**:
  Đồng bộ hóa phương thức `storeFromCart` với schema hiện hành của bảng `orders` và `order_items`:
  ```php
  $user = Auth::user();
  $order = Order::create([
      'user_id'          => $user->id,
      'shipping_name'    => $user->name,
      'shipping_email'   => $user->email,
      'shipping_phone'   => $user->phoneNumber ?? '0000000000',
      'shipping_address' => 'Địa chỉ mặc định theo tài khoản',
      'payment_method'   => 'COD',
      'total_amount'     => $totalPrice,
      'discount_amount'  => 0,
      'shipping_fee'     => 0,
      'status'           => 'pending',
  ]);

  foreach ($items as $cartItem) {
      $product = $cartItem->product;
      if (!$product) continue;
      OderItem::create([
          'order_id'     => $order->id,
          'product_id'   => $product->id,
          'product_name' => $product->name,
          'quantity'     => (int) $cartItem->quantity,
          'price'        => (float) $product->price,
      ]);
  }
  ```

---

### 5. [CRITICAL] Khắc phục lỗi Crash `Undefined array key "code_input"` trong `checkCoupon`
- **Vị trí**: `app/Http/Controllers/CartController.php` dòng 431-435
- **Nguyên nhân**: Truy cập `$data['code_input']` trực tiếp mà không validate.
- **Giải pháp**:
  Cập nhật `checkCoupon` trong `app/Http/Controllers/CartController.php`:
  ```php
  public function checkCoupon(Request $request)
  {
      $request->validate([
          'code_input' => 'required|string|max:50',
      ], [
          'code_input.required' => 'Vui lòng nhập mã giảm giá.',
      ]);

      $coupon = Coupon::where('code', trim($request->input('code_input')))->first();

      if (!$coupon) {
          return redirect()->back()->with('error', 'Mã giảm giá sai hoặc không tồn tại.');
      }
      if ($coupon->expiry_date && Carbon::now()->gt(Carbon::parse($coupon->expiry_date))) {
          return redirect()->back()->with('error', 'Mã giảm giá đã hết hạn.');
      }
      if ($coupon->quantity <= 0) {
          return redirect()->back()->with('error', 'Mã giảm giá đã hết số lượng.');
      }

      Session::put('coupon', [
          'id'    => $coupon->id,
          'code'  => $coupon->code,
          'type'  => $coupon->type,
          'value' => $coupon->value,
      ]);

      return redirect()->back()->with('success', 'Áp dụng mã thành công!');
  }
  ```

---

### 6. [CRITICAL] Khắc phục Crash Null Pointer `$item->category->name` trong Blade
- **Vị trí**:
  - `resources/views/admin/product/show_product.blade.php` dòng 67
  - `resources/views/client/home/product_category.blade.php` dòng 173
  - `resources/views/client/home/index_home.blade.php` dòng 273, 363
- **Giải pháp**: Sử dụng toán tử null-safe `?->` hoặc null coalescing `??`:
  ```blade
  {{-- Trong show_product.blade.php: --}}
  <td>{{ $item->category?->name ?? 'Không phân loại' }}</td>

  {{-- Trong product_category.blade.php: --}}
  <a href="#" class="d-block mb-2">{{ $item->category?->name ?? 'Sản phẩm' }}</a>

  {{-- Trong index_home.blade.php: --}}
  <a href="{{ route('product.detail', $product->id) }}" class="d-block mb-2">
      {{ $product->category?->name ?? 'Sản phẩm' }}
  </a>
  ```

---

### 7. [FUNCTIONAL] Chuyển đổi các Route Xóa dữ liệu từ GET sang DELETE/POST có CSRF Protection
- **Vị trí**: `routes/web.php` và `public/admin/js/main.js`
- **Nguyên nhân**: Dùng GET cho DELETE vi phạm RESTful và tạo lỗ hổng CSRF.
- **Giải pháp**:
  - Trong `routes/web.php`:
    ```php
    Route::delete('/delete-brand/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');
    Route::delete('/delete-product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::delete('/delete-category/{id}', [CategoryController::class, 'destroy'])->name('delete-category');
    Route::delete('/delete-coupon/{id}', [CouponController::class, 'destroy'])->name('coupon.delete');
    Route::delete('/admin/orders/delete/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
    Route::delete('/admin/users/delete/{id}', [AdminController::class, 'destroy_user'])->name('admin.users.destroy');
    ```
  - Trong `public/admin/js/main.js` hàm `DeleteData(url)`:
    ```javascript
    function DeleteData(url) {
        Swal.fire({
            title: "Xác nhận",
            text: "Bạn có chắc muốn xoá dữ liệu này?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Hủy",
            reverseButtons: true,
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    method: "DELETE",
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                .done(function (res) {
                    if (res) {
                        window.location.reload();
                    } else {
                        toastr.error("Đã có lỗi khi xoá dữ liệu", "Lỗi");
                    }
                })
                .fail(function () {
                    toastr.error("Không thể kết nối máy chủ", "Lỗi");
                });
            }
        });
    }
    ```

---

### 8. [FUNCTIONAL] Hoàn thiện Form Đăng ký: Unique Email, Password Confirmation và Error Feedback
- **Vị trí**:
  - `app/Http/Controllers/AdminController.php` dòng 102-118
  - `resources/views/admin/auth/register_admin.blade.php` dòng 25-60
- **Giải pháp**:
  - Trong `AdminController.php`:
    ```php
    public function submit_register(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'phoneNumber'           => ['required', 'regex:/^(0|\+84)(3|5|7|8|9)\d{8}$/'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'email.unique'          => 'Email này đã được sử dụng.',
            'password.confirmed'    => 'Xác nhận mật khẩu không khớp.',
            'phoneNumber.regex'     => 'Số điện thoại không đúng định dạng Việt Nam.',
        ]);

        User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phoneNumber' => $validated['phoneNumber'],
            'password'    => bcrypt($validated['password']),
            'role'        => 'user',
            'IsActive'    => 1,
        ]);

        return redirect()->route('login')->with('success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.');
    }
    ```
  - Trong `resources/views/admin/auth/register_admin.blade.php`:
    Đổi tên input `name="check-password"` thành `name="password_confirmation"`, thêm hiển thị lỗi `@if($errors->any())` và giữ lại giá trị cũ `value="{{ old('name') }}"`, `value="{{ old('email') }}"`, `value="{{ old('phoneNumber') }}"`.

---

### 9. [FUNCTIONAL] Bọc `OrderController@destroy` trong Database Transaction
- **Vị trí**: `app/Http/Controllers/OrderController.php` dòng 121-144
- **Giải pháp**:
  ```php
  public function destroy($id)
  {
      DB::beginTransaction();
      try {
          $order = Order::findOrFail($id);
          $order->orderItems()->delete();
          $order->delete();
          DB::commit();
          return true;
      } catch (\Throwable $e) {
          DB::rollBack();
          Log::error("Lỗi xóa đơn hàng: " . $e->getMessage());
          return false;
      }
  }
  ```

---

### 10. [FUNCTIONAL] Khắc phục Race Condition Tồn kho bằng `lockForUpdate()`
- **Vị trí**: `app/Http/Controllers/OrderController.php` dòng 269-275
- **Giải pháp**:
  ```php
  // Thay thế Product::find(...) bằng query có khóa dòng
  $prod = Product::where('id', $item['product_id'])->lockForUpdate()->first();
  if ($prod) {
      if ($prod->stockQuantity < $item['quantity']) {
          throw new \Exception('Sản phẩm "' . $prod->name . '" không đủ số lượng tồn kho (còn: ' . $prod->stockQuantity . ').');
      }
      $prod->decrement('stockQuantity', $item['quantity']);
  }
  ```

---

### 11. [SECURITY] Loại bỏ `role` khỏi `$fillable` trong Model `User`
- **Vị trí**: `app/Models/User.php` dòng 21-28
- **Nguyên nhân**: Cho phép mass assignment gán quyền `role`.
- **Giải pháp**:
  ```php
  protected $fillable = [
      'name',
      'email',
      'phoneNumber',
      'password',
      'IsActive',
      // 'role', <-- Loại bỏ role khỏi fillable để tránh leo thang đặc quyền
  ];
  ```

---

### 12. [UI/UX] Sửa lỗi cú pháp broken link tại `index_home.blade.php` dòng 268
- **Vị trí**: `resources/views/client/home/index_home.blade.php` dòng 268
- **Code hiện tại**:
  ```blade
  <div class="product-details">
      <a href="    }}"><i class="fa fa-eye fa-1x"></i></a>
  </div>
  ```
- **Code sửa lại**:
  ```blade
  <div class="product-details">
      <a href="{{ route('product.detail', $product->id) }}"><i class="fa fa-eye fa-1x"></i></a>
  </div>
  ```

---

### 13. [UI/UX] Sửa link 404 `/shop` tại `show_product.blade.php` dòng 9
- **Vị trí**: `resources/views/admin/product/show_product.blade.php` dòng 9
- **Code hiện tại**:
  ```blade
  <a href="{{ url('/shop') }}" class="btn btn-sm btn-outline-secondary">Xóa tìm kiếm</a>
  ```
- **Code sửa lại**:
  ```blade
  <a href="{{ url('/show-product') }}" class="btn btn-sm btn-outline-secondary">Xóa tìm kiếm</a>
  ```

---

### 14. [UI/UX] Phân định Đăng nhập Khách và Đăng nhập Admin
- **Vị trí**: `resources/views/client/cart/show_cart.blade.php:10` và `public/client/js/cart.js:85`
- **Nguyên nhân**: Khách mua hàng bị redirect sang `/admin`.
- **Giải pháp**: Thay vì chuyển hướng về `/admin`, chuyển hướng về `/login` (hoặc view login client thân thiện nếu tách riêng auth client/admin).

---

## V. CAVEATS (GIỚI HẠN & GIẢ THIẾT)

1. **Email Service Configuration**: Tính năng quên mật khẩu (`PasswordResetController.php`) hiện chỉ ghi link vào log file (`storage/logs/laravel.log`) mà chưa gửi mail thực tế qua SMTP/Mailgun. Cần cấu hình mail server trước khi đưa vào sản xuất.
2. **Cấu trúc Thư mục Public**: Ứng dụng đang phụ thuộc vào symlink junction `public/public` trên Windows để phục vụ các asset hardcode dạng `asset('public/...')`. Nếu triển khai trên máy chủ Linux mà không có cấu hình DocumentRoot chuẩn hoặc symlink tương ứng, tài nguyên tĩnh sẽ bị lỗi 404.
3. **Mã giảm giá Khách vãng lai**: Luồng khách vãng lai đặt hàng có logic lưu session cart và coupon, tuy nhiên route `/dat-hang` hiện vẫn bị chặn bởi middleware `auth`. Đội phát triển cần thống nhất nghiệp vụ có cho phép mua hàng không cần đăng ký hay bắt buộc đăng ký tài khoản.

---

## VI. CONCLUSION (KẾT LUẬN ĐÁNH GIÁ)

Hệ thống Laravel hiện tại đã xây dựng được khung luồng nghiệp vụ cơ bản từ xem hàng, tìm kiếm, giỏ hàng, áp mã giảm giá đến quản lý đơn hàng. Tuy nhiên, tồn tại **6 lỗi Critical có khả năng gây sập trang ngay lập tức (Crash 500)** khi người dùng tương tác với các tính năng:
- Bấm vào thanh toán (`/thanh-toan`)
- Nhập mã giảm giá rỗng (`/check-coupon`)
- Xem danh mục hoặc trang sản phẩm có dữ liệu thiếu danh mục liên kết
- Render view `welcome.blade.php`
- Đặt hàng từ session cart qua `storeFromCart`

Đồng thời hệ thống đang tồn tại lỗ hổng bảo mật nghiêm trọng khi sử dụng **HTTP GET cho tất cả các thao tác xóa dữ liệu quản trị**, và nguy cơ leo thang đặc quyền do **`role` nằm trong `$fillable`**.

Tất cả các lỗi trên đã được định vị chính xác file/dòng code và cung cấp mã nguồn khắc phục hoàn chỉnh, có thể copy-paste áp dụng ngay.

---

## VII. VERIFICATION METHOD (PHƯƠNG PHÁP XÁC MINH ĐỘC LẬP)

Để kiểm chứng độc lập toàn bộ các quan sát và lỗi nêu trong báo cáo, thực hiện các bước sau:

1. **Chạy script kiểm tra route và tái hiện lỗi runtime**:
   ```bash
   php .agents/explorer_backend/verify_findings.php
   ```
   Kết quả kỳ vọng:
   - `route('register')`: Báo lỗi `RouteNotFoundException`.
   - `CheckoutController@processOrder`: Báo `DOES NOT EXIST`.
   - `HomeController@show_category_home`: Báo `SQLSTATE[42S22] Unknown column 'status'`.
   - `CartController@checkCoupon` không tham số: Báo `Undefined array key "code_input"`.

2. **Chạy script đối soát named routes trên toàn bộ Views**:
   ```bash
   php .agents/explorer_backend/check_routes.php
   ```
   Kết quả kỳ vọng:
   - Liệt kê toàn bộ các route name xuất hiện trong Blade templates và chỉ ra route nào bị thiếu trong `routes/web.php`.

3. **Chạy bộ kiểm thử tự động của dự án**:
   ```bash
   php artisan test
   ```
   (Bộ test hiện tại kiểm tra 16 ca kiểm thử trên SQLite in-memory).

4. **Kiểm tra trực quan trên trình duyệt**:
   - Truy cập `http://localhost:8000/show-category-home` -> Kiểm tra lỗi cột `status`.
   - Truy cập `http://localhost:8000/shop` -> Kiểm tra lỗi 404 (từ link "Xóa tìm kiếm").

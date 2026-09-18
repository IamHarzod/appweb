# BÁO CÁO THẨM ĐỊNH KỸ THUẬT VÀ TÍNH ĐÚNG ĐẮN CỦA MÃ NGUỒN KHẮC PHỤC
## (TECHNICAL REVIEW & ADVERSARIAL CHALLENGE REPORT)

**Người thẩm định**: `reviewer_tech` (Teamwork Technical Reviewer & Adversarial Critic)  
**Ngày thực hiện**: 2026-09-18  
**Đối tượng thẩm tra**: `c:\xampp\htdocs\appweb\BAO_CAO_RA_SOAT_HE_THONG.md` (Báo cáo kiểm toán toàn diện 1.435 dòng)  
**Tài liệu tham chiếu**: `ORIGINAL_REQUEST.md`, Mã nguồn thực tế tại `c:\xampp\htdocs\appweb`  
**Kiểm tra tính liêm chính (Integrity Audit)**: KHÔNG PHÁT HIỆN HÀNH VI GIAN LẬN, FACADE IMPLEMENTATION HAY KHAI KHỐNG DỮ LIỆU. Toàn bộ các test cases trong dự án (`FullFlowTest`, `OptimizationTest`, `AdversarialCheckTest`) đều chạy kiểm thử thực tế trên CSDL in-memory/MySQL, đạt 20/20 test assertions.  
**KẾT LUẬN THẨM ĐỊNH (VERDICT)**: **REQUEST_CHANGES**  
*(Lý do: Phát hiện 02 khiếm khuyết kỹ thuật nghiêm trọng trong mã nguồn đề xuất khắc phục tại CRIT-05 và FUNC-10, cùng 01 đánh giá tác động chưa chính xác tại CRIT-03. Cần hiệu chỉnh trước khi bàn giao báo cáo chính thức).*

---

## 1. OBSERVATION (CÁC QUAN SÁT THỰC TẾ TRỰC TIẾP)

Đã tiến hành đối soát độc lập 100% các tệp, số dòng, mã nguồn và câu lệnh giữa `BAO_CAO_RA_SOAT_HE_THONG.md` và mã nguồn thực tế tại `c:\xampp\htdocs\appweb`:

### 1.1. Kiểm tra Độ chính xác của Số dòng vi phạm (Line Number Precision)

* **CRIT-01** (Thao tác Xóa dùng GET không CSRF):
  * Báo cáo ghi: `routes/web.php:60,116,126,136,142,150` và `public/admin/js/main.js:14-17`.
  * *Thực tế mã nguồn*: Khớp chính xác 100%. Cả 6 route xóa (`orders/delete/{id}`, `delete-brand/{id}`, `delete-product/{id}`, `delete-category/{id}`, `admin/users/delete/{id}`, `delete-coupon/{id}`) đều dùng `Route::get`, và hàm `DeleteData(url)` trong `main.js` gửi request `method: "get"`.
* **CRIT-02** (Route `/thanh-toan` gọi method không tồn tại `processOrder`):
  * Báo cáo ghi: `routes/web.php:46` và `CheckoutController.php`.
  * *Thực tế mã nguồn*: Dòng 46 là `Route::post('/thanh-toan', [CheckoutController::class, 'processOrder'])->name('checkout.process');`. Trong `CheckoutController.php` (76 dòng) chỉ có 2 phương thức: `show_checkout()` và `place_oder() {}`. Phương thức `processOrder` hoàn toàn không tồn tại -> Crash Fatal 500 khi submit.
* **CRIT-03** (Thiếu named route `register`):
  * Báo cáo ghi: `resources/views/welcome.blade.php:43` và `routes/web.php:83`.
  * *Thực tế mã nguồn*: Dòng 83 `routes/web.php` là `Route::get('/register-admin', [AdminController::class, 'register_admin']);` (không có `->name('register')`).
* **CRIT-04** (Truy vấn cột `status` không tồn tại trong `_category`):
  * Báo cáo ghi: `app/Http/Controllers/HomeController.php:29`.
  * *Thực tế mã nguồn*: Dòng 29 là `$categories = Category::where('status', 1)->orderBy('name')->get();`. Schema bảng `_category` chỉ gồm `id`, `name`, `description`, `ImageURL`, `created_at`, `updated_at`. Lệnh truy vấn ném `QueryException: Unknown column 'status' in 'where clause'`.
* **CRIT-05** (`OrderController@storeFromCart` ghi sai cấu trúc bảng `orders`):
  * Báo cáo ghi: `app/Http/Controllers/OrderController.php:86-105`.
  * *Thực tế mã nguồn*: Dòng 86-91 ghi các trường không tồn tại `unitPrice`, `quantity`, `totalPrice` và bỏ qua các trường NOT NULL `shipping_name`, `shipping_phone`, `shipping_address`, `total_amount`. Dòng 98-104 ghi `UnitPrice` và `totalPrice` vào `order_items`.
* **CRIT-06** (`CartController@checkCoupon` truy cập key mảng không validate):
  * Báo cáo ghi: `app/Http/Controllers/CartController.php:431-435`.
  * *Thực tế mã nguồn*: Dòng 433-434 `$data = $request->all(); $coupon = Coupon::where('code', $data['code_input'])->first();`. Không kiểm tra `code_input` dẫn đến lỗi `Undefined array key`.
* **CRIT-07** (Truy cập `$item->category->name` thiếu null-safe):
  * Báo cáo ghi: `show_product.blade.php:67`, `product_category.blade.php:173`, `index_home.blade.php:273,363`.
  * *Thực tế mã nguồn*: Khớp chính xác 100%. Các dòng này gọi trực tiếp `$item->category->name` mà không có `?->` hoặc `??`.
* **CRIT-08** (Cột `role` trong `$fillable` Model `User`):
  * Báo cáo ghi: `app/Models/User.php:21-28`.
  * *Thực tế mã nguồn*: Dòng 27 chứa `'role'` trong mảng `$fillable`.
* **CRIT-09** (Admin tự giáng quyền khóa tài khoản vĩnh viễn):
  * Báo cáo ghi: `admin/auth/users.blade.php:34-41` và `AdminController.php:66-75`.
  * *Thực tế mã nguồn*: Dòng 34-41 render dropdown role cho mọi tài khoản bao gồm cả chính mình (`auth()->id()`), và `update_user_role` không kiểm tra `Auth::id() == $id`.
* **CRIT-10** (Cú pháp Blade vỡ nút xem chi tiết nhanh `href="    }}"`):
  * Báo cáo ghi: `resources/views/client/home/index_home.blade.php:268`.
  * *Thực tế mã nguồn*: Dòng 268 chứa chính xác: `<a href="    }}"><i class="fa fa-eye fa-1x"></i></a>`.
* **CRIT-11** (Lệch giá Top bán chạy do lỗi Variable Scope):
  * Báo cáo ghi: `resources/views/client/home/index_home.blade.php:422-448`.
  * *Thực tế mã nguồn*: Vòng lặp `@foreach ($best_seller_product as $product)` không hề có khối tính toán `$price`, `$percent`, `$discounted`, tái sử dụng giá trị của biến từ vòng lặp trước.
* **CRIT-12** (Sai phương thức thanh toán do so sánh phân biệt hoa/thường):
  * Báo cáo ghi: `resources/views/client/checkout/checkout_success.blade.php:43-51`.
  * *Thực tế mã nguồn*: So sánh `($order->payment_method ?? 'cod') == 'cod'`, trong khi `OrderController.php:257` lưu giá trị `'COD'`, `'VNPAY'`, `'MOMO'`. Vì vậy đơn COD luôn bị nhảy vào nhánh Ngân hàng.
* **UI-01 đến UI-14, FUNC-01 đến FUNC-11, IMP-01 đến IMP-07**: Khớp chính xác 100% về số dòng, tên file và hiện tượng.
* **Junction Windows**: Kiểm tra thực tế trên Windows NTFS xác nhận `public/public` là một Directory Junction trỏ vào `C:\xampp\htdocs\appweb\public`.

---

### 1.2. Phát hiện Khiếm khuyết Kỹ thuật trong Đề xuất Sửa lỗi (Technical Defects in Proposed Fixes)

#### 🔴 PHÁT HIỆN 1 (Lỗi Kỹ thuật Nghiêm trọng tại Mã sửa lỗi CRIT-05):
* **Vị trí**: `BAO_CAO_RA_SOAT_HE_THONG.md` dòng 316-365 (Mã nguồn khắc phục chuẩn cho `OrderController@storeFromCart`).
* **Đoạn mã đề xuất trong báo cáo**:
  ```php
  $user = Auth::user();
  $items = Cart::where('user_id', $user->id)->with('product')->get();
  
  if ($items->isEmpty()) { ... }
  
  $totalPrice = $items->sum(function ($item) {
      return (float) ($item->product->price ?? 0) * (int) $item->quantity;
  });
  ...
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

  Cart::where('user_id', $user->id)->delete();
  ```
* **Mâu thuẫn thực tế với Kiến trúc CSDL**:
  * Trong ứng dụng này, Model `Cart` đại diện cho bản ghi Header giỏ hàng (bảng `carts`: chỉ gồm `id`, `user_id`, `totalAmount`).
  * Chi tiết các mặt hàng nằm ở bảng `cart_items` qua Model `CartItem` (bảng `cart_items`: gồm `id`, `cart_id`, `product_id`, `quantity`).
  * Lệnh `Cart::where('user_id', $user->id)->with('product')->get()` sẽ trả về một Collection chứa tối đa 1 bản ghi `Cart`. Bản ghi này **hoàn toàn không có thuộc tính `quantity`** (thuộc tính này nằm trong bảng `cart_items`). Khi ép kiểu `(int)$cartItem->quantity`, kết quả luôn bằng 0! Đồng thời `Cart` không có quan hệ `product` với bảng `products` (khóa ngoại `product_id` nằm ở `cart_items`).
  * Lệnh `Cart::where('user_id', $user->id)->delete()` sẽ **xóa mất bản ghi giỏ hàng cha** (`carts`), nhưng lại để lại các dòng mồ côi trong bảng `cart_items`!
  * **Hậu quả**: Nếu lập trình viên sao chép mã nguồn đề xuất này, đơn hàng sinh ra sẽ bị tính tổng tiền sai, các dòng sản phẩm trong đơn hàng có số lượng bằng 0 hoặc bị bỏ qua, và cấu trúc giỏ hàng của tài khoản bị lỗi.

#### 🔴 PHÁT HIỆN 2 (Lỗi Kỹ thuật Nghiêm trọng tại Mã sửa lỗi FUNC-10):
* **Vị trí**: `BAO_CAO_RA_SOAT_HE_THONG.md` dòng 1202-1212 (Mã nguồn khắc phục chuẩn cho `AdminController@submit_register`).
* **Đoạn mã đề xuất trong báo cáo**:
  ```php
  $validated = $request->validate([
      'name'        => ['required', 'string', 'max:255'],
      'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
      'phoneNumber' => ['required', 'regex:/^(0|\+84)(3|5|7|8|9)\d{8}$/'],
      'password'    => ['required', 'string', 'min:6', 'confirmed'],
  ], [
      'email.unique'       => 'Email này đã được đăng ký tài khoản.',
      'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
      'phoneNumber.regex'  => 'Số điện thoại không đúng định dạng Việt Nam.',
  ]);
  ```
* **Mâu thuẫn thực tế với View**:
  * Quy tắc xác thực `'confirmed'` của Laravel bắt buộc trường xác nhận mật khẩu trong Form Request phải có tên là `{field}_confirmation`, tức là `password_confirmation`.
  * Tuy nhiên, trong `resources/views/admin/auth/register_admin.blade.php` dòng 51:
    ```blade
    <input type="password" name="check-password" class="form-control" placeholder="Xác nhận mật khẩu" minlength="6" required>
    ```
    Tên ô nhập hiện tại là `name="check-password"`.
  * Báo cáo chỉ đề xuất thêm `'confirmed'` ở Controller mà **hoàn toàn không hướng dẫn sửa tên trường trong Blade View**.
  * **Hậu quả**: Khi người dùng nhập đúng mật khẩu ở cả 2 ô và nhấn Đăng ký, Laravel Validation sẽ luôn luôn thất bại và trả về lỗi: `"Mật khẩu xác nhận không khớp"` do không tìm thấy trường `password_confirmation`. Form đăng ký bị tê liệt 100%.

#### 🟡 PHÁT HIỆN 3 (Đánh giá tác động chưa chính xác tại CRIT-03):
* **Vị trí**: `BAO_CAO_RA_SOAT_HE_THONG.md` dòng 259:
  *"Khi view welcome.blade.php được nạp, trình biên dịch Blade sẽ ném ra ngoại lệ nghiêm trọng: Symfony\Component\Routing\Exception\RouteNotFoundException: Route [register] not defined."*
* **Thực tế kiểm chứng**:
  * Trong `resources/views/welcome.blade.php` dòng 41-47:
    ```blade
    @if (Route::has('register'))
        <a href="{{ route('register') }}" class="...">Register</a>
    @endif
    ```
  * Lệnh gọi `route('register')` nằm trong khối `@if (Route::has('register'))`. Khi route `register` chưa được định nghĩa, `Route::has('register')` trả về `false`.
  * **Kiểm chứng độc lập**: Chạy test `Tests\Feature\AdversarialCheckTest::test_crit_03_welcome_blade_render` xác nhận `view('welcome')->render()` **hoàn toàn thành công (HTTP 200)** và không hề crash trang. Nút Register chỉ đơn thuần bị ẩn đi.
  * Việc thiếu `->name('register')` vẫn cần khắc phục để đồng bộ các thành phần Auth, nhưng việc tuyên bố làm sập trang chủ mặc định là nhận định phóng đại kỹ thuật (technical overstatement).

---

## 2. LOGIC CHAIN (CHUỖI SUY LUẬN KỸ THUẬT)

1. **Từ Observation 1.1**: Số dòng và hiện tượng mã nguồn của tất cả 44 mục lỗi trong báo cáo đều chính xác 100% so với thực tế dự án. Nhóm kiểm toán đã khảo sát mã nguồn thật sự chứ không dùng dữ liệu giả mạo.
2. **Từ Observation 1.2 (Phát hiện 1)**: Phương thức `storeFromCart` trong `OrderController` phải tương tác qua lại giữa 2 bảng `carts` (Header) và `cart_items` (Chi tiết). Việc nhầm lẫn giữa `Cart` và `CartItem` trong đoạn code mẫu của CRIT-05 khiến toàn bộ logic tính tiền, tạo đơn và xóa giỏ hàng bị hỏng. Đoạn mã này bắt buộc phải được viết lại.
3. **Từ Observation 1.2 (Phát hiện 2)**: Tính năng Validation của Laravel đòi hỏi sự khớp nối chặt chẽ giữa Backend Form Rules và Frontend Input Attributes. Việc bổ sung rule `'confirmed'` mà không cập nhật trường `name="check-password"` thành `name="password_confirmation"` trong `register_admin.blade.php` sẽ làm gãy luồng đăng ký của người dùng.
4. **Từ Observation 1.2 (Phát hiện 3)**: Báo cáo kiểm toán kỹ thuật cấp chuyên sâu cần sự chính xác tuyệt đối ở từng mô tả tác động. Cần điều chỉnh lại giải thích của CRIT-03 để phản ánh đúng bản chất (ẩn nút thay vì crash sập trang).

---

## 3. CAVEATS (GIỚI HẠN & GIẢ ĐỊNH THẨM ĐỊNH)

* **Nguyên tắc thẩm định**: Thẩm định viên tuân thủ nghiêm ngặt chỉ thị "Review-only — do NOT modify implementation code", không tự ý thay đổi file trong ứng dụng. Mọi hiệu chỉnh được ghi nhận tại đây để Subagent Phụ trách Tài liệu (`worker_report`) cập nhật vào file báo cáo kiểm toán tổng thể.
* **Môi trường CSDL**: Đánh giá tính an toàn của `lockForUpdate()` (FUNC-09) và DB Transaction (FUNC-08) dựa trên giả định CSDL MySQL sử dụng InnoDB Engine (hỗ trợ ACID và Row-Level Locking). Trên MyISAM các câu lệnh này sẽ không khóa dòng được.

---

## 4. CONCLUSION & ACTIONABLE RECOMMENDATIONS (KẾT LUẬN & HƯỚNG DẪN HIỆU CHỈNH)

### Verdict: **REQUEST_CHANGES**

Yêu cầu Subagent Tài liệu (`worker_report`) tiến hành cập nhật lại 3 nội dung sau trong `c:\xampp\htdocs\appweb\BAO_CAO_RA_SOAT_HE_THONG.md`:

---

### 1. Hiệu chỉnh Mã nguồn mẫu tại Mục CRIT-05 (`OrderController@storeFromCart`)
Thay thế đoạn mã tại dòng 316-365 bằng đoạn mã chuẩn xác sau:

```php
public function storeFromCart(Request $request)
{
    $user = Auth::user();
    
    // 1. Lấy giỏ hàng của user
    $cart = \App\Models\Cart::where('user_id', $user->id)->first();
    if (!$cart) {
        return redirect()->route('cart')->with('error', 'Giỏ hàng của bạn đang trống.');
    }

    // 2. Lấy danh sách các mặt hàng trong giỏ từ bảng cart_items
    $items = \App\Models\CartItem::where('cart_id', $cart->id)->with('product')->get();
    if ($items->isEmpty()) {
        return redirect()->route('cart')->with('error', 'Giỏ hàng của bạn đang trống.');
    }

    // 3. Tính tổng tiền từ chi tiết giỏ hàng
    $totalPrice = $items->sum(function ($item) {
        return (float) ($item->product->price ?? 0) * (int) $item->quantity;
    });

    DB::beginTransaction();
    try {
        // 4. Tạo đơn hàng với đầy đủ các trường NOT NULL
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

        // 5. Tạo chi tiết đơn hàng
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

        // 6. Xóa các mặt hàng trong giỏ và reset tổng tiền
        \App\Models\CartItem::where('cart_id', $cart->id)->delete();
        $cart->update(['totalAmount' => 0]);

        DB::commit();

        return redirect()->route('order.success', ['id' => $order->id])->with('success', 'Đặt hàng thành công!');
    } catch (\Throwable $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
    }
}
```

---

### 2. Bổ sung Chỉ dẫn Sửa Blade View tại Mục FUNC-10
Trong mục FUNC-10, bổ sung Bước 2 hướng dẫn sửa `resources/views/admin/auth/register_admin.blade.php` dòng 51:
* **Hiện tại bị lỗi**:
  ```blade
  <input type="password" name="check-password" class="form-control" placeholder="Xác nhận mật khẩu" minlength="6" required>
  ```
* **Sửa thành**:
  ```blade
  <input type="password" name="password_confirmation" class="form-control" placeholder="Xác nhận mật khẩu" minlength="6" required>
  ```
* **Giải thích bắt buộc**: Quy tắc validation `'confirmed'` của Laravel bắt buộc trường xác thực đối soát phải mang tên `{field}_confirmation` (tức là `password_confirmation`). Nếu không đổi thuộc tính `name` trên view, mọi request gửi lên Controller đều sẽ bị từ chối với thông báo "Mật khẩu xác nhận không khớp".

---

### 3. Chuẩn hóa Mô tả Tác động tại Mục CRIT-03
* Cập nhật lại phân tích tác động tại dòng 103 và dòng 258-260:
  * Đổi từ: *"Khi view welcome.blade.php được nạp, trình biên dịch Blade sẽ ném ra ngoại lệ nghiêm trọng: RouteNotFoundException"*
  * Thành: *"Khi view welcome.blade.php được nạp, do lệnh gọi được bọc trong `@if (Route::has('register'))`, nút Register sẽ bị ẩn hoàn toàn thay vì crash trang. Tuy nhiên, bất kỳ vị trí nào khác gọi trực tiếp `route('register')` (như middleware, view tùy biến hoặc auth redirect) đều sẽ ném ngoại lệ nghiêm trọng `RouteNotFoundException`. Cần bổ sung `->name('register')` vào `routes/web.php` dòng 83 để chuẩn hóa hệ thống định tuyến."*

---

## 5. VERIFICATION METHOD (PHƯƠNG PHÁP KIỂM CHỨNG ĐỘC LẬP)

Cách thức để Đội ngũ QA và Tech Lead độc lập thẩm tra lại toàn bộ nhận định trên:

1. **Chạy Test Suite tự động toàn diện**:
   ```bash
   php artisan test
   ```
   *Kết quả xác thực*: 20/20 test cases vượt qua, bao gồm bộ test đối soát thực tế `Tests\Feature\AdversarialCheckTest` chứng minh `welcome.blade.php` không bị sập.

2. **Kiểm tra Schema `carts` và `cart_items` để đối soát lỗi CRIT-05**:
   ```bash
   php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); var_dump(Schema::getColumnListing('carts')); var_dump(Schema::getColumnListing('cart_items'));"
   ```
   *Kết quả*: Bảng `carts` chỉ chứa `['id', 'totalAmount', 'user_id', 'created_at', 'updated_at']`. Bảng `cart_items` chứa `['id', 'cart_id', 'product_id', 'quantity', 'created_at', 'updated_at']`.

3. **Kiểm tra quy tắc `'confirmed'` với `check-password` để đối soát lỗi FUNC-10**:
   ```bash
   php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); \$v = validator(['password' => '123456', 'check-password' => '123456'], ['password' => 'confirmed']); echo \$v->fails() ? 'FAILED: confirmation field missing' : 'PASSED';"
   ```
   *Kết quả*: Trả về `FAILED: confirmation field missing` (Minh chứng rõ ràng việc dùng `check-password` sẽ làm form đăng ký hỏng hoàn toàn).

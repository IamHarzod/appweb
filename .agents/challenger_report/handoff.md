# BÁO CÁO THẨM TRA ĐỐI KHÁNG VÀ KIỂM CHỨNG THỰC NGHIỆM
## (ADVERSARIAL CHALLENGE & EMPIRICAL VERIFICATION REPORT)

**Tác nhân thực hiện**: `challenger_report` (Empirical Challenger / Critic Specialist)  
**Đối tượng thẩm tra**: `c:\xampp\htdocs\appweb\BAO_CAO_RA_SOAT_HE_THONG.md` (Báo cáo kiểm toán hệ thống toàn diện)  
**Thời gian thực hiện**: 2026-09-18  
**Trạng thái thẩm tra**: Hoàn tất kịch bản kiểm thử thực nghiệm (Empirical Verification Completed)  
**Phán quyết tổng thể (Verdict)**: **REQUEST_CHANGES** (Yêu cầu hiệu chỉnh cục bộ 2 điểm và bổ sung 1 lỗ hổng bảo mật nghiêm trọng bị bỏ sót)

---

## 1. OBSERVATION (QUAN SÁT THỰC TẾ & DỮ LIỆU THỰC NGHIỆM)

### 1.1. Kết quả kiểm tra độ chính xác của trích dẫn (File Paths & Line Numbers)
Toàn bộ 44 mục lỗi và kiến nghị trong báo cáo `BAO_CAO_RA_SOAT_HE_THONG.md` đã được đối soát trực tiếp trên cây thư mục mã nguồn `c:\xampp\htdocs\appweb`:

1. **CRIT-01 (HTTP GET Deletions & Missing CSRF)**:
   - `routes/web.php:60`: `Route::get('/admin/orders/delete/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');` (Chính xác).
   - `routes/web.php:116`: `Route::get('/delete-brand/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');` (Chính xác).
   - `routes/web.php:126`: `Route::get('/delete-product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');` (Chính xác).
   - `routes/web.php:136`: `Route::get('/delete-category/{id}', [CategoryController::class, 'destroy'])->name('delete-category');` (Chính xác).
   - `routes/web.php:142`: `Route::get('/admin/users/delete/{id}', [AdminController::class, 'destroy_user'])->name('admin.users.destroy');` (Chính xác).
   - `routes/web.php:150`: `Route::get('/delete-coupon/{id}', [CouponController::class, 'destroy'])->name('coupon.delete');` (Chính xác).
   - `public/admin/js/main.js:14-17`: `method: "get"` (Chính xác).

2. **CRIT-02 (`/thanh-toan` gọi `CheckoutController@processOrder`)**:
   - `routes/web.php:46`: `Route::post('/thanh-toan', [CheckoutController::class, 'processOrder'])->name('checkout.process');` (Chính xác).
   - `app/Http/Controllers/CheckoutController.php`: Chỉ có 2 hàm `show_checkout` và stub `place_oder()`. Phương thức `processOrder` hoàn toàn không tồn tại.
   - Thử nghiệm gửi `POST /thanh-toan`: ném ngoại lệ HTTP 500 `BadMethodCallException: Method App\Http\Controllers\CheckoutController::processOrder does not exist`.

3. **CRIT-03 (View gọi `route('register')`)**:
   - `resources/views/welcome.blade.php:41-47`:
     ```blade
     @if (Route::has('register'))
         <a
             href="{{ route('register') }}"
             class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
             Register
         </a>
     @endif
     ```
   - Thử nghiệm thực thi render Blade: `view('welcome')->render()`. Kết quả: **THÀNH CÔNG, KHÔNG NÉM BẤT KỲ NGOẠI LỆ NÀO** (`RouteNotFoundException` không bao giờ xảy ra do được bao bọc bởi `@if (Route::has('register'))`).
   - Tuyến đường trang chủ trong `routes/web.php:21` trả về `view("client.home.index_home")`, không hề gọi `welcome.blade.php`.

4. **CRIT-04 (`HomeController.php:29` query cột `status` trong `_category`)**:
   - `app/Http/Controllers/HomeController.php:29`: `Category::where('status', 1)->orderBy('name')->get();` (Chính xác).
   - `database/migrations/2025_09_27_125910_create__category_table.php`: Bảng `_category` chỉ có các cột `id`, `created_at`, `updated_at`, `name`, `description`, `ImageURL`. Không có cột `status`.
   - Thử nghiệm gửi `GET /show-category-home`: ném ngoại lệ HTTP 500 `Illuminate\Database\QueryException: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'status' in 'where clause'`.

5. **CRIT-05 (`OrderController@storeFromCart` ghi sai cấu trúc bảng `orders`)**:
   - `app/Http/Controllers/OrderController.php:86-91`: Ghi các trường `unitPrice`, `quantity`, `totalPrice` vào Model `Order`.
   - `database/migrations/2025_11_24_150504_create_orders_table.php`: Bảng `orders` chỉ có `total_amount`, không có `unitPrice`, `quantity`, `totalPrice`. Đồng thời các trường `shipping_name`, `shipping_email`, `shipping_phone`, `shipping_address` là bắt buộc (`NOT NULL`) nhưng không được truyền giá trị.

6. **CRIT-06 (`CartController@checkCoupon` crash do thiếu key `code_input`)**:
   - `app/Http/Controllers/CartController.php:433-434`: `$data = $request->all(); $coupon = Coupon::where('code', $data['code_input'])->first();` (Chính xác).
   - Thử nghiệm gửi `POST /check-coupon` với payload rỗng: ném ngoại lệ HTTP 500 `ErrorException: Undefined array key "code_input"`.

7. **CRIT-07 (Null-safety trên quan hệ category)**:
   - `show_product.blade.php:67`: `<td>{{ $item->category->name }}</td>` (Chính xác).
   - `product_category.blade.php:173`: `<a href="#" class="d-block mb-2">{{ $item->category->name }}</a>` (Chính xác).
   - `index_home.blade.php:273,363`: Gọi `$product->category->name` không có null-safe operator (Chính xác).

8. **CRIT-08 (Mass Assignment qua `role` trong `User.php`)**:
   - `app/Models/User.php:27`: `'role'` nằm trong `$fillable` (Chính xác).
   - Quan sát logic đăng ký hiện tại trong `AdminController.php:115-116`:
     ```php
     $arrayData = ['name' => $request->name, 'email' => $request->email, 'phoneNumber' => $request->phoneNumber, 'password' => bcrypt($request->password)];
     User::create($arrayData);
     ```
     Hàm `submit_register` khởi tạo mảng tường minh 4 trường, không truyền trực tiếp `$request->all()`. Do đó, kẻ tấn công chèn `role=admin` vào payload hiện tại **chưa thể** leo thang quyền ngay lập tức trên endpoint đăng ký này (đây là rủi ro kiến trúc / latent vulnerability chứ không phải active exploit trên endpoint hiện hữu).

9. **CRIT-09 (Admin tự đổi role của chính mình)**:
   - `admin/auth/users.blade.php:34-41` và `AdminController.php:66-75`: Không hề kiểm tra `Auth::id() == $id`, cho phép admin tự giáng quyền xuống `user`, dẫn tới bị khóa ngoài khu vực quản trị (Chính xác).

10. **CRIT-10 (Cú pháp Blade `href="    }}"` trên Trang chủ)**:
    - `resources/views/client/home/index_home.blade.php:268`: `<a href="    }}"><i class="fa fa-eye fa-1x"></i></a>` (Chính xác 100%).

11. **CRIT-11 (Sai lệch giá Top bán chạy do Variable Scope)**:
    - `resources/views/client/home/index_home.blade.php:422-448`: Vòng lặp `@foreach ($best_seller_product as $product)` không hề có khối `@php` tính lại `$price`, `$percent`, `$discounted`. Toàn bộ sản phẩm Top bán chạy đều hiển thị giá và % khuyến mãi của sản phẩm cuối cùng thuộc vòng lặp `$all_product` trước đó (Chính xác 100%).

12. **CRIT-12 (So sánh phân biệt hoa/thường payment method)**:
    - `resources/views/client/checkout/checkout_success.blade.php:44`: `@if (($order->payment_method ?? 'cod') == 'cod')`
    - `OrderController.php:257` & `checkout_index.blade.php:266`: Phương thức thanh toán được lưu trữ dạng chữ hoa `'COD'`, `'VNPAY'`, `'MOMO'`.
    - Phép so sánh `'COD' == 'cod'` trả về `false`, khiến mọi đơn hàng COD và VNPAY đều bị rơi vào nhánh `@else: Thanh toán qua Ngân hàng` (Chính xác 100%).

13. **Kiểm tra UI-01 đến UI-14, FUNC-01 đến FUNC-11, IMP-01 đến IMP-07**:
    - `home_layout.blade.php:84,122,170-206`: Thanh tìm kiếm, giỏ hàng, menu tài khoản đều bị gán `d-none d-lg-block`, trên mobile hoàn toàn không có cách nào tìm kiếm hoặc xem giỏ hàng (Chính xác).
    - `product_category.blade.php:243-260`: Tab `#tab-6` rỗng hoàn toàn (Chính xác).
    - `checkout_index.blade.php:177,241`: Hàng Tạm tính và Tổng cộng có 5 ô (`th` + 4 `td`) trong bảng có `thead` 4 cột (Chính xác).
    - `show_product.blade.php:9`: Link nút Xóa tìm kiếm trỏ vào `/shop` (route không tồn tại, trả về 404) (Chính xác).
    - `public/client/js/cart.js:85` & `show_cart.blade.php:10`: Điều hướng khách chưa đăng nhập vào `/admin` thay vì `/login` (Chính xác).
    - `AdminController.php:21-24`: `show_dasboard()` trả về `view("layout.admin_layout")`, không truyền view con vào `@yield('view-content')`, màn hình trắng tinh (Chính xác).
    - `admin_layout.blade.php:173`: Nạp `dashboard-1.js` trên mọi trang quản trị, gây lỗi `Uncaught TypeError` khi không tìm thấy canvas (Chính xác).
    - `admin_layout.blade.php`: Lẫn lộn cú pháp Bootstrap 5 (`btn-close`, `data-bs-dismiss`) trên template Bootstrap 4.3.1 (Chính xác).
    - `profile_layout.blade.php:41` có `</form>` mồ côi; `checkout_index.blade.php:389-391` có `</body></html>` thừa; `index_home.blade.php:317` có `</i></i>` (Chính xác).
    - `my_orders.blade.php:85`: Phân trang render Tailwind SVG khổng lồ (Chính xác).
    - `add_brand.blade.php:19`, `add_product.blade.php:160`: Nút thừa chữ "Button" (Chính xác).
    - `show_product.blade.php:70`: Lỗi chính tả tiếng Việt "Đang kinh doan" (Chính xác).
    - `public/client/js/main.js:162`: Gọi hàm easing `easeInOutExpo` không tồn tại (Chính xác).
    - `show_category.blade.php:4-20`: Flash messages đặt trước `@section('view-content')` bị Blade compiler loại bỏ hoàn toàn (Chính xác).
    - `add_coupon.blade.php:73`: Nút Đóng gọi `CloseModal('ModalCreateCoupon')` trong khi ID modal thực tế là `ModalEdit` (Chính xác).
    - `orders/index.blade.php:76-122`: Modal chi tiết và thẻ script đặt sau `@endsection` (Chính xác).
    - `OrderController.php:121-144`: Phương thức xóa đơn hàng không có Database Transaction (Chính xác).
    - `OrderController.php:269-275`: Trừ kho thiếu `lockForUpdate()` (Chính xác).
    - `AdminController.php:105-113`: Thiếu validate email unique và password confirmed (Chính xác).
    - `public/public`: Windows Junction link xác nhận tồn tại qua lệnh `Get-Item` (Chính xác).
    - `routes/web.php:97-107`: Route debug nhạy cảm `/test-password-reset/{email}` (Chính xác).
    - `login_client.blade.php`: Tệp rỗng 0 bytes (Chính xác).

---

### 1.2. Phát hiện lỗi sai và lỗ hổng bị bỏ sót (Discrepancies & Overlooked Flaws)

#### Phát hiện 1: CRIT-03 là False Positive (Sai lệch về khả năng gây lỗi Crash)
- **Trong báo cáo viết**:
  > *"Tác động: Khi view welcome.blade.php được nạp, trình biên dịch Blade sẽ ném ra ngoại lệ nghiêm trọng: Symfony\Component\Routing\Exception\RouteNotFoundException: Route [register] not defined."*
- **Thực tế kiểm chứng**:
  Trong `welcome.blade.php` dòng 41 có điều kiện kiểm tra an toàn:
  ```blade
  @if (Route::has('register'))
      <a href="{{ route('register') }}" ...>Register</a>
  @endif
  ```
  Khi helper `route('register')` chưa được đặt tên, `Route::has('register')` trả về `false`, khối code trên **hoàn toàn bị bỏ qua**, không có bất kỳ ngoại lệ nào được ném ra. Thử nghiệm PHPUnit đã kiểm chứng `view('welcome')->render()` chạy mượt mà và trả về mã HTML hoàn chỉnh. Ngoài ra, view này là file mẫu mặc định của Laravel và không hề được đăng ký vào bất kỳ route nào trong `routes/web.php`.
- **Đánh giá rủi ro**: Không thể xếp mục này vào nhóm `Critical` (Lỗi Nghiêm trọng gây sập trang). Đây chỉ là một mục Dead code / Cleanup ở mức độ Minor hoặc Info.

#### Phát hiện 2: Sai lệch thông tin phiên bản Framework (Laravel 12.x thay vì Laravel 11.x)
- **Trong báo cáo viết (Mục 1.1 dòng 41, Mục 2 dòng 122, Mục 3.2 dòng 912)**:
  > *"Hệ thống được phát triển trên nền tảng Laravel Framework phiên bản 11.x"*
- **Thực tế kiểm chứng**:
  - Tệp `composer.json` dòng 10: `"laravel/framework": "^12.0"`
  - Lệnh CLI `php artisan --version` trả về: `Laravel Framework 12.32.3`.
  - Hệ thống đang chạy trên **Laravel 12.x**, không phải 11.x. Mặc dù hành vi phân trang mặc định (Tailwind CSS) của Laravel 12 tương đồng với Laravel 11, thông số báo cáo cần được ghi nhận chính xác tuyệt đối.

#### Phát hiện 3 (NGHIÊM TRỌNG): Lỗ hổng IDOR làm lộ lọt toàn bộ thông tin định danh khách hàng (PII Leak) bị BỎ SÓT
- **Vị trí vi phạm**: `app/Http/Controllers/OrderController.php:316-318` và `resources/views/client/checkout/checkout_success.blade.php:30-40`.
- **Mã nguồn thực tế**:
  ```php
  // OrderController.php dòng 310-318:
  public function showSuccess($id)
  {
      $order = Order::with('orderItems')->findOrFail($id);

      // LỖI LOGIC KIỂM QUYỀN:
      if (Auth::check() && $order->user_id !== Auth::id()) {
          abort(403);
      }
      $categories = \App\Models\Category::all();
      return view('client.checkout.checkout_success', compact('order', 'categories'));
  }
  ```
- **Phân tích lỗ hổng đối kháng (Attack Scenario)**:
  - Lập trình viên viết: `if (Auth::check() && $order->user_id !== Auth::id()) abort(403);`.
  - Nếu kẻ tấn công hoặc bất kỳ người lạ nào **KHÔNG ĐĂNG NHẬP (Unauthenticated / Guest)** truy cập URL:
    `http://localhost/appweb/dat-hang-thanh-cong/1`, `http://localhost/appweb/dat-hang-thanh-cong/2`...
  - Khi đó `Auth::check()` là `FALSE` -> Điều kiện `if` bị bỏ qua hoàn toàn!
  - Trang web render toàn bộ thông tin nhạy cảm của khách hàng: **Họ và tên, Số điện thoại cá nhân, Địa chỉ nhà ở chi tiết, Email, Chi tiết sản phẩm đã mua và Tổng số tiền**!
  - **Cảnh báo đặc biệt**: Trong báo cáo hiện tại (mục `FUNC-11` dòng 1226), tác giả đề xuất đưa route `order.success` ra ngoài middleware `auth` để phục vụ khách vãng lai nhưng **không hề cảnh báo hoặc đưa ra giải pháp bảo vệ IDOR** này. Nếu làm theo khuyến nghị đó mà không sửa logic trong Controller, toàn bộ cơ sở dữ liệu khách hàng sẽ bị cào quét (scraping) công khai!

---

## 2. LOGIC CHAIN (CHUỖI SUY LUẬN TỪ QUAN SÁT ĐẾN KẾT LUẬN)

1. **Từ Quan sát 1.1**:
   - 42 trên tổng số 44 mục lỗi (tỷ lệ 95.5%) đã được xác minh chính xác từng số dòng, từng tệp mã nguồn và hiện tượng lỗi.
   - Báo cáo đã bao quát toàn diện các khía cạnh: bảo mật (CSRF qua GET, role giáng quyền), tính toàn vẹn dữ liệu (thiếu transaction, sai schema bảng orders, thiếu lock kho), hiển thị (Bootstrap lệch cột, mất thanh navbar mobile, xung đột DataTables).
   - Các đoạn mã khắc phục (Copy-pasteable fixes) có chất lượng kỹ thuật cao, bám sát thực tế Laravel và giải quyết triệt để vấn đề được nêu.

2. **Từ Quan sát 1.2 (Phát hiện 1 - CRIT-03)**:
   - CRIT-03 khẳng định việc thiếu route `register` làm sập ứng dụng 500 Fatal khi tải `welcome.blade.php`.
   - Thực nghiệm kiểm chứng bằng PHP CLI và PHPUnit cho thấy câu lệnh `@if (Route::has('register'))` đã ngăn chặn triệt để lỗi `RouteNotFoundException`. Hơn nữa, `welcome.blade.php` không được sử dụng ở bất kỳ tuyến đường nào.
   - -> *Suy luận*: Việc gán nhãn `Critical` cho CRIT-03 là sai lệch về mức độ nghiêm trọng và là một False Positive về khả năng gây sập hệ thống. Cần chuyển mục này sang nhóm Đề xuất Tối ưu (Cleanup) hoặc gỡ khỏi danh sách lỗi nghiêm trọng.

3. **Từ Quan sát 1.2 (Phát hiện 2 - Framework Version)**:
   - File `composer.json` và lệnh `php artisan --version` đều xác nhận phiên bản là Laravel 12.32.3.
   - -> *Suy luận*: Cần đính chính văn bản báo cáo từ Laravel 11.x thành Laravel 12.x để đảm bảo tính xác thực và chuẩn xác kỹ thuật cao nhất của tài liệu bàn giao.

4. **Từ Quan sát 1.2 (Phát hiện 3 - IDOR / PII Exposure)**:
   - Lỗ hổng trong `OrderController::showSuccess` cho phép khách vãng lai xem trọn vẹn thông tin đơn hàng của bất kỳ ai thông qua việc duyệt ID tuần tự.
   - Báo cáo ban đầu không phát hiện ra lỗ hổng này, thậm chí ở mục `FUNC-11` còn đề xuất mở public route này mà thiếu cơ chế session guard (ví dụ: kiểm tra `session('order_id') == $id` hoặc sử dụng Signed URL).
   - -> *Suy luận*: Đây là một lỗ hổng bảo mật rò rỉ dữ liệu cá nhân cực kỳ nghiêm trọng (Critical Security & Privacy Flaw) bắt buộc phải được bổ sung vào Báo cáo kiểm toán chính thức.

---

## 3. CAVEATS (GIỚI HẠN VÀ GIẢ ĐỊNH)

1. **Môi trường Cơ sở Dữ liệu**:
   - Kiểm tra thực nghiệm được thực hiện trên môi trường phát triển cục bộ XAMPP Windows. Một số kiểm thử liên quan đến truy vấn DB (như `HomeController@show_category_home` crash cột status) được xác nhận thông qua kiểm tra migration schema và đối chiếu exception thực tế.
2. **Cổng thanh toán bên thứ ba (VNPAY / MOMO)**:
   - Do hệ thống chưa tích hợp cấu hình API Keys / Merchant ID thực tế của cổng VNPAY và MOMO trong file `.env`, quy trình chuyển hướng thanh toán trực tuyến hiện chỉ dừng lại ở mức độ logic code cục bộ trong Controller.
3. **Các phần còn lại của hệ thống**:
   - Không có cảnh báo hay ngoại lệ nào chưa được làm rõ. Tất cả các kết luận đối kháng đều dựa trên việc chạy mã thực tế.

---

## 4. CONCLUSION (KẾT LUẬN & PHÁN QUYẾT)

### Phán quyết: **REQUEST_CHANGES** (Yêu cầu cập nhật hoàn thiện báo cáo)

Báo cáo kiểm toán `BAO_CAO_RA_SOAT_HE_THONG.md` có chất lượng rất xuất sắc về độ bao phủ (44 mục lỗi), độ chính xác tuyệt đối về số dòng mã và tính khả thi của các giải pháp vá lỗi. Tuy nhiên, để đạt tiêu chuẩn bàn giao chuyên nghiệp cấp cao nhất (Production-Grade Audit Deliverable), yêu cầu `worker_report` thực hiện 3 điều chỉnh bắt buộc sau:

1. **Hạ cấp hoặc đính chính CRIT-03**:
   - Giải thích rõ `welcome.blade.php` không gây crash 500 do đã có guard `@if (Route::has('register'))` và là view mẫu chưa dùng. Giảm mức độ từ `Critical` xuống `Cleanup / Minor` hoặc thay thế bằng một lỗi Critical thực sự khác.
2. **Bổ sung Lỗ hổng Bảo mật Nghiêm trọng [CRIT-13] IDOR làm lộ PII Khách hàng**:
   - Bổ sung phân tích lỗ hổng tại `app/Http/Controllers/OrderController.php:310-322` (`showSuccess`).
   - Cung cấp giải pháp vá lỗi chuẩn: Sử dụng `session('last_order_id')` hoặc Laravel Signed URLs để chỉ cho phép chính người vừa hoàn tất thanh toán được xem hóa đơn thành công, ngăn chặn kẻ lạ duyệt ID để đánh cắp thông tin cá nhân khách hàng.
3. **Cập nhật chính xác phiên bản Laravel Framework**:
   - Sửa đổi các vị trí ghi "Laravel 11.x" thành **"Laravel 12.x (v12.32.3)"** tại Mục 1.1 và Mục UI-10.

---

## 5. VERIFICATION METHOD (HƯỚNG DẪN KIỂM TRỨNG ĐỘC LẬP)

Để kiểm chứng độc lập các luận điểm đối kháng trên, Kỹ sư kiểm tra có thể thực thi các lệnh sau:

1. **Kiểm chứng False Positive của `welcome.blade.php` (Không crash)**:
   Chạy lệnh PHP CLI:
   ```bash
   php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class); \$kernel->bootstrap(); echo (view('welcome')->render() ? 'SUCCESS_NO_CRASH' : 'FAIL');"
   ```
   *Kết quả mong đợi*: In ra `SUCCESS_NO_CRASH`. Chứng minh CRIT-03 không gây lỗi sập trang.

2. **Kiểm chứng phiên bản Framework**:
   ```bash
   php artisan --version
   ```
   *Kết quả mong đợi*: `Laravel Framework 12.32.3`.

3. **Kiểm chứng Lỗ hổng IDOR trên `showSuccess`**:
   Soát mã nguồn tại dòng 316 file `app/Http/Controllers/OrderController.php`:
   ```php
   if (Auth::check() && $order->user_id !== Auth::id()) {
       abort(403);
   }
   ```
   *Nhận định*: Khi khách chưa đăng nhập (`!Auth::check()`), điều kiện bị bypass hoàn toàn và hiển thị thông tin người mua của đơn hàng `$id`.

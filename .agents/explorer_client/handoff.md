# BÁO CÁO KIỂM TRA TOÀN DIỆN GIAO DIỆN CLIENT & BLADE VIEWS (REQUIREMENT R1)

**Mã phân hệ**: Requirement R1 - Client-side UI & Views Inspection  
**Tác nhân thực hiện**: `explorer_client` (`teamwork_preview_explorer`)  
**Thời gian thực hiện**: 2026-09-18  
**Đường dẫn thư mục làm việc**: `c:\xampp\htdocs\appweb\.agents\explorer_client\`  
**Phạm vi kiểm tra**: Toàn bộ Blade views, layout templates, partials, forms, UI components, assets và route/controller liên quan phía người dùng (Client).

---

## 1. OBSERVATION (Quan sát thực tế & Bằng chứng kiểm toán)

### 1.1. Danh mục và hiện trạng các file view phía Client
Qua rà soát đệ quy toàn bộ `resources/views/`, các view phục vụ khu vực Client gồm:
1. **Layout & Chân trang**:
   - `resources/views/layout/home_layout.blade.php` (293 dòng): Layout khung chính cho toàn bộ trang Client.
   - `resources/views/layout/footer_home.blade.php` (112 dòng): Chân trang (footer partial) được `@include` vào `home_layout`.
   - `resources/views/layout/profile_layout.blade.php` (56 dòng): Kế thừa `home_layout`, hiển thị trang thông tin cá nhân.
2. **Trang chức năng (Client views)**:
   - `resources/views/client/home/index_home.blade.php` (509 dòng): Trang chủ (Hero slider, Khuyến mãi, Sản phẩm mới, Tất cả sản phẩm, Top bán chạy).
   - `resources/views/client/home/product_category.blade.php` (268 dòng): Trang danh mục sản phẩm & hiển thị kết quả tìm kiếm.
   - `resources/views/client/product/detail.blade.php` (187 dòng): Trang chi tiết sản phẩm.
   - `resources/views/client/cart/show_cart.blade.php` (319 dòng): Trang giỏ hàng, nhập coupon, tính cước vận chuyển.
   - `resources/views/client/checkout/checkout_index.blade.php` (392 dòng): Trang nhập địa chỉ giao hàng và chọn phương thức thanh toán.
   - `resources/views/client/checkout/checkout_success.blade.php` (118 dòng): Trang thông báo đặt hàng thành công.
   - `resources/views/client/orders/my_orders.blade.php` (91 dòng): Trang lịch sử đơn hàng của người dùng.
   - `resources/views/client/orders/show.blade.php` (142 dòng): Trang chi tiết đơn hàng của người dùng.
3. **Views xác thực (Auth views)**:
   - `resources/views/client/auth/login_client.blade.php`: **File rỗng 0 bytes**, không chứa bất kỳ nội dung nào.
   - `resources/views/auth/login.blade.php` (61 dòng): View đăng nhập độc lập nhưng **không được gọi** bởi Controller nào (Dead code).
   - `resources/views/admin/auth/login_admin.blade.php` (96 dòng): Được Controller (`AdminController::login`) dùng chung cho cả Admin và Client (Route `/login` và `/admin`).
   - `resources/views/admin/auth/register_admin.blade.php` (132 dòng): Được dùng làm trang đăng ký duy nhất của hệ thống (`/register-admin`).
   - `resources/views/admin/auth/forgot_password.blade.php` & `reset_password.blade.php`: Giao diện quên/đặt lại mật khẩu mang phong cách giao diện Admin.

---

### 1.2. Chi tiết các quan sát và bằng chứng sai sót phát hiện được

#### [OBS-01] Lỗi cú pháp Blade vỡ thẻ HTML nghiêm trọng tại trang chủ
- **File**: `resources/views/client/home/index_home.blade.php`, dòng 268.
- **Nội dung code thực tế**:
  ```blade
  267: <div class="product-details">
  268:     <a href="    }}"><i class="fa fa-eye fa-1x"></i></a>
  269: </div>
  ```
- **Hiện tượng**: Thẻ `<a href="    }}">` bị cắt cụt biểu thức Blade `{{ route('product.detail', $product->id) }}`, để lại khoảng trắng và dấu đóng ngoặc nhọn `}}`, làm hỏng nút xem chi tiết nhanh và sinh HTML không hợp lệ.

#### [OBS-02] Lỗi hiển thị sai giá và sai khuyến mãi ở danh sách "Top bán chạy" (Variable Scope Bug)
- **File**: `resources/views/client/home/index_home.blade.php`, dòng 422 - 448.
- **Nội dung code thực tế**:
  ```blade
  422: @foreach ($best_seller_product as $product)
  ...
  442:     @if ($percent > 0)
  443:         <del class="me-2 fs-5">{{ $fmt($price) }}</del>
  444:         <span class="text-primary fs-5">{{ $fmt($discounted) }}</span>
  445:     @else
  446:         <span class="text-primary fs-5">{{ $fmt($price) }}</span>
  447:     @endif
  ```
- **Hiện tượng**: Trong vòng lặp `$best_seller_product`, lập trình viên **hoàn toàn không khai báo khối `@php`** để tính lại `$price`, `$percent`, `$discounted`, `$fmt` dựa trên sản phẩm hiện tại của vòng lặp!
- **Hệ quả**: Toàn bộ sản phẩm trong khối "Top bán chạy" đều lấy giá và % giảm giá của **sản phẩm cuối cùng trong vòng lặp `$all_product` trước đó**. Nếu danh mục trước rỗng hoặc thay đổi, trang sẽ crash với lỗi `Undefined variable $price / $percent`.

#### [OBS-03] Lỗi so sánh chuỗi phân biệt hoa/thường làm sai lệch phương thức thanh toán
- **File**: `resources/views/client/checkout/checkout_success.blade.php`, dòng 43 - 51.
- **Nội dung code thực tế**:
  ```blade
  43: <h6 class="text-uppercase text-muted small mt-4">Phương thức thanh toán</h6>
  44: <p>
  45:     @if (($order->payment_method ?? 'cod') == 'cod')
  46:         Thanh toán khi nhận hàng (COD)
  47:     @elseif(($order->payment_method ?? '') == 'vnpay')
  48:         Thanh toán qua VNPAY
  49:     @else
  50:         Thanh toán qua Ngân hàng
  51:     @endif
  52: </p>
  ```
- **Đối chiếu Backend (`OrderController.php`, dòng 257)**:
  `'payment_method' => $request->payment_method ?? 'COD',` (lưu giá trị in hoa: `'COD'`, `'VNPAY'`, `'MOMO'`).
- **Hệ quả**: Phép so sánh `'COD' == 'cod'` và `'VNPAY' == 'vnpay'` trong PHP luôn trả về `false`. Do đó, **tất cả các đơn hàng COD hoặc VNPAY đều bị hiển thị sai thành "Thanh toán qua Ngân hàng"** trên trang thành công.

#### [OBS-04] Chuyển hướng người dùng Client vào trang quản trị Admin (`/admin`)
- **File 1**: `public/client/js/cart.js`, dòng 85:
  ```javascript
  // Handle unauthenticated -> redirect to login page
  if (response.status === 401) {
      this.showMessage("Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng", "warning");
      try {
          window.location.href = "/admin"; // <--- CHUYỂN HƯỚNG VÀO TRANG ADMIN
      } catch (_) {}
      return false;
  }
  ```
- **File 2**: `resources/views/client/cart/show_cart.blade.php`, dòng 10:
  ```blade
  6: @if (!Auth::check())
  ...
  10:     <a href="{{ route('admin') }}" class="btn btn-primary">Đăng nhập ngay</a>
  ```
- **File 3**: `resources/views/admin/auth/forgot_password.blade.php` (dòng 62) & `reset_password.blade.php` (dòng 78):
  `href="{{ route('admin') }}"`
- **Hệ quả**: Khách hàng mua sắm khi chưa đăng nhập nhấn nút đăng nhập từ giỏ hàng hoặc thêm sản phẩm sẽ bị đẩy vào màn hình đăng nhập Admin (`/admin`).

#### [OBS-05] Mất hoàn toàn thanh tìm kiếm, giỏ hàng và đăng nhập trên màn hình di động (Responsive Breakpoint Defect)
- **File**: `resources/views/layout/home_layout.blade.php`, dòng 84, 122, 178.
- **Nội dung code thực tế**:
  - Dòng 84: Khối Topbar (Help, Phone, My Dashboard / Đăng nhập) có class `d-none d-lg-block`.
  - Dòng 122: Khối Brand, Search bar (`#keywords`) và Cart Counter có class `d-none d-lg-block`.
  - Dòng 168 - 206: Khối Navbar trên mobile (`< lg`) chỉ hiển thị Brand và nút toggle xổ xuống danh sách danh mục và nút Hotline, **không hề có ô tìm kiếm, không có nút giỏ hàng, không có liên kết đăng nhập/tài khoản**.
- **Hệ quả**: Trên điện thoại di động và máy tính bảng (viewport < 992px), người dùng không thể tìm kiếm sản phẩm, không thể xem giỏ hàng và không thể đăng nhập tài khoản.

#### [OBS-06] Lệch số lượng cột (Column count mismatch) trong bảng hóa đơn Checkout
- **File**: `resources/views/client/checkout/checkout_index.blade.php`, dòng 153 - 257.
- **Nội dung code thực tế**:
  - `<thead>`: Có 4 cột (`th`: Tên sản phẩm, Đơn giá, Số lượng, Tổng tiền).
  - Dòng 177 - 191 (`Tạm tính`): Có tới 5 thẻ (`th` + `td` + `td` + `td` + `td`).
  - Dòng 241 - 255 (`TỔNG CỘNG`): Có tới 5 thẻ (`th` + `td` + `td` + `td` + `td`).
- **Hệ quả**: Cấu trúc bảng HTML bị vỡ, các đường viền bảng (borders) bị lệch cột, co giãn méo mó trên giao diện desktop và tablet.

#### [OBS-07] Thẻ đóng HTML thừa và tag lạc loài (Orphaned HTML Tags)
- **File 1**: `resources/views/layout/profile_layout.blade.php`, dòng 41:
  Xuất hiện thẻ đóng `</form>` nhưng phía trên hoàn toàn không mở thẻ `<form>`.
- **File 2**: `resources/views/client/checkout/checkout_index.blade.php`, dòng 389 - 391:
  Xuất hiện thẻ `</body></html>` nằm cuối file con, trong khi file cha `layout.home_layout` đã có cặp thẻ này.
- **File 3**: `resources/views/client/home/index_home.blade.php`, dòng 317, 400, 462:
  Cú pháp đóng 2 lần thẻ icon: `<i class="fas fa-random"></i></i>`.
- **File 4**: `resources/views/client/home/index_home.blade.php`, dòng 321, 403, 465 & `product_category.blade.php`, dòng 222:
  Thẻ `<span>` không được đóng trước thẻ đóng `</a>`:
  `<a href="#" ...><span class="rounded-circle btn-sm-square border"><i class="fas fa-heart"></i></a>`.

#### [OBS-08] Chế độ xem danh sách (List View) bị trắng tinh không có dữ liệu
- **File**: `resources/views/client/home/product_category.blade.php`, dòng 148 - 153 và 243 - 260.
- **Nội dung code thực tế**:
  - Nút chuyển tab List view: `<a class="bg-light" data-bs-toggle="pill" href="#tab-6">`.
  - Khối nội dung `#tab-6`:
    ```blade
    243: <div id="tab-6" class="products tab-pane fade show p-0">
    244:     <div class="row g-4 products-mini">
    ... (toàn bộ code bên trong bị comment hoặc để trống)
    260:     </div>
    261: </div>
    ```
- **Hệ quả**: Khi khách hàng nhấp vào biểu tượng xem danh sách (list icon), toàn bộ sản phẩm trên màn hình biến mất, hiển thị một khoảng trắng trống rỗng.

#### [OBS-09] Thiếu giữ lại giá trị cũ (`old()`) và thông báo lỗi validation trên các biểu mẫu
- **File 1**: `resources/views/client/checkout/checkout_index.blade.php`:
  - Input `shipping_phone` (dòng 111): Không có thuộc tính `value="{{ old('shipping_phone') }}"`.
  - Input `shipping_address` (dòng 141): Không có thuộc tính `value="{{ old('shipping_address') }}"`.
  - Textarea `ghichu` (dòng 145): Không có `{{ old('ghichu') }}`.
  - Phụ thuộc API bên ngoài `https://esgoo.net/api-tinhthanh`: Khi submit form lỗi và redirect back, 3 select Tỉnh/Huyện/Xã bị reset về rỗng.
- **File 2**: `resources/views/admin/auth/register_admin.blade.php` (dòng 28 - 55):
  - Các ô `name`, `email`, `phoneNumber` không có `value="{{ old(...) }}"`.
  - View hoàn toàn không có khối hiển thị lỗi validation (`@if($errors->any())` hoặc `@error(...)`), khiến người dùng không biết vì sao đăng ký thất bại khi gặp lỗi validate ở controller (như sai định dạng số điện thoại).

#### [OBS-10] Phụ thuộc hàm Easing không tồn tại trong `main.js`
- **File**: `public/client/js/main.js`, dòng 162:
  `$("html, body").animate({ scrollTop: 0 }, 1500, "easeInOutExpo");`
- **Thực tế**: File `home_layout.blade.php` chỉ load thư viện jQuery cơ bản (`jquery.min.js`), không load jQuery UI hay `jquery.easing.min.js`.
- **Hệ quả**: Nhấp nút Back-to-top gây ra lỗi JavaScript `jQuery.easing[this.easing] is not a function`.

#### [OBS-11] Antipattern đường dẫn tài nguyên `asset('public/...')` và Junction Folder
- **File**: Toàn bộ các view và layout đều dùng `asset('public/client/...')`, `asset('public/uploads/...')`.
- **Hệ quả**: Trong cấu hình chuẩn của Laravel, thư mục webroot là `public/`, do đó `asset('public/...')` sinh ra URL `/public/...`. Để sửa tạm, một liên kết Windows Junction `public/public -> public` đã được tạo thủ công. Nếu đưa lên server Linux/Docker hoặc môi trường staging, toàn bộ CSS/JS/hình ảnh sẽ bị 404.

#### [OBS-12] Phân trang mặc định Tailwind trong giao diện Bootstrap 5
- **File**: `resources/views/client/orders/my_orders.blade.php`, dòng 85: `{{ $orders->links() }}`.
- **Thực tế**: `AppServiceProvider.php` không gọi `Paginator::useBootstrapFive()`.
- **Hệ quả**: Laravel 11 tự động render các thẻ SVG và class Tailwind CSS khổng lồ làm vỡ hoàn toàn chân bảng danh sách đơn hàng.

---

## 2. LOGIC CHAIN (Chuỗi suy luận & Phân tích nguyên nhân)

```
[Bắt đầu rà soát giao diện Client]
      │
      ├─► [Kiểm tra Blade Syntax & Templates]
      │         ├─ Phát hiện dòng 268 index_home.blade.php: href="    }}"
      │         │     └─► Nguyên nhân: Copy/paste dở dang biểu thức route.
      │         │     └─► Tác động: Nút xem chi tiết hỏng, cú pháp HTML không hợp lệ.
      │         │
      │         ├─ Phát hiện dòng 422 index_home.blade.php: Vòng lặp best_seller thiếu @php tính giá
      │         │     └─► Nguyên nhân: Lấy lại biến $price, $discounted của vòng lặp trước.
      │         │     └─► Tác động: Khách hàng thấy sai lệch giá tiền và khuyến mãi của Top bán chạy.
      │         │
      │         ├─ Phát hiện dòng 41 profile_layout.blade.php: Thẻ </form> mồ côi
      │         └─ Phát hiện dòng 389 checkout_index.blade.php: Thừa </body></html>
      │
      ├─► [Kiểm tra Tích hợp Dữ liệu & Nghiệp vụ]
      │         ├─ So sánh checkout_success.blade.php ($order->payment_method == 'cod') với OrderController ('COD')
      │         │     └─► Nguyên nhân: So sánh chuỗi phân biệt hoa thường trong PHP.
      │         │     └─► Tác động: 100% đơn COD và VNPAY hiển thị sai thành "Thanh toán qua Ngân hàng".
      │         │
      │         ├─ So sánh nút đăng nhập giỏ hàng (show_cart & cart.js) trỏ về /admin
      │         │     └─► Nguyên nhân: Thiếu hệ thống view Auth riêng cho Client, gộp chung vào Admin.
      │         │     └─► Tác động: Người dùng phổ thông bị dẫn vào khu vực quản trị.
      │         │
      │         └─ Kiểm tra List view tab (#tab-6) trong product_category.blade.php
      │               └─► Nguyên nhân: Bỏ quên code HTML khi cắt template.
      │               └─► Tác động: Khách đổi sang dạng danh sách thì màn hình trắng tinh.
      │
      └─► [Kiểm tra UI/UX & Tính Responsive]
                ├─ Rà soát viewport nhỏ (< lg) trong home_layout.blade.php
                │     └─► Nguyên nhân: Dùng class `d-none d-lg-block` cho toàn bộ thanh tìm kiếm, giỏ hàng, user menu.
                │     └─► Tác động: Mất toàn bộ điều hướng thiết yếu trên Mobile.
                │
                └─ Rà soát các liên kết footer, hotline, header
                      └─► Nguyên nhân: Dùng liên kết giả href="#", hotline lệch nhau giữa header và footer.
                      └─► Tác động: Website thiếu tính hoàn thiện chuyên nghiệp.
```

---

## 3. CAVEATS (Phạm vi chưa kiểm tra & Giả định)

1. **Phạm vi quản trị (Admin Views & Controllers)**: Thuộc nhiệm vụ R2/R3, do subagent chuyên trách rà soát, không nằm trong báo cáo chuyên sâu này (trừ các view Auth bị dùng chung giữa Admin và Client).
2. **Cổng thanh toán thực tế (VNPAY / MoMo Sandbox)**: Chưa kiểm tra kết nối API ngân hàng thực tế, chỉ kiểm tra luồng submit form và giao diện hiển thị phương thức thanh toán.
3. **Cơ sở dữ liệu mẫu**: Giả định cấu trúc bảng `products`, `_category`, `orders`, `oder_items` giữ nguyên như migrations hiện tại.

---

## 4. CONCLUSION & CLASSIFICATION (Bảng phân loại lỗi & Giải pháp chi tiết)

### BẢNG TỔNG HỢP PHÂN LOẠI LỖI

| Phân loại | Tên lỗi | File & Dòng | Mức độ ảnh hưởng |
|:---|:---|:---|:---|
| **Critical** | Cú pháp Blade vỡ nút xem chi tiết | `client/home/index_home.blade.php:268` | Vỡ HTML, hỏng link xem sản phẩm |
| **Critical** | Sai lệch giá & % giảm giá Top bán chạy | `client/home/index_home.blade.php:442-447` | Hiển thị sai giá tiền của toàn bộ sản phẩm bán chạy |
| **Critical** | Hiển thị sai phương thức thanh toán | `client/checkout/checkout_success.blade.php:44` | Đơn hàng COD/VNPAY bị hiểu nhầm thành chuyển khoản |
| **Critical** | Redirect người dùng Client vào Admin | `cart.js:85`, `show_cart.blade.php:10` | Sai luồng bảo mật và trải nghiệm người dùng |
| **UI/UX** | Mất thanh tìm kiếm, giỏ hàng, menu trên Mobile | `layout/home_layout.blade.php:84,122,168` | Người dùng mobile không thể thao tác mua sắm |
| **UI/UX** | Tab xem dạng danh sách (List View) bị rỗng | `client/home/product_category.blade.php:243` | Màn hình biến mất sản phẩm khi bấm nút |
| **UI/UX** | Lệch cột bảng thanh toán Checkout | `client/checkout/checkout_index.blade.php:177,241` | Cột Tạm tính và Tổng cộng nhảy sai định dạng |
| **UI/UX** | Thẻ đóng HTML mồ côi (`</form>`, `</body></html>`) | `profile_layout.blade.php:41`, `checkout_index.blade.php:389` | Sai chuẩn W3C, nguy cơ vỡ layout DOM |
| **UI/UX** | Lỗi JS Back-to-top do thiếu jQuery Easing | `public/client/js/main.js:162` | Ném Exception Console khi cuộn lên đầu |
| **Functional** | Mất dữ liệu đã nhập & thiếu lỗi đăng ký | `admin/auth/register_admin.blade.php:28-55` | Người dùng không biết lỗi gì khi đăng ký không thành |
| **Functional** | Thiếu retain old inputs trang Checkout | `client/checkout/checkout_index.blade.php:111-145` | Phải nhập lại toàn bộ thông tin khi validate thất bại |
| **Improvements**| Antipattern `asset('public/...')` & Junction | Toàn bộ các Blade views | Nguy cơ hỏng toàn bộ static assets trên Linux |
| **Improvements**| Thiếu giao diện Client Auth & Trang tĩnh (404, About) | `resources/views/client/auth/`, `errors/` | Trải nghiệm người dùng chưa đồng bộ |

---

### DANH SÁCH GIẢI PHÁP MÃ NGUỒN CỤ THỂ (Copy-pasteable Solutions)

#### 1. Khắc phục lỗi cú pháp Blade trong `resources/views/client/home/index_home.blade.php`
- **Vị trí**: Dòng 268
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

#### 2. Khắc phục lỗi tính giá và % giảm giá trong khối "Top bán chạy" (`index_home.blade.php`)
- **Vị trí**: Dòng 440 - 450
- **Code hiện tại**:
  ```blade
  <a href="{{ route('product.detail', $product->id) }}" class="d-block h4">
      {{ $product->name }} <br></a>
  @if ($percent > 0)
      <del class="me-2 fs-5">{{ $fmt($price) }}</del>
      <span class="text-primary fs-5">{{ $fmt($discounted) }}</span>
  @else
      <span class="text-primary fs-5">{{ $fmt($price) }}</span>
  @endif
  ```
- **Code sửa lại** (Bổ sung khối tính toán độc lập cho từng sản phẩm trong vòng lặp):
  ```blade
  <a href="{{ route('product.detail', $product->id) }}" class="d-block h4">
      {{ $product->name }} <br></a>
  @php
      $itemPrice = (float) ($product->price ?? 0);
      $itemPercent = max(0, min(100, (int) ($product->discountPercent ?? 0)));
      $itemDiscounted = ($itemPrice * (100 - $itemPercent)) / 100;
  @endphp
  @if ($itemPercent > 0)
      <del class="me-2 fs-5">{{ number_format($itemPrice, 0, ',', '.') }}đ</del>
      <span class="text-primary fs-5">{{ number_format($itemDiscounted, 0, ',', '.') }}đ</span>
  @else
      <span class="text-primary fs-5">{{ number_format($itemPrice, 0, ',', '.') }}đ</span>
  @endif
  ```

---

#### 3. Khắc phục lỗi so sánh phương thức thanh toán trong `checkout_success.blade.php`
- **Vị trí**: Dòng 44 - 51
- **Code hiện tại**:
  ```blade
  @if (($order->payment_method ?? 'cod') == 'cod')
      Thanh toán khi nhận hàng (COD)
  @elseif(($order->payment_method ?? '') == 'vnpay')
      Thanh toán qua VNPAY
  @else
      Thanh toán qua Ngân hàng
  @endif
  ```
- **Code sửa lại** (Sử dụng `strtoupper` hoặc so sánh không phân biệt hoa thường):
  ```blade
  @php
      $method = strtoupper($order->payment_method ?? 'COD');
  @endphp
  @if ($method === 'COD')
      Thanh toán khi nhận hàng (COD)
  @elseif ($method === 'VNPAY')
      Thanh toán qua VNPAY
  @elseif ($method === 'MOMO')
      Thanh toán qua Ví MoMo
  @else
      Thanh toán chuyển khoản ngân hàng
  @endif
  ```

---

#### 4. Khắc phục chuyển hướng nhầm vào Admin trong `public/client/js/cart.js` và `show_cart.blade.php`
- **Trong `public/client/js/cart.js` (dòng 85)**:
  ```javascript
  // Thay thế:
  window.location.href = "/admin";
  // Bằng:
  window.location.href = "/login";
  ```
- **Trong `resources/views/client/cart/show_cart.blade.php` (dòng 10)**:
  ```blade
  <!-- Thay thế: -->
  <a href="{{ route('admin') }}" class="btn btn-primary">Đăng nhập ngay</a>
  <!-- Bằng: -->
  <a href="{{ route('login') }}" class="btn btn-primary">Đăng nhập ngay</a>
  ```

---

#### 5. Khắc phục tính Responsive trên màn hình di động trong `layout/home_layout.blade.php`
- **Nguyên nhân**: Cả 2 khối Topbar và Main Header chứa Search + Cart đều bị ẩn (`d-none d-lg-block`).
- **Khắc phục**:
  Thêm biểu tượng Giỏ hàng có huy hiệu đếm và ô tìm kiếm nhỏ vào Navbar Mobile (ngay cạnh nút menu hamburger trong `nav-bar`):
  ```blade
  <!-- Ngay trước button.navbar-toggler ở dòng 178: -->
  <div class="d-flex align-items-center d-lg-none me-2">
      <a href="{{ route('cart') }}" class="position-relative text-white me-3">
          <i class="fas fa-shopping-cart fa-lg"></i>
          <span class="cart-counter position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
      </a>
      @auth
          <a href="{{ route('profile') }}" class="text-white me-2" title="Tài khoản"><i class="fas fa-user-circle fa-lg"></i></a>
      @else
          <a href="{{ route('login') }}" class="text-white me-2" title="Đăng nhập"><i class="fas fa-sign-in-alt fa-lg"></i></a>
      @endauth
  </div>
  ```

---

#### 6. Khắc phục lệch số cột bảng Checkout trong `checkout_index.blade.php`
- **Vị trí**: Dòng 177 - 191 và dòng 241 - 255.
- **Code hiện tại**: Dùng 5 ô (`th` + 4 `td`) trong khi bảng chỉ có 4 cột.
- **Code sửa lại cho hàng "Tạm tính"**:
  ```blade
  <tr>
      <th scope="row" colspan="2" class="py-4 text-start">
          <p class="mb-0 text-dark py-2 fw-bold">Tạm tính tiền hàng</p>
      </th>
      <td class="py-4 text-center"></td>
      <td class="py-4 text-center">
          <div class="py-2 border-bottom border-top">
              <p class="mb-0 text-dark fw-bold">{{ number_format($subtotal ?? 0, 0, ',', '.') }} VNĐ</p>
          </div>
      </td>
  </tr>
  ```
- **Code sửa lại cho hàng "TỔNG CỘNG"**:
  ```blade
  <tr>
      <th scope="row" colspan="2" class="py-4 text-start">
          <p class="mb-0 text-dark text-uppercase py-2 fw-bold">TỔNG CỘNG</p>
      </th>
      <td class="py-4 text-center"></td>
      <td class="py-4 text-center">
          <div class="py-2 border-bottom border-top">
              <p class="mb-0 text-danger fs-5 fw-bold">{{ number_format($totalPrice ?? 0, 0, ',', '.') }} VNĐ</p>
          </div>
      </td>
  </tr>
  ```

---

#### 7. Dọn dẹp thẻ đóng thừa và HTML lỗi
- **File `layout/profile_layout.blade.php` (dòng 41)**:
  Xóa bỏ dòng `</form>`.
- **File `checkout_index.blade.php` (dòng 389 - 391)**:
  Xóa bỏ các dòng:
  ```html
  </body>
  </html>
  ```
- **File `index_home.blade.php` và `product_category.blade.php`**:
  Thay thế các cụm `</i></i>` thành `</i>` và đóng đúng thẻ `<span>`:
  ```blade
  <span class="rounded-circle btn-sm-square border"><i class="fas fa-heart"></i></span>
  ```

---

#### 8. Khắc phục List View rỗng trong `product_category.blade.php`
- **Vị trí**: Dòng 243 - 260.
- **Giải pháp**: Đồng bộ hiển thị danh sách sản phẩm trong `#tab-6` theo layout ngang (horizontal card):
  ```blade
  <div id="tab-6" class="products tab-pane fade show p-0">
      <div class="row g-4 products-mini">
          @foreach ($product as $item)
              <div class="col-12">
                  <div class="card border p-3 rounded d-flex flex-row align-items-center">
                      <img src="{{ asset('public/uploads/products/' . $item->imageURL) }}" style="width: 100px; height: 100px; object-fit: cover;" class="rounded me-4" alt="{{ $item->name }}">
                      <div class="flex-grow-1">
                          <h5 class="mb-1"><a href="{{ route('product.detail', $item->id) }}" class="text-dark">{{ $item->name }}</a></h5>
                          <p class="text-muted small mb-2">{{ $item->category->name ?? '' }}</p>
                          <p class="text-danger fw-bold mb-0">{{ number_format($item->price, 0, ',', '.') }} VNĐ</p>
                      </div>
                      <div>
                          <button class="btn btn-primary rounded-pill px-4 py-2 add-to-cart-btn" data-product-id="{{ $item->id }}" data-authenticated="{{ Auth::check() ? 'true' : 'false' }}">
                              <i class="fas fa-shopping-cart me-1"></i> Thêm giỏ hàng
                          </button>
                      </div>
                  </div>
              </div>
          @endforeach
      </div>
  </div>
  ```

---

#### 9. Khắc phục lỗi JS Back-to-top trong `public/client/js/main.js`
- **Vị trí**: Dòng 162
- **Thay thế**:
  ```javascript
  $(".back-to-top").click(function () {
      $("html, body").animate({ scrollTop: 0 }, 800); // Bỏ easing "easeInOutExpo" không được nạp
      return false;
  });
  ```

---

#### 10. Bổ sung Bootstrap 5 Paginator trong `app/Providers/AppServiceProvider.php`
- **Khắc phục lỗi vỡ giao diện phân trang**:
  ```php
  use Illuminate\Pagination\Paginator;

  public function boot(): void
  {
      Paginator::useBootstrapFive();
      // ... giữ nguyên view composer hiện tại
  }
  ```

---

## 5. VERIFICATION METHOD (Phương pháp kiểm chứng độc lập)

Để kiểm chứng độc lập tất cả các quan sát và kết luận trên mà không làm thay đổi mã nguồn, người kiểm tra có thể thực hiện theo các bước sau:

1. **Kiểm chứng lỗi cú pháp Blade trên `index_home.blade.php`**:
   - Chạy lệnh xem file trực tiếp:
     ```powershell
     Get-Content -Path "resources/views/client/home/index_home.blade.php" | Select-Object -Index 267
     ```
   - Kết quả trả về đúng chuỗi: `<a href="    }}"><i class="fa fa-eye fa-1x"></i></a>`.

2. **Kiểm chứng lỗi tính sai giá Top bán chạy**:
   - Xem dòng 420 đến 448 của `resources/views/client/home/index_home.blade.php`. Xác nhận không có thẻ `@php` nào gán lại `$price` hay `$percent` bên trong vòng lặp `$best_seller_product`.

3. **Kiểm chứng lỗi so sánh thanh toán `checkout_success.blade.php`**:
   - So sánh dòng 45 của `checkout_success.blade.php` (`$order->payment_method == 'cod'`) với dòng 257 của `OrderController.php` (`'payment_method' => $request->payment_method ?? 'COD'`). Chạy lệnh PHP tương đương:
     ```powershell
     php -r "var_dump('COD' == 'cod');"
     ```
     Kết quả in ra `bool(false)` xác nhận đơn COD luôn rơi vào nhánh `@else`.

4. **Kiểm chứng lỗi chuyển hướng Admin trong giỏ hàng**:
   - Mở file `public/client/js/cart.js` dòng 85 và xác nhận lệnh `window.location.href = "/admin";`.
   - Mở file `resources/views/client/cart/show_cart.blade.php` dòng 10 và xác nhận thẻ `<a href="{{ route('admin') }}">`.

5. **Kiểm chứng lỗi Responsive**:
   - Mở file `resources/views/layout/home_layout.blade.php`, tìm các dòng 84 và 122 có class `d-none d-lg-block`, đồng thời kiểm tra phần tử `#navbarCollapse` ở dòng 183 không hề có form tìm kiếm hay giỏ hàng.

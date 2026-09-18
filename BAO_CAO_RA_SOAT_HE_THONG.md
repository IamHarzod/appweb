# BÁO CÁO RÀ SOÁT, KIỂM TOÁN HỆ THỐNG VÀ KHUYẾN NGHỊ KHẮC PHỤC TOÀN DIỆN
## (COMPREHENSIVE AUDIT & RECOMMENDATIONS REPORT)

**Dự án**: Website Bán hàng Trực tuyến Laravel (`appweb`)  
**Tác nhân thực hiện**: Đội ngũ Kiểm toán Mã nguồn Chuyên sâu (Teamwork Audit Team)  
**Thời gian hoàn thành**: 2026-09-18  
**Tình trạng báo cáo**: Sẵn sàng thẩm định & Xuất bản chính thức (Publication-Ready Deliverable)  
**Tài liệu tham chiếu**: `ORIGINAL_REQUEST.md`, `explorer_client/handoff.md`, `explorer_admin/handoff.md`, `explorer_backend/handoff.md`  

---

## MỤC LỤC

1. [TỔNG QUAN HỆ THỐNG VÀ PHẠM VI RÀ SOÁT](#1-tổng-quan-hệ-thống-và-phạm-vi-rà-soát)
   - 1.1. Kiến trúc hệ thống và Ngăn xếp công nghệ
   - 1.2. Thống kê phạm vi kiểm toán
   - 1.3. Tóm tắt kết quả kiểm toán theo cấp độ rủi ro
2. [BẢNG MA TRẬN PHÂN LOẠI LỖI TOÀN DIỆN (AUDIT MATRIX)](#2-bảng-ma-trận-phân-loại-lỗi-toàn-diện-audit-matrix)
3. [DANH MỤC LỖI CHI TIẾT VÀ GIẢI PHÁP KHẮC PHỤC HOÀN CHỈNH](#3-danh-mục-lỗi-chi-tiết-và-giải-pháp-khắc-phục-hoàn-chỉnh)
   - 3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues)
   - 3.2. Nhóm 2: Lỗi Giao diện & Hiển thị (UI/UX Issues)
   - 3.3. Nhóm 3: Lỗi Luồng Nghiệp vụ & Xử lý Dữ liệu (Functional Issues)
   - 3.4. Nhóm 4: Đề xuất Tối ưu Hệ thống (Improvements)
4. [LỘ TRÌNH VÀ KẾ HOẠCH TRIỂN KHAI KHẮC PHỤC (ROADMAP & ACTION PLAN)](#4-lộ-trình-và-kế-hoạch-triển-khai-khắc-phục-roadmap--action-plan)
   - 4.1. Giai đoạn 1: Hotfix khẩn cấp trong 24h - 48h
   - 4.2. Giai đoạn 2: Sửa lỗi Nghiệp vụ & Luồng dữ liệu (1 tuần)
   - 4.3. Giai đoạn 3: Hoàn thiện UI/UX & Responsive Mobile (1 tuần)
   - 4.4. Giai đoạn 4: Tối ưu hiệu năng & Chuẩn hóa mã nguồn (1 tuần)
5. [HƯỚNG DẪN KIỂM CHỨNG & THẨM TRA ĐỘC LẬP (VERIFICATION GUIDE)](#5-hướng-dẫn-kiểm-chứng--thẩm-tra-độc-lập-verification-guide)
   - 5.1. Kiểm tra tĩnh qua dòng lệnh CLI
   - 5.2. Kịch bản kiểm chứng tự động qua PHP CLI script
   - 5.3. Kịch bản kiểm thử tương tác trên trình duyệt (Browser Test Cases)
   - 5.4. Bảng kiểm tra nghiệm thu (Sign-off Checklist)

---

## 1. TỔNG QUAN HỆ THỐNG VÀ PHẠM VI RÀ SOÁT

### 1.1. Kiến trúc hệ thống và Ngăn xếp công nghệ

Hệ thống được phát triển trên nền tảng **Laravel 12.x (Laravel Framework 12.32.3)**, chạy trên môi trường **PHP 8.2+** và hệ quản trị cơ sở dữ liệu **MySQL** (InnoDB Engine, utf8mb4). Kiến trúc phần mềm tuân theo mô hình **MVC (Model - View - Controller)** kết hợp các thành phần mở rộng của Laravel:

* **Backend & Routing**:
  * Laravel Routing với 71 routes (chia thành Guest routes, Auth protected routes, và Admin middleware protected routes).
  * 11 Controllers phụ trách các phân hệ: `HomeController`, `ProductController`, `CategoryController`, `BrandController`, `CartController`, `CheckoutController`, `OrderController`, `CouponController`, `AdminController`, `ProfilesController`, `PasswordResetController`.
  * Eloquent ORM ánh xạ các bảng chính: `users`, `products`, `_category`, `thuonghieu`, `orders`, `oder_items`, `coupons`, `password_reset_tokens`.
  * Service Provider: `AppServiceProvider` cung cấp View Composer chia sẻ dữ liệu danh mục toàn cục cho client layouts.
* **Frontend & Giao diện**:
  * **Khu vực Khách hàng (Client-side)**: Blade templates, Bootstrap 5.3, FontAwesome 5/6, Owl Carousel, thư viện AJAX tùy chỉnh (`cart.js`, `main.js`).
  * **Khu vực Quản trị (Admin-side)**: Blade templates dựa trên Quixlab Admin Theme chạy **Bootstrap 4.3.1**, jQuery 3.3.1, DataTables, SweetAlert2, Toastr, MetisMenu.
* **Môi trường triển khai hiện tại**:
  * Windows XAMPP Server (`c:\xampp\htdocs\appweb`).
  * Sử dụng Windows NTFS Junction Link (`public/public -> public`) để giải quyết vấn đề tiền tố tài nguyên tĩnh.

---

### 1.2. Thống kê phạm vi kiểm toán

Cuộc kiểm toán đã được thực hiện bằng cách rà soát 100% mã nguồn thực tế của toàn bộ các tầng:

| Phân hệ kiểm toán | Số lượng tệp / Thành phần | Chi tiết phạm vi rà soát |
|:---|:---:|:---|
| **Views Phía Client** | 11 tệp Blade | 100% Views tại `resources/views/client/` (Home, Category, Product detail, Cart, Checkout, My Orders), `resources/views/layout/` (`home_layout`, `footer_home`, `profile_layout`), và `resources/views/auth/`. |
| **Views Phía Admin** | 21 tệp Blade | 100% Views tại `resources/views/admin/` (Product CRUD, Category CRUD, Brand CRUD, Orders index & detail, Users & Roles, Coupons CRUD, Auth admin) và `resources/views/layout/admin_layout.blade.php`. |
| **Hệ thống Routing** | 71 Routes | 100% Route định nghĩa trong `routes/web.php` và `routes/console.php`. |
| **Tầng Controllers** | 11 Controllers | Toàn bộ phương thức xử lý HTTP, logic validation, session, transactions, và truy vấn DB trong `app/Http/Controllers/`. |
| **Tầng Models & Data** | 7 Models & Migrations | Toàn bộ các model Eloquent trong `app/Models/`, quan hệ dữ liệu (Relationships), `$fillable`, kiểu dữ liệu và migrations trong `database/migrations/`. |
| **Tài nguyên tĩnh (Assets)** | ~250 tệp CSS/JS/Img | Toàn bộ tài nguyên trong `public/admin/`, `public/client/`, `public/uploads/` và cấu trúc liên kết thư mục `public/public`. |

---

### 1.3. Tóm tắt kết quả kiểm toán theo cấp độ rủi ro

Qua kiểm toán toàn diện, hệ thống phát hiện tổng cộng **45 vấn đề cần xử lý**, phân bổ theo 4 mức độ rủi ro:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        TỔNG HỢP KẾT QUẢ KIỂM TOÁN                      │
├────────────────────────┬─────────────┬─────────────────────────────────┤
│ Phân loại rủi ro       │ Số lượng    │ Mức độ ảnh hưởng chính          │
├────────────────────────┼─────────────┼─────────────────────────────────┤
│ 1. Critical (Khẩn cấp) │ 13 lỗi      │ Crash 500 fatal, hổng CSRF GET, │
│                        │             │ IDOR lộ PII, leo thang, khóa ad │
│ 2. UI/UX (Giao diện)   │ 14 lỗi      │ Mất menu mobile, vỡ responsive, │
│                        │             │ tab rỗng, lỗi JS console        │
│ 3. Functional (Nghiệp vụ) 11 lỗi    │ Không đổi được status đơn hàng, │
│                        │             │ nuốt flash, mất form input, lock│
│ 4. Improvements (Tối ưu) 7 đề xuất   │ Gỡ junction Windows, tối ưu JS, │
│                        │             │ i18n DataTables, paginate server│
├────────────────────────┼─────────────┼─────────────────────────────────┤
│ TỔNG CỘNG              │ 45 MỤC      │ Cần khắc phục theo lộ trình     │
└────────────────────────┴─────────────┴─────────────────────────────────┘
```

---

## 2. BẢNG MA TRẬN PHÂN LOẠI LỖI TOÀN DIỆN (AUDIT MATRIX)

| Mã lỗi | Tên lỗi & Hiện tượng | Phân loại | Tệp & Dòng mã vi phạm | Tác động / Mức độ rủi ro |
|:---|:---|:---:|:---|:---|
| **CRIT-01** | Toàn bộ thao tác Xóa Admin dùng HTTP GET không CSRF | Critical | `routes/web.php:60,116,126,136,142,150`<br>`public/admin/js/main.js:14-17` | Lỗ hổng CSRF nghiêm trọng; Bot/Crawler xóa sạch Database |
| **CRIT-02** | Route `/thanh-toan` gọi method không tồn tại `processOrder` | Critical | `routes/web.php:46`<br>`CheckoutController.php` | Crash 500 Fatal (`BadMethodCallException`) |
| **CRIT-03** | View gọi named route `route('register')` chưa từng định nghĩa | Critical | `resources/views/welcome.blade.php:43`<br>`routes/web.php:83` | Nút Register bị ẩn trong `@if (Route::has('register'))`; ném lỗi `RouteNotFoundException` nếu gọi trực tiếp |
| **CRIT-04** | Truy vấn cột `status` không tồn tại trong bảng `_category` | Critical | `app/Http/Controllers/HomeController.php:29` | Crash 500 Fatal (`QueryException SQLSTATE 42S22`) |
| **CRIT-05** | `OrderController@storeFromCart` ghi sai cấu trúc bảng `orders` | Critical | `app/Http/Controllers/OrderController.php:86-105` | Crash 500 Fatal (`Field doesn't have a default value`) |
| **CRIT-06** | `CartController@checkCoupon` truy cập key mảng không validate | Critical | `app/Http/Controllers/CartController.php:433-434` | Crash 500 Fatal (`ErrorException: Undefined array key`) |
| **CRIT-07** | Truy cập `$item->category->name` thiếu null-safe operator | Critical | `show_product.blade.php:67`<br>`product_category.blade.php:173`<br>`index_home.blade.php:273,363` | Crash 500 Fatal (`Attempt to read property on null`) |
| **CRIT-08** | Lỗ hổng Mass Assignment leo thang đặc quyền qua cột `role` | Critical | `app/Models/User.php:27` | Người dùng có thể tự phong làm Admin qua request payload |
| **CRIT-09** | Lỗ hổng Admin tự giáng quyền dẫn tới khóa tài khoản vĩnh viễn | Critical | `admin/auth/users.blade.php:34-41`<br>`AdminController.php:66-75` | Admin tự đổi role của mình thành user -> Khóa quyền admin |
| **CRIT-10** | Cú pháp Blade vỡ nút xem chi tiết nhanh `href="    }}"` | Critical | `resources/views/client/home/index_home.blade.php:268` | Vỡ HTML, hỏng link xem sản phẩm trên Trang chủ |
| **CRIT-11** | Sai lệch giá tiền và khuyến mãi Top bán chạy (Variable Scope) | Critical | `resources/views/client/home/index_home.blade.php:422-448` | Hiển thị sai toàn bộ giá tiền & % giảm của Top sản phẩm |
| **CRIT-12** | Hiển thị sai phương thức thanh toán do so sánh phân biệt hoa/thường | Critical | `resources/views/client/checkout/checkout_success.blade.php:43-51` | Đơn hàng COD/VNPAY bị hiển thị nhầm thành Chuyển khoản |
| **CRIT-13** | Lỗ hổng IDOR trên trang xác nhận đơn hàng (`showSuccess`) làm lộ PII | Critical | `app/Http/Controllers/OrderController.php:315-325`<br>`checkout_success.blade.php:28-40` | Khách vãng lai bypass kiểm quyền, duyệt ID quét toàn bộ PII (tên, SĐT, địa chỉ, email) |
| **UI-01** | Mất hoàn toàn thanh tìm kiếm, giỏ hàng, menu trên Mobile | UI/UX | `resources/views/layout/home_layout.blade.php:84,122,178` | Người dùng điện thoại không thể tìm hàng, xem giỏ, đăng nhập |
| **UI-02** | Chế độ xem danh sách (List View `#tab-6`) bị rỗng trắng tinh | UI/UX | `resources/views/client/home/product_category.blade.php:243-260` | Bấm chuyển tab danh sách thì toàn bộ sản phẩm biến mất |
| **UI-03** | Lệch số lượng cột bảng thanh toán Checkout (5 cột vs 4 cột) | UI/UX | `resources/views/client/checkout/checkout_index.blade.php:177,241` | Cấu trúc bảng HTML bị lệch viền, méo mó giao diện |
| **UI-04** | Nút "Xóa tìm kiếm" sản phẩm admin trỏ link 404 `/shop` | UI/UX | `resources/views/admin/product/show_product.blade.php:9` | Gặp lỗi 404 Not Found khi bấm bỏ lọc tìm kiếm |
| **UI-05** | Khách mua hàng bấm Đăng nhập bị điều hướng vào Admin (`/admin`) | UI/UX | `public/client/js/cart.js:85`<br>`show_cart.blade.php:10` | Sai luồng nghiệp vụ, gây hoang mang cho khách hàng |
| **UI-06** | Dashboard Admin (`/admin/dashboard`) trắng trơn phần thân | UI/UX | `app/Http/Controllers/AdminController.php:21-24`<br>`admin_layout.blade.php:135` | Trang chủ quản trị không có KPI cards, biểu đồ, bảng dữ liệu |
| **UI-07** | Script `dashboard-1.js` ném Uncaught TypeError trên TẤT CẢ trang | UI/UX | `resources/views/layout/admin_layout.blade.php:173`<br>`dashboard-1.js:8,105,241` | Crash console JS toàn hệ thống, chặn các scripts tiếp theo |
| **UI-08** | Lẫn lộn cú pháp Bootstrap 5 trên nền giao diện Bootstrap 4.3.1 | UI/UX | `add_product.blade.php:7`<br>`add_category.blade.php:9,50`<br>`add_coupon.blade.php:7,74` | Nút đóng modal không có style (hiện chữ X thô), `gap-2` hỏng |
| **UI-09** | Thẻ đóng HTML mồ côi (`</form>`, `</body></html>`, `</i></i>`) | UI/UX | `profile_layout.blade.php:41`<br>`checkout_index.blade.php:389`<br>`index_home.blade.php:317` | Sai chuẩn W3C HTML5, nguy cơ lỗi DOM tree và render |
| **UI-10** | Vỡ phân trang Bootstrap do Laravel 12 mặc định dùng Tailwind SVG | UI/UX | `resources/views/client/orders/my_orders.blade.php:85`<br>`AppServiceProvider.php` | Mũi tên và nút phân trang SVG to khổng lồ làm vỡ layout |
| **UI-11** | Nút bấm thừa có chữ "Button" cạnh các ô tải tệp lên (File input) | UI/UX | `add_product.blade.php:160`<br>`edit_product.blade.php:123`<br>`add_brand.blade.php:19` | Nút vô nghĩa sót lại từ mẫu template, mất mỹ quan |
| **UI-12** | Thiếu giao diện Empty State trên toàn bộ bảng dữ liệu Admin | UI/UX | `show_product.blade.php:53`<br>`show_category.blade.php:45`<br>`orders/index.blade.php:38` | Khi chưa có dữ liệu bảng hiện khoảng trắng trơn thiếu chuyên nghiệp |
| **UI-13** | Lỗi chính tả tiếng Việt "Đang kinh doan" và thiếu badge sản phẩm | UI/UX | `resources/views/admin/product/show_product.blade.php:62,70,71` | Giá tiền không có dấu chấm, sai chính tả, trạng thái thiếu màu |
| **UI-14** | Lỗi JS Back-to-top do gọi hàm easing không tồn tại | UI/UX | `public/client/js/main.js:162` | Ném lỗi `jQuery.easing[this.easing] is not a function` khi cuộn |
| **FUNC-01** | Admin hoàn toàn không có tính năng cập nhật trạng thái đơn hàng | Functional | `resources/views/admin/orders/`<br>`app/Http/Controllers/OrderController.php` | Đơn hàng kẹt vĩnh viễn ở trạng thái "pending", không thể xử lý |
| **FUNC-02** | Toàn bộ form CRUD thiếu hiển thị `@error` và thiếu giữ lại `old()` | Functional | Các view `add_product`, `edit_product`, `add_category`, `add_brand`, `add_coupon` | Bị xóa trắng form khi nhập sai, không biết sai trường nào |
| **FUNC-03** | Toàn bộ Flash Messages đặt ngoài `@section` bị Blade nuốt mất | Functional | `show_category.blade.php:4-20`<br>`show_brand.blade.php:3-19`<br>`orders/index.blade.php:3-18` | Không có thông báo Thành công / Thất bại sau khi thao tác |
| **FUNC-04** | Xung đột DataTables client-side và Server-side pagination trên Đơn hàng | Functional | `resources/views/admin/orders/index.blade.php:26,71`<br>`public/admin/js/main.js:67` | Chỉ tìm kiếm được trong 20 bản ghi trang 1, xuất hiện 2 thanh phân trang |
| **FUNC-05** | Nút "Đóng" modal Coupon gọi sai ID modal không đóng được | Functional | `resources/views/admin/coupon/add_coupon.blade.php:73` | Người dùng bị kẹt modal, không đóng được bảng thêm coupon |
| **FUNC-06** | Nút "Hủy bỏ" modal sửa coupon là thẻ `<a>` gây reload toàn trang | Functional | `resources/views/admin/coupon/edit_coupon.blade.php:90` | Mất trải nghiệm AJAX modal, làm tải lại toàn bộ trang |
| **FUNC-07** | Modal chi tiết đơn hàng và script đặt sau `@endsection` | Functional | `resources/views/admin/orders/index.blade.php:76-122` | Nội dung bị in ra sau thẻ `</html>`, lỗi backdrop che đen màn hình |
| **FUNC-08** | Phương thức xóa đơn hàng `destroy()` không dùng DB Transaction | Functional | `app/Http/Controllers/OrderController.php:121-144` | Nguy cơ xóa chi tiết đơn hàng nhưng đơn hàng chính vẫn còn |
| **FUNC-09** | Kiểm tra và trừ tồn kho khi đặt hàng thiếu `lockForUpdate()` | Functional | `app/Http/Controllers/OrderController.php:269-275` | Race condition khi nhiều khách mua cùng lúc dẫn đến âm kho |
| **FUNC-10** | Form Đăng ký tài khoản thiếu validate unique email & confirmation | Functional | `app/Http/Controllers/AdminController.php:105-113` | Đăng ký trùng lặp email, không đối soát mật khẩu xác nhận |
| **FUNC-11** | Khách vãng lai bị chặn ở middleware `auth` khi bấm Đặt hàng | Functional | `routes/web.php:53`<br>`app/Http/Controllers/OrderController.php:190-202` | Mất khách hàng vãng lai muốn mua nhanh không cần lập nick |
| **IMP-01** | Tiền tố `asset('public/...')` phụ thuộc Windows Junction `public/public` | Improvements| Toàn bộ Blade Views & `public/public` | Toàn bộ CSS/JS/ảnh sẽ bị 404 khi chuyển sang máy chủ Linux/Docker |
| **IMP-02** | Layout Admin tải 12 scripts đồ thị/bản đồ demo không sử dụng | Improvements| `resources/views/layout/admin_layout.blade.php:145-177` | Lãng phí tài nguyên mạng, làm chậm tốc độ tải trang 2-3 lần |
| **IMP-03** | Thư viện DataTables hiển thị mặc định tiếng Anh | Improvements| `public/admin/js/main.js:67` | Trải nghiệm quản trị viên chưa thân thiện với người dùng Việt |
| **IMP-04** | View Composer truy vấn Category lặp lại trong mỗi request | Improvements| `app/Providers/AppServiceProvider.php:25-34` | Thừa truy vấn database, chưa tận dụng cache |
| **IMP-05** | Lộ Route Test mật khẩu `/test-password-reset/{email}` | Improvements| `routes/web.php:96-107` | Lỗ hổng rò rỉ thông tin token đặt lại mật khẩu |
| **IMP-06** | Thiếu phân trang Server-side cho Product, Category, Brand, User | Improvements| Các Controllers Admin (`show_product`, `show_category`, v.v.) | Nguy cơ tràn RAM PHP khi dữ liệu vượt quá vài ngàn bản ghi |
| **IMP-07** | Thiếu hệ thống giao diện Auth riêng biệt cho Khách hàng & Trang 404 | Improvements| `resources/views/client/auth/`<br>`resources/views/errors/` | Trải nghiệm người dùng thiếu tính chuyên nghiệp đồng bộ |

---

## 3. DANH MỤC LỖI CHI TIẾT VÀ GIẢI PHÁP KHẮC PHỤC HOÀN CHỈNH

---

### 3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues)

#### [CRIT-01] Tất cả thao tác Xóa (Delete) trong Admin dùng phương thức HTTP GET và không có bảo vệ CSRF
* **Tệp và số dòng vi phạm**:
  * `routes/web.php`: Dòng 60, 116, 126, 136, 142, 150.
  * `public/admin/js/main.js`: Dòng 14-17.
* **Mã nguồn thực tế bị lỗi**:
  ```php
  // routes/web.php:
  Route::get('/admin/orders/delete/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
  Route::get('/delete-brand/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');
  Route::get('/delete-product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
  Route::get('/delete-category/{id}', [CategoryController::class, 'destroy'])->name('delete-category');
  Route::get('/admin/users/delete/{id}', [AdminController::class, 'destroy_user'])->name('admin.users.destroy');
  Route::get('/delete-coupon/{id}', [CouponController::class, 'destroy'])->name('coupon.delete');
  ```
  ```javascript
  // public/admin/js/main.js:
  $.ajax({
      method: "get",
      url: url,
  })
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Vi phạm nguyên tắc thiết kế RESTful và chuẩn an toàn HTTP RFC 7231 (GET phải là phương thức an toàn và mang tính idempotent).
  * *Tác động*: Lỗ hổng bảo mật CSRF (Cross-Site Request Forgery) cực kỳ nghiêm trọng. Kẻ tấn công có thể chèn link `<img src="http://domain.com/delete-product/1">` trên một diễn đàn bên ngoài; khi Quản trị viên đang đăng nhập ghé thăm, sản phẩm sẽ bị xóa ngay lập tức. Ngoài ra, các bộ máy thu thập dữ liệu (Google Bot, Bing Bot) hoặc tính năng tải trước liên kết của trình duyệt (Link Prefetching) sẽ tự động duyệt các link GET này và xóa sạch toàn bộ cơ sở dữ liệu.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  1. Trong `routes/web.php`, đổi toàn bộ sang `Route::delete`:
     ```php
     Route::delete('/admin/orders/delete/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
     Route::delete('/delete-brand/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');
     Route::delete('/delete-product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
     Route::delete('/delete-category/{id}', [CategoryController::class, 'destroy'])->name('delete-category');
     Route::delete('/admin/users/delete/{id}', [AdminController::class, 'destroy_user'])->name('admin.users.destroy');
     Route::delete('/delete-coupon/{id}', [CouponController::class, 'destroy'])->name('coupon.delete');
     ```
  2. Trong `resources/views/layout/admin_layout.blade.php`, bổ sung meta token vào thẻ `<head>`:
     ```blade
     <meta name="csrf-token" content="{{ csrf_token() }}">
     ```
  3. Trong `public/admin/js/main.js`, sửa hàm `DeleteData(url)`:
     ```javascript
     function DeleteData(url) {
         Swal.fire({
             title: "Xác nhận xóa?",
             text: "Dữ liệu sau khi xóa sẽ không thể phục hồi!",
             icon: "warning",
             showCancelButton: true,
             confirmButtonText: "Đồng ý",
             cancelButtonText: "Hủy bỏ",
             reverseButtons: true
         }).then((result) => {
             if (result.value || result.isConfirmed) {
                 $.ajax({
                     method: "DELETE",
                     url: url,
                     headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     }
                 }).done(function (res) {
                     toastr.success("Xóa dữ liệu thành công!");
                     setTimeout(() => window.location.reload(), 800);
                 }).fail(function (xhr) {
                     toastr.error("Có lỗi xảy ra khi xóa dữ liệu!", "Lỗi");
                 });
             }
         });
     }
     ```

---

#### [CRIT-02] Route `/thanh-toan` gọi method không tồn tại `CheckoutController@processOrder`
* **Tệp và số dòng vi phạm**:
  * `routes/web.php`: Dòng 46.
  * `app/Http/Controllers/CheckoutController.php`.
* **Mã nguồn thực tế bị lỗi**:
  ```php
  // routes/web.php (dòng 46):
  Route::post('/thanh-toan', [CheckoutController::class, 'processOrder'])->name('checkout.process');
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Khai báo route ánh xạ tới phương thức `processOrder` của `CheckoutController`, nhưng trong Controller này chỉ có các phương thức `show_checkout()` và stub `place_oder()`.
  * *Tác động*: Bất kỳ request nào gửi tới `/thanh-toan` hoặc gọi `route('checkout.process')` sẽ gây crash ứng dụng với ngoại lệ `BadMethodCallException: Method App\Http\Controllers\CheckoutController::processOrder does not exist` (Mã lỗi HTTP 500).
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Sửa trong `routes/web.php` dòng 46 để ánh xạ đúng vào phương thức xử lý đặt hàng đã có của `OrderController`:
  ```php
  Route::post('/thanh-toan', [App\Http\Controllers\OrderController::class, 'placeOrder'])->name('checkout.process');
  ```

---

#### [CRIT-03] View gọi named route `route('register')` chưa từng được định nghĩa
* **Tệp và số dòng vi phạm**:
  * `resources/views/welcome.blade.php`: Dòng 43.
  * `routes/web.php`: Dòng 83.
* **Mã nguồn thực tế bị lỗi**:
  ```blade
  {{-- resources/views/welcome.blade.php (dòng 43): --}}
  <a href="{{ route('register') }}" class="...">Register</a>
  ```
  ```php
  // routes/web.php (dòng 83):
  Route::get('/register-admin', [AdminController::class, 'register_admin']); // Không có ->name('register')
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Template `resources/views/welcome.blade.php` mặc định của Laravel gọi helper `route('register')`, nhưng route đăng ký trong dự án được tùy biến thành `/register-admin` và lập trình viên chưa gán tên định danh `->name('register')`.
  * *Tác động & Sắc thái kỹ thuật (Technical Nuance)*: Trong `resources/views/welcome.blade.php` (dòng 41-47), lệnh gọi `route('register')` được bảo vệ an toàn bởi khối điều kiện `@if (Route::has('register'))`. Khi route chưa được đặt tên, `Route::has('register')` trả về `false` giúp nút "Register" được ẩn đi một cách êm đẹp (graceful degradation) chứ không làm trang bị fatal crash 500. Tuy nhiên, nếu bất kỳ thành phần nào khác trong tương lai (middleware chuyển hướng, blade template tùy chỉnh, email verification hoặc auth flow) gọi trực tiếp `route('register')`, ứng dụng sẽ lập tức ném ngoại lệ nghiêm trọng: `Symfony\Component\Routing\Exception\RouteNotFoundException: Route [register] not defined`. Do đó, việc đặt tên route chuẩn hóa là bắt buộc để đồng bộ hệ thống định tuyến Laravel.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Bổ sung tên định danh `name('register')` vào `routes/web.php` dòng 83:
  ```php
  Route::get('/register-admin', [AdminController::class, 'register_admin'])->name('register');
  ```

---

#### [CRIT-04] Truy vấn cột `status` không tồn tại trong bảng `_category`
* **Tệp và số dòng vi phạm**:
  * `app/Http/Controllers/HomeController.php`: Dòng 29.
  * `routes/web.php`: Dòng 22 (`Route::get('/show-category-home', [HomeController::class, 'show_category_home']);`).
* **Mã nguồn thực tế bị lỗi**:
  ```php
  // app/Http/Controllers/HomeController.php (dòng 29):
  public function show_category_home()
  {
      $categories = Category::where('status', 1)->orderBy('name')->get();
      return view('layout.home_layout', compact('categories'));
  }
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Bảng `_category` trong cơ sở dữ liệu chỉ được thiết kế với các cột `id`, `name`, `description`, `ImageURL`, `created_at`, `updated_at`. Cột `status` hoàn toàn không tồn tại trong schema.
  * *Tác động*: Khi người dùng hoặc API truy cập URL `/show-category-home`, ứng dụng crash ngay lập tức:
    `Illuminate\Database\QueryException: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'status' in 'where clause'`.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Sửa trong `app/Http/Controllers/HomeController.php`:
  ```php
  public function show_category_home()
  {
      $categories = Category::orderBy('name')->get();
      return view('layout.home_layout', compact('categories'));
  }
  ```

---

#### [CRIT-05] `OrderController@storeFromCart` ghi sai cấu trúc bảng `orders` và thiếu các trường NOT NULL
* **Tệp và số dòng vi phạm**:
  * `app/Http/Controllers/OrderController.php`: Dòng 86-105.
* **Mã nguồn thực tế bị lỗi**:
  ```php
  // app/Http/Controllers/OrderController.php:
  $order = Order::create([
      'user_id' => $user->id,
      'unitPrice' => $totalPrice, // Cột không tồn tại
      'quantity' => $items->sum('quantity'), // Cột không tồn tại
      'totalPrice' => $totalPrice, // Cột không tồn tại trong DB, DB là total_amount
  ]);
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Đoạn mã legacy chép từ phiên bản cũ sử dụng các cột `unitPrice`, `quantity`, `totalPrice` không có trong migration của bảng `orders`. Đồng thời bỏ qua các cột bắt buộc `NOT NULL` của bảng như `shipping_name`, `shipping_email`, `shipping_phone`, `shipping_address`, `total_amount`. Đặc biệt, cấu trúc CSDL của dự án tách biệt giữa bảng `carts` (Header giỏ hàng: `id`, `user_id`, `totalAmount`) và bảng `cart_items` (Chi tiết giỏ hàng: `id`, `cart_id`, `product_id`, `quantity`). Mã xử lý phải truy vấn qua Model `Cart` và quan hệ `$cart->cartItems()` mới có thể trích xuất chính xác số lượng (`quantity`) và sản phẩm (`product`).
  * *Tác động*: Khi người dùng checkout từ giỏ hàng thông qua route `orders.store_from_cart`, hệ thống ném ngoại lệ cơ sở dữ liệu: `QueryException: Field 'shipping_name' doesn't have a default value`, quy trình đặt hàng bị gãy hoàn toàn. Nếu viết sai cấu trúc `Cart` thay vì `CartItem`, đơn hàng tạo ra sẽ bị số lượng 0 hoặc xóa nhầm bản ghi header giỏ hàng và để lại dữ liệu mồ côi trong `cart_items`.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Cập nhật phương thức `storeFromCart` trong `app/Http/Controllers/OrderController.php`:
  ```php
  public function storeFromCart(Request $request)
  {
      $user = Auth::user();
      
      // 1. Lấy giỏ hàng của user từ bảng carts
      $cart = \App\Models\Cart::where('user_id', $user->id)->first();
      if (!$cart) {
          return redirect()->route('cart')->with('error', 'Giỏ hàng của bạn đang trống.');
      }

      // 2. Lấy danh sách các mặt hàng trong giỏ từ bảng cart_items qua quan hệ cartItems
      $items = $cart->cartItems()->with('product')->get();
      if ($items->isEmpty()) {
          return redirect()->route('cart')->with('error', 'Giỏ hàng của bạn đang trống.');
      }

      // 3. Tính tổng tiền từ chi tiết giỏ hàng
      $totalPrice = $items->sum(function ($item) {
          return (float) ($item->product->price ?? 0) * (int) $item->quantity;
      });

      DB::beginTransaction();
      try {
          // 4. Tạo đơn hàng với đầy đủ các trường NOT NULL theo đúng migration orders
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

          // 5. Tạo chi tiết đơn hàng vào bảng oder_items
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

          // 6. Xóa chi tiết các mặt hàng trong giỏ và reset tổng tiền bảng carts
          $cart->cartItems()->delete();
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

#### [CRIT-06] `CartController@checkCoupon` truy cập trực tiếp key mảng không qua validation
* **Tệp và số dòng vi phạm**:
  * `app/Http/Controllers/CartController.php`: Dòng 431-435.
* **Mã nguồn thực tế bị lỗi**:
  ```php
  public function checkCoupon(Request $request)
  {
      $data = $request->all();
      $coupon = Coupon::where('code', $data['code_input'])->first();
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Truy cập chỉ mục `$data['code_input']` mà không kiểm tra sự tồn tại hoặc validate dữ liệu đầu vào.
  * *Tác động*: Khi khách hàng nhấn nút áp dụng mã giảm giá nhưng để trống ô nhập hoặc request gửi payload không đúng định dạng, PHP ném ngoại lệ Fatal: `ErrorException: Undefined array key "code_input"`, trả về lỗi HTTP 500.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Sửa trong `app/Http/Controllers/CartController.php`:
  ```php
  public function checkCoupon(Request $request)
  {
      $request->validate([
          'code_input' => 'required|string|max:50',
      ], [
          'code_input.required' => 'Vui lòng nhập mã giảm giá.',
      ]);

      $codeInput = trim($request->input('code_input'));
      $coupon = Coupon::where('code', $codeInput)->first();

      if (!$coupon) {
          return redirect()->back()->with('error', 'Mã giảm giá không tồn tại.');
      }

      if ($coupon->expiry_date && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($coupon->expiry_date))) {
          return redirect()->back()->with('error', 'Mã giảm giá đã hết hạn sử dụng.');
      }

      if ($coupon->quantity <= 0) {
          return redirect()->back()->with('error', 'Mã giảm giá đã hết lượt sử dụng.');
      }

      Session::put('coupon', [
          'id'    => $coupon->id,
          'code'  => $coupon->code,
          'type'  => $coupon->type,
          'value' => $coupon->value,
      ]);

      return redirect()->back()->with('success', 'Áp dụng mã giảm giá thành công!');
  }
  ```

---

#### [CRIT-07] Truy cập `$item->category->name` thiếu null-safe operator gây Crash Fatal
* **Tệp và số dòng vi phạm**:
  * `resources/views/admin/product/show_product.blade.php`: Dòng 67.
  * `resources/views/client/home/product_category.blade.php`: Dòng 173.
  * `resources/views/client/home/index_home.blade.php`: Dòng 273, 363.
* **Mã nguồn thực tế bị lỗi**:
  ```blade
  {{-- show_product.blade.php (dòng 67): --}}
  <td>{{ $item->category->name }}</td>

  {{-- product_category.blade.php (dòng 173): --}}
  <a href="#" class="d-block mb-2">{{ $item->category->name }}</a>
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Truy cập trực tiếp thuộc tính `name` trên quan hệ `$item->category` mà không dùng toán tử null-safe (`?->`) hoặc null coalescing (`??`).
  * *Tác động*: Nếu có bất kỳ sản phẩm nào có `category_id` bị mồ côi (do danh mục đã bị xóa hoặc dữ liệu seed không khớp), Eloquent trả về `null`. Khi render, Blade sẽ ném lỗi: `Error: Attempt to read property 'name' on null`, làm tê liệt toàn bộ trang quản lý sản phẩm của Admin và trang chủ/danh mục của Client.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  * Trong `resources/views/admin/product/show_product.blade.php` dòng 67:
    ```blade
    <td>{{ $item->category?->name ?? 'Chưa phân loại' }}</td>
    ```
  * Trong `resources/views/client/home/product_category.blade.php` dòng 173:
    ```blade
    <a href="#" class="d-block mb-2">{{ $item->category?->name ?? 'Sản phẩm' }}</a>
    ```
  * Trong `resources/views/client/home/index_home.blade.php` dòng 273 & 363:
    ```blade
    <a href="{{ route('product.detail', $product->id) }}" class="d-block mb-2">
        {{ $product->category?->name ?? 'Sản phẩm' }}
    </a>
    ```

---

#### [CRIT-08] Lỗ hổng Mass Assignment leo thang đặc quyền qua cột `role` trong Model `User`
* **Tệp và số dòng vi phạm**:
  * `app/Models/User.php`: Dòng 21-28.
* **Mã nguồn thực tế bị lỗi**:
  ```php
  protected $fillable = [
      'name',
      'email',
      'password',
      'role',        // <--- LỖ HỔNG LEO THANG ĐẶC QUYỀN
      'phoneNumber',
      'IsActive',
  ];
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Cột phân quyền `role` được khai báo trực tiếp trong thuộc tính `$fillable` của Model Eloquent.
  * *Tác động*: Kẻ tấn công có thể chèn tham số `role=admin` vào payload của form đăng ký hoặc cập nhật hồ sơ (`User::create($request->all())`), từ đó tự động nâng quyền tài khoản cá nhân lên thành Quản trị viên tối cao (Privilege Escalation), chiếm quyền điều khiển website.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Sửa trong `app/Models/User.php`:
  ```php
  protected $fillable = [
      'name',
      'email',
      'password',
      'phoneNumber',
      'IsActive',
      // Loại bỏ 'role' khỏi $fillable. Mọi thao tác gán quyền phải thực hiện tường minh: $user->role = 'admin'; $user->save();
  ];
  ```

---

#### [CRIT-09] Lỗ hổng Admin tự giáng quyền dẫn tới khóa tài khoản vĩnh viễn
* **Tệp và số dòng vi phạm**:
  * `resources/views/admin/auth/users.blade.php`: Dòng 34-41.
  * `app/Http/Controllers/AdminController.php`: Dòng 66-75.
* **Mã nguồn thực tế bị lỗi**:
  ```blade
  {{-- users.blade.php: --}}
  <form action="{{ route('admin.users.role', $u->id) }}" method="POST" class="d-flex align-items-center">
      @csrf
      <select name="role" class="form-control mr-2" style="max-width: 180px;">
          <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>User</option>
          <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
      </select>
      <button type="submit" class="btn btn-primary">Gán quyền</button>
  </form>
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Hệ thống có chặn không cho Admin tự xóa tài khoản của mình (`if ($u->id === auth()->id())`), nhưng ở cột phân quyền lại không có kiểm tra tương tự. Controller cũng không xác thực logic này.
  * *Tác động*: Quản trị viên thao tác nhầm đổi quyền của chính mình từ `admin` thành `user`. Ngay khi lưu, `AdminMiddleware` sẽ từ chối truy cập và tài khoản bị khóa vĩnh viễn bên ngoài khu vực quản trị.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  1. Trong `resources/views/admin/auth/users.blade.php` dòng 34:
     ```blade
     @if ($u->id === auth()->id())
         <span class="badge badge-success px-3 py-2"><i class="fa fa-shield"></i> Quản trị viên (Bạn)</span>
     @else
         <form action="{{ route('admin.users.role', $u->id) }}" method="POST" class="d-flex align-items-center">
             @csrf
             <select name="role" class="form-control form-control-sm mr-2" style="max-width: 140px;">
                 <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>User</option>
                 <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
             </select>
             <button type="submit" class="btn btn-sm btn-primary">Gán quyền</button>
         </form>
     @endif
     ```
  2. Trong `app/Http/Controllers/AdminController.php` phương thức `update_user_role`:
     ```php
     public function update_user_role(Request $request, $id)
     {
         if (Auth::id() == $id && $request->role !== 'admin') {
             return redirect()->back()->with('error', 'Bạn không thể tự hạ quyền quản trị của chính mình!');
         }
         
         $user = User::findOrFail($id);
         $user->role = $request->input('role') === 'admin' ? 'admin' : 'user';
         $user->save();

         return redirect()->back()->with('success', 'Cập nhật phân quyền thành công!');
     }
     ```

---

#### [CRIT-10] Cú pháp Blade vỡ nút xem chi tiết nhanh `href="    }}"` trên Trang chủ
* **Tệp và số dòng vi phạm**:
  * `resources/views/client/home/index_home.blade.php`: Dòng 268.
* **Mã nguồn thực tế bị lỗi**:
  ```blade
  <div class="product-details">
      <a href="    }}"><i class="fa fa-eye fa-1x"></i></a>
  </div>
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Lỗi thao tác khi chỉnh sửa mã nguồn, xóa mất tên hàm route nhưng để sót lại khoảng trắng và cặp dấu đóng `}}`.
  * *Tác động*: Nút xem nhanh sản phẩm trên trang chủ không hoạt động, sinh ra mã HTML không hợp lệ gây lỗi định dạng DOM.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Sửa dòng 268 trong `resources/views/client/home/index_home.blade.php`:
  ```blade
  <div class="product-details">
      <a href="{{ route('product.detail', $product->id) }}"><i class="fa fa-eye fa-1x"></i></a>
  </div>
  ```

---

#### [CRIT-11] Sai lệch giá tiền và khuyến mãi Top bán chạy do lỗi Variable Scope
* **Tệp và số dòng vi phạm**:
  * `resources/views/client/home/index_home.blade.php`: Dòng 422-448.
* **Mã nguồn thực tế bị lỗi**:
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
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Trong vòng lặp `@foreach ($best_seller_product as $product)`, lập trình viên không khai báo khối `@php` để tính lại `$price`, `$percent`, `$discounted` cho sản phẩm hiện tại của vòng lặp.
  * *Tác động*: Vòng lặp lấy lại giá trị của các biến `$price`, `$discounted` từ **sản phẩm cuối cùng của vòng lặp `$all_product` trước đó**. Kết quả là 100% sản phẩm trong khối "Top bán chạy" đều hiển thị sai giá và sai mức giảm giá. Nếu danh sách trước rỗng, trang sẽ crash với lỗi `Undefined variable $price`.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Sửa từ dòng 440 đến 448 trong `resources/views/client/home/index_home.blade.php`:
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

#### [CRIT-12] Sai lệch hiển thị phương thức thanh toán trên trang thông báo thành công
* **Tệp và số dòng vi phạm**:
  * `resources/views/client/checkout/checkout_success.blade.php`: Dòng 43-51.
* **Mã nguồn thực tế bị lỗi**:
  ```blade
  @if (($order->payment_method ?? 'cod') == 'cod')
      Thanh toán khi nhận hàng (COD)
  @elseif(($order->payment_method ?? '') == 'vnpay')
      Thanh toán qua VNPAY
  @else
      Thanh toán qua Ngân hàng
  @endif
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Trong `OrderController.php` dòng 257, phương thức thanh toán được lưu trữ dạng in hoa: `'COD'`, `'VNPAY'`, `'MOMO'`. Phép so sánh chuỗi trong PHP `('COD' == 'cod')` luôn trả về `false`.
  * *Tác động*: Mọi đơn hàng thanh toán COD hoặc VNPAY sau khi đặt hàng thành công đều bị hiển thị sai thành "Thanh toán qua Ngân hàng", gây hoang mang cho khách hàng vì tưởng rằng đơn hàng chưa được xác nhận hoặc phải chuyển khoản.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Sửa trong `resources/views/client/checkout/checkout_success.blade.php`:
  ```blade
  @php
      $method = strtoupper($order->payment_method ?? 'COD');
  @endphp
  @if ($method === 'COD')
      Thanh toán khi nhận hàng (COD)
  @elseif ($method === 'VNPAY')
      Thanh toán trực tuyến qua VNPAY
  @elseif ($method === 'MOMO')
      Thanh toán qua Ví điện tử MoMo
  @else
      Thanh toán chuyển khoản qua Ngân hàng
  @endif
  ```

---

#### [CRIT-13] Lỗ hổng IDOR trên `OrderController@showSuccess` làm lộ lọt toàn bộ thông tin định danh cá nhân (PII) của khách hàng
* **Tệp và số dòng vi phạm**:
  * `app/Http/Controllers/OrderController.php`: Dòng 315-325 (`showSuccess`).
  * `resources/views/client/checkout/checkout_success.blade.php`: Dòng 28-40.
* **Mã nguồn thực tế bị lỗi**:
  ```php
  // app/Http/Controllers/OrderController.php (dòng 310-322):
  public function showSuccess($id)
  {
      // Lấy đơn hàng kèm theo chi tiết sản phẩm
      $order = Order::with('orderItems')->findOrFail($id);

      // Kiểm tra quyền: Chỉ cho xem nếu là chủ đơn hàng
      if (Auth::check() && $order->user_id !== Auth::id()) {
          abort(403);
      }
      $categories = \App\Models\Category::all();

      return view('client.checkout.checkout_success', compact('order', 'categories'));
  }
  ```
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân (Root Cause)*: Lỗ hổng kiểm soát truy cập trực tiếp đối tượng (Insecure Direct Object References - IDOR). Câu lệnh kiểm tra quyền: `if (Auth::check() && $order->user_id !== Auth::id()) abort(403);` chỉ được kích hoạt khi người dùng **đã đăng nhập** (`Auth::check() == true`). Khi bất kỳ ai truy cập với tư cách **khách vãng lai chưa đăng nhập** (`!Auth::check()`), điều kiện kiểm tra bị bỏ qua hoàn toàn (`false && ...` trả về `false`).
  * *Tác động (Impact)*: Bất kỳ ai không cần đăng nhập đều có thể truy cập `/dat-hang-thanh-cong/{id}` và dễ dàng vét cạn (enumerate/scrape) thông tin bằng cách tăng dần ID đơn hàng (`/dat-hang-thanh-cong/1`, `/2`, `/3`...). Trang `checkout_success.blade.php` sẽ in ra toàn bộ dữ liệu định danh cá nhân (PII) cực kỳ nhạy cảm của khách hàng, bao gồm:
    * Họ và tên người nhận (`$order->shipping_name`)
    * Số điện thoại cá nhân (`$order->shipping_phone`)
    * Địa chỉ nhà ở chi tiết (`$order->shipping_address`)
    * Địa chỉ email (`$order->shipping_email`)
    * Danh sách toàn bộ sản phẩm đã mua, số lượng, đơn giá và tổng số tiền thanh toán
  * *Nguy cơ mở rộng*: Khi áp dụng giải pháp tại [FUNC-11] (cho phép khách vãng lai đặt hàng và mở route `order.success` ra ngoài middleware `auth`), nếu không vá lỗi IDOR này thì toàn bộ CSDL đơn hàng và thông tin khách hàng của website sẽ bị lộ lọt 100% trên Internet.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Áp dụng cơ chế xác thực phiên (Session-based verification) hoặc Signed URLs để đảm bảo chỉ người tạo đơn trong phiên làm việc hiện tại hoặc chính chủ tài khoản mới được xem thông tin đơn hàng:

  *Phương án 1: Bảo vệ bằng Session Flash kết hợp kiểm tra User ID (Khuyên dùng cho Web Flow)*:
  Trong `app/Http/Controllers/OrderController.php`, cập nhật hàm `showSuccess`:
  ```php
  public function showSuccess($id)
  {
      $order = Order::with('orderItems')->findOrFail($id);

      // Phòng chống IDOR:
      if (Auth::check()) {
          // Nếu user đã đăng nhập: Phải đúng là chủ sở hữu đơn hàng
          if ($order->user_id !== Auth::id()) {
              abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
          }
      } else {
          // Nếu là khách vãng lai: Bắt buộc phải có token đơn hàng vừa đặt trong Session
          if (session('placed_order_id') != $order->id) {
              abort(403, 'Phiên làm việc đã hết hạn hoặc bạn không có quyền xem đơn hàng này.');
          }
      }

      $categories = \App\Models\Category::all();
      return view('client.checkout.checkout_success', compact('order', 'categories'));
  }
  ```
  Đồng thời, tại các điểm tạo đơn hàng thành công trong `OrderController.php` (`placeOrder` dòng 303 và `storeFromCart`):
  ```php
  // Lưu ID đơn hàng vào session flash trước khi redirect
  session()->flash('placed_order_id', $order->id);

  return redirect()->route('order.success', ['id' => $order->id])->with('success', 'Đặt hàng thành công!');
  ```

  *Phương án 2: Sử dụng Laravel Signed Route (Tùy chọn nâng cao)*:
  ```php
  // Tại Controller sau khi đặt hàng:
  return redirect()->to(URL::temporarySignedRoute(
      'order.success',
      now()->addMinutes(30),
      ['id' => $order->id]
  ))->with('success', 'Đặt hàng thành công!');

  // Trong routes/web.php:
  Route::get('/dat-hang-thanh-cong/{id}', [OrderController::class, 'showSuccess'])
      ->name('order.success')
      ->middleware('signed');
  ```

---

### 3.2. Nhóm 2: Lỗi Giao diện & Hiển thị (UI/UX Issues)

#### [UI-01] Mất hoàn toàn thanh tìm kiếm, giỏ hàng và menu tài khoản trên màn hình di động
* **Tệp và số dòng vi phạm**:
  * `resources/views/layout/home_layout.blade.php`: Dòng 84, 122, 168-206.
* **Mã nguồn thực tế bị lỗi**:
  * Dòng 84: Khối Topbar (Help, Phone, My Dashboard / Đăng nhập) gán class `d-none d-lg-block`.
  * Dòng 122: Khối Brand, Ô tìm kiếm (`#keywords`) và Cart Counter gán class `d-none d-lg-block`.
  * Dòng 168-206: Khối Navbar trên mobile (`< lg`) chỉ có nút hamburger mở danh mục danh mục và số hotline.
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Ẩn toàn bộ header chính trên màn hình `< 992px` nhưng không bố trí các nút chức năng thay thế vào thanh navigation mobile.
  * *Tác động*: Trên điện thoại và máy tính bảng, khách hàng không có cách nào tìm kiếm sản phẩm, không thể xem giỏ hàng và không thể đăng nhập/quản lý tài khoản.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Bổ sung cụm điều hướng mobile ngay cạnh nút hamburger (trước thẻ `<button class="navbar-toggler">` ở dòng 178 `resources/views/layout/home_layout.blade.php`):
  ```blade
  <div class="d-flex align-items-center d-lg-none me-3">
      <a href="{{ route('cart') }}" class="position-relative text-white me-3" title="Giỏ hàng">
          <i class="fas fa-shopping-cart fa-lg"></i>
          <span class="cart-counter position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
      </a>
      @auth
          <a href="{{ route('profile') }}" class="text-white me-2" title="Tài khoản cá nhân"><i class="fas fa-user-circle fa-lg"></i></a>
      @else
          <a href="{{ route('login') }}" class="text-white me-2" title="Đăng nhập"><i class="fas fa-sign-in-alt fa-lg"></i></a>
      @endauth
  </div>
  ```

---

#### [UI-02] Chế độ xem danh sách (List View tab `#tab-6`) bị rỗng trắng tinh
* **Tệp và số dòng vi phạm**:
  * `resources/views/client/home/product_category.blade.php`: Dòng 148-153 và 243-260.
* **Mã nguồn thực tế bị lỗi**:
  Nút kích hoạt tab `#tab-6` có sẵn, nhưng khối nội dung `#tab-6` bên trong bị để trống:
  ```blade
  <div id="tab-6" class="products tab-pane fade show p-0">
      <div class="row g-4 products-mini">
          <!-- Bị bỏ trống hoàn toàn -->
      </div>
  </div>
  ```
* **Phân tích nguyên nhân & Tác động**:
  * Khi khách hàng bấm vào biểu tượng xem dạng danh sách (List view icon), màn hình ẩn tab lưới và hiển thị tab trống, khiến tất cả sản phẩm biến mất.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Điền nội dung hoàn chỉnh cho tab `#tab-6` trong `resources/views/client/home/product_category.blade.php`:
  ```blade
  <div id="tab-6" class="products tab-pane fade p-0">
      <div class="row g-3">
          @forelse ($product as $item)
              <div class="col-12">
                  <div class="card border rounded p-3 d-flex flex-row align-items-center shadow-sm">
                      <img src="{{ asset('public/uploads/products/' . $item->imageURL) }}" 
                           style="width: 110px; height: 110px; object-fit: cover;" 
                           class="rounded me-3" alt="{{ $item->name }}">
                      <div class="flex-grow-1">
                          <h5 class="mb-1"><a href="{{ route('product.detail', $item->id) }}" class="text-dark">{{ $item->name }}</a></h5>
                          <p class="text-muted small mb-1">{{ $item->category?->name ?? 'Sản phẩm' }}</p>
                          <p class="text-danger fw-bold mb-0 fs-5">{{ number_format($item->price, 0, ',', '.') }} VNĐ</p>
                      </div>
                      <div class="ms-3">
                          <button class="btn btn-primary rounded-pill px-4 py-2 add-to-cart-btn" 
                                  data-product-id="{{ $item->id }}" 
                                  data-authenticated="{{ Auth::check() ? 'true' : 'false' }}">
                              <i class="fas fa-shopping-cart me-1"></i> Thêm giỏ
                          </button>
                      </div>
                  </div>
              </div>
          @empty
              <div class="col-12 text-center py-5 text-muted">Không tìm thấy sản phẩm nào trong danh mục này.</div>
          @endforelse
      </div>
  </div>
  ```

---

#### [UI-03] Lệch số lượng cột trong bảng hóa đơn Checkout
* **Tệp và số dòng vi phạm**:
  * `resources/views/client/checkout/checkout_index.blade.php`: Dòng 153-257.
* **Mã nguồn thực tế bị lỗi**:
  `<thead>` định nghĩa 4 cột (`Tên sản phẩm`, `Đơn giá`, `Số lượng`, `Tổng tiền`). Tuy nhiên ở hàng Tạm tính (dòng 177) và Tổng cộng (dòng 241), code lại render 5 thẻ (`th` + 4 `td`).
* **Phân tích nguyên nhân & Tác động**:
  * Bảng HTML bị vi phạm số lượng cột, viền bảng bị đứt gãy, cột tính tiền bị nhảy lệch sang phải.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Sửa lại hàng Tạm tính và Tổng cộng trong `resources/views/client/checkout/checkout_index.blade.php`:
  ```blade
  {{-- Hàng Tạm tính: --}}
  <tr>
      <th scope="row" colspan="2" class="py-3 text-start">
          <span class="text-dark fw-bold">Tạm tính tiền hàng</span>
      </th>
      <td class="py-3 text-center"></td>
      <td class="py-3 text-end">
          <span class="text-dark fw-bold">{{ number_format($subtotal ?? 0, 0, ',', '.') }} VNĐ</span>
      </td>
  </tr>

  {{-- Hàng TỔNG CỘNG: --}}
  <tr>
      <th scope="row" colspan="2" class="py-3 text-start">
          <span class="text-dark text-uppercase fw-bold">TỔNG CỘNG</span>
      </th>
      <td class="py-3 text-center"></td>
      <td class="py-3 text-end">
          <span class="text-danger fs-5 fw-bold">{{ number_format($totalPrice ?? 0, 0, ',', '.') }} VNĐ</span>
      </td>
  </tr>
  ```

---

#### [UI-04] Nút "Xóa tìm kiếm" sản phẩm admin trỏ vào route `/shop` không tồn tại
* **Tệp và số dòng vi phạm**:
  * `resources/views/admin/product/show_product.blade.php`: Dòng 9.
* **Mã nguồn thực tế bị lỗi**:
  ```blade
  <a href="{{ url('/shop') }}" class="btn btn-sm btn-outline-secondary">Xóa tìm kiếm</a>
  ```
* **Phân tích nguyên nhân & Tác động**:
  * Route `/shop` không hề tồn tại trong `routes/web.php`. Bấm nút này gây lỗi 404 Not Found.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Sửa dòng 9 thành:
  ```blade
  <a href="{{ url('/show-product') }}" class="btn btn-sm btn-outline-secondary">Xóa tìm kiếm</a>
  ```

---

#### [UI-05] Khách mua hàng bấm Đăng nhập bị điều hướng vào Admin (`/admin`)
* **Tệp và số dòng vi phạm**:
  * `public/client/js/cart.js`: Dòng 85.
  * `resources/views/client/cart/show_cart.blade.php`: Dòng 10.
* **Mã nguồn thực tế bị lỗi**:
  ```javascript
  // cart.js:
  window.location.href = "/admin";
  ```
  ```blade
  <!-- show_cart.blade.php: -->
  <a href="{{ route('admin') }}" class="btn btn-primary">Đăng nhập ngay</a>
  ```
* **Phân tích nguyên nhân & Tác động**:
  * Đưa khách hàng phổ thông vào màn hình đăng nhập có tiêu đề Quản trị viên, gây hoang mang và vi phạm nguyên tắc bảo mật phân vùng người dùng.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  * Trong `public/client/js/cart.js` dòng 85: đổi `"/admin"` thành `"/login"`.
  * Trong `resources/views/client/cart/show_cart.blade.php` dòng 10: đổi `route('admin')` thành `route('login')`.

---

#### [UI-06] Dashboard Admin (`/admin/dashboard`) hoàn toàn trống rỗng
* **Tệp và số dòng vi phạm**:
  * `app/Http/Controllers/AdminController.php`: Dòng 21-24.
  * `resources/views/layout/admin_layout.blade.php`: Dòng 135.
* **Mã nguồn thực tế bị lỗi**:
  ```php
  public function show_dasboard()
  {
      return view("layout.admin_layout"); // Trả về layout mà không có view con!
  }
  ```
* **Phân tích nguyên nhân & Tác động**:
  * Khu vực `@yield('view-content')` không có nội dung, trang Dashboard chỉ có thanh sidebar và header, phần thân trắng tinh.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  1. Tạo file `resources/views/admin/dashboard.blade.php`:
     ```blade
     @extends('layout.admin_layout')
     @section('view-content')
     <div class="container-fluid mt-3">
         <div class="row">
             <div class="col-lg-3 col-sm-6">
                 <div class="card gradient-1">
                     <div class="card-body">
                         <h3 class="card-title text-white">Tổng Sản Phẩm</h3>
                         <div class="d-inline-block">
                             <h2 class="text-white">{{ $totalProducts }}</h2>
                         </div>
                         <span class="float-right display-5 opacity-5"><i class="fa fa-shopping-cart"></i></span>
                     </div>
                 </div>
             </div>
             <div class="col-lg-3 col-sm-6">
                 <div class="card gradient-2">
                     <div class="card-body">
                         <h3 class="card-title text-white">Tổng Đơn Hàng</h3>
                         <div class="d-inline-block">
                             <h2 class="text-white">{{ $totalOrders }}</h2>
                         </div>
                         <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                     </div>
                 </div>
             </div>
             <div class="col-lg-3 col-sm-6">
                 <div class="card gradient-3">
                     <div class="card-body">
                         <h3 class="card-title text-white">Khách Hàng</h3>
                         <div class="d-inline-block">
                             <h2 class="text-white">{{ $totalUsers }}</h2>
                         </div>
                         <span class="float-right display-5 opacity-5"><i class="fa fa-users"></i></span>
                     </div>
                 </div>
             </div>
             <div class="col-lg-3 col-sm-6">
                 <div class="card gradient-4">
                     <div class="card-body">
                         <h3 class="card-title text-white">Doanh Thu</h3>
                         <div class="d-inline-block">
                             <h2 class="text-white">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h2>
                         </div>
                         <span class="float-right display-5 opacity-5"><i class="fa fa-line-chart"></i></span>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     @endsection
     ```
  2. Sửa hàm `show_dasboard` trong `app/Http/Controllers/AdminController.php`:
     ```php
     public function show_dasboard()
     {
         $totalProducts = \App\Models\Product::count();
         $totalOrders   = \App\Models\Order::count();
         $totalRevenue  = \App\Models\Order::where('status', '!=', 'cancelled')->sum('total_amount');
         $totalUsers    = \App\Models\User::where('role', 'user')->count();

         return view('admin.dashboard', compact('totalProducts', 'totalOrders', 'totalRevenue', 'totalUsers'));
     }
     ```

---

#### [UI-07] Script `dashboard-1.js` ném Uncaught TypeError trên TẤT CẢ các trang Admin
* **Tệp và số dòng vi phạm**:
  * `resources/views/layout/admin_layout.blade.php`: Dòng 173.
  * `public/admin/js/dashboard/dashboard-1.js`: Dòng 8, 105, 241.
* **Mã nguồn thực tế bị lỗi**:
  Layout nạp `<script src="{{ asset('public/admin/js/dashboard/dashboard-1.js') }}"></script>` cho tất cả các trang quản trị.
* **Phân tích nguyên nhân & Tác động**:
  * `dashboard-1.js` tìm kiếm các thẻ canvas demo như `#sold-product`, `#morris-bar-chart`. Khi vào trang Product hay Category, các phần tử này là `null`, dẫn đến lỗi: `Uncaught TypeError: Cannot read properties of null (reading 'getContext')`, làm nghẽn luồng thực thi JS.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  * Xóa bỏ dòng nạp `dashboard-1.js` ở dòng 173 trong `resources/views/layout/admin_layout.blade.php`.
  * Thay thế bằng `@stack('scripts')` để chỉ nạp ở trang nào có đồ thị tương ứng.

---

#### [UI-08] Lẫn lộn cú pháp Bootstrap 5 trên nền giao diện Bootstrap 4.3.1
* **Tệp và số dòng vi phạm**:
  * `resources/views/admin/product/add_product.blade.php`: Dòng 7 (`btn-close`, `data-bs-dismiss="modal"`).
  * `resources/views/admin/category/add_category.blade.php`: Dòng 9, 50.
  * `resources/views/admin/coupon/add_coupon.blade.php`: Dòng 7, 74.
* **Phân tích nguyên nhân & Tác động**:
  * Admin theme chạy Bootstrap 4.3.1. Các class `btn-close` và thuộc tính `data-bs-dismiss` là của Bootstrap 5 nên hoàn toàn không có tác dụng trong Bootstrap 4. Nút đóng modal hiển thị ký tự X thô kệch, không bấm đóng được.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Đổi tất cả các nút đóng modal trong Admin sang chuẩn Bootstrap 4:
  ```blade
  <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="CloseModal('ModalEdit')">
      <span aria-hidden="true">&times;</span>
  </button>
  ```

---

#### [UI-09] Thẻ đóng HTML mồ côi và cú pháp thẻ lỗi
* **Tệp và số dòng vi phạm**:
  * `resources/views/layout/profile_layout.blade.php`: Dòng 41 (thẻ đóng `</form>` thừa không có thẻ mở).
  * `resources/views/client/checkout/checkout_index.blade.php`: Dòng 389-391 (thẻ `</body></html>` thừa trong view con).
  * `resources/views/client/home/index_home.blade.php`: Dòng 317, 400, 462 (`</i></i>` đóng 2 lần).
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  * Xóa thẻ `</form>` thừa tại dòng 41 file `profile_layout.blade.php`.
  * Xóa các thẻ `</body></html>` cuối file `checkout_index.blade.php`.
  * Thay thế các cặp `</i></i>` thành `</i>` trong `index_home.blade.php`.

---

#### [UI-10] Vỡ giao diện phân trang do Laravel 12 mặc định render Tailwind CSS
* **Tệp và số dòng vi phạm**:
  * `resources/views/client/orders/my_orders.blade.php`: Dòng 85 (`{{ $orders->links() }}`).
  * `app/Providers/AppServiceProvider.php`.
* **Phân tích nguyên nhân & Tác động**:
  * Laravel 12.x (Laravel Framework 12.32.3) mặc định sử dụng Tailwind CSS cho phân trang. Khi gọi `$orders->links()` trong giao diện Bootstrap, các icon SVG bị phóng to chiếm trọn màn hình, vỡ layout bảng lịch sử đơn hàng.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Trong `app/Providers/AppServiceProvider.php`, bổ sung lệnh thiết lập Bootstrap 5 cho phân trang:
  ```php
  use Illuminate\Pagination\Paginator;

  public function boot(): void
  {
      Paginator::useBootstrapFive();
      // ... giữ nguyên logic view composer
  }
  ```

---

#### [UI-11] Nút bấm thừa có nhãn "Button" bên cạnh các ô tải tệp lên
* **Tệp và số dòng vi phạm**:
  * `resources/views/admin/product/add_product.blade.php`: Dòng 160-162.
  * `resources/views/admin/product/edit_product.blade.php`: Dòng 123-125.
  * `resources/views/admin/brand/add_brand.blade.php`: Dòng 19-21.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Xóa bỏ đoạn mã HTML mẫu bị sót lại trong các file trên:
  ```html
  <!-- XÓA BỎ KHỐI NÀY: -->
  <div class="input-group-prepend">
      <button class="btn btn-primary" type="button">Button</button>
  </div>
  ```

---

#### [UI-12] Thiếu trạng thái Empty State trên toàn bộ các bảng dữ liệu Admin
* **Tệp và số dòng vi phạm**:
  * `show_product.blade.php`, `show_category.blade.php`, `show_brand.blade.php`, `orders/index.blade.php`, `users.blade.php`.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Chuyển đổi `@foreach` sang `@forelse ... @empty` trong các bảng quản trị:
  ```blade
  @forelse ($items as $item)
      <!-- Nội dung từng hàng -->
  @empty
      <tr>
          <td colspan="10" class="text-center py-4 text-muted">
              <i class="fa fa-info-circle mr-1"></i> Hiện chưa có dữ liệu nào để hiển thị.
          </td>
      </tr>
  @endforelse
  ```

---

#### [UI-13] Lỗi chính tả tiếng Việt "Đang kinh doan", thiếu định dạng tiền tệ và thiếu badge sản phẩm
* **Tệp và số dòng vi phạm**:
  * `resources/views/admin/product/show_product.blade.php`: Dòng 62, 70, 71.
* **Mã nguồn thực tế bị lỗi**:
  ```blade
  <td>{{ $item->price }}</td>
  <td>{{ $item->Status ? 'Đang kinh doan' : 'Ngừng kinh doanh' }}</td>
  <td>{{ $item->IsActive ? 'Active' : 'Inactive' }}</td>
  ```
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  ```blade
  <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
  <td>
      <span class="badge {{ $item->Status ? 'badge-success' : 'badge-danger' }}">
          {{ $item->Status ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}
      </span>
  </td>
  <td>
      <span class="badge {{ $item->IsActive ? 'badge-primary' : 'badge-secondary' }}">
          {{ $item->IsActive ? 'Kích hoạt' : 'Tạm khóa' }}
      </span>
  </td>
  ```

---

#### [UI-14] Lỗi JavaScript Back-to-top do phụ thuộc easing `easeInOutExpo` không được nạp
* **Tệp và số dòng vi phạm**:
  * `public/client/js/main.js`: Dòng 162.
* **Mã nguồn thực tế bị lỗi**:
  `$("html, body").animate({ scrollTop: 0 }, 1500, "easeInOutExpo");`
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Sửa dòng 162 `public/client/js/main.js` (bỏ tham số easing bên thứ ba không nạp):
  ```javascript
  $(".back-to-top").click(function () {
      $("html, body").animate({ scrollTop: 0 }, 800);
      return false;
  });
  ```

---

### 3.3. Nhóm 3: Lỗi Luồng Nghiệp vụ & Xử lý Dữ liệu (Functional Issues)

#### [FUNC-01] Admin hoàn toàn không có tính năng cập nhật trạng thái đơn hàng (Order Status)
* **Tệp và số dòng vi phạm**:
  * `app/Http/Controllers/OrderController.php`.
  * `resources/views/admin/orders/index.blade.php` & `detail_modal.blade.php`.
* **Phân tích nguyên nhân & Tác động**:
  * Hệ thống chỉ cho phép Admin "Xem chi tiết" hoặc "Xóa" đơn hàng. Không hề có phương thức hay giao diện để chuyển trạng thái từ `pending` sang `processing`, `shipping`, `completed`, `cancelled`.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  1. Thêm route trong `routes/web.php`:
     ```php
     Route::post('/admin/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');
     ```
  2. Bổ sung phương thức vào `app/Http/Controllers/OrderController.php`:
     ```php
     public function updateStatus(Request $request, $id)
     {
         $request->validate([
             'status' => 'required|in:pending,processing,shipping,completed,cancelled'
         ]);

         $order = Order::findOrFail($id);
         $order->status = $request->input('status');
         $order->save();

         return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
     }
     ```
  3. Bổ sung dropdown cập nhật vào `resources/views/admin/orders/detail_modal.blade.php`:
     ```blade
     <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="mt-3 p-2 border rounded bg-light">
         @csrf
         <div class="form-group mb-0 d-flex align-items-center">
             <label class="mr-2 mb-0 font-weight-bold">Trạng thái:</label>
             <select name="status" class="form-control form-control-sm mr-2" style="max-width: 180px;">
                 <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                 <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                 <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                 <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                 <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Hủy đơn hàng</option>
             </select>
             <button type="submit" class="btn btn-sm btn-success">Cập nhật</button>
         </div>
     </form>
     ```

---

#### [FUNC-02] Toàn bộ Form CRUD Thêm & Sửa thiếu hiển thị lỗi Validation và thiếu giữ lại dữ liệu cũ
* **Tệp và số dòng vi phạm**:
  * Các file: `add_product.blade.php`, `edit_product.blade.php`, `add_category.blade.php`, `add_brand.blade.php`, `add_coupon.blade.php`, `register_admin.blade.php`.
* **Phân tích nguyên nhân & Tác động**:
  * Khi Controller trả về lỗi validation (ví dụ: ảnh không hợp lệ, giá trị âm, email trùng), form bị xóa sạch toàn bộ nội dung đã nhập và không hiển thị thông báo lỗi cụ thể cho từng trường, gây ức chế và lãng phí thời gian của quản trị viên.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Chuẩn hóa các trường nhập liệu theo mẫu sau:
  ```blade
  <div class="form-group">
      <label>Tên sản phẩm <span class="text-danger">*</span></label>
      <input type="text" name="name" 
             class="form-control @error('name') is-invalid @enderror" 
             value="{{ old('name', $product->name ?? '') }}" 
             placeholder="Nhập tên sản phẩm...">
      @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
      @enderror
  </div>
  ```

---

#### [FUNC-03] Toàn bộ Flash Messages đặt ngoài `@section('view-content')` bị Blade nuốt mất
* **Tệp và số dòng vi phạm**:
  * `show_category.blade.php`: Dòng 4-20.
  * `show_brand.blade.php`: Dòng 3-19.
  * `orders/index.blade.php`: Dòng 3-18.
  * `show_coupon.blade.php`: Dòng 2-18.
  * `show_product.blade.php`: Thiếu hoàn toàn.
* **Phân tích nguyên nhân & Tác động**:
  * Trong Blade, mã nằm ngoài `@section` khi kế thừa `@extends` sẽ bị compiler bỏ qua hoàn toàn. Người dùng thực hiện thêm, sửa, xóa thành công không hề nhận được bất kỳ thông báo phản hồi nào.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Thêm đoạn script hiển thị Toastr tập trung vào cuối file `resources/views/layout/admin_layout.blade.php` (trước thẻ đóng `</body>`):
  ```blade
  <script>
      @if(session('success'))
          toastr.success("{{ session('success') }}", "Thành công");
      @endif
      @if(session('error'))
          toastr.error("{{ session('error') }}", "Lỗi");
      @endif
      @if(session('warning'))
          toastr.warning("{{ session('warning') }}", "Cảnh báo");
      @endif
  </script>
  ```

---

#### [FUNC-04] Xung đột DataTables Client-side và Phân trang Server-side của Laravel trên trang Đơn hàng
* **Tệp và số dòng vi phạm**:
  * `resources/views/admin/orders/index.blade.php`: Dòng 26 và 71.
  * `public/admin/js/main.js`: Dòng 67.
* **Phân tích nguyên nhân & Tác động**:
  * Bảng gán `id="myTable"` kích hoạt DataTables client-side trên 20 bản ghi của trang hiện tại, trong khi Controller dùng `$orders = Order::paginate(20)`. DataTables hiển thị "Showing 1 to 20 of 20 entries" và bộ lọc tìm kiếm chỉ tìm được trong 20 bản ghi này chứ không tìm trên toàn bộ cơ sở dữ liệu.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Đổi id bảng trong `resources/views/admin/orders/index.blade.php` dòng 26 thành `id="orderTableNoDt"` (không gắn class DataTables client-side tự động), sử dụng thanh tìm kiếm Server-side kết hợp phân trang chuẩn của Laravel.

---

#### [FUNC-05] Nút "Đóng" modal Coupon gọi sai ID modal không đóng được
* **Tệp và số dòng vi phạm**:
  * `resources/views/admin/coupon/add_coupon.blade.php`: Dòng 73.
* **Mã nguồn thực tế bị lỗi**:
  `<button type="button" class="btn btn-secondary" onclick="CloseModal('ModalCreateCoupon')" data-bs-dismiss="modal">Đóng</button>`
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Sửa dòng 73 thành:
  ```blade
  <button type="button" class="btn btn-secondary" onclick="CloseModal('ModalEdit')" data-dismiss="modal">Đóng</button>
  ```

---

#### [FUNC-06] Nút "Hủy bỏ" trong modal sửa coupon là thẻ `<a>` gây reload toàn trang
* **Tệp và số dòng vi phạm**:
  * `resources/views/admin/coupon/edit_coupon.blade.php`: Dòng 90.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Thay thẻ `<a>` bằng button:
  ```blade
  <button type="button" class="btn btn-secondary" onclick="CloseModal('ModalEdit')">Hủy bỏ</button>
  ```

---

#### [FUNC-07] Modal Chi tiết Đơn hàng và Script bị đặt sau `@endsection`
* **Tệp và số dòng vi phạm**:
  * `resources/views/admin/orders/index.blade.php`: Dòng 76-122.
* **Phân tích nguyên nhân & Tác động**:
  * Đặt sau `@endsection` làm đoạn mã HTML và JS bị render sau thẻ đóng `</html>` của trang web, vi phạm cấu trúc DOM W3C và có thể làm backdrop modal bị kẹt che đen toàn bộ màn hình.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Di chuyển toàn bộ khối `<div class="modal fade" id="modalOrderDetails">` và thẻ `<script>` vào bên trong `@section('view-content')`.

---

#### [FUNC-08] Phương thức xóa đơn hàng `destroy()` không dùng DB Transaction
* **Tệp và số dòng vi phạm**:
  * `app/Http/Controllers/OrderController.php`: Dòng 121-144.
* **Phân tích nguyên nhân & Tác động**:
  * Nếu lệnh xóa bảng cha `orders` gặp lỗi (do khóa ngoại hoặc mất kết nối DB) sau khi các bản ghi con `oder_items` đã bị xóa, dữ liệu sẽ rơi vào trạng thái mất toàn vẹn vĩnh viễn.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Bọc phương thức `destroy()` trong Database Transaction:
  ```php
  public function destroy($id)
  {
      DB::beginTransaction();
      try {
          $order = Order::findOrFail($id);
          $order->orderItems()->delete();
          $order->delete();
          DB::commit();
          return response()->json(['success' => true, 'message' => 'Xóa đơn hàng thành công!']);
      } catch (\Throwable $e) {
          DB::rollBack();
          Log::error("Lỗi xóa đơn hàng: " . $e->getMessage());
          return response()->json(['success' => false, 'message' => 'Không thể xóa đơn hàng!'], 500);
      }
  }
  ```

---

#### [FUNC-09] Kiểm tra và trừ tồn kho khi đặt hàng thiếu `lockForUpdate()`
* **Tệp và số dòng vi phạm**:
  * `app/Http/Controllers/OrderController.php`: Dòng 269-275.
* **Phân tích nguyên nhân & Tác động**:
  * Lỗi tranh chấp tài nguyên (Race Condition). Khi 2 người dùng đặt sản phẩm cuối cùng cùng một thời điểm, cả 2 tiến trình đều đọc thấy `stockQuantity = 1` và đều trừ kho, dẫn đến số lượng tồn kho bị âm.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Áp dụng Pessimistic Locking trong quá trình thanh toán:
  ```php
  $prod = Product::where('id', $item['product_id'])->lockForUpdate()->first();
  if (!$prod || $prod->stockQuantity < $item['quantity']) {
      throw new \Exception('Sản phẩm "' . ($prod->name ?? '') . '" không đủ số lượng trong kho.');
  }
  $prod->decrement('stockQuantity', $item['quantity']);
  ```

---

#### [FUNC-10] Form Đăng ký tài khoản thiếu xác thực Unique Email và Password Confirmation
* **Tệp và số dòng vi phạm**:
  * `app/Http/Controllers/AdminController.php`: Dòng 105-113.
  * `resources/views/admin/auth/register_admin.blade.php`: Dòng 51.
* **Phân tích nguyên nhân & Tác động**:
  * *Nguyên nhân*: Controller `AdminController@submit_register` chưa áp dụng quy tắc kiểm tra email duy nhất (`unique:users,email`) và chưa kiểm tra xác nhận mật khẩu (`confirmed`). Đồng thời, trong Blade view `resources/views/admin/auth/register_admin.blade.php` dòng 51, ô nhập lại mật khẩu đang đặt thuộc tính `name="check-password"` thay vì `name="password_confirmation"`.
  * *Tác động*: Cho phép đăng ký nhiều tài khoản trùng email gây xung đột CSDL. Đặc biệt, nếu chỉ thêm rule `'password' => 'confirmed'` ở Controller mà không sửa thuộc tính `name` trên Blade view, thì do quy tắc `'confirmed'` của Laravel bắt buộc trường xác nhận phải mang tên `{field}_confirmation` (tức `password_confirmation`), 100% các lượt người dùng đăng ký sẽ bị báo lỗi "Mật khẩu xác nhận không khớp" và form đăng ký sẽ tê liệt hoàn toàn.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  *Bước 1: Sửa Backend Validation trong `app/Http/Controllers/AdminController.php`*:
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
  *Bước 2: Sửa thuộc tính name trong Blade View `resources/views/admin/auth/register_admin.blade.php` dòng 51*:
  ```blade
  {{-- Trước khi sửa (dòng 51): --}}
  <input type="password" name="check-password" class="form-control" placeholder="Xác nhận mật khẩu" minlength="6" required>

  {{-- Sửa thành: --}}
  <input type="password" name="password_confirmation" class="form-control" placeholder="Xác nhận mật khẩu" minlength="6" required>
  ```

---

#### [FUNC-11] Khách vãng lai bị chặn ở middleware `auth` khi bấm Đặt hàng
* **Tệp và số dòng vi phạm**:
  * `routes/web.php`: Dòng 53 (`Route::post('/dat-hang', [OrderController::class, 'placeOrder'])` nằm trong group `Route::middleware('auth')`).
  * `app/Http/Controllers/OrderController.php`: Dòng 190-202 (có code kiểm tra `Auth::check() ? Auth::id() : null`).
* **Phân tích nguyên nhân & Tác động**:
  * Controller đã chuẩn bị sẵn luồng cho khách vãng lai đặt hàng (`user_id = null`), nhưng Route lại bị bọc kín trong middleware `auth`. Khách vãng lai submit form đặt hàng lập tức bị đá văng về trang đăng nhập và mất trắng dữ liệu đã điền trên form.
* **Mã nguồn khắc phục chuẩn (Copy-pasteable)**:
  Đưa route `dathang` và `order.success` ra ngoài group `auth` trong `routes/web.php`:
  ```php
  Route::post('/dat-hang', [OrderController::class, 'placeOrder'])->name('dathang');
  Route::get('/dat-hang-thanh-cong/{id}', [OrderController::class, 'showSuccess'])->name('order.success');
  ```

---

### 3.4. Nhóm 4: Đề xuất Tối ưu Hệ thống (Improvements)

#### [IMP-01] Chuẩn hóa đường dẫn tài nguyên `asset()` và gỡ bỏ liên kết Windows Junction `public/public`
* **Vấn đề**: Toàn bộ view dùng `asset('public/...')`. Trên Windows lập trình viên tạo junction link `public/public` trỏ vào chính nó. Khi đưa lên Linux/Docker/Staging, liên kết này sẽ gãy và toàn bộ CSS/JS/Ảnh bị lỗi 404.
* **Khắc phục**:
  1. Xóa junction link trên Windows bằng lệnh: `cmd /c rmdir c:\xampp\htdocs\appweb\public\public`.
  2. Dùng công cụ tìm kiếm và thay thế đồng loạt trong thư mục `resources/views/`:
     * Thay `asset('public/admin/` bằng `asset('admin/`
     * Thay `asset('public/client/` bằng `asset('client/`
     * Thay `asset('public/uploads/` bằng `asset('uploads/`

---

#### [IMP-02] Tối ưu tải Assets trong `admin_layout.blade.php`
* **Vấn đề**: Layout chung đang nạp 12 thư viện biểu đồ và bản đồ demo (`raphael.min.js`, `morris.min.js`, `flot.js`, `jqvmap`, `gaugeJS`, `circle-progress.min.js`, `counterup.min.js`) dù các trang CRUD thông thường không hề dùng tới.
* **Khắc phục**: Gỡ bỏ 12 script này khỏi `admin_layout.blade.php`. Chỉ nhúng tại `admin/dashboard.blade.php` bằng cú pháp `@push('scripts')`. Tốc độ tải trang quản trị sẽ tăng từ 2 đến 3 lần.

---

#### [IMP-03] Bổ sung gói ngôn ngữ tiếng Việt (i18n) cho thư viện DataTables
* **Vấn đề**: Các bảng quản trị hiển thị giao diện mặc định bằng tiếng Anh: "Show entries", "Search:", "Previous", "Next".
* **Khắc phục**: Cập nhật hàm khởi tạo trong `public/admin/js/main.js`:
  ```javascript
  $("#myTable").DataTable({
      language: {
          search: "Tìm kiếm:",
          lengthMenu: "Hiển thị _MENU_ dòng",
          info: "Hiển thị _START_ đến _END_ trong _TOTAL_ bản ghi",
          infoEmpty: "Không có dữ liệu",
          infoFiltered: "(lọc từ _MAX_ bản ghi)",
          zeroRecords: "Không tìm thấy kết quả phù hợp",
          paginate: {
              first: "Đầu",
              previous: "Trước",
              next: "Sau",
              last: "Cuối"
          }
      }
  });
  ```

---

#### [IMP-04] Tối ưu hóa truy vấn View Composer tránh duplicate query Category
* **Vấn đề**: `AppServiceProvider.php` truy vấn `Category::orderBy('id', 'desc')->get()` trên mỗi request của client, trong khi danh mục sản phẩm là dữ liệu ít biến động.
* **Khắc phục**: Sử dụng Cache của Laravel với thời gian ghi nhớ 1 giờ:
  ```php
  use Illuminate\Support\Facades\Cache;

  View::composer(['layout.home_layout', 'layout.profile_layout', 'client.*'], function ($view) {
      if (Schema::hasTable('_category')) {
          $categories = Cache::remember('global_categories', 3600, function () {
              return Category::orderBy('name', 'asc')->get();
          });
          $view->with('categories', $categories);
      }
  });
  ```

---

#### [IMP-05] Xóa bỏ Route Test nhạy cảm `/test-password-reset/{email}`
* **Vấn đề**: `routes/web.php` dòng 97-107 chứa route debug công khai trả về token đặt lại mật khẩu của người dùng qua JSON mà không có bảo vệ xác thực.
* **Khắc phục**: Xóa bỏ hoàn toàn route này trước khi triển khai hệ thống lên môi trường Staging/Production.

---

#### [IMP-06] Bổ sung phân trang Server-side cho Product, Category, Brand, User
* **Vấn đề**: Các Controller hiện tại dùng `Product::all()`, `Category::all()` tải toàn bộ dữ liệu vào bộ nhớ RAM. Khi hệ thống có hàng ngàn sản phẩm, trang web sẽ bị quá tải bộ nhớ PHP và gây đơ trình duyệt.
* **Khắc phục**: Chuyển đổi đồng bộ sang `->paginate(15)` trong Controller kết hợp `{{ $items->links() }}` tại View.

---

#### [IMP-07] Tách biệt giao diện Đăng nhập/Đăng ký Client và Xây dựng trang báo lỗi 404/500
* **Vấn đề**: Chưa có trang đăng nhập riêng cho khách hàng (file `resources/views/client/auth/login_client.blade.php` đang rỗng 0 bytes), phải dùng chung giao diện Admin. Chưa có các view `resources/views/errors/404.blade.php`, `500.blade.php`.
* **Khắc phục**: Hoàn thiện form đăng nhập mang phong cách giao diện Client tại `resources/views/client/auth/login_client.blade.php` và thêm các trang lỗi chuẩn để nâng cao trải nghiệm người dùng.

---

## 4. LỘ TRÌNH VÀ KẾ HOẠCH TRIỂN KHAI KHẮC PHỤC (ROADMAP & ACTION PLAN)

Để đảm bảo hệ thống vận hành ổn định, an toàn và không gây gián đoạn dịch vụ, khuyến nghị áp dụng lộ trình xử lý theo 4 giai đoạn:

```
                                  LỘ TRÌNH TRIỂN KHAI KHẮC PHỤC (4 GIAI ĐOẠN)
  ┌───────────────────────────────────────────────────────────────────────────────────────────────────────┐
  │ GIAI ĐOẠN 1: HOTFIX KHẨN CẤP (24h - 48h)                                                              │
  │ • Đổi Route Xóa GET sang DELETE + CSRF (CRIT-01)        • Sửa route /thanh-toan gọi method (CRIT-02)  │
  │ • Thêm name('register') (CRIT-03)                       • Bỏ cột 'status' ở _category query (CRIT-04) │
  │ • Fix crash checkCoupon (CRIT-06)                       • Bổ sung null-safe relation category(CRIT-07)│
  │ • Khóa role trong $fillable User (CRIT-08)              • Chặn Admin tự giáng quyền (CRIT-09)         │
  │ • Fix cú pháp Blade href=" }}" (CRIT-10)                • Fix giá Top bán chạy (CRIT-11)              │
  │ • Vá lỗ hổng IDOR lộ PII showSuccess (CRIT-13)                                                        │
  └──────────────────────────────────────────────────────────────────┬────────────────────────────────────┘
                                                                     │
  ┌──────────────────────────────────────────────────────────────────▼────────────────────────────────────┐
  │ GIAI ĐOẠN 2: SỬA LỖI NGHIỆP VỤ & LUỒNG DỮ LIỆU (1 TUẦN)                                              │
  │ • Cập nhật schema storeFromCart (CRIT-05)               • Làm tính năng đổi Order Status (FUNC-01)    │
  │ • Đưa Flash message vào trong section/toastr (FUNC-03)  • Bổ sung validation & old() forms (FUNC-02)  │
  │ • Sửa phương thức thanh toán checkout success (CRIT-12) • Bọc Transaction khi xóa đơn (FUNC-08)       │
  │ • Khóa tồn kho lockForUpdate khi đặt hàng (FUNC-09)     • Tách biệt luồng khách vãng lai (FUNC-11)    │
  └──────────────────────────────────────────────────────────────────┬────────────────────────────────────┘
                                                                     │
  ┌──────────────────────────────────────────────────────────────────▼────────────────────────────────────┐
  │ GIAI ĐOẠN 3: HOÀN THIỆN UI/UX & RESPONSIVE MOBILE (1 TUẦN)                                            │
  │ • Thêm Search, Cart, Auth vào Mobile Navbar (UI-01)     • Điền giao diện List View #tab-6 (UI-02)     │
  │ • Sửa lệch cột bảng Checkout (UI-03)                    • Xây dựng giao diện Dashboard Admin (UI-06)  │
  │ • Gỡ dashboard-1.js khỏi layout chung (UI-07)           • Chuẩn hóa cú pháp Bootstrap 4 admin (UI-08) │
  │ • Cấu hình Bootstrap 5 Paginator (UI-10)                • Sửa link 404 /shop và bỏ nút thừa (UI-04,11)│
  └──────────────────────────────────────────────────────────────────┬────────────────────────────────────┘
                                                                     │
  ┌──────────────────────────────────────────────────────────────────▼────────────────────────────────────┐
  │ GIAI ĐOẠN 4: TỐI ƯU HIỆU NĂNG & CHUẨN HÓA MÃ NGUỒN (1 TUẦN)                                          │
  │ • Gỡ bỏ Windows Junction public/public & chuẩn hóa asset (IMP-01) • Xóa route test nhạy cảm (IMP-05)  │
  │ • Tối ưu hóa tải assets admin (IMP-02)                  • Cài đặt gói tiếng Việt DataTables (IMP-03)  │
  │ • Áp dụng Cache View Composer (IMP-04)                  • Phân trang Server-side toàn diện (IMP-06)   │
  │ • Hoàn thiện Client Auth & Trang lỗi 404/500 (IMP-07)                                                 │
  └───────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### Bảng phân bổ nguồn lực và Tiêu chí nghiệm thu (KPI Acceptance Criteria)

| Giai đoạn | Thời gian | Nhân sự phụ trách | Tiêu chí nghiệm thu hoàn thành (KPI Acceptance) |
|:---|:---:|:---|:---|
| **GĐ 1: Hotfix Khẩn cấp** | 24 - 48h | 01 Backend Dev | 100% các route không bị crash 500 khi submit form; không thể xóa dữ liệu qua HTTP GET; không còn lỗi null pointer trong Blade. |
| **GĐ 2: Nghiệp vụ & Dữ liệu** | 01 tuần | 01 Backend Dev + 01 QA | Admin đổi được trạng thái đơn hàng; Giỏ hàng tạo đơn chính xác `total_amount`; flash message hiển thị đầy đủ; không bị âm kho khi mua đồng thời. |
| **GĐ 3: UI/UX & Mobile** | 01 tuần | 01 Frontend Dev | Kiểm tra hiển thị chuẩn trên iPhone/Android (viewport 375px - 768px); Dashboard admin có số liệu KPI; không có lỗi đỏ trong Console F12. |
| **GĐ 4: Tối ưu & Chuẩn hóa** | 01 tuần | 01 Fullstack Dev + DevOps | Hệ thống chạy độc lập không cần junction `public/public`; thời gian tải trang admin < 1.2s; DataTables hiển thị tiếng Việt; xóa sạch route debug. |

---

## 5. HƯỚNG DẪN KIỂM CHỨNG & THẨM TRA ĐỘC LẬP (VERIFICATION GUIDE)

Dành cho Đội ngũ Kiểm thử (QA), Kỹ sư thẩm tra độc lập (Auditor) hoặc Tech Lead đối soát kết quả.

### 5.1. Kiểm tra tĩnh qua dòng lệnh CLI

1. **Kiểm tra cú pháp PHP toàn bộ dự án**:
   ```bash
   php -l app/Http/Controllers/OrderController.php
   php -l app/Http/Controllers/CheckoutController.php
   php -l app/Http/Controllers/HomeController.php
   php -l app/Http/Controllers/CartController.php
   php -l app/Http/Controllers/AdminController.php
   ```
2. **Kiểm tra danh sách Route và Named Routes**:
   ```bash
   php artisan route:list
   ```
   *Kiểm tra*: Xác nhận không có route nào trỏ tới phương thức không tồn tại, và route `register` đã có trong danh sách.
3. **Kiểm tra liên kết Junction `public/public`**:
   ```powershell
   Get-Item "c:\xampp\htdocs\appweb\public\public"
   ```
   *Kiểm tra*: Nếu thuộc tính `LinkType` là `Junction`, hệ thống đang trong tình trạng phụ thuộc liên kết thư mục Windows.

---

### 5.2. Kịch bản kiểm chứng tự động qua PHP CLI script

Chạy trực tiếp kịch bản kiểm tra độc lập tại thư mục gốc:
```bash
php .agents/explorer_backend/verify_findings.php
```
**Kết quả mong đợi khi chưa vá lỗi**:
* `route('register')`: Ném lỗi `RouteNotFoundException`.
* `CheckoutController@processOrder`: Báo lỗi `Method does not exist`.
* `HomeController@show_category_home`: Báo lỗi SQL cột `status`.
* `CartController@checkCoupon` không tham số: Báo lỗi `Undefined array key "code_input"`.

**Kết quả mong đợi sau khi vá lỗi**:
* Toàn bộ 4 bài kiểm tra đều hiển thị `[PASSED]`.

---

### 5.3. Kịch bản kiểm thử tương tác trên trình duyệt (Browser Test Cases)

| Mã test case | Quy trình thực hiện | Kết quả trước khi sửa (Lỗi) | Kết quả kỳ vọng sau khi sửa |
|:---|:---|:---|:---|
| **TC-SEC-01** | Đăng nhập Admin, mở tab mới gõ URL: `http://localhost/appweb/delete-product/1` | Sản phẩm bị xóa ngay lập tức (Lỗ hổng GET) | Trả về lỗi `405 Method Not Allowed` |
| **TC-SEC-02** | Khách vãng lai chưa đăng nhập, gõ trực tiếp URL `/dat-hang-thanh-cong/1` (IDOR) | Hiển thị trọn vẹn thông tin PII của khách (Họ tên, SĐT, địa chỉ, đơn hàng) | Bị chặn `403 Forbidden` do thiếu phiên đặt hàng hợp lệ |
| **TC-CART-01** | Vào trang giỏ hàng, nhấn nút Áp dụng mã giảm giá mà không nhập chữ nào | Trang web crash màn hình đen HTTP 500 | Hiển thị thông báo đỏ: "Vui lòng nhập mã giảm giá" |
| **TC-RESP-01** | Mở trình duyệt F12 -> Chuyển sang chế độ xem Mobile (iPhone 12/14) | Mất thanh tìm kiếm, mất nút giỏ hàng, mất nút đăng nhập | Xuất hiện nút giỏ hàng (có số đếm) và nút tài khoản trên header |
| **TC-ORDER-01**| Đặt một đơn hàng COD, sau khi thành công xem trang xác nhận | Hiển thị: "Phương thức thanh toán: Thanh toán qua Ngân hàng" | Hiển thị: "Phương thức thanh toán: Thanh toán khi nhận hàng (COD)" |
| **TC-DASH-01** | Đăng nhập Admin, truy cập `/admin/dashboard` | Toàn bộ phần thân trang trắng trơn, Console báo lỗi JS | Hiển thị 4 thẻ thống kê (Sản phẩm, Đơn hàng, Doanh thu, Khách hàng), Console không có lỗi |
| **TC-PAGI-01** | Vào trang "Đơn hàng của tôi" (`/my-orders`) khi có nhiều hơn 10 đơn | Các nút phân trang SVG to khổng lồ chiếm toàn màn hình | Hiển thị thanh phân trang Bootstrap nhỏ gọn, cân đối |

---

### 5.4. Bảng kiểm tra nghiệm thu (Sign-off Checklist)

- [ ] **Bảo mật**: Đã chuyển 100% các route Xóa sang phương thức `DELETE` và kiểm tra CSRF token.
- [ ] **Bảo mật**: Đã gỡ bỏ `role` khỏi `$fillable` trong `app/Models/User.php`.
- [ ] **Bảo mật**: Đã vá lỗ hổng IDOR tại `showSuccess` bằng Session flash hoặc Signed URLs, ngăn chặn lộ lọt dữ liệu định danh cá nhân (PII) khách hàng.
- [ ] **Bảo mật**: Đã xóa route test `/test-password-reset/{email}`.
- [ ] **Độ tin cậy**: Tất cả các lệnh gọi quan hệ danh mục trong Blade đều sử dụng toán tử null-safe (`?->` hoặc `??`).
- [ ] **Nghiệp vụ**: Quản trị viên cập nhật được trạng thái đơn hàng và xem chi tiết đơn hàng bình thường.
- [ ] **Nghiệp vụ**: Toàn bộ flash messages hiển thị chính xác qua Toastr.
- [ ] **Nghiệp vụ**: Tất cả form CRUD đều giữ lại giá trị cũ (`old()`) và hiển thị thông báo đỏ (`@error`) khi validate không đạt.
- [ ] **Trải nghiệm**: Responsive mobile hoạt động hoàn hảo, khách hàng tìm kiếm và thêm giỏ hàng dễ dàng trên điện thoại.
- [ ] **Trải nghiệm**: Không còn bất kỳ lỗi nào xuất hiện trong Developer Console (F12) trên cả giao diện Client và Admin.
- [ ] **Triển khai**: Đã xóa bỏ junction `public/public`, toàn bộ hàm `asset()` chuẩn hóa để sẵn sàng chạy trên Linux/Docker.

---
*Báo cáo được hoàn thiện và đóng gói chuyển giao bởi Teamwork Audit Subagent (`worker_report`).*

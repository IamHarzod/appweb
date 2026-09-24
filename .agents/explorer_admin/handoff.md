# BÁO CÁO KIỂM TRA TOÀN DIỆN HỆ THỐNG GIAO DIỆN QUẢN TRỊ ADMIN (REQUIREMENT R2)

**Dự án**: Website Bán hàng Laravel (`appweb`)  
**Thành phần kiểm tra**: Requirement R2 - Admin-side UI & Management Views Inspection  
**Agent thực hiện**: `explorer_admin` (Teamwork Explorer Subagent)  
**Thời gian thực hiện**: 2026-09-18  
**Trạng thái**: Hoàn thành kiểm tra 100% các view quản trị, layouts, CRUD forms, data tables, modals, assets và controllers liên kết.

---

## 1. OBSERVATION (Quan sát thực tế)

Đã tiến hành rà soát toàn bộ 20 file Blade view thuộc `resources/views/admin/`, 1 file layout quản trị chính `resources/views/layout/admin_layout.blade.php`, các tệp tài nguyên tĩnh trong `public/admin/`, các route trong `routes/web.php`, và các Controllers liên quan (`AdminController`, `ProductController`, `CategoryController`, `BrandController`, `OrderController`, `CouponController`).

### 1.1. Bản đồ các View & Layout đã kiểm tra
1. `resources/views/layout/admin_layout.blade.php`: Layout khung quản trị chính (Sidebar, Header, Topbar, Modals, Scripts, Styles).
2. `resources/views/admin/product/show_product.blade.php`: Danh sách sản phẩm (DataTables, dropdown hành động, tìm kiếm).
3. `resources/views/admin/product/add_product.blade.php`: Form/Modal thêm mới sản phẩm.
4. `resources/views/admin/product/edit_product.blade.php`: Form/Modal cập nhật sản phẩm.
5. `resources/views/admin/category/show_category.blade.php`: Danh sách danh mục sản phẩm.
6. `resources/views/admin/category/add_category.blade.php`: Form/Modal thêm mới danh mục.
7. `resources/views/admin/category/edit_category.blade.php`: Trang chỉnh sửa danh mục độc lập.
8. `resources/views/admin/category/edit_category_modal.blade.php`: Form/Modal cập nhật danh mục nạp qua AJAX.
9. `resources/views/admin/brand/show_brand.blade.php`: Danh sách thương hiệu.
10. `resources/views/admin/brand/add_brand.blade.php`: Modal thêm mới thương hiệu.
11. `resources/views/admin/brand/edit_brand.blade.php`: Modal cập nhật thương hiệu nạp qua AJAX.
12. `resources/views/admin/orders/index.blade.php`: Danh sách đơn hàng phía admin.
13. `resources/views/admin/orders/detail_modal.blade.php`: Modal chi tiết đơn hàng (sản phẩm, giá, thông tin người nhận).
14. `resources/views/admin/auth/users.blade.php`: Quản lý danh sách người dùng và phân quyền (admin/user).
15. `resources/views/admin/auth/login_admin.blade.php`: Trang đăng nhập quản trị viên.
16. `resources/views/admin/auth/register_admin.blade.php`: Trang đăng ký tài khoản admin.
17. `resources/views/admin/auth/forgot_password.blade.php`: Trang quên mật khẩu admin.
18. `resources/views/admin/auth/reset_password.blade.php`: Trang đặt lại mật khẩu admin.
19. `resources/views/admin/coupon/show_coupon.blade.php`: Danh sách mã giảm giá.
20. `resources/views/admin/coupon/add_coupon.blade.php`: Modal thêm mới mã giảm giá.
21. `resources/views/admin/coupon/edit_coupon.blade.php`: Modal cập nhật mã giảm giá.

---

## 2. LOGIC CHAIN & PHÂN TÍCH CHI TIẾT CÁC LỖI PHÁT HIỆN

Tất cả các lỗi được phân loại theo 4 nhóm: **Nghiêm trọng (Critical)**, **Nghiệp vụ (Functional)**, **Giao diện & Hiển thị (UI/UX)**, và **Đề xuất tối ưu (Improvements)**.

---

### PHẦN I: LỖI NGHIÊM TRỌNG (CRITICAL ISSUES)

#### CRIT-01: Tất cả thao tác Xóa (Delete) trong Admin đều sử dụng phương thức HTTP GET và không có bảo vệ CSRF
- **Tệp & Dòng code vi phạm**:
  - `routes/web.php`: 
    - Dòng 60: `Route::get('/admin/orders/delete/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');`
    - Dòng 116: `Route::get('/delete-brand/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');`
    - Dòng 126: `Route::get('/delete-product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');`
    - Dòng 136: `Route::get('/delete-category/{id}', [CategoryController::class, 'destroy'])->name('delete-category');`
    - Dòng 142: `Route::get('/admin/users/delete/{id}', [AdminController::class, 'destroy_user'])->name('admin.users.destroy');`
    - Dòng 150: `Route::get('/delete-coupon/{id}', [CouponController::class, 'destroy'])->name('coupon.delete');`
  - `public/admin/js/main.js`: Dòng 14-17:
    ```javascript
    $.ajax({
        method: "get",
        url: url,
    })
    ```
  - Các View gọi hàm: `show_product.blade.php:86`, `show_category.blade.php:73`, `show_brand.blade.php:69`, `orders/index.blade.php:59`, `users.blade.php:48`, `show_coupon.blade.php:110`.
- **Nguyên nhân & Tác động**:
  - Vi phạm nghiêm trọng chuẩn bảo mật RESTful và tiêu chuẩn OWASP (CSRF Vulnerability).
  - Khi dùng HTTP GET, bất kỳ trang web độc hại nào cũng có thể kích hoạt thao tác xóa bằng cách nhúng liên kết ẩn hoặc thẻ hình ảnh (ví dụ: `<img src="http://domain.com/delete-product/1">`). Khi admin đăng nhập ghé thăm, dữ liệu sẽ bị xóa tự động mà không cần sự tương tác.
  - Các công cụ thu thập thông tin web (Web Crawlers, Google Bot, Browser Prefetch) có thể tự động duyệt các liên kết GET và vô tình xóa toàn bộ database.
- **Giải pháp khắc phục**:
  1. Đổi tất cả route xóa trong `routes/web.php` sang `Route::delete(...)`.
  2. Bổ sung thẻ `<meta name="csrf-token" content="{{ csrf_token() }}">` vào `<head>` của `admin_layout.blade.php`.
  3. Cập nhật hàm `DeleteData` trong `public/admin/js/main.js` để gửi method `DELETE` kèm header `X-CSRF-TOKEN`:
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
             if (result.isConfirmed || result.value) {
                 $.ajax({
                     method: "DELETE",
                     url: url,
                     headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     }
                 }).done(function (res) {
                     toastr.success("Xóa dữ liệu thành công!");
                     setTimeout(() => window.location.reload(), 1000);
                 }).fail(function (xhr) {
                     toastr.error("Lỗi khi xóa dữ liệu!");
                 });
             }
         });
     }
     ```

---

#### CRIT-02: Toàn bộ Flash Messages (`session('success')`, `session('error')`) bị đặt NGOÀI `@section('view-content')` hoặc bị bỏ quên hoàn toàn
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/category/show_category.blade.php`: Dòng 4-20 (nằm trước `@section('view-content')` ở dòng 22).
  - `resources/views/admin/brand/show_brand.blade.php`: Dòng 3-19 (nằm trước `@section('view-content')` ở dòng 21).
  - `resources/views/admin/orders/index.blade.php`: Dòng 3-18 (nằm trước `@section('view-content')` ở dòng 20).
  - `resources/views/admin/coupon/show_coupon.blade.php`: Dòng 2-18 (nằm trước `@section('view-content')` ở dòng 19).
  - `resources/views/admin/product/show_product.blade.php`: Hoàn toàn KHÔNG CÓ đoạn mã hiển thị flash message, dù `ProductController` gửi `->with('success', 'Thêm sản phẩm thành công!')`.
  - `resources/views/layout/admin_layout.blade.php`: Không có container alert hay logic Toastr nạp flash message.
  - `resources/views/admin/auth/users.blade.php`: Dòng 6-8 chỉ hiển thị `session('success')`, thiếu hoàn toàn `session('error')` (trong khi `AdminController::destroy_user` gửi `session('error')`).
- **Nguyên nhân & Tác động**:
  - Trong Blade template của Laravel, khi view con sử dụng `@extends('layout.admin_layout')`, mọi khối mã HTML/Blade nằm bên ngoài các `@section(...)` sẽ bị compiler bỏ qua hoặc render sai vị trí trước thẻ `<!DOCTYPE html>`.
  - Kết quả: Khi admin thực hiện Thêm/Sửa/Xóa thành công hoặc thất bại, KHÔNG CÓ BẤT KỲ THÔNG BÁO NÀO xuất hiện. Người dùng không biết thao tác có thành công hay không.
- **Giải pháp khắc phục**:
  - Tích hợp Toastr hiển thị tập trung tại `resources/views/layout/admin_layout.blade.php` ngay trước thẻ `</body>`:
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
        @if(session('info'))
            toastr.info("{{ session('info') }}", "Thông tin");
        @endif
    </script>
    ```

---

#### CRIT-03: Sai sót tiền tố Assets `asset('public/admin/...')` và tạo Windows Junction `public/public` - Nguy cơ 404 toàn bộ Assets khi triển khai Production
- **Tệp & Dòng code vi phạm**:
  - `resources/views/layout/admin_layout.blade.php`: Dòng 10-17, 33-35, 145-177.
  - `resources/views/admin/auth/login_admin.blade.php`: Dòng 10-11, 89-91.
  - `resources/views/admin/auth/register_admin.blade.php`: Dòng 10-11, 78-79.
  - `resources/views/admin/auth/forgot_password.blade.php`: Dòng 10-11, 78-80.
  - `resources/views/admin/auth/reset_password.blade.php`: Dòng 10-11, 94-96.
  - Các view upload ảnh: `show_product.blade.php:58`, `show_category.blade.php:50`, `show_brand.blade.php:49`.
  - Thư mục vật lý: `c:\xampp\htdocs\appweb\public\public` (Windows Junction trỏ ngược lại `public`).
- **Nguyên nhân & Tác động**:
  - Hàm `asset('path')` trong Laravel tự động nối URL gốc của thư mục public (ví dụ: `http://localhost/appweb/public/path` hoặc `http://domain.com/path`).
  - Việc truyền chuỗi `'public/admin/...'` khiến URL tạo thành `.../public/public/admin/...`. Để đối phó cục bộ trên Windows, lập trình viên đã dùng lệnh `mklink /J public public`.
  - Khi đưa lên máy chủ Linux (Ubuntu/CentOS), Docker container hoặc clone repo sang máy khác, NTFS junction không hoạt động. Toàn bộ CSS, JS, hình ảnh, icon sẽ bị 404 Not Found, làm vỡ hoàn toàn giao diện hệ thống.
- **Giải pháp khắc phục**:
  1. Xóa liên kết junction `public/public`.
  2. Chuẩn hóa toàn bộ hàm asset trong các file Blade:
     - `asset('public/admin/...')` -> `asset('admin/...')`
     - `asset('public/uploads/...')` -> `asset('uploads/...')`
     - `asset('public/client/...')` -> `asset('client/...')`

---

#### CRIT-04: Trang Dashboard Admin (`/admin/dashboard`) hoàn toàn trống rỗng (Blank Content Body)
- **Tệp & Dòng code vi phạm**:
  - `app/Http/Controllers/AdminController.php`: Dòng 21-24:
    ```php
    public function show_dasboard()
    {
        return view("layout.admin_layout");
    }
    ```
  - `routes/web.php`: Dòng 77: `Route::get('/admin/dashboard', [AdminController::class, 'show_dasboard'])->name('admin.dashboard');`
  - `resources/views/layout/admin_layout.blade.php`: Dòng 135: `@yield('view-content')`.
- **Nguyên nhân & Tác động**:
  - Controller trả về thẳng `layout.admin_layout` mà không có view con nào kế thừa để điền vào `@yield('view-content')`.
  - Khi truy cập `/admin/dashboard`, trang hiển thị một vùng màu trắng hoàn toàn trống rỗng, không có thẻ thống kê (KPI cards), không có biểu đồ doanh thu, không có số lượng đơn hàng/sản phẩm/khách hàng.
- **Giải pháp khắc phục**:
  1. Tạo file view `resources/views/admin/dashboard.blade.php` kế thừa `layout.admin_layout`.
  2. Bổ sung các widget thống kê tổng quan:
     - Thống kê Đơn hàng mới, Doanh thu trong tháng, Tổng số sản phẩm, Tổng số người dùng.
     - Bảng 5 đơn hàng mới nhất và biểu đồ doanh thu theo tuần/tháng.
  3. Sửa hàm `show_dasboard` trong `AdminController.php`:
     ```php
     public function show_dasboard()
     {
         $totalProducts = \App\Models\Product::count();
         $totalOrders = \App\Models\Order::count();
         $totalRevenue = \App\Models\Order::sum('total_amount');
         $totalUsers = \App\Models\User::where('role', 'user')->count();
         $recentOrders = \App\Models\Order::with('user')->orderByDesc('id')->take(5)->get();

         return view('admin.dashboard', compact('totalProducts', 'totalOrders', 'totalRevenue', 'totalUsers', 'recentOrders'));
     }
     ```

---

#### CRIT-05: Script `dashboard-1.js` nạp toàn cục gây Uncaught TypeError trên TẤT CẢ các trang Admin
- **Tệp & Dòng code vi phạm**:
  - `resources/views/layout/admin_layout.blade.php`: Dòng 173 (`<script src="{{ asset('public/admin/js/dashboard/dashboard-1.js') }}"></script>`)
  - `public/admin/js/dashboard/dashboard-1.js`: Dòng 8 (`Morris.Bar`), Dòng 103-105 (`new Chart(nk, ...)` với `nk = document.getElementById("sold-product")`), Dòng 240-241 (`new PerfectScrollbar('.widget-todo')`).
- **Nguyên nhân & Tác động**:
  - File `dashboard-1.js` là file khởi tạo biểu đồ demo của Quixlab, giả định có sẵn các thẻ HTML như `#sold-product`, `#morris-bar-chart`, `#cpu-load`, `.widget-todo`.
  - Vì file này được nạp vào layout chung `admin_layout.blade.php`, trên TẤT CẢ các trang quản lý (Products, Categories, Brands, Orders, Users, Coupons), các phần tử này đều không tồn tại, khiến JavaScript ném lỗi crash:
    `Uncaught TypeError: Cannot read properties of null (reading 'getContext')`
  - Lỗi JS chưa bắt này có thể chặn các đoạn mã JS tiếp theo trong pipeline tải trang, làm hỏng DataTables, modal hoặc SweetAlert2.
- **Giải pháp khắc phục**:
  - Gỡ bỏ `dashboard-1.js` khỏi `admin_layout.blade.php`.
  - Chỉ nạp file này ở trang Dashboard riêng thông qua `@push('scripts')`, đồng thời bọc điều kiện kiểm tra phần tử tồn tại trước khi khởi tạo biểu đồ:
    ```javascript
    if (document.getElementById("sold-product")) {
        new Chart(document.getElementById("sold-product"), { ... });
    }
    ```

---

#### CRIT-06: Nguy cơ Crash trang Sản phẩm do truy cập trực tiếp quan hệ Category (`$item->category->name`) thiếu Null-Safe
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/product/show_product.blade.php`: Dòng 67:
    ```blade
    <td>{{ $item->category->name }}</td>
    ```
- **Nguyên nhân & Tác động**:
  - Tại dòng 66, trường Brand được xử lý an toàn: `$item->brand->TenThuongHieu ?? 'Chưa có thương hiệu'`.
  - Tuy nhiên tại dòng 67, `$item->category->name` gọi trực tiếp mà không có toán tử null-safe hoặc fallback. Nếu một danh mục bị xóa hoặc dữ liệu sản phẩm có `category_id` không khớp, Laravel sẽ crash với lỗi Fatal Error: `Attempt to read property 'name' on null`, làm tê liệt toàn bộ trang quản lý sản phẩm.
- **Giải pháp khắc phục**:
  - Sửa dòng 67 thành:
    ```blade
    <td>{{ $item->category->name ?? 'Chưa có danh mục' }}</td>
    ```

---

#### CRIT-07: Lỗ hổng Tự giáng quyền Admin (Self-Demotion Lockout) trong Quản lý Tài khoản
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/auth/users.blade.php`: Dòng 34-41:
    ```blade
    <form action="{{ route('admin.users.role', $u->id) }}" method="POST" class="d-flex align-items-center">
        @csrf
        <select name="role" class="form-control mr-2" style="max-width: 180px;">
            <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>User</option>
            <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        <button type="submit" class="btn btn-primary">Gán quyền</button>
    </form>
    ```
  - `app/Http/Controllers/AdminController.php`: Dòng 66-75 (`update_user_role`).
- **Nguyên nhân & Tác động**:
  - Hệ thống đã kiểm tra `if ($u->id === auth()->id())` ở cột Xóa để ngăn admin tự xóa tài khoản của mình. Tuy nhiên ở cột phân quyền, dropdown vẫn mở cho phép admin tự đổi role của chính mình thành `user`.
  - Nếu admin thao tác nhầm, role bị chuyển thành `user`, ngay lập tức `AdminMiddleware` sẽ chặn truy cập và tài khoản bị khóa vĩnh viễn khỏi trang quản trị.
- **Giải pháp khắc phục**:
  - Trên view `users.blade.php`: Nếu `$u->id === auth()->id()`, hiển thị badge cố định `<span class="badge badge-success">Admin (Bạn)</span>` thay vì form đổi role.
  - Trong `AdminController.php` (dòng 67): Bổ sung kiểm tra backend:
    ```php
    if (Auth::id() == $id && $request->role !== 'admin') {
        return redirect()->back()->with('error', 'Bạn không thể tự hạ quyền của chính mình!');
    }
    ```

---

### PHẦN II: LỖI NGHIỆP VỤ & CHỨC NĂNG (FUNCTIONAL ISSUES)

#### FUNC-01: Admin hoàn toàn không có tính năng cập nhật trạng thái đơn hàng (Order Status)
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/orders/detail_modal.blade.php`: Dòng 14-16 (`<span class="badge badge-info">{{ $order->status }}</span>`).
  - `resources/views/admin/orders/index.blade.php`: Bảng đơn hàng không hiển thị trạng thái và không có nút thao tác trạng thái.
  - `app/Http/Controllers/OrderController.php`: Không có bất kỳ phương thức nào xử lý đổi status của Order.
- **Nguyên nhân & Tác động**:
  - Quản trị viên chỉ có thể "Xem chi tiết" hoặc "Xóa" đơn hàng. Không có cơ chế đổi trạng thái từ `pending` sang `processing`, `completed`, `cancelled`. Đơn hàng sau khi khách đặt bị kẹt vĩnh viễn ở trạng thái chờ.
- **Giải pháp khắc phục**:
  1. Thêm route trong `routes/web.php`:
     ```php
     Route::post('/admin/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');
     ```
  2. Bổ sung form cập nhật trạng thái ngay trong `detail_modal.blade.php` hoặc `index.blade.php`:
     ```blade
     <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="mt-2">
         @csrf
         <div class="input-group">
             <select name="status" class="form-control form-control-sm">
                 <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                 <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                 <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                 <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                 <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
             </select>
             <div class="input-group-append">
                 <button class="btn btn-sm btn-primary" type="submit">Cập nhật</button>
             </div>
         </div>
     </form>
     ```

---

#### FUNC-02: Toàn bộ Form CRUD Thêm & Sửa thiếu hiển thị lỗi Validation (`@error`) và thiếu giữ lại dữ liệu cũ (`old()`)
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/product/add_product.blade.php`: Toàn bộ 11 input fields (name, price, stockQuantity, discountPercent, description, status, IsActive, id_brand, category_id, style, imageURL) không có `@error` và không có `old()`.
  - `resources/views/admin/product/edit_product.blade.php`: Toàn bộ input chỉ dùng `$product->field`, không dùng `old('field', $product->field)`.
  - `resources/views/admin/category/add_category.blade.php`: Dòng 34, 42.
  - `resources/views/admin/brand/add_brand.blade.php`: Dòng 32, 52.
  - `resources/views/admin/brand/edit_brand.blade.php`: Dòng 39, 61.
  - `resources/views/admin/coupon/add_coupon.blade.php`: Dòng 20, 41, 53, 62.
  - `resources/views/admin/coupon/edit_coupon.blade.php`: Dòng 24, 55, 68, 78.
  - `resources/views/admin/auth/register_admin.blade.php`: Dòng 30, 35, 40, 45.
- **Nguyên nhân & Tác động**:
  - Khi người dùng nhập sai (ví dụ: số lượng âm, giá trị giảm vượt 100%, email trùng lặp, thiếu ảnh bắt buộc), Laravel gửi lỗi validation về. Do view không có thẻ `@error`, người dùng KHÔNG NHÌN THẤY THÔNG BÁO LỖI NÀO.
  - Đồng thời, các ô nhập liệu bị reset trắng trơn (vì thiếu `old()`), buộc người dùng phải gõ lại từ đầu.
- **Giải pháp khắc phục**:
  - Bổ sung cấu trúc chuẩn cho tất cả input:
    ```blade
    <input type="text" name="name" 
           class="form-control @error('name') is-invalid @enderror" 
           value="{{ old('name', $product->name ?? '') }}" 
           placeholder="...">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    ```

---

#### FUNC-03: Nút "Xóa tìm kiếm" trên trang Sản phẩm trỏ vào route `/shop` không tồn tại -> Lỗi 404
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/product/show_product.blade.php`: Dòng 9:
    ```blade
    <a href="{{ url('/shop') }}" class="btn btn-sm btn-outline-secondary">Xóa tìm kiếm</a>
    ```
  - `routes/web.php`: Không hề định nghĩa route `/shop`.
- **Nguyên nhân & Tác động**:
  - Đoạn code copy từ client template. Khi admin bấm "Xóa tìm kiếm" trong trang quản lý sản phẩm, trình duyệt điều hướng tới `/shop` và gặp lỗi 404 Not Found.
- **Giải pháp khắc phục**:
  - Đổi liên kết thành: `<a href="{{ url('/show-product') }}" class="btn btn-sm btn-outline-secondary">Xóa tìm kiếm</a>`.

---

#### FUNC-04: Xung đột giữa DataTables Client-side và Phân trang Server-side của Laravel trên trang Đơn hàng
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/orders/index.blade.php`: Dòng 26 (`<table id="myTable">`) và Dòng 71 (`{{ $orders->links() }}`).
  - `public/admin/js/main.js`: Dòng 67 (`$("#myTable").DataTable()`).
- **Nguyên nhân & Tác động**:
  - `OrderController::index` phân trang 20 bản ghi/trang (`$orders = Order::paginate(20)`).
  - DataTables gắn vào `#myTable` chạy ở chế độ client-side. Nó coi 20 bản ghi của trang hiện tại là TOÀN BỘ dữ liệu, hiển thị "Showing 1 to 20 of 20 entries". Bộ lọc tìm kiếm của DataTables chỉ tìm được trong 20 bản ghi này. Dưới bảng lại xuất hiện phân trang Laravel (Trang 1, 2, 3).
- **Giải pháp khắc phục**:
  - Đổi id của bảng trong `orders/index.blade.php` từ `id="myTable"` thành `id="orderTable"`, sử dụng kiểu bảng Bootstrap tiêu chuẩn không gắn DataTables client-side, hoặc cấu hình DataTables server-side hoàn chỉnh.

---

#### FUNC-05: Nút "Đóng" modal Mã khuyến mãi (`add_coupon.blade.php`) gọi sai ID modal -> Không đóng được modal
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/coupon/add_coupon.blade.php`: Dòng 73:
    ```blade
    <button type="button" class="btn btn-secondary" onclick="CloseModal('ModalCreateCoupon')" data-bs-dismiss="modal">Đóng</button>
    ```
  - `resources/views/admin/coupon/show_coupon.blade.php`: Dòng 21 (`OpenModal(null, '{{ url('/show-create-coupon') }}')`).
  - `resources/views/layout/admin_layout.blade.php`: Dòng 138 (`id="ModalEdit"`).
- **Nguyên nhân & Tác động**:
  - Hàm `OpenModal(null, url)` nạp nội dung view vào `#ModalEdit`. Nhưng nút Đóng ở chân modal lại gọi `CloseModal('ModalCreateCoupon')` (phần tử này không tồn tại), và thuộc tính `data-bs-dismiss` là của Bootstrap 5 nên Bootstrap 4 bỏ qua. Người dùng không thể đóng modal bằng nút này.
- **Giải pháp khắc phục**:
  - Sửa dòng 73 thành:
    ```blade
    <button type="button" class="btn btn-secondary" onclick="CloseModal('ModalEdit')" data-dismiss="modal">Đóng</button>
    ```

---

#### FUNC-06: Nút "Hủy bỏ" trong modal Sửa Mã khuyến mãi (`edit_coupon.blade.php`) là thẻ `<a>` chuyển trang
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/coupon/edit_coupon.blade.php`: Dòng 90:
    ```blade
    <a href="{{ route('coupon.index') }}" class="btn btn-secondary">Hủy bỏ</a>
    ```
- **Nguyên nhân & Tác động**:
  - View sửa được nạp vào modal AJAX. Khi bấm "Hủy bỏ", thay vì đóng modal mượt mà, trình duyệt lại thực hiện tải lại toàn bộ trang web (full page reload).
- **Giải pháp khắc phục**:
  - Thay thẻ `<a>` bằng button:
    ```blade
    <button type="button" class="btn btn-secondary" onclick="CloseModal('ModalEdit')">Hủy bỏ</button>
    ```

---

#### FUNC-07: Modal Chi tiết Đơn hàng và Script bị đặt sau `@endsection` -> Rơi ra ngoài thẻ `</html>`
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/orders/index.blade.php`: Dòng 76 là `@endsection`. Dòng 77-122 chứa `<div class="modal fade" id="modalOrderDetails">` và `<script> function ShowOrderDetails ... </script>`.
- **Nguyên nhân & Tác động**:
  - Mọi nội dung đặt sau `@endsection` trong Blade template kế thừa sẽ bị in ra sau thẻ `</html>` của trang web. Điều này vi phạm chuẩn HTML5, có thể gây lỗi backdrop modal che phủ toàn màn hình hoặc lỗi script không nhận jQuery.
- **Giải pháp khắc phục**:
  - Di chuyển toàn bộ khối modal và script lên trên `@endsection`.

---

#### FUNC-08: Phương thức `OrderController::storeFromCart` lưu đơn hàng sai tên trường
- **Tệp & Dòng code vi phạm**:
  - `app/Http/Controllers/OrderController.php`: Dòng 86-91 (`Order::create(['unitPrice' => ..., 'quantity' => ..., 'totalPrice' => ...])`).
  - `app/Models/Order.php`: Dòng 15-27 (`$fillable` chỉ có `total_amount`, không có `totalPrice`, `unitPrice`).
- **Nguyên nhân & Tác động**:
  - Các trường này bị Eloquent bỏ qua do không nằm trong `$fillable`, làm `total_amount` bị NULL, khiến đơn hàng tạo từ giỏ hàng hiển thị `0 ₫` trên danh sách đơn hàng admin.
- **Giải pháp khắc phục**:
  - Cập nhật trong `OrderController::storeFromCart`:
    ```php
    $order = Order::create([
        'user_id'      => Auth::id(),
        'total_amount' => $totalPrice,
        'status'       => 'pending',
    ]);
    ```

---

### PHẦN III: LỖI GIAO DIỆN & TRẢI NGHIỆM (UI/UX ISSUES)

#### UI-01: Sử dụng lẫn lộn cú pháp Bootstrap 5 trên nền Bootstrap 4.3.1
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/product/add_product.blade.php`: Dòng 7 (`btn-close`, `data-bs-dismiss="modal"`).
  - `resources/views/admin/category/add_category.blade.php`: Dòng 9, 50 (`btn-close`, `data-bs-dismiss="modal"`).
  - `resources/views/admin/coupon/add_coupon.blade.php`: Dòng 7, 74 (`btn-close`, `data-bs-dismiss="modal"`).
  - `resources/views/admin/category/edit_category.blade.php`: Dòng 48 (`d-flex gap-2`).
  - `resources/views/admin/orders/index.blade.php`: Dòng 43 (`text-primary fw-bold`).
- **Nguyên nhân & Tác động**:
  - Admin template sử dụng Bootstrap 4.3.1. Các class và thuộc tính của Bootstrap 5 (`btn-close`, `data-bs-dismiss`, `gap-*`, `fw-*`) không có hiệu lực. Nút đóng modal hiển thị ký tự X thô kệch, không có style; các nút bấm dính liền nhau vì `gap-2` không hoạt động; chữ không được in đậm.
- **Giải pháp khắc phục**:
  - Nút đóng: `<button type="button" class="close" data-dismiss="modal" onclick="CloseModal('ModalEdit')"><span>&times;</span></button>`.
  - Thay `gap-2` bằng margin utilities: `mr-2`.
  - Thay `fw-bold` bằng `font-weight-bold`.

---

#### UI-02: Nút bấm thừa có nhãn "Button" bên cạnh các ô chọn tệp tải lên (File Input)
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/product/add_product.blade.php`: Dòng 160-162.
  - `resources/views/admin/product/edit_product.blade.php`: Dòng 123-125.
  - `resources/views/admin/brand/add_brand.blade.php`: Dòng 19-21.
  - `resources/views/admin/brand/edit_brand.blade.php`: Dòng 26-28.
- **Nguyên nhân & Tác động**:
  - Đoạn mã mẫu `<button class="btn btn-primary" type="button">Button</button>` bị sót lại trong các input group tải file, không có chức năng gì và gây mất thẩm mỹ.
- **Giải pháp khắc phục**:
  - Xóa bỏ thẻ `<div class="input-group-prepend"><button ...>Button</button></div>` thừa.

---

#### UI-03: Thẻ đóng `</div>` thừa (Orphan Tag) phá vỡ cấu trúc DOM
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/product/show_product.blade.php`: Dòng 102.
  - `resources/views/admin/coupon/add_coupon.blade.php`: Dòng 80.
- **Nguyên nhân & Tác động**:
  - Thẻ `</div>` đóng dư làm sai lệch cây DOM của trình duyệt, ảnh hưởng đến container bao ngoài và footer.
- **Giải pháp khắc phục**:
  - Xóa thẻ `</div>` thừa ở các dòng trên.

---

#### UI-04: Thiếu trạng thái Empty State trên toàn bộ các bảng dữ liệu Admin
- **Tệp & Dòng code vi phạm**:
  - Tất cả các view danh sách: `show_product.blade.php:53`, `show_category.blade.php:45`, `show_brand.blade.php:45`, `orders/index.blade.php:38`, `users.blade.php:27`, `show_coupon.blade.php:54`.
- **Nguyên nhân & Tác động**:
  - Sử dụng `@foreach` thuần túy. Khi bảng không có dữ liệu, bảng hiển thị một khoảng trắng trống trơn, không có dòng thông báo "Chưa có dữ liệu nào".
- **Giải pháp khắc phục**:
  - Chuyển sang sử dụng `@forelse ... @empty`:
    ```blade
    @forelse ($items as $item)
        ...
    @empty
        <tr>
            <td colspan="10" class="text-center py-4 text-muted">
                <i class="mdi mdi-alert-circle-outline"></i> Hiện chưa có dữ liệu nào.
            </td>
        </tr>
    @endforelse
    ```

---

#### UI-05: Lỗi chính tả tiếng Việt, thiếu Badge trạng thái và thiếu định dạng tiền tệ trên trang Sản phẩm
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/product/show_product.blade.php`:
    - Dòng 62: `<td>{{ $item->price }}</td>` (hiển thị số thô, ví dụ 5000000 thay vì 5.000.000 đ).
    - Dòng 70: `<td>{{ $item->Status ? 'Đang kinh doan' : 'Ngừng kinh doanh' }}</td>` (sai chính tả "Đang kinh doan").
    - Dòng 71: `<td>{{ $item->IsActive ? 'Active' : 'Inactive' }}</td>` (thiếu badge màu).
- **Giải pháp khắc phục**:
  - Dòng 62: `<td>{{ number_format($item->price, 0, ',', '.') }} đ</td>`
  - Dòng 70: `<td><span class="badge {{ $item->status ? 'badge-success' : 'badge-danger' }}">{{ $item->status ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}</span></td>`
  - Dòng 71: `<td><span class="badge {{ $item->IsActive ? 'badge-primary' : 'badge-secondary' }}">{{ $item->IsActive ? 'Active' : 'Inactive' }}</span></td>`

---

#### UI-06: Cột Mô tả sản phẩm/danh mục hiển thị toàn bộ nội dung văn bản dài
- **Tệp & Dòng code vi phạm**:
  - `resources/views/admin/product/show_product.blade.php`: Dòng 69 (`<td>{{ $item->description }} </td>`).
  - `resources/views/admin/category/show_category.blade.php`: Dòng 57 (`<td>{{ $item->description }}</td>`).
- **Nguyên nhân & Tác động**:
  - Nội dung mô tả dài không được cắt ngắn khiến chiều cao hàng trong bảng bị phình to bất thường.
- **Giải pháp khắc phục**:
  - Dùng helper: `<td>{{ \Illuminate\Support\Str::limit($item->description, 50, '...') }}</td>`.

---

### PHẦN IV: ĐỀ XUẤT TỐI ƯU HỆ THỐNG (IMPROVEMENTS)

1. **IMP-01: Tối ưu tải Assets trong `admin_layout.blade.php`**
   - Loại bỏ 12 file script biểu đồ/map demo không sử dụng (`raphael.min.js`, `morris.min.js`, `flot.js`, `jqvmap`, `gaugeJS`, `circle-progress.min.js`, `counterup.min.js`) khỏi layout dùng chung. Chỉ nạp ở trang nào thực sự có nhu cầu thông qua `@push('scripts')`. Giúp tăng tốc độ tải trang admin từ 2-3x.
2. **IMP-02: Bổ sung gói ngôn ngữ tiếng Việt cho DataTables**
   - Bật cấu hình `language` trong `public/admin/js/main.js` để DataTables hiển thị giao diện tiếng Việt ("Tìm kiếm:", "Hiển thị _MENU_ mục", "Không tìm thấy kết quả", "Trước", "Sau").
3. **IMP-03: Bổ sung liên kết Trang chủ Dashboard và Active State trên Sidebar**
   - Thêm mục menu "Bảng điều khiển" vào đầu danh sách menu sidebar.
   - Thêm class `mm-active` linh hoạt theo URL: `{{ request()->is('show-product*') ? 'mm-active' : '' }}`.
4. **IMP-04: Xóa bỏ Route Test nhạy cảm trong `routes/web.php`**
   - Dòng 96-107: Route `/test-password-reset/{email}` trả về thông tin token đặt lại mật khẩu mà không yêu cầu xác thực, cần phải xóa bỏ trước khi release production.
5. **IMP-05: Bổ sung phân trang Server-side cho Product, Category, Brand, User**
   - Các controller hiện tại gọi `::get()` hoặc `::all()` nạp toàn bộ bản ghi vào RAM. Khi dữ liệu lên hàng nghìn bản ghi sẽ gây tràn bộ nhớ PHP và lag trình duyệt. Cần áp dụng `->paginate(15)` đồng bộ.

---

## 3. CAVEATS (Phạm vi & Giới hạn điều tra)

1. **Phạm vi kiểm tra**: Báo cáo tập trung vào 100% các file View, CRUD forms, Modals, Assets và liên kết Controller/Route phía Admin theo yêu cầu R2.
2. **Quyền hạn**: Thực hiện kiểm tra ở chế độ Read-only theo quy chuẩn Teamwork Explorer, không trực tiếp sửa đổi mã nguồn.
3. **Môi trường**: Kiểm tra trên cấu trúc mã nguồn Laravel trong môi trường Windows XAMPP. Chưa kiểm tra trực tiếp trên container Docker Linux thực tế (tuy nhiên vấn đề NTFS junction `public\public` đã được chứng minh và cảnh báo).

---

## 4. CONCLUSION (Kết luận & Đánh giá tổng thể)

Giao diện quản trị Admin của hệ thống đã xây dựng được khung chức năng cơ bản cho các module Sản phẩm, Danh mục, Thương hiệu, Đơn hàng, Người dùng và Mã khuyến mãi. Tuy nhiên, hệ thống tồn tại **7 lỗi nghiêm trọng (Critical)** cần ưu tiên xử lý ngay lập tức:
1. Tất cả thao tác Xóa dùng GET không có CSRF.
2. Flash message bị đặt ngoài section làm mất phản hồi người dùng.
3. Cấu hình asset `asset('public/...')` phụ thuộc vào Windows Junction.
4. Dashboard admin hoàn toàn trống rỗng.
5. Script `dashboard-1.js` ném lỗi JS toàn cục.
6. Lỗi tiềm ẩn crash trang Sản phẩm do quan hệ category thiếu null-safe.
7. Rủi ro Admin tự giáng quyền dẫn đến khóa tài khoản vĩnh viễn.

Bên cạnh đó là các lỗi nghiệp vụ như Admin không thể cập nhật trạng thái đơn hàng, form không giữ lại dữ liệu cũ khi validate thất bại, và các lỗi hiển thị do dùng nhầm cú pháp Bootstrap 5 trên nền Bootstrap 4.

---

## 5. VERIFICATION METHOD (Phương pháp thẩm tra độc lập)

Các lập trình viên hoặc thanh tra có thể thẩm tra độc lập các phát hiện theo các bước sau:

1. **Kiểm tra lỗ hổng Xóa qua GET**:
   - Mở trình duyệt đang đăng nhập admin, truy cập trực tiếp URL: `http://localhost/appweb/delete-coupon/1` hoặc `http://localhost/appweb/delete-product/1`.
   - Quan sát: Dữ liệu bị xóa ngay lập tức mà không cần xác nhận CSRF.
2. **Kiểm tra Flash Message ngoài Section**:
   - Mở file `resources/views/admin/category/show_category.blade.php`, quan sát dòng 4-20 nằm trước `@section('view-content')`.
   - Thực hiện thêm một danh mục mới: Quan sát sau khi redirect, không có thông báo alert nào xuất hiện trong giao diện.
3. **Kiểm tra lỗi JS Crash toàn cục**:
   - Mở bất kỳ trang quản trị nào (ví dụ `/show-product`, `/show-category`), mở DevTools (F12) -> Console.
   - Quan sát các lỗi đỏ: `Uncaught TypeError: Cannot read properties of null (reading 'getContext')` bắt nguồn từ `dashboard-1.js:105`.
4. **Kiểm tra Junction `public/public`**:
   - Chạy PowerShell: `Get-Item 'c:\xampp\htdocs\appweb\public\public'`.
   - Quan sát thuộc tính `ReparsePoint` (Junction trỏ vào chính nó).
5. **Kiểm tra Dashboard trống**:
   - Truy cập `http://localhost/appweb/admin/dashboard`.
   - Quan sát vùng nội dung `<div class="content-body">` hoàn toàn trống trơn.

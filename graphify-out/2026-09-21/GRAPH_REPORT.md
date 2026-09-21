# Graph Report - appweb  (2026-09-21)

## Corpus Check
- 157 files · ~80,699 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 12 file(s) not represented in the graph (top: (none) 7, .ico 2, .example 1)

## Summary
- 1001 nodes · 1608 edges · 144 communities (61 shown, 83 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 105 edges (avg confidence: 0.92)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d46a9675`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Order
- web.php
- TestCase
- composer.json
- Controllers/OrderController.php
- Cart
- OderItemController
- package.json
- GHNService
- SampleDataSeeder
- UserFactory.php
- 3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues)
- 3.2. Nhóm 2: Lỗi Giao diện & Hiển thị (UI/UX Issues)
- Illuminate\Database\Schema\Blueprint
- Illuminate\Database\Migrations\Migration
- PHPUnit\Framework\TestCase
- logging.php
- artisan
- HƯỚNG DẪN KIỂM THỬ HỆ THỐNG - TESTER GUIDE
- 3.4. Nhóm 4: Đề xuất Tối ưu Hệ thống (Improvements)
- Illuminate\Support\Facades\Schema
- Module Quản Lý Đơn Hàng (Order Management)
- User
- console.php
- admin.brand.add_brand
- admin.category.add_category
- admin.product.add_product
- layout.footer_home
- Hướng dẫn sử dụng JavaScript cho chức năng giỏ hàng
- API DOCUMENTATION
- CartServiceTest
- Category
- 3.3. Nhóm 3: Lỗi Luồng Nghiệp vụ & Xử lý Dữ liệu (Functional Issues)
- CartController.php
- AppServiceProvider.php
- Illuminate\Http\Request
- FullFlowTest
- OderItem
- Controller
- BÁO CÁO RÀ SOÁT, KIỂM TOÁN HỆ THỐNG VÀ KHUYẾN NGHỊ KHẮC PHỤC TOÀN DIỆN
- HỆ THỐNG QUẢN LÝ BÁN HÀNG - E-COMMERCE MANAGEMENT SYSTEM
- AdminOrderManagementTest
- AdminController
- .orders
- Coupon
- 5. HƯỚNG DẪN KIỂM CHỨNG & THẨM TRA ĐỘC LẬP (VERIFICATION GUIDE)
- Tổng Kết
- 🚀 TÍNH NĂNG CHÍNH
- CẤU HÌNH MÔI TRƯỜNG
- Cấu trúc Module
- Cách sử dụng
- MODULE DOCUMENTATION
- Manual Testing Checklist
- Hướng dẫn sử dụng chức năng đăng nhập và quên mật khẩu
- DOCUMENTATION - HỆ THỐNG QUẢN LÝ BÁN HÀNG
- Cấu trúc file đã tạo/cập nhật
- bootstrap/app.php
- DATABASE SCHEMA
- ERROR HANDLING
- FILE UPLOAD SYSTEM
- Tính Năng Chưa Thực Hiện (Future)
- Các Vấn Đề Đã Khắc Phục
- 🛠️ CÀI ĐẶT VÀ CHẠY DỰ ÁN
- SampleDataSeeder.php
- AUTHENTICATION & AUTHORIZATION
- 🔧 CẤU HÌNH QUAN TRỌNG
- DEPLOYMENT GUIDE
- MAINTENANCE
- graphify (Mode: Always Use the Graph)
- Tính Năng Đã Thực Hiện
- Troubleshooting
- Database Schema
- 📊 BÁO CÁO KIỂM THỬ
- 📞 HỖ TRỢ
- 📈 ROADMAP

## God Nodes (most connected - your core abstractions)
1. `User` - 59 edges
2. `Order` - 47 edges
3. `Product` - 45 edges
4. `Category` - 43 edges
5. `Controller` - 30 edges
6. `Cart` - 28 edges
7. `Coupon` - 28 edges
8. `Brand` - 23 edges
9. `CartItem` - 22 edges
10. `AdminCatalogManagementTest` - 21 edges

## Surprising Connections (you probably didn't know these)
- `5.1 Cart Management` --references--> `CartController`  [INFERRED]
  BAO_CAO_KIEM_THU_CRUD.md → app/Http/Controllers/CartController.php
- `2. Lỗi Import Controller Trong Routes` --references--> `CouponController`  [INFERRED]
  BUG_FIXES.md → app/Http/Controllers/CouponController.php
- `[CRIT-08] Lỗ hổng Mass Assignment leo thang đặc quyền qua cột `role` trong Model `User`` --references--> `User`  [INFERRED]
  BAO_CAO_RA_SOAT_HE_THONG.md → app/Models/User.php
- `5.2 Authentication` --references--> `PasswordResetController`  [INFERRED]
  BAO_CAO_KIEM_THU_CRUD.md → app/Http/Controllers/Auth/PasswordResetController.php
- `1.1. Kiến trúc hệ thống và Ngăn xếp công nghệ` --references--> `PasswordResetController`  [INFERRED]
  BAO_CAO_RA_SOAT_HE_THONG.md → app/Http/Controllers/Auth/PasswordResetController.php

## Import Cycles
- None detected.

## Communities (144 total, 83 thin omitted)

### Community 0 - "Order"
Cohesion: 0.10
Nodes (4): OrderController, OrderController, Order, GHNOrderService

### Community 1 - "web.php"
Cohesion: 0.18
Nodes (3): PasswordResetController, HomeController, Illuminate\Support\Facades\Route

### Community 2 - "TestCase"
Cohesion: 0.18
Nodes (7): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile, Illuminate\Support\Facades\Storage, ExampleTest, OptimizationTest, TestCase

### Community 3 - "composer.json"
Cohesion: 0.04
Nodes (45): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+37 more)

### Community 4 - "Controllers/OrderController.php"
Cohesion: 0.24
Nodes (4): OrderItem, OrderService, Illuminate\Http\Client\ConnectionException, Illuminate\Support\Facades\Log

### Community 5 - "Cart"
Cohesion: 0.06
Nodes (17): CartController, CheckoutController, Cart, CartItem, HistorySearch, PaymentTransaction, ProductReview, CartService (+9 more)

### Community 6 - "OderItemController"
Cohesion: 0.11
Nodes (5): OderItemController, PlaceOrderRequest, StoreOderItemRequest, UpdateOderItemRequest, Illuminate\Foundation\Http\FormRequest

### Community 7 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 9 - "SampleDataSeeder"
Cohesion: 0.19
Nodes (5): DatabaseSeeder, OderItemSeeder, SampleDataSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 10 - "UserFactory.php"
Cohesion: 0.27
Nodes (5): OderItemFactory, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, static

### Community 11 - "3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues)"
Cohesion: 0.11
Nodes (17): AdminMiddleware, Authenticate, 3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues), [CRIT-01] Tất cả thao tác Xóa (Delete) trong Admin dùng phương thức HTTP GET và không có bảo vệ CSRF, [CRIT-03] View gọi named route `route('register')` chưa từng được định nghĩa, [CRIT-04] Truy vấn cột `status` không tồn tại trong bảng `_category`, [CRIT-06] `CartController@checkCoupon` truy cập trực tiếp key mảng không qua validation, [CRIT-07] Truy cập `$item->category->name` thiếu null-safe operator gây Crash Fatal (+9 more)

### Community 12 - "3.2. Nhóm 2: Lỗi Giao diện & Hiển thị (UI/UX Issues)"
Cohesion: 0.12
Nodes (15): 3.2. Nhóm 2: Lỗi Giao diện & Hiển thị (UI/UX Issues), [UI-01] Mất hoàn toàn thanh tìm kiếm, giỏ hàng và menu tài khoản trên màn hình di động, [UI-02] Chế độ xem danh sách (List View tab `#tab-6`) bị rỗng trắng tinh, [UI-03] Lệch số lượng cột trong bảng hóa đơn Checkout, [UI-04] Nút "Xóa tìm kiếm" sản phẩm admin trỏ vào route `/shop` không tồn tại, [UI-05] Khách mua hàng bấm Đăng nhập bị điều hướng vào Admin (`/admin`), [UI-06] Dashboard Admin (`/admin/dashboard`) hoàn toàn trống rỗng, [UI-07] Script `dashboard-1.js` ném Uncaught TypeError trên TẤT CẢ các trang Admin (+7 more)

### Community 16 - "PHPUnit\Framework\TestCase"
Cohesion: 0.38
Nodes (3): PHPUnit\Framework\TestCase, ExampleTest, HelloWorldTest

### Community 17 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 19 - "HƯỚNG DẪN KIỂM THỬ HỆ THỐNG - TESTER GUIDE"
Cohesion: 0.05
Nodes (41): 1.1 Đăng nhập Admin, 1.2 Quên mật khẩu, 1.3 Đăng xuất, 2.1 Xem danh sách sản phẩm, 2.2 Tạo sản phẩm mới, 2.3 Chỉnh sửa sản phẩm, 2.4 Xóa sản phẩm, 3.1 Xem danh sách danh mục (+33 more)

### Community 21 - "3.4. Nhóm 4: Đề xuất Tối ưu Hệ thống (Improvements)"
Cohesion: 0.25
Nodes (8): 3.4. Nhóm 4: Đề xuất Tối ưu Hệ thống (Improvements), [IMP-01] Chuẩn hóa đường dẫn tài nguyên `asset()` và gỡ bỏ liên kết Windows Junction `public/public`, [IMP-02] Tối ưu tải Assets trong `admin_layout.blade.php`, [IMP-03] Bổ sung gói ngôn ngữ tiếng Việt (i18n) cho thư viện DataTables, [IMP-04] Tối ưu hóa truy vấn View Composer tránh duplicate query Category, [IMP-05] Xóa bỏ Route Test nhạy cảm `/test-password-reset/{email}`, [IMP-06] Bổ sung phân trang Server-side cho Product, Category, Brand, User, [IMP-07] Tách biệt giao diện Đăng nhập/Đăng ký Client và Xây dựng trang báo lỗi 404/500

### Community 36 - "Module Quản Lý Đơn Hàng (Order Management)"
Cohesion: 0.22
Nodes (8): API Endpoints Summary, Credits, Manual Test Flow, Module Quản Lý Đơn Hàng (Order Management), Notes, Testing, Tinker Test, Tổng quan

### Community 38 - "User"
Cohesion: 0.12
Nodes (5): User, Illuminate\Contracts\Auth\CanResetPassword, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, AuthWorkflowTest

### Community 90 - "Hướng dẫn sử dụng JavaScript cho chức năng giỏ hàng"
Cohesion: 0.08
Nodes (24): 1. Layout chính (`resources/views/layout/home_layout.blade.php`), 1. Tự động (Đã được thiết lập sẵn), 2. Sử dụng thủ công, 2. Trang sản phẩm (`resources/views/client/home/index_home.blade.php`), 3. Trang chi tiết sản phẩm (`resources/views/client/product/detail.blade.php`), 3. Với form số lượng, 4. Controllers và Routes, ✅ Bộ đếm giỏ hàng (+16 more)

### Community 91 - "API DOCUMENTATION"
Cohesion: 0.10
Nodes (21): API DOCUMENTATION, Authentication Endpoints, Brand Management Endpoints, Category Management Endpoints, Create Brand, Create Category, Create Product, Delete Brand (+13 more)

### Community 93 - "Category"
Cohesion: 0.05
Nodes (22): BrandController, CategoryController, Brand, Category, 2.1 Thông tin module, 2.2 Chức năng CRUD, 2.3 Đánh giá tổng thể, 2. MODULE QUẢN LÝ DANH MỤC (CATEGORY) (+14 more)

### Community 94 - "3.3. Nhóm 3: Lỗi Luồng Nghiệp vụ & Xử lý Dữ liệu (Functional Issues)"
Cohesion: 0.17
Nodes (12): 3.3. Nhóm 3: Lỗi Luồng Nghiệp vụ & Xử lý Dữ liệu (Functional Issues), [FUNC-01] Admin hoàn toàn không có tính năng cập nhật trạng thái đơn hàng (Order Status), [FUNC-02] Toàn bộ Form CRUD Thêm & Sửa thiếu hiển thị lỗi Validation và thiếu giữ lại dữ liệu cũ, [FUNC-03] Toàn bộ Flash Messages đặt ngoài `@section('view-content')` bị Blade nuốt mất, [FUNC-04] Xung đột DataTables Client-side và Phân trang Server-side của Laravel trên trang Đơn hàng, [FUNC-05] Nút "Đóng" modal Coupon gọi sai ID modal không đóng được, [FUNC-06] Nút "Hủy bỏ" trong modal sửa coupon là thẻ `<a>` gây reload toàn trang, [FUNC-07] Modal Chi tiết Đơn hàng và Script bị đặt sau `@endsection` (+4 more)

### Community 95 - "CartController.php"
Cohesion: 0.23
Nodes (6): Carbon\Carbon, Illuminate\Support\Collection, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\Session, Illuminate\Validation\Rule, now

### Community 96 - "AppServiceProvider.php"
Cohesion: 0.33
Nodes (5): AppServiceProvider, Illuminate\Pagination\Paginator, Illuminate\Support\Facades\Cache, Illuminate\Support\Facades\View, Illuminate\Support\ServiceProvider

### Community 97 - "Illuminate\Http\Request"
Cohesion: 0.19
Nodes (4): ProductController, Product, 1.1 Thông tin module, Illuminate\Http\Request

### Community 99 - "OderItem"
Cohesion: 0.23
Nodes (3): OderItem, OderItemPolicy, Illuminate\Auth\Access\Response

### Community 100 - "Controller"
Cohesion: 0.39
Nodes (6): Controller, ProfilesController, Illuminate\Foundation\Auth\Access\AuthorizesRequests, Illuminate\Foundation\Bus\DispatchesJobs, Illuminate\Foundation\Validation\ValidatesRequests, Illuminate\Routing\Controller

### Community 101 - "BÁO CÁO RÀ SOÁT, KIỂM TOÁN HỆ THỐNG VÀ KHUYẾN NGHỊ KHẮC PHỤC TOÀN DIỆN"
Cohesion: 0.29
Nodes (6): 3. DANH MỤC LỖI CHI TIẾT VÀ GIẢI PHÁP KHẮC PHỤC HOÀN CHỈNH, 4. LỘ TRÌNH VÀ KẾ HOẠCH TRIỂN KHAI KHẮC PHỤC (ROADMAP & ACTION PLAN), BÁO CÁO RÀ SOÁT, KIỂM TOÁN HỆ THỐNG VÀ KHUYẾN NGHỊ KHẮC PHỤC TOÀN DIỆN, Bảng phân bổ nguồn lực và Tiêu chí nghiệm thu (KPI Acceptance Criteria), (COMPREHENSIVE AUDIT & RECOMMENDATIONS REPORT), MỤC LỤC

### Community 102 - "HỆ THỐNG QUẢN LÝ BÁN HÀNG - E-COMMERCE MANAGEMENT SYSTEM"
Cohesion: 0.20
Nodes (10): 📄 Báo cáo chi tiết, Bảo mật, 📁 CẤU TRÚC DỰ ÁN, Cần cải thiện, HỆ THỐNG QUẢN LÝ BÁN HÀNG - E-COMMERCE MANAGEMENT SYSTEM, 📝 LICENSE, 🔗 Links quan trọng, 🚨 LƯU Ý QUAN TRỌNG (+2 more)

### Community 104 - "AdminController"
Cohesion: 0.05
Nodes (35): AdminController, 1.2 Chức năng CRUD, 1.3 Đánh giá tổng thể, 1. MODULE QUẢN LÝ SẢN PHẨM (PRODUCT), 4.1 Thông tin module, 4.2 Chức năng CRUD, 4.3 Đánh giá tổng thể, 4. MODULE QUẢN LÝ NGƯỜI DÙNG (USER) (+27 more)

### Community 105 - ".orders"
Cohesion: 0.38
Nodes (6): [CRIT-05] `OrderController@storeFromCart` ghi sai cấu trúc bảng `orders` và thiếu các trường NOT NULL, 1. Lỗi Migration - Bảng `orders` Đã Tồn Tại, 2. Lỗi Import Controller Trong Routes, 3. Lỗi Giá Trị Giảm Giá Không Được Lưu Vào Database, 4. Lỗi Miễn Phí Vận Chuyển Không Hiển Thị, Ngày 24-25/11/2025

### Community 107 - "5. HƯỚNG DẪN KIỂM CHỨNG & THẨM TRA ĐỘC LẬP (VERIFICATION GUIDE)"
Cohesion: 0.33
Nodes (5): 5.1. Kiểm tra tĩnh qua dòng lệnh CLI, 5.2. Kịch bản kiểm chứng tự động qua PHP CLI script, 5.3. Kịch bản kiểm thử tương tác trên trình duyệt (Browser Test Cases), 5.4. Bảng kiểm tra nghiệm thu (Sign-off Checklist), 5. HƯỚNG DẪN KIỂM CHỨNG & THẨM TRA ĐỘC LẬP (VERIFICATION GUIDE)

### Community 108 - "Tổng Kết"
Cohesion: 0.25
Nodes (7): Commands Đã Chạy:, Cách Kiểm Tra Log:, Files Đã Sửa:, Migrations Đã Tạo:, Nhật Ký Sửa Lỗi (Bug Fixes Log), Tổng Kết, Vấn Đề Còn Tồn Tại (Cần Test):

### Community 109 - "🚀 TÍNH NĂNG CHÍNH"
Cohesion: 0.40
Nodes (5): ✅ Giỏ Hàng & Đơn Hàng, ✅ Hệ Thống Xác Thực, ✅ Module CRUD Hoàn Chỉnh, ✅ Quản Lý File Upload, 🚀 TÍNH NĂNG CHÍNH

### Community 110 - "CẤU HÌNH MÔI TRƯỜNG"
Cohesion: 0.50
Nodes (4): Cài đặt, Cấu hình .env, CẤU HÌNH MÔI TRƯỜNG, Yêu cầu hệ thống

### Community 111 - "Cấu trúc Module"
Cohesion: 0.25
Nodes (8): 1. Models, 2. Controllers, 3. Routes (`routes/web.php`), 4. Views, 5. Menu Admin, Admin Views, Client Views, Cấu trúc Module

### Community 112 - "Cách sử dụng"
Cohesion: 0.50
Nodes (4): Cách sử dụng, Quên mật khẩu, Test chức năng, Đăng nhập

### Community 114 - "MODULE DOCUMENTATION"
Cohesion: 0.29
Nodes (7): 1. ProductController, 2. CategoryController, 3. BrandController, Methods, Methods, Methods, MODULE DOCUMENTATION

### Community 115 - "Manual Testing Checklist"
Cohesion: 0.29
Nodes (7): Authentication, Automated Testing (Recommended), Brand Module, Category Module, Manual Testing Checklist, Product Module, TESTING GUIDE

### Community 116 - "Hướng dẫn sử dụng chức năng đăng nhập và quên mật khẩu"
Cohesion: 0.29
Nodes (7): 1. Validation đăng nhập, 2. Xử lý lỗi đăng nhập, 3. Chức năng quên mật khẩu, Các chức năng đã được cải thiện, Cần cấu hình thêm, Hướng dẫn sử dụng chức năng đăng nhập và quên mật khẩu, Lưu ý quan trọng

### Community 117 - "DOCUMENTATION - HỆ THỐNG QUẢN LÝ BÁN HÀNG"
Cohesion: 0.33
Nodes (6): Chức năng chính, CẤU TRÚC DỰ ÁN, DOCUMENTATION - HỆ THỐNG QUẢN LÝ BÁN HÀNG, MỤC LỤC, Thông tin cơ bản, TỔNG QUAN HỆ THỐNG

### Community 118 - "Cấu trúc file đã tạo/cập nhật"
Cohesion: 0.33
Nodes (6): Controllers, Cấu trúc file đã tạo/cập nhật, Database, Models, Routes, Views

### Community 119 - "bootstrap/app.php"
Cohesion: 0.33
Nodes (3): Illuminate\Foundation\Application, Illuminate\Foundation\Configuration\Exceptions, Illuminate\Foundation\Configuration\Middleware

### Community 120 - "DATABASE SCHEMA"
Cohesion: 0.40
Nodes (5): Bảng Brands, Bảng Categories, Bảng Products, Bảng Users, DATABASE SCHEMA

### Community 121 - "ERROR HANDLING"
Cohesion: 0.40
Nodes (5): Common Error Scenarios, Database Errors, ERROR HANDLING, File Upload Errors, Validation Errors

### Community 122 - "FILE UPLOAD SYSTEM"
Cohesion: 0.40
Nodes (5): Cấu trúc thư mục, File Naming Convention, FILE UPLOAD SYSTEM, Security Features, Supported Formats

### Community 123 - "Tính Năng Chưa Thực Hiện (Future)"
Cohesion: 0.33
Nodes (6): 🔲 Advanced Features, 🔲 Order Status, 🔲 Payment Integration, 🔲 Shipping, 🔲 Testing, Tính Năng Chưa Thực Hiện (Future)

### Community 125 - "Các Vấn Đề Đã Khắc Phục"
Cohesion: 0.40
Nodes (4): 1. Giỏ hàng dùng chung giữa users, 2. Đơn hàng không hiển thị admin dashboard, 3. Data không lưu vào DB, Các Vấn Đề Đã Khắc Phục

### Community 126 - "🛠️ CÀI ĐẶT VÀ CHẠY DỰ ÁN"
Cohesion: 0.50
Nodes (4): Cài đặt, 🛠️ CÀI ĐẶT VÀ CHẠY DỰ ÁN, Truy cập hệ thống, Yêu cầu hệ thống

### Community 127 - "SampleDataSeeder.php"
Cohesion: 0.19
Nodes (6): Exception, Illuminate\Support\Facades\DB, Illuminate\Support\Facades\File, Illuminate\Support\Facades\Http, Illuminate\Support\Facades\Mail, Illuminate\Support\Str

### Community 128 - "AUTHENTICATION & AUTHORIZATION"
Cohesion: 0.50
Nodes (4): AUTHENTICATION & AUTHORIZATION, Middleware, Password Reset Flow, User Roles

### Community 129 - "🔧 CẤU HÌNH QUAN TRỌNG"
Cohesion: 0.50
Nodes (4): 🔧 CẤU HÌNH QUAN TRỌNG, Database, File Upload, Security

### Community 130 - "DEPLOYMENT GUIDE"
Cohesion: 0.50
Nodes (4): Deployment Commands, DEPLOYMENT GUIDE, Environment Variables, Production Checklist

### Community 131 - "MAINTENANCE"
Cohesion: 0.50
Nodes (4): MAINTENANCE, Monitoring, Regular Tasks, Troubleshooting

### Community 132 - "graphify (Mode: Always Use the Graph)"
Cohesion: 0.50
Nodes (3): graphify (Mode: Always Use the Graph), Project Rules: c:\xampp\htdocs\appweb, Strict Policy: Always Use The Graph

### Community 134 - "Tính Năng Đã Thực Hiện"
Cohesion: 0.40
Nodes (5): ✅ Bug Fixes, ✅ Core Features, ✅ Security, Tính Năng Đã Thực Hiện, ✅ UI/UX

### Community 135 - "Troubleshooting"
Cohesion: 0.50
Nodes (4): Lỗi: "No data available in table", Lỗi: "SQLSTATE[23000]: Integrity constraint violation", Lỗi: "Undefined variable $cart", Troubleshooting

### Community 140 - "Database Schema"
Cohesion: 0.67
Nodes (3): Database Schema, Table: `oder_items`, Table: `orders`

### Community 141 - "📊 BÁO CÁO KIỂM THỬ"
Cohesion: 0.67
Nodes (3): 📊 BÁO CÁO KIỂM THỬ, Kết quả kiểm thử CRUD, Điểm tổng thể: **8.5/10**

### Community 142 - "📞 HỖ TRỢ"
Cohesion: 0.67
Nodes (3): Development, 📞 HỖ TRỢ, Troubleshooting

### Community 143 - "📈 ROADMAP"
Cohesion: 0.67
Nodes (3): 📈 ROADMAP, Version 2.0 (Planned), Version 3.0 (Future)

## Knowledge Gaps
- **287 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+282 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 494 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **83 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `BÁO CÁO KIỂM THỬ CHỨC NĂNG CRUD HỆ THỐNG QUẢN LÝ BÁN HÀNG` connect `AdminController` to `README.md`, `Category`?**
  _High betweenness centrality (0.201) - this node is a cross-community bridge._
- **Why does `DOCUMENTATION - HỆ THỐNG QUẢN LÝ BÁN HÀNG` connect `DOCUMENTATION - HỆ THỐNG QUẢN LÝ BÁN HÀNG` to `AUTHENTICATION & AUTHORIZATION`, `DEPLOYMENT GUIDE`, `MAINTENANCE`, `CẤU HÌNH MÔI TRƯỜNG`, `MODULE DOCUMENTATION`, `Manual Testing Checklist`, `DATABASE SCHEMA`, `ERROR HANDLING`, `FILE UPLOAD SYSTEM`, `API DOCUMENTATION`, `README.md`?**
  _High betweenness centrality (0.127) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `Order`, `web.php`, `TestCase`, `OderItem`, `FullFlowTest`, `Cart`, `AdminController`, `.orders`, `SampleDataSeeder`, `3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues)`, `3.2. Nhóm 2: Lỗi Giao diện & Hiển thị (UI/UX Issues)`, `Category`, `CartController.php`, `SampleDataSeeder.php`?**
  _High betweenness centrality (0.118) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `User` (e.g. with `4.1 Thông tin module` and `[CRIT-08] Lỗ hổng Mass Assignment leo thang đặc quyền qua cột `role` trong Model `User``) actually correct?**
  _`User` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `Order` (e.g. with `3. Lỗi Giá Trị Giảm Giá Không Được Lưu Vào Database` and `4. Lỗi Miễn Phí Vận Chuyển Không Hiển Thị`) actually correct?**
  _`Order` has 3 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _287 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Order` be split into smaller, more focused modules?**
  _Cohesion score 0.10338680926916222 - nodes in this community are weakly interconnected._
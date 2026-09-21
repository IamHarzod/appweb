# Graph Report - appweb  (2026-09-21)

## Corpus Check
- 155 files · ~78,735 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 12 file(s) not represented in the graph (top: (none) 7, .ico 2, .example 1)

## Summary
- 992 nodes · 1587 edges · 138 communities (56 shown, 82 thin omitted)
- Extraction: 93% EXTRACTED · 7% INFERRED · 0% AMBIGUOUS · INFERRED: 105 edges (avg confidence: 0.92)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `d46a9675`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Order
- Category
- TestCase
- composer.json
- .view
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
- Brand
- 3.3. Nhóm 3: Lỗi Luồng Nghiệp vụ & Xử lý Dữ liệu (Functional Issues)
- CartController.php
- PasswordResetController
- Product
- FullFlowTest
- OderItem
- Controller
- Authenticate.php
- HỆ THỐNG QUẢN LÝ BÁN HÀNG - E-COMMERCE MANAGEMENT SYSTEM
- AdminOrderManagementTest
- .users
- .orders
- Coupon
- 1.2 Chức năng CRUD
- Tổng Kết
- Illuminate\Http\Request
- 2.2 Chức năng CRUD
- Cấu trúc Module
- BÁO CÁO KIỂM THỬ CHỨC NĂNG CRUD HỆ THỐNG QUẢN LÝ BÁN HÀNG
- Illuminate\Support\Facades\Schema
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
- 7. KHUYẾN NGHỊ
- SampleDataSeeder.php
- AUTHENTICATION & AUTHORIZATION
- Brand Management Endpoints
- DEPLOYMENT GUIDE
- MAINTENANCE
- graphify (Mode: Always Use the Graph)
- Product Management Endpoints
- Tính Năng Đã Thực Hiện
- Troubleshooting

## God Nodes (most connected - your core abstractions)
1. `User` - 59 edges
2. `Order` - 47 edges
3. `Category` - 43 edges
4. `Product` - 43 edges
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
- `4.1 Thông tin module` --references--> `AdminController`  [INFERRED]
  BAO_CAO_KIEM_THU_CRUD.md → app/Http/Controllers/AdminController.php
- `5.2 Authentication` --references--> `AdminController`  [INFERRED]
  BAO_CAO_KIEM_THU_CRUD.md → app/Http/Controllers/AdminController.php

## Import Cycles
- None detected.

## Communities (138 total, 82 thin omitted)

### Community 0 - "Order"
Cohesion: 0.12
Nodes (3): OrderController, Order, GHNOrderService

### Community 1 - "Category"
Cohesion: 0.13
Nodes (11): CategoryController, HomeController, ProfilesController, Category, 2.1 Thông tin module, 1.1. Kiến trúc hệ thống và Ngăn xếp công nghệ, 1.2. Thống kê phạm vi kiểm toán, 1.3. Tóm tắt kết quả kiểm toán theo cấp độ rủi ro (+3 more)

### Community 2 - "TestCase"
Cohesion: 0.17
Nodes (7): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile, Illuminate\Support\Facades\Storage, ExampleTest, OptimizationTest, TestCase

### Community 3 - "composer.json"
Cohesion: 0.04
Nodes (45): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+37 more)

### Community 5 - "Cart"
Cohesion: 0.05
Nodes (19): CartController, CheckoutController, Cart, CartItem, HistorySearch, OrderItem, PaymentTransaction, CartService (+11 more)

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
Cohesion: 0.15
Nodes (12): 3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues), [CRIT-01] Tất cả thao tác Xóa (Delete) trong Admin dùng phương thức HTTP GET và không có bảo vệ CSRF, [CRIT-03] View gọi named route `route('register')` chưa từng được định nghĩa, [CRIT-04] Truy vấn cột `status` không tồn tại trong bảng `_category`, [CRIT-05] `OrderController@storeFromCart` ghi sai cấu trúc bảng `orders` và thiếu các trường NOT NULL, [CRIT-06] `CartController@checkCoupon` truy cập trực tiếp key mảng không qua validation, [CRIT-07] Truy cập `$item->category->name` thiếu null-safe operator gây Crash Fatal, [CRIT-08] Lỗ hổng Mass Assignment leo thang đặc quyền qua cột `role` trong Model `User` (+4 more)

### Community 12 - "3.2. Nhóm 2: Lỗi Giao diện & Hiển thị (UI/UX Issues)"
Cohesion: 0.05
Nodes (39): AppServiceProvider, 2. BẢNG MA TRẬN PHÂN LOẠI LỖI TOÀN DIỆN (AUDIT MATRIX), 3.2. Nhóm 2: Lỗi Giao diện & Hiển thị (UI/UX Issues), 3.4. Nhóm 4: Đề xuất Tối ưu Hệ thống (Improvements), 3. DANH MỤC LỖI CHI TIẾT VÀ GIẢI PHÁP KHẮC PHỤC HOÀN CHỈNH, 4. LỘ TRÌNH VÀ KẾ HOẠCH TRIỂN KHAI KHẮC PHỤC (ROADMAP & ACTION PLAN), 5.1. Kiểm tra tĩnh qua dòng lệnh CLI, 5.2. Kịch bản kiểm chứng tự động qua PHP CLI script (+31 more)

### Community 16 - "PHPUnit\Framework\TestCase"
Cohesion: 0.38
Nodes (3): PHPUnit\Framework\TestCase, ExampleTest, HelloWorldTest

### Community 17 - "logging.php"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 19 - "HƯỚNG DẪN KIỂM THỬ HỆ THỐNG - TESTER GUIDE"
Cohesion: 0.05
Nodes (41): 1.1 Đăng nhập Admin, 1.2 Quên mật khẩu, 1.3 Đăng xuất, 2.1 Xem danh sách sản phẩm, 2.2 Tạo sản phẩm mới, 2.3 Chỉnh sửa sản phẩm, 2.4 Xóa sản phẩm, 3.1 Xem danh sách danh mục (+33 more)

### Community 36 - "Module Quản Lý Đơn Hàng (Order Management)"
Cohesion: 0.25
Nodes (7): API Endpoints Summary, Credits, Manual Test Flow, Module Quản Lý Đơn Hàng (Order Management), Testing, Tinker Test, Tổng quan

### Community 38 - "User"
Cohesion: 0.12
Nodes (5): User, Illuminate\Contracts\Auth\CanResetPassword, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, AuthWorkflowTest

### Community 90 - "Hướng dẫn sử dụng JavaScript cho chức năng giỏ hàng"
Cohesion: 0.08
Nodes (23): 1. Layout chính (`resources/views/layout/home_layout.blade.php`), 1. Tự động (Đã được thiết lập sẵn), 2. Sử dụng thủ công, 2. Trang sản phẩm (`resources/views/client/home/index_home.blade.php`), 3. Trang chi tiết sản phẩm (`resources/views/client/product/detail.blade.php`), 3. Với form số lượng, ✅ Bộ đếm giỏ hàng, CSRF token lỗi: (+15 more)

### Community 91 - "API DOCUMENTATION"
Cohesion: 0.18
Nodes (11): API DOCUMENTATION, Authentication Endpoints, Category Management Endpoints, Create Category, Delete Category, Get All Categories, Login, Password Reset (+3 more)

### Community 93 - "Brand"
Cohesion: 0.07
Nodes (11): BrandController, Brand, 3.1 Thông tin module, 3.2 Chức năng CRUD, 3.3 Đánh giá tổng thể, 3. MODULE QUẢN LÝ THƯƠNG HIỆU (BRAND), ✅ CREATE (Tạo mới), ✅ DELETE (Xóa) (+3 more)

### Community 94 - "3.3. Nhóm 3: Lỗi Luồng Nghiệp vụ & Xử lý Dữ liệu (Functional Issues)"
Cohesion: 0.18
Nodes (11): 3.3. Nhóm 3: Lỗi Luồng Nghiệp vụ & Xử lý Dữ liệu (Functional Issues), [FUNC-01] Admin hoàn toàn không có tính năng cập nhật trạng thái đơn hàng (Order Status), [FUNC-02] Toàn bộ Form CRUD Thêm & Sửa thiếu hiển thị lỗi Validation và thiếu giữ lại dữ liệu cũ, [FUNC-03] Toàn bộ Flash Messages đặt ngoài `@section('view-content')` bị Blade nuốt mất, [FUNC-04] Xung đột DataTables Client-side và Phân trang Server-side của Laravel trên trang Đơn hàng, [FUNC-05] Nút "Đóng" modal Coupon gọi sai ID modal không đóng được, [FUNC-06] Nút "Hủy bỏ" trong modal sửa coupon là thẻ `<a>` gây reload toàn trang, [FUNC-07] Modal Chi tiết Đơn hàng và Script bị đặt sau `@endsection` (+3 more)

### Community 95 - "CartController.php"
Cohesion: 0.14
Nodes (10): Carbon\Carbon, Illuminate\Http\Client\ConnectionException, Illuminate\Support\Collection, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Log, Illuminate\Support\Facades\Mail, Illuminate\Support\Facades\Session (+2 more)

### Community 96 - "PasswordResetController"
Cohesion: 0.25
Nodes (4): PasswordResetController, 5.1 Cart Management, 5.2 Authentication, 5. CÁC MODULE KHÁC

### Community 97 - "Product"
Cohesion: 0.24
Nodes (3): ProductController, Product, 1.1 Thông tin module

### Community 99 - "OderItem"
Cohesion: 0.25
Nodes (3): OderItem, OderItemPolicy, Illuminate\Auth\Access\Response

### Community 100 - "Controller"
Cohesion: 0.60
Nodes (5): Controller, Illuminate\Foundation\Auth\Access\AuthorizesRequests, Illuminate\Foundation\Bus\DispatchesJobs, Illuminate\Foundation\Validation\ValidatesRequests, Illuminate\Routing\Controller

### Community 101 - "Authenticate.php"
Cohesion: 0.29
Nodes (6): AdminMiddleware, Authenticate, [CRIT-09] Lỗ hổng Admin tự giáng quyền dẫn tới khóa tài khoản vĩnh viễn, Closure, Illuminate\Auth\Middleware\Authenticate, Symfony\Component\HttpFoundation\Response

### Community 102 - "HỆ THỐNG QUẢN LÝ BÁN HÀNG - E-COMMERCE MANAGEMENT SYSTEM"
Cohesion: 0.06
Nodes (32): 📄 Báo cáo chi tiết, 📊 BÁO CÁO KIỂM THỬ, Bảo mật, Cài đặt, 🛠️ CÀI ĐẶT VÀ CHẠY DỰ ÁN, 🔧 CẤU HÌNH QUAN TRỌNG, 📁 CẤU TRÚC DỰ ÁN, Cần cải thiện (+24 more)

### Community 104 - ".users"
Cohesion: 0.25
Nodes (8): 4.1 Thông tin module, 4.2 Chức năng CRUD, 4.3 Đánh giá tổng thể, 4. MODULE QUẢN LÝ NGƯỜI DÙNG (USER), ✅ CREATE (Tạo mới), ❌ DELETE (Xóa), ✅ READ (Đọc/Xem), ✅ UPDATE (Cập nhật)

### Community 105 - ".orders"
Cohesion: 0.24
Nodes (9): [FUNC-08] Phương thức xóa đơn hàng `destroy()` không dùng DB Transaction, 1. Lỗi Migration - Bảng `orders` Đã Tồn Tại, 2. Lỗi Import Controller Trong Routes, 3. Lỗi Giá Trị Giảm Giá Không Được Lưu Vào Database, 4. Lỗi Miễn Phí Vận Chuyển Không Hiển Thị, Ngày 24-25/11/2025, Database Schema, Table: `oder_items` (+1 more)

### Community 107 - "1.2 Chức năng CRUD"
Cohesion: 0.29
Nodes (7): 1.2 Chức năng CRUD, 1.3 Đánh giá tổng thể, 1. MODULE QUẢN LÝ SẢN PHẨM (PRODUCT), ✅ CREATE (Tạo mới), ✅ DELETE (Xóa), ✅ READ (Đọc/Xem), ✅ UPDATE (Cập nhật)

### Community 108 - "Tổng Kết"
Cohesion: 0.25
Nodes (7): Commands Đã Chạy:, Cách Kiểm Tra Log:, Files Đã Sửa:, Migrations Đã Tạo:, Nhật Ký Sửa Lỗi (Bug Fixes Log), Tổng Kết, Vấn Đề Còn Tồn Tại (Cần Test):

### Community 109 - "Illuminate\Http\Request"
Cohesion: 0.25
Nodes (3): AdminController, [UI-06] Dashboard Admin (`/admin/dashboard`) hoàn toàn trống rỗng, Illuminate\Http\Request

### Community 110 - "2.2 Chức năng CRUD"
Cohesion: 0.29
Nodes (7): 2.2 Chức năng CRUD, 2.3 Đánh giá tổng thể, 2. MODULE QUẢN LÝ DANH MỤC (CATEGORY), ✅ CREATE (Tạo mới), ✅ DELETE (Xóa), ✅ READ (Đọc/Xem), ✅ UPDATE (Cập nhật)

### Community 111 - "Cấu trúc Module"
Cohesion: 0.25
Nodes (8): 1. Models, 2. Controllers, 3. Routes (`routes/web.php`), 4. Views, 5. Menu Admin, Admin Views, Client Views, Cấu trúc Module

### Community 112 - "BÁO CÁO KIỂM THỬ CHỨC NĂNG CRUD HỆ THỐNG QUẢN LÝ BÁN HÀNG"
Cohesion: 0.25
Nodes (8): 6.1 Điểm mạnh, 6.2 Điểm cần cải thiện, 6.3 Bảo mật, 6. ĐÁNH GIÁ TỔNG QUAN, 8. KẾT LUẬN, BÁO CÁO KIỂM THỬ CHỨC NĂNG CRUD HỆ THỐNG QUẢN LÝ BÁN HÀNG, THÔNG TIN CHUNG, TỔNG QUAN CÁC MODULE CRUD

### Community 114 - "MODULE DOCUMENTATION"
Cohesion: 0.29
Nodes (7): 1. ProductController, 2. CategoryController, 3. BrandController, Methods, Methods, Methods, MODULE DOCUMENTATION

### Community 115 - "Manual Testing Checklist"
Cohesion: 0.29
Nodes (7): Authentication, Automated Testing (Recommended), Brand Module, Category Module, Manual Testing Checklist, Product Module, TESTING GUIDE

### Community 116 - "Hướng dẫn sử dụng chức năng đăng nhập và quên mật khẩu"
Cohesion: 0.18
Nodes (11): 1. Validation đăng nhập, 2. Xử lý lỗi đăng nhập, 3. Chức năng quên mật khẩu, Các chức năng đã được cải thiện, Cách sử dụng, Cần cấu hình thêm, Hướng dẫn sử dụng chức năng đăng nhập và quên mật khẩu, Lưu ý quan trọng (+3 more)

### Community 117 - "DOCUMENTATION - HỆ THỐNG QUẢN LÝ BÁN HÀNG"
Cohesion: 0.20
Nodes (10): Chức năng chính, Cài đặt, Cấu hình .env, CẤU HÌNH MÔI TRƯỜNG, CẤU TRÚC DỰ ÁN, DOCUMENTATION - HỆ THỐNG QUẢN LÝ BÁN HÀNG, MỤC LỤC, Thông tin cơ bản (+2 more)

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

### Community 126 - "7. KHUYẾN NGHỊ"
Cohesion: 0.50
Nodes (4): 7.1 Ưu tiên cao, 7.2 Ưu tiên trung bình, 7.3 Ưu tiên thấp, 7. KHUYẾN NGHỊ

### Community 127 - "SampleDataSeeder.php"
Cohesion: 0.22
Nodes (4): Exception, Illuminate\Support\Facades\File, Illuminate\Support\Facades\Http, Illuminate\Support\Str

### Community 128 - "AUTHENTICATION & AUTHORIZATION"
Cohesion: 0.50
Nodes (4): AUTHENTICATION & AUTHORIZATION, Middleware, Password Reset Flow, User Roles

### Community 129 - "Brand Management Endpoints"
Cohesion: 0.40
Nodes (5): Brand Management Endpoints, Create Brand, Delete Brand, Get All Brands, Update Brand

### Community 130 - "DEPLOYMENT GUIDE"
Cohesion: 0.50
Nodes (4): Deployment Commands, DEPLOYMENT GUIDE, Environment Variables, Production Checklist

### Community 131 - "MAINTENANCE"
Cohesion: 0.50
Nodes (4): MAINTENANCE, Monitoring, Regular Tasks, Troubleshooting

### Community 132 - "graphify (Mode: Always Use the Graph)"
Cohesion: 0.50
Nodes (3): graphify (Mode: Always Use the Graph), Project Rules: c:\xampp\htdocs\appweb, Strict Policy: Always Use The Graph

### Community 133 - "Product Management Endpoints"
Cohesion: 0.40
Nodes (5): Create Product, Delete Product, Get All Products, Product Management Endpoints, Update Product

### Community 134 - "Tính Năng Đã Thực Hiện"
Cohesion: 0.40
Nodes (5): ✅ Bug Fixes, ✅ Core Features, ✅ Security, Tính Năng Đã Thực Hiện, ✅ UI/UX

### Community 135 - "Troubleshooting"
Cohesion: 0.50
Nodes (4): Lỗi: "No data available in table", Lỗi: "SQLSTATE[23000]: Integrity constraint violation", Lỗi: "Undefined variable $cart", Troubleshooting

## Knowledge Gaps
- **287 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+282 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 489 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **82 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `BÁO CÁO KIỂM THỬ CHỨC NĂNG CRUD HỆ THỐNG QUẢN LÝ BÁN HÀNG` connect `BÁO CÁO KIỂM THỬ CHỨC NĂNG CRUD HỆ THỐNG QUẢN LÝ BÁN HÀNG` to `PasswordResetController`, `.users`, `1.2 Chức năng CRUD`, `2.2 Chức năng CRUD`, `README.md`, `Brand`, `7. KHUYẾN NGHỊ`?**
  _High betweenness centrality (0.190) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `PasswordResetController`, `TestCase`, `OderItem`, `.view`, `Cart`, `FullFlowTest`, `.users`, `.orders`, `SampleDataSeeder`, `3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues)`, `Illuminate\Http\Request`, `SampleDataSeeder.php`, `Brand`, `CartController.php`?**
  _High betweenness centrality (0.128) - this node is a cross-community bridge._
- **Why does `Category` connect `Category` to `Product`, `TestCase`, `OderItem`, `FullFlowTest`, `Cart`, `User`, `SampleDataSeeder`, `3.2. Nhóm 2: Lỗi Giao diện & Hiển thị (UI/UX Issues)`, `CartServiceTest`, `SampleDataSeeder.php`, `Brand`, `CartController.php`?**
  _High betweenness centrality (0.118) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `User` (e.g. with `4.1 Thông tin module` and `[CRIT-08] Lỗ hổng Mass Assignment leo thang đặc quyền qua cột `role` trong Model `User``) actually correct?**
  _`User` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `Order` (e.g. with `3. Lỗi Giá Trị Giảm Giá Không Được Lưu Vào Database` and `4. Lỗi Miễn Phí Vận Chuyển Không Hiển Thị`) actually correct?**
  _`Order` has 3 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _287 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Order` be split into smaller, more focused modules?**
  _Cohesion score 0.1225296442687747 - nodes in this community are weakly interconnected._
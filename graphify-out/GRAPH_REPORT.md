# Graph Report - appweb  (2026-09-28)

## Corpus Check
- 176 files · ~87,378 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 20 file(s) not represented in the graph (top: .drawio 8, (none) 7, .ico 2)

## Summary
- 1096 nodes · 1836 edges · 148 communities (56 shown, 92 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 114 edges (avg confidence: 0.91)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `0290334c`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Order
- Category
- TestCase
- composer.json
- Illuminate\Database\Eloquent\Model
- Cart
- OderItem
- package.json
- GHNService
- SampleDataSeeder
- Illuminate\Support\Str
- Illuminate\Http\Request
- 3.2. Nhóm 2: Lỗi Giao diện & Hiển thị (UI/UX Issues)
- Illuminate\Database\Schema\Blueprint
- Illuminate\Database\Migrations\Migration
- PHPUnit\Framework\TestCase
- logging.php
- artisan
- HƯỚNG DẪN KIỂM THỬ HỆ THỐNG - TESTER GUIDE
- .view
- Illuminate\Support\Facades\Schema
- AdminCatalogManagementTest
- BÁO CÁO KIỂM THỬ CHỨC NĂNG CRUD HỆ THỐNG QUẢN LÝ BÁN HÀNG
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
- 3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues)
- CartController.php
- 1.1. Kiến trúc hệ thống và Ngăn xếp công nghệ
- Product
- MoMoService
- VNPayService
- 1.2 Chức năng CRUD
- 2.2 Chức năng CRUD
- HỆ THỐNG QUẢN LÝ BÁN HÀNG - E-COMMERCE MANAGEMENT SYSTEM
- 3.2 Chức năng CRUD
- ReportsAndReviewsTest
- .orders
- Coupon
- .placeOrder
- Tổng Kết
- Luồng Đặt Hàng
- CẤU HÌNH MÔI TRƯỜNG
- Cấu trúc Module
- Hướng dẫn sử dụng chức năng đăng nhập và quên mật khẩu
- MODULE DOCUMENTATION
- Manual Testing Checklist
- Các chức năng đã được cải thiện
- DOCUMENTATION - HỆ THỐNG QUẢN LÝ BÁN HÀNG
- Cấu trúc file đã tạo/cập nhật
- 6. ĐÁNH GIÁ TỔNG QUAN
- DATABASE SCHEMA
- ERROR HANDLING
- FILE UPLOAD SYSTEM
- Tính Năng Chưa Thực Hiện (Future)
- Các Vấn Đề Đã Khắc Phục
- SampleDataSeeder.php
- AUTHENTICATION & AUTHORIZATION
- DEPLOYMENT GUIDE
- MAINTENANCE
- graphify (Mode: Always Use the Graph)
- Tính Năng Đã Thực Hiện
- Troubleshooting

## God Nodes (most connected - your core abstractions)
1. `User` - 73 edges
2. `Order` - 60 edges
3. `Product` - 53 edges
4. `Category` - 43 edges
5. `Controller` - 37 edges
6. `Cart` - 28 edges
7. `Coupon` - 28 edges
8. `GHNService` - 24 edges
9. `Brand` - 23 edges
10. `CartItem` - 22 edges

## Surprising Connections (you probably didn't know these)
- `5.1 Cart Management` --references--> `CartController`  [INFERRED]
  BAO_CAO_KIEM_THU_CRUD.md → app/Http/Controllers/CartController.php
- `2. Lỗi Import Controller Trong Routes` --references--> `CouponController`  [INFERRED]
  BUG_FIXES.md → app/Http/Controllers/CouponController.php
- `[CRIT-08] Lỗ hổng Mass Assignment leo thang đặc quyền qua cột `role` trong Model `User`` --references--> `User`  [INFERRED]
  BAO_CAO_RA_SOAT_HE_THONG.md → app/Models/User.php
- `4.1 Thông tin module` --references--> `AdminController`  [INFERRED]
  BAO_CAO_KIEM_THU_CRUD.md → app/Http/Controllers/AdminController.php
- `1.1. Kiến trúc hệ thống và Ngăn xếp công nghệ` --references--> `AdminController`  [INFERRED]
  BAO_CAO_RA_SOAT_HE_THONG.md → app/Http/Controllers/AdminController.php

## Import Cycles
- None detected.

## Communities (148 total, 92 thin omitted)

### Community 0 - "Order"
Cohesion: 0.12
Nodes (3): OrderController, Order, GHNOrderService

### Community 1 - "Category"
Cohesion: 0.15
Nodes (7): CategoryController, HomeController, ProfilesController, Category, 2.1 Thông tin module, 4. Controllers và Routes, Illuminate\Support\Facades\Route

### Community 2 - "TestCase"
Cohesion: 0.21
Nodes (7): Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, Illuminate\Http\UploadedFile, Illuminate\Support\Facades\Storage, ExampleTest, ReportsDemoSeederTest, TestCase

### Community 3 - "composer.json"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 4 - "Illuminate\Database\Eloquent\Model"
Cohesion: 0.15
Nodes (8): HistorySearch, OrderItem, PaymentTransaction, ProductReview, ReportsDemoSeeder, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model, Illuminate\Support\Facades\Hash

### Community 5 - "Cart"
Cohesion: 0.10
Nodes (6): CartController, Cart, CartItem, CartService, OrderService, Lỗi: "Call to undefined method updateTotal()"

### Community 6 - "OderItem"
Cohesion: 0.07
Nodes (8): OderItemController, StoreOderItemRequest, UpdateOderItemRequest, OderItem, OderItemPolicy, Illuminate\Auth\Access\Response, Illuminate\Foundation\Http\FormRequest, AdminOrderManagementTest

### Community 7 - "package.json"
Cohesion: 0.09
Nodes (19): devDependencies, axios, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, private (+11 more)

### Community 8 - "GHNService"
Cohesion: 0.13
Nodes (3): GHNController, LocationController, GHNService

### Community 9 - "SampleDataSeeder"
Cohesion: 0.19
Nodes (5): DatabaseSeeder, OderItemSeeder, SampleDataSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 10 - "Illuminate\Support\Str"
Cohesion: 0.19
Nodes (5): OderItemFactory, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Str, static

### Community 11 - "Illuminate\Http\Request"
Cohesion: 0.06
Nodes (27): ReportController, ReviewController, AdminController, GoogleAuthController, PasswordResetController, Controller, AdminMiddleware, Authenticate (+19 more)

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

### Community 21 - ".view"
Cohesion: 0.17
Nodes (4): OrderController, [CRIT-05] `OrderController@storeFromCart` ghi sai cấu trúc bảng `orders` và thiếu các trường NOT NULL, [CRIT-13] Lỗ hổng IDOR trên `OrderController@showSuccess` làm lộ lọt toàn bộ thông tin định danh cá nhân (PII) của khách hàng, 2. Controllers

### Community 34 - "BÁO CÁO KIỂM THỬ CHỨC NĂNG CRUD HỆ THỐNG QUẢN LÝ BÁN HÀNG"
Cohesion: 0.25
Nodes (8): 7.1 Ưu tiên cao, 7.2 Ưu tiên trung bình, 7.3 Ưu tiên thấp, 7. KHUYẾN NGHỊ, 8. KẾT LUẬN, BÁO CÁO KIỂM THỬ CHỨC NĂNG CRUD HỆ THỐNG QUẢN LÝ BÁN HÀNG, THÔNG TIN CHUNG, TỔNG QUAN CÁC MODULE CRUD

### Community 36 - "Module Quản Lý Đơn Hàng (Order Management)"
Cohesion: 0.22
Nodes (8): API Endpoints Summary, Credits, Manual Test Flow, Module Quản Lý Đơn Hàng (Order Management), Notes, Testing, Tinker Test, Tổng quan

### Community 38 - "User"
Cohesion: 0.06
Nodes (8): User, Illuminate\Contracts\Auth\CanResetPassword, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, AuthWorkflowTest, FullFlowTest, GoogleAuthTest, OptimizationTest

### Community 90 - "Hướng dẫn sử dụng JavaScript cho chức năng giỏ hàng"
Cohesion: 0.08
Nodes (23): 1. Layout chính (`resources/views/layout/home_layout.blade.php`), 1. Tự động (Đã được thiết lập sẵn), 2. Sử dụng thủ công, 2. Trang sản phẩm (`resources/views/client/home/index_home.blade.php`), 3. Trang chi tiết sản phẩm (`resources/views/client/product/detail.blade.php`), 3. Với form số lượng, ✅ Bộ đếm giỏ hàng, CSRF token lỗi: (+15 more)

### Community 91 - "API DOCUMENTATION"
Cohesion: 0.10
Nodes (21): API DOCUMENTATION, Authentication Endpoints, Brand Management Endpoints, Category Management Endpoints, Create Brand, Create Category, Create Product, Delete Brand (+13 more)

### Community 93 - "Brand"
Cohesion: 0.13
Nodes (6): BrandController, Brand, 3.1 Thông tin module, ✅ READ (Đọc/Xem), Exception, Illuminate\Support\Facades\File

### Community 94 - "3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues)"
Cohesion: 0.04
Nodes (44): CheckoutController, 2. BẢNG MA TRẬN PHÂN LOẠI LỖI TOÀN DIỆN (AUDIT MATRIX), 3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues), 3.3. Nhóm 3: Lỗi Luồng Nghiệp vụ & Xử lý Dữ liệu (Functional Issues), 3.4. Nhóm 4: Đề xuất Tối ưu Hệ thống (Improvements), 3. DANH MỤC LỖI CHI TIẾT VÀ GIẢI PHÁP KHẮC PHỤC HOÀN CHỈNH, 4. LỘ TRÌNH VÀ KẾ HOẠCH TRIỂN KHAI KHẮC PHỤC (ROADMAP & ACTION PLAN), 5.1. Kiểm tra tĩnh qua dòng lệnh CLI (+36 more)

### Community 95 - "CartController.php"
Cohesion: 0.16
Nodes (11): Carbon\Carbon, Illuminate\Support\Collection, Illuminate\Support\Facades\Auth, Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Log, Illuminate\Support\Facades\Mail, Illuminate\Support\Facades\Session, Laravel\Socialite\Facades\Socialite (+3 more)

### Community 96 - "1.1. Kiến trúc hệ thống và Ngăn xếp công nghệ"
Cohesion: 0.22
Nodes (8): AppServiceProvider, 1.1. Kiến trúc hệ thống và Ngăn xếp công nghệ, 1.2. Thống kê phạm vi kiểm toán, 1.3. Tóm tắt kết quả kiểm toán theo cấp độ rủi ro, 1. TỔNG QUAN HỆ THỐNG VÀ PHẠM VI RÀ SOÁT, Illuminate\Pagination\Paginator, Illuminate\Support\Facades\View, Illuminate\Support\ServiceProvider

### Community 97 - "Product"
Cohesion: 0.22
Nodes (3): ProductController, Product, 1.1 Thông tin module

### Community 100 - "1.2 Chức năng CRUD"
Cohesion: 0.29
Nodes (7): 1.2 Chức năng CRUD, 1.3 Đánh giá tổng thể, 1. MODULE QUẢN LÝ SẢN PHẨM (PRODUCT), ✅ CREATE (Tạo mới), ✅ DELETE (Xóa), ✅ READ (Đọc/Xem), ✅ UPDATE (Cập nhật)

### Community 101 - "2.2 Chức năng CRUD"
Cohesion: 0.29
Nodes (7): 2.2 Chức năng CRUD, 2.3 Đánh giá tổng thể, 2. MODULE QUẢN LÝ DANH MỤC (CATEGORY), ✅ CREATE (Tạo mới), ✅ DELETE (Xóa), ✅ READ (Đọc/Xem), ✅ UPDATE (Cập nhật)

### Community 102 - "HỆ THỐNG QUẢN LÝ BÁN HÀNG - E-COMMERCE MANAGEMENT SYSTEM"
Cohesion: 0.06
Nodes (32): 📄 Báo cáo chi tiết, 📊 BÁO CÁO KIỂM THỬ, Bảo mật, Cài đặt, 🛠️ CÀI ĐẶT VÀ CHẠY DỰ ÁN, 🔧 CẤU HÌNH QUAN TRỌNG, 📁 CẤU TRÚC DỰ ÁN, Cần cải thiện (+24 more)

### Community 103 - "3.2 Chức năng CRUD"
Cohesion: 0.33
Nodes (6): 3.2 Chức năng CRUD, 3.3 Đánh giá tổng thể, 3. MODULE QUẢN LÝ THƯƠNG HIỆU (BRAND), ✅ CREATE (Tạo mới), ✅ DELETE (Xóa), ✅ UPDATE (Cập nhật)

### Community 104 - "ReportsAndReviewsTest"
Cohesion: 0.09
Nodes (20): 4.1 Thông tin module, 4.2 Chức năng CRUD, 4.3 Đánh giá tổng thể, 4. MODULE QUẢN LÝ NGƯỜI DÙNG (USER), ✅ CREATE (Tạo mới), ❌ DELETE (Xóa), ✅ READ (Đọc/Xem), ✅ UPDATE (Cập nhật) (+12 more)

### Community 105 - ".orders"
Cohesion: 0.28
Nodes (8): 1. Lỗi Migration - Bảng `orders` Đã Tồn Tại, 2. Lỗi Import Controller Trong Routes, 3. Lỗi Giá Trị Giảm Giá Không Được Lưu Vào Database, 4. Lỗi Miễn Phí Vận Chuyển Không Hiển Thị, Ngày 24-25/11/2025, Database Schema, Table: `oder_items`, Table: `orders`

### Community 108 - "Tổng Kết"
Cohesion: 0.25
Nodes (7): Commands Đã Chạy:, Cách Kiểm Tra Log:, Files Đã Sửa:, Migrations Đã Tạo:, Nhật Ký Sửa Lỗi (Bug Fixes Log), Tổng Kết, Vấn Đề Còn Tồn Tại (Cần Test):

### Community 109 - "Luồng Đặt Hàng"
Cohesion: 0.40
Nodes (5): Bước 1: User thêm sản phẩm vào giỏ, Bước 2: User xem giỏ hàng, Bước 3: User bấm "Đặt hàng", Bước 4: Admin xem đơn, Luồng Đặt Hàng

### Community 110 - "CẤU HÌNH MÔI TRƯỜNG"
Cohesion: 0.50
Nodes (4): Cài đặt, Cấu hình .env, CẤU HÌNH MÔI TRƯỜNG, Yêu cầu hệ thống

### Community 111 - "Cấu trúc Module"
Cohesion: 0.29
Nodes (7): 1. Models, 3. Routes (`routes/web.php`), 4. Views, 5. Menu Admin, Admin Views, Client Views, Cấu trúc Module

### Community 112 - "Hướng dẫn sử dụng chức năng đăng nhập và quên mật khẩu"
Cohesion: 0.29
Nodes (7): Cách sử dụng, Cần cấu hình thêm, Hướng dẫn sử dụng chức năng đăng nhập và quên mật khẩu, Lưu ý quan trọng, Quên mật khẩu, Test chức năng, Đăng nhập

### Community 114 - "MODULE DOCUMENTATION"
Cohesion: 0.29
Nodes (7): 1. ProductController, 2. CategoryController, 3. BrandController, Methods, Methods, Methods, MODULE DOCUMENTATION

### Community 115 - "Manual Testing Checklist"
Cohesion: 0.29
Nodes (7): Authentication, Automated Testing (Recommended), Brand Module, Category Module, Manual Testing Checklist, Product Module, TESTING GUIDE

### Community 116 - "Các chức năng đã được cải thiện"
Cohesion: 0.50
Nodes (4): 1. Validation đăng nhập, 2. Xử lý lỗi đăng nhập, 3. Chức năng quên mật khẩu, Các chức năng đã được cải thiện

### Community 117 - "DOCUMENTATION - HỆ THỐNG QUẢN LÝ BÁN HÀNG"
Cohesion: 0.33
Nodes (6): Chức năng chính, CẤU TRÚC DỰ ÁN, DOCUMENTATION - HỆ THỐNG QUẢN LÝ BÁN HÀNG, MỤC LỤC, Thông tin cơ bản, TỔNG QUAN HỆ THỐNG

### Community 118 - "Cấu trúc file đã tạo/cập nhật"
Cohesion: 0.33
Nodes (6): Controllers, Cấu trúc file đã tạo/cập nhật, Database, Models, Routes, Views

### Community 119 - "6. ĐÁNH GIÁ TỔNG QUAN"
Cohesion: 0.50
Nodes (4): 6.1 Điểm mạnh, 6.2 Điểm cần cải thiện, 6.3 Bảo mật, 6. ĐÁNH GIÁ TỔNG QUAN

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

### Community 127 - "SampleDataSeeder.php"
Cohesion: 0.50
Nodes (3): Illuminate\Http\Client\ConnectionException, Illuminate\Support\Facades\Cache, Illuminate\Support\Facades\Http

### Community 128 - "AUTHENTICATION & AUTHORIZATION"
Cohesion: 0.50
Nodes (4): AUTHENTICATION & AUTHORIZATION, Middleware, Password Reset Flow, User Roles

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

## Knowledge Gaps
- **295 isolated node(s):** `$schema`, `name`, `type`, `description`, `keywords` (+290 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 528 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **92 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `BÁO CÁO KIỂM THỬ CHỨC NĂNG CRUD HỆ THỐNG QUẢN LÝ BÁN HÀNG` connect `BÁO CÁO KIỂM THỬ CHỨC NĂNG CRUD HỆ THỐNG QUẢN LÝ BÁN HÀNG` to `1.2 Chức năng CRUD`, `2.2 Chức năng CRUD`, `3.2 Chức năng CRUD`, `ReportsAndReviewsTest`, `Illuminate\Http\Request`, `6. ĐÁNH GIÁ TỔNG QUAN`, `README.md`?**
  _High betweenness centrality (0.150) - this node is a cross-community bridge._
- **Why does `User` connect `User` to `TestCase`, `Illuminate\Database\Eloquent\Model`, `Cart`, `OderItem`, `ReportsAndReviewsTest`, `.orders`, `SampleDataSeeder`, `Illuminate\Http\Request`, `3.2. Nhóm 2: Lỗi Giao diện & Hiển thị (UI/UX Issues)`, `.view`, `SampleDataSeeder.php`, `AdminCatalogManagementTest`, `3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues)`, `CartController.php`?**
  _High betweenness centrality (0.146) - this node is a cross-community bridge._
- **Why does `Category` connect `Category` to `1.1. Kiến trúc hệ thống và Ngăn xếp công nghệ`, `Product`, `TestCase`, `Illuminate\Database\Eloquent\Model`, `Cart`, `OderItem`, `User`, `SampleDataSeeder`, `CartServiceTest`, `AdminCatalogManagementTest`, `SampleDataSeeder.php`, `Brand`, `3.1. Nhóm 1: Lỗi Nghiêm trọng (Critical Issues)`, `CartController.php`?**
  _High betweenness centrality (0.101) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `User` (e.g. with `4.1 Thông tin module` and `[CRIT-08] Lỗ hổng Mass Assignment leo thang đặc quyền qua cột `role` trong Model `User``) actually correct?**
  _`User` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 3 inferred relationships involving `Order` (e.g. with `3. Lỗi Giá Trị Giảm Giá Không Được Lưu Vào Database` and `4. Lỗi Miễn Phí Vận Chuyển Không Hiển Thị`) actually correct?**
  _`Order` has 3 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _295 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Order` be split into smaller, more focused modules?**
  _Cohesion score 0.1225296442687747 - nodes in this community are weakly interconnected._
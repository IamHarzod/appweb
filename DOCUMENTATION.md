# DOCUMENTATION - HỆ THỐNG QUẢN LÝ BÁN HÀNG

## MỤC LỤC
1. [Tổng quan hệ thống](#tổng-quan-hệ-thống)
2. [Cấu trúc dự án](#cấu-trúc-dự-án)
3. [Cấu hình môi trường](#cấu-hình-môi-trường)
4. [Database Schema](#database-schema)
5. [API Documentation](#api-documentation)
6. [Module Documentation](#module-documentation)
7. [Authentication & Authorization](#authentication--authorization)
8. [File Upload System](#file-upload-system)
9. [Error Handling](#error-handling)
10. [Testing Guide](#testing-guide)
11. [Deployment Guide](#deployment-guide)

---

## TỔNG QUAN HỆ THỐNG

### Thông tin cơ bản
- **Tên**: E-commerce Management System
- **Framework**: Laravel 11.x
- **Database**: MySQL 8.0+
- **PHP Version**: 8.1+
- **Frontend**: Blade Templates + Bootstrap
- **Architecture**: MVC Pattern

### Chức năng chính
- Quản lý sản phẩm (Products)
- Quản lý danh mục (Categories)
- Quản lý thương hiệu (Brands)
- Quản lý người dùng (Users)
- Quản lý giỏ hàng (Cart)
- Hệ thống xác thực (Authentication)
- Quản lý đơn hàng (Orders)

---

## CẤU TRÚC DỰ ÁN

```
appweb/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php
│   │   │   ├── BrandController.php
│   │   │   ├── CartController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── ProductController.php
│   │   │   └── Auth/
│   │   │       └── PasswordResetController.php
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   │   ├── Brand.php
│   │   ├── Cart.php
│   │   ├── CartItem.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   └── User.php
│   └── Providers/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── admin/          # Admin panel assets
│   ├── client/        # Client-side assets
│   └── uploads/        # Uploaded files
├── resources/
│   └── views/
│       ├── admin/      # Admin views
│       ├── client/     # Client views
│       └── layout/     # Layout templates
└── routes/
    └── web.php
```

---

## CẤU HÌNH MÔI TRƯỜNG

### Yêu cầu hệ thống
- PHP >= 8.1
- MySQL >= 8.0
- Composer
- Node.js & NPM (cho frontend assets)

### Cài đặt
```bash
# Clone repository
git clone <repository-url>
cd appweb

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build
```

### Cấu hình .env
```env
APP_NAME="E-commerce Management"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webbanhang
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

---

## DATABASE SCHEMA

### Bảng Users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phoneNumber VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    IsActive TINYINT(1) DEFAULT 1,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Bảng Products
```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stockQuantity INT NOT NULL,
    discountPercent DECIMAL(5,2) DEFAULT 0,
    imageURL VARCHAR(255),
    status TINYINT(1) NOT NULL,
    IsActive TINYINT(1) DEFAULT 1,
    style VARCHAR(255),
    category_id BIGINT UNSIGNED,
    id_brand BIGINT UNSIGNED,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES _category(id),
    FOREIGN KEY (id_brand) REFERENCES brand(id)
);
```

### Bảng Categories
```sql
CREATE TABLE _category (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    ImageURL VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Bảng Brands
```sql
CREATE TABLE brand (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    TenThuongHieu VARCHAR(255) NOT NULL,
    Logo VARCHAR(255),
    MoTa TEXT,
    TrangThai TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## API DOCUMENTATION

### Authentication Endpoints

#### Login
```http
POST /submit-login-admin
Content-Type: application/x-www-form-urlencoded

email=admin@example.com
password=password123
remember=1
```

**Response:**
```json
{
    "success": true,
    "redirect": "/admin/dashboard"
}
```

#### Register
```http
POST /submit-register-admin
Content-Type: application/x-www-form-urlencoded

name=John Doe
email=john@example.com
phone=0987654321
password=password123
```

#### Password Reset Request
```http
POST /password/email
Content-Type: application/x-www-form-urlencoded

email=user@example.com
```

#### Password Reset
```http
POST /password/reset
Content-Type: application/x-www-form-urlencoded

token=reset_token_here
email=user@example.com
password=newpassword123
password_confirmation=newpassword123
```

### Product Management Endpoints

#### Get All Products
```http
GET /show-product
```

#### Create Product
```http
POST /create-product
Content-Type: multipart/form-data

name=iPhone 15 Pro
price=29990000
stockQuantity=50
discountPercent=10
description=Latest iPhone model
status=1
IsActive=1
style=Premium
imageURL=@/path/to/image.jpg
id_brand=1
category_id=1
```

#### Update Product
```http
PUT /update-product/{id}
Content-Type: multipart/form-data

name=iPhone 15 Pro Max
price=32990000
stockQuantity=30
# ... other fields
```

#### Delete Product
```http
GET /delete-product/{id}
```

### Category Management Endpoints

#### Get All Categories
```http
GET /show-category
```

#### Create Category
```http
POST /create-category
Content-Type: multipart/form-data

name=Electronics
description=Electronic devices and gadgets
ImageURL=@/path/to/image.jpg
```

#### Update Category
```http
POST /update-category/{id}
Content-Type: multipart/form-data

name=Smartphones
description=Mobile phones and accessories
```

#### Delete Category
```http
GET /delete-category/{id}
```

### Brand Management Endpoints

#### Get All Brands
```http
GET /show-brand
```

#### Create Brand
```http
POST /create-brand
Content-Type: multipart/form-data

TenThuongHieu=Apple
MoTa=Technology company
TrangThai=1
Logo=@/path/to/logo.png
```

#### Update Brand
```http
POST /update-brand
Content-Type: multipart/form-data

id=1
TenThuongHieu=Apple Inc.
MoTa=Leading technology company
TrangThai=1
```

#### Delete Brand
```http
GET /delete-brand/{id}
```

---

## MODULE DOCUMENTATION

### 1. ProductController

#### Methods

**show_product()**
- **Purpose**: Hiển thị danh sách tất cả sản phẩm
- **Route**: `GET /show-product`
- **Returns**: View với danh sách sản phẩm

**show_create_product()**
- **Purpose**: Hiển thị form tạo sản phẩm mới
- **Route**: `GET /show-create-product`
- **Returns**: View với form và danh sách brands, categories

**create_product(Request $request)**
- **Purpose**: Tạo sản phẩm mới
- **Route**: `POST /create-product`
- **Validation**: Đầy đủ validation rules
- **File Upload**: Hỗ trợ upload ảnh sản phẩm
- **Returns**: Redirect với success message

**show_edit($id)**
- **Purpose**: Hiển thị form chỉnh sửa sản phẩm
- **Route**: `GET /show-edit-product/{id}`
- **Returns**: View với form edit và dữ liệu sản phẩm

**update(Request $request, $id)**
- **Purpose**: Cập nhật sản phẩm
- **Route**: `PUT/POST /update-product/{id}`
- **Validation**: Tương tự create
- **File Handling**: Xóa ảnh cũ khi upload ảnh mới
- **Returns**: Redirect với success message

**destroy($id)**
- **Purpose**: Xóa sản phẩm
- **Route**: `GET /delete-product/{id}`
- **File Cleanup**: Xóa ảnh sản phẩm
- **Returns**: Boolean (true/false)

### 2. CategoryController

#### Methods

**show_category()**
- **Purpose**: Hiển thị danh sách danh mục
- **Route**: `GET /show-category`
- **Ordering**: Sắp xếp theo ID giảm dần
- **Returns**: View với danh sách categories

**create_category(Request $request)**
- **Purpose**: Tạo danh mục mới
- **Route**: `POST /create-category`
- **Validation**: Name required, description optional
- **File Upload**: Hỗ trợ upload ảnh danh mục
- **Returns**: Redirect với success message

**edit($id)**
- **Purpose**: Hiển thị form chỉnh sửa danh mục
- **Route**: `GET /edit-category/{id}`
- **Returns**: View với form edit

**update(Request $request, $id)**
- **Purpose**: Cập nhật danh mục
- **Route**: `POST /update-category/{id}`
- **File Handling**: Xóa ảnh cũ khi upload ảnh mới
- **Returns**: Redirect với success message

**destroy($id)**
- **Purpose**: Xóa danh mục
- **Route**: `GET /delete-category/{id}`
- **File Cleanup**: Xóa ảnh danh mục
- **Returns**: Boolean (true/false)

### 3. BrandController

#### Methods

**show_brand()**
- **Purpose**: Hiển thị danh sách thương hiệu
- **Route**: `GET /show-brand`
- **Returns**: View với danh sách brands

**create_brand(Request $request)**
- **Purpose**: Tạo thương hiệu mới
- **Route**: `POST /create-brand`
- **File Upload**: Bắt buộc upload logo
- **Validation**: Kiểm tra file hợp lệ
- **Returns**: Redirect về danh sách

**showEdit($id)**
- **Purpose**: Hiển thị form chỉnh sửa thương hiệu
- **Route**: `GET /show-edit-brand/{id}`
- **Returns**: View với form edit

**update_brand(Request $request)**
- **Purpose**: Cập nhật thương hiệu
- **Route**: `POST /update-brand`
- **File Handling**: Xóa logo cũ khi upload logo mới
- **Error Handling**: Try-catch với exception
- **Returns**: Redirect về danh sách

**destroy($id)**
- **Purpose**: Xóa thương hiệu
- **Route**: `GET /delete-brand/{id}`
- **File Cleanup**: Xóa logo thương hiệu
- **Returns**: Boolean (true/false)

---

## AUTHENTICATION & AUTHORIZATION

### Middleware
- **auth**: Kiểm tra user đã đăng nhập
- **admin**: Kiểm tra user có role admin

### User Roles
- **admin**: Quyền truy cập đầy đủ
- **user**: Quyền hạn chế

### Password Reset Flow
1. User nhập email tại `/password/reset`
2. System tạo token và lưu vào database
3. Token được gửi qua email (hiện tại ghi log)
4. User click link reset với token
5. User nhập mật khẩu mới
6. System cập nhật password và xóa token

---

## FILE UPLOAD SYSTEM

### Cấu trúc thư mục
```
public/uploads/
├── products/     # Ảnh sản phẩm
├── categories/    # Ảnh danh mục
└── brands/        # Logo thương hiệu
```

### File Naming Convention
- Format: `{timestamp}_{slugged_name}.{extension}`
- Example: `1738320000_iphone-15-pro.jpg`

### Security Features
- File type validation (mimes)
- File size limit (4MB)
- Safe filename generation
- Old file cleanup on update/delete

### Supported Formats
- **Images**: jpg, jpeg, png, gif, webp
- **Max Size**: 4MB per file

---

## ERROR HANDLING

### Validation Errors
- Sử dụng Laravel validation rules
- Custom error messages cho từng field
- Hiển thị errors trong form với Bootstrap styling

### File Upload Errors
- Kiểm tra file hợp lệ
- Kiểm tra kích thước file
- Kiểm tra định dạng file
- Error messages chi tiết

### Database Errors
- Try-catch blocks cho các thao tác database
- Logging errors với Laravel Log
- Graceful error handling

### Common Error Scenarios
1. **File upload fails**: Return error message
2. **Database constraint violation**: Show validation error
3. **File not found**: Handle gracefully
4. **Permission denied**: Show access denied message

---

## TESTING GUIDE

### Manual Testing Checklist

#### Product Module
- [ ] Tạo sản phẩm mới với đầy đủ thông tin
- [ ] Tạo sản phẩm với validation errors
- [ ] Upload ảnh sản phẩm
- [ ] Cập nhật thông tin sản phẩm
- [ ] Thay đổi ảnh sản phẩm
- [ ] Xóa sản phẩm
- [ ] Kiểm tra ảnh cũ được xóa khi update/delete

#### Category Module
- [ ] Tạo danh mục mới
- [ ] Upload ảnh danh mục
- [ ] Cập nhật danh mục
- [ ] Xóa danh mục
- [ ] Kiểm tra relationship với products

#### Brand Module
- [ ] Tạo thương hiệu mới
- [ ] Upload logo (bắt buộc)
- [ ] Cập nhật thông tin thương hiệu
- [ ] Thay đổi logo
- [ ] Xóa thương hiệu
- [ ] Kiểm tra relationship với products

#### Authentication
- [ ] Đăng nhập với thông tin đúng
- [ ] Đăng nhập với thông tin sai
- [ ] Validation form đăng nhập
- [ ] Đăng ký tài khoản mới
- [ ] Quên mật khẩu flow
- [ ] Reset mật khẩu với token hợp lệ
- [ ] Reset mật khẩu với token hết hạn

### Automated Testing (Recommended)
```bash
# Unit Tests
php artisan test

# Feature Tests
php artisan test --testsuite=Feature

# Specific Test
php artisan test tests/Feature/ProductTest.php
```

---

## DEPLOYMENT GUIDE

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database
- [ ] Set up email service
- [ ] Configure file storage
- [ ] Set up SSL certificate
- [ ] Configure web server (Apache/Nginx)
- [ ] Set up monitoring
- [ ] Configure backups

### Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=production_db
DB_USERNAME=production_user
DB_PASSWORD=secure_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
```

### Deployment Commands
```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Generate optimized config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## MAINTENANCE

### Regular Tasks
- **Daily**: Check error logs
- **Weekly**: Backup database
- **Monthly**: Update dependencies
- **Quarterly**: Security audit

### Monitoring
- Application logs: `storage/logs/laravel.log`
- Web server logs
- Database performance
- File storage usage

### Troubleshooting
1. **500 Error**: Check logs, verify permissions
2. **File upload fails**: Check disk space, permissions
3. **Database errors**: Check connection, migrations
4. **Slow performance**: Check queries, indexes

---

**Documentation Version**: 1.0  
**Last Updated**: 19/10/2025  
**Maintained By**: Development Team

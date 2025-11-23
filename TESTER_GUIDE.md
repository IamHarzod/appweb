# HƯỚNG DẪN KIỂM THỬ HỆ THỐNG - TESTER GUIDE

## 📋 THÔNG TIN CHUNG

-   **Hệ thống**: E-commerce Management System
-   **URL**: http://localhost:8000
-   **Admin Panel**: http://localhost:8000/admin
-   **Ngày tạo**: 19/10/2025
-   **Phiên bản**: 1.0

---

## 🎯 MỤC TIÊU KIỂM THỬ

Kiểm tra toàn diện các chức năng CRUD của hệ thống quản lý bán hàng, bao gồm:

-   Quản lý sản phẩm (Products)
-   Quản lý danh mục (Categories)
-   Quản lý thương hiệu (Brands)
-   Quản lý người dùng (Users)
-   Hệ thống xác thực (Authentication)

---

## 🔐 CHUẨN BỊ KIỂM THỬ

### Tài khoản test

```
Admin Account:
- Email: admin@test.com
- Password: admin123

User Account:
- Email: user@test.com
- Password: user123
```

### Dữ liệu test cần chuẩn bị

-   **Images**: Chuẩn bị ít nhất 5 ảnh test (jpg, png) với kích thước < 4MB
-   **Test Data**: Danh sách sản phẩm, danh mục, thương hiệu để test

---

## 📝 CHECKLIST KIỂM THỬ

### ✅ PHẦN 1: KIỂM THỬ XÁC THỰC (AUTHENTICATION)

#### 1.1 Đăng nhập Admin

-   [ ] **Test Case 1.1.1**: Đăng nhập với thông tin đúng
    -   Truy cập: `http://localhost:8000/admin`
    -   Nhập email: `admin@test.com`
    -   Nhập password: `admin123`
    -   **Expected**: Chuyển đến dashboard admin
-   [ ] **Test Case 1.1.2**: Đăng nhập với email sai
    -   Nhập email: `wrong@test.com`
    -   Nhập password: `admin123`
    -   **Expected**: Hiển thị lỗi "Email hoặc mật khẩu không đúng"
-   [ ] **Test Case 1.1.3**: Đăng nhập với password sai
    -   Nhập email: `admin@test.com`
    -   Nhập password: `wrongpassword`
    -   **Expected**: Hiển thị lỗi "Email hoặc mật khẩu không đúng"
-   [ ] **Test Case 1.1.4**: Đăng nhập với email trống
    -   Để trống email
    -   Nhập password: `admin123`
    -   **Expected**: Hiển thị lỗi "Vui lòng nhập địa chỉ email"
-   [ ] **Test Case 1.1.5**: Đăng nhập với password trống
    -   Nhập email: `admin@test.com`
    -   Để trống password
    -   **Expected**: Hiển thị lỗi "Vui lòng nhập mật khẩu"

#### 1.2 Quên mật khẩu

-   [ ] **Test Case 1.2.1**: Quên mật khẩu với email tồn tại
    -   Click "Quên mật khẩu?"
    -   Nhập email: `admin@test.com`
    -   Click "Gửi liên kết khôi phục"
    -   **Expected**: Hiển thị "Liên kết khôi phục mật khẩu đã được gửi đến email của bạn"
-   [ ] **Test Case 1.2.2**: Quên mật khẩu với email không tồn tại
    -   Nhập email: `notexist@test.com`
    -   **Expected**: Hiển thị lỗi "Địa chỉ email này không tồn tại trong hệ thống"

#### 1.3 Đăng xuất

-   [ ] **Test Case 1.3.1**: Đăng xuất thành công
    -   Click "Đăng xuất" hoặc truy cập `/logout-admin`
    -   **Expected**: Chuyển về trang đăng nhập

---

### ✅ PHẦN 2: KIỂM THỬ QUẢN LÝ SẢN PHẨM (PRODUCTS)

#### 2.1 Xem danh sách sản phẩm

-   [ ] **Test Case 2.1.1**: Truy cập danh sách sản phẩm
    -   Truy cập: `/show-product`
    -   **Expected**: Hiển thị danh sách tất cả sản phẩm với thông tin đầy đủ

#### 2.2 Tạo sản phẩm mới

-   [ ] **Test Case 2.2.1**: Tạo sản phẩm với đầy đủ thông tin

    -   Truy cập: `/show-create-product`
    -   Nhập thông tin:
        -   Tên: "iPhone 15 Pro Max"
        -   Giá: 32990000
        -   Số lượng: 50
        -   Giảm giá: 10
        -   Mô tả: "Điện thoại cao cấp nhất của Apple"
        -   Trạng thái: Hoạt động
        -   Thương hiệu: Apple
        -   Danh mục: Điện thoại
        -   Upload ảnh: chọn file ảnh
    -   Click "Thêm sản phẩm"
    -   **Expected**: Chuyển về danh sách với thông báo "Thêm sản phẩm thành công!"

-   [ ] **Test Case 2.2.2**: Tạo sản phẩm thiếu tên

    -   Để trống trường "Tên sản phẩm"
    -   **Expected**: Hiển thị lỗi "Tên sản phẩm là bắt buộc"

-   [ ] **Test Case 2.2.3**: Tạo sản phẩm với giá âm

    -   Nhập giá: -1000000
    -   **Expected**: Hiển thị lỗi validation

-   [ ] **Test Case 2.2.4**: Upload file không phải ảnh

    -   Chọn file .txt hoặc .pdf
    -   **Expected**: Hiển thị lỗi "File phải là ảnh"

-   [ ] **Test Case 2.2.5**: Upload ảnh quá lớn (>4MB)
    -   Chọn ảnh có kích thước > 4MB
    -   **Expected**: Hiển thị lỗi "Kích thước file không được vượt quá 4MB"

#### 2.3 Chỉnh sửa sản phẩm

-   [ ] **Test Case 2.3.1**: Cập nhật thông tin sản phẩm

    -   Click "Chỉnh sửa" trên sản phẩm bất kỳ
    -   Thay đổi tên: "iPhone 15 Pro Max - Updated"
    -   Thay đổi giá: 33990000
    -   Click "Cập nhật"
    -   **Expected**: Chuyển về danh sách với thông báo "Cập nhật sản phẩm thành công!"

-   [ ] **Test Case 2.3.2**: Thay đổi ảnh sản phẩm
    -   Upload ảnh mới
    -   **Expected**: Ảnh cũ được xóa, ảnh mới được lưu

#### 2.4 Xóa sản phẩm

-   [ ] **Test Case 2.4.1**: Xóa sản phẩm thành công
    -   Click "Xóa" trên sản phẩm bất kỳ
    -   Confirm xóa
    -   **Expected**: Sản phẩm biến mất khỏi danh sách, ảnh được xóa

---

### ✅ PHẦN 3: KIỂM THỬ QUẢN LÝ DANH MỤC (CATEGORIES)

#### 3.1 Xem danh sách danh mục

-   [ ] **Test Case 3.1.1**: Truy cập danh sách danh mục
    -   Truy cập: `/show-category`
    -   **Expected**: Hiển thị danh sách danh mục sắp xếp theo ID giảm dần

#### 3.2 Tạo danh mục mới

-   [ ] **Test Case 3.2.1**: Tạo danh mục với đầy đủ thông tin

    -   Click "Thêm danh mục"
    -   Nhập thông tin:
        -   Tên: "Laptop Gaming"
        -   Mô tả: "Máy tính xách tay chơi game"
        -   Upload ảnh: chọn file ảnh
    -   Click "Thêm"
    -   **Expected**: Chuyển về danh sách với thông báo "Thêm danh mục thành công!"

-   [ ] **Test Case 3.2.2**: Tạo danh mục thiếu tên
    -   Để trống trường "Tên danh mục"
    -   **Expected**: Hiển thị lỗi "Tên danh mục là bắt buộc"

#### 3.3 Chỉnh sửa danh mục

-   [ ] **Test Case 3.3.1**: Cập nhật thông tin danh mục
    -   Click "Chỉnh sửa" trên danh mục bất kỳ
    -   Thay đổi tên: "Laptop Gaming Pro"
    -   Click "Cập nhật"
    -   **Expected**: Thông báo "Cập nhật danh mục thành công!"

#### 3.4 Xóa danh mục

-   [ ] **Test Case 3.4.1**: Xóa danh mục thành công
    -   Click "Xóa" trên danh mục bất kỳ
    -   **Expected**: Danh mục biến mất, ảnh được xóa

---

### ✅ PHẦN 4: KIỂM THỬ QUẢN LÝ THƯƠNG HIỆU (BRANDS)

#### 4.1 Xem danh sách thương hiệu

-   [ ] **Test Case 4.1.1**: Truy cập danh sách thương hiệu
    -   Truy cập: `/show-brand`
    -   **Expected**: Hiển thị danh sách thương hiệu với logo

#### 4.2 Tạo thương hiệu mới

-   [ ] **Test Case 4.2.1**: Tạo thương hiệu với đầy đủ thông tin

    -   Click "Thêm thương hiệu"
    -   Nhập thông tin:
        -   Tên thương hiệu: "Samsung"
        -   Mô tả: "Công ty công nghệ Hàn Quốc"
        -   Trạng thái: Hoạt động
        -   Upload logo: chọn file ảnh (bắt buộc)
    -   Click "Thêm"
    -   **Expected**: Chuyển về danh sách

-   [ ] **Test Case 4.2.2**: Tạo thương hiệu không upload logo
    -   Không chọn file logo
    -   **Expected**: Hiển thị lỗi "File không hợp lệ"

#### 4.3 Chỉnh sửa thương hiệu

-   [ ] **Test Case 4.3.1**: Cập nhật thông tin thương hiệu
    -   Click "Chỉnh sửa" trên thương hiệu bất kỳ
    -   Thay đổi tên: "Samsung Electronics"
    -   Click "Cập nhật"
    -   **Expected**: Thông tin được cập nhật

#### 4.4 Xóa thương hiệu

-   [ ] **Test Case 4.4.1**: Xóa thương hiệu thành công
    -   Click "Xóa" trên thương hiệu bất kỳ
    -   **Expected**: Thương hiệu biến mất, logo được xóa

---

### ✅ PHẦN 5: KIỂM THỬ QUẢN LÝ NGƯỜI DÙNG (USERS)

#### 5.1 Xem danh sách người dùng

-   [ ] **Test Case 5.1.1**: Truy cập danh sách người dùng
    -   Truy cập: `/admin/users`
    -   **Expected**: Hiển thị danh sách người dùng với thông tin cơ bản

#### 5.2 Thay đổi quyền người dùng

-   [ ] **Test Case 5.2.1**: Thay đổi role từ user sang admin
    -   Chọn user có role "user"
    -   Thay đổi role thành "admin"
    -   Click "Cập nhật"
    -   **Expected**: Thông báo "Cập nhật quyền thành công"

---

### ✅ PHẦN 6: KIỂM THỬ TÍNH NĂNG KHÁC

#### 6.1 Giỏ hàng (Cart)

-   [ ] **Test Case 6.1.1**: Thêm sản phẩm vào giỏ hàng

    -   Truy cập trang sản phẩm
    -   Click "Thêm vào giỏ hàng"
    -   **Expected**: Sản phẩm được thêm vào giỏ hàng

-   [ ] **Test Case 6.1.2**: Cập nhật số lượng trong giỏ hàng
    -   Truy cập giỏ hàng: `/show-cart`
    -   Thay đổi số lượng sản phẩm
    -   **Expected**: Tổng tiền được tính lại

#### 6.2 Responsive Design

-   [ ] **Test Case 6.2.1**: Kiểm tra trên desktop

    -   Truy cập các trang trên màn hình desktop
    -   **Expected**: Giao diện hiển thị đúng

-   [ ] **Test Case 6.2.2**: Kiểm tra trên mobile
    -   Truy cập các trang trên điện thoại
    -   **Expected**: Giao diện responsive, dễ sử dụng

---

## 🐛 BUG REPORTING TEMPLATE

### Mẫu báo cáo lỗi:

```
**Bug ID**: BUG-001
**Module**: Product Management
**Severity**: High/Medium/Low
**Priority**: High/Medium/Low

**Description**:
Mô tả chi tiết lỗi gặp phải

**Steps to Reproduce**:
1. Bước 1
2. Bước 2
3. Bước 3

**Expected Result**:
Kết quả mong đợi

**Actual Result**:
Kết quả thực tế

**Environment**:
- Browser: Chrome/Firefox/Safari
- OS: Windows/Mac/Linux
- Screen Resolution: 1920x1080

**Screenshots**:
[Đính kèm ảnh chụp màn hình nếu có]

**Additional Notes**:
Ghi chú thêm
```

---

## 📊 TEST RESULTS SUMMARY

### Kết quả kiểm thử tổng thể:

-   [ ] **Authentication**: Pass/Fail
-   [ ] **Product Management**: Pass/Fail
-   [ ] **Category Management**: Pass/Fail
-   [ ] **Brand Management**: Pass/Fail
-   [ ] **User Management**: Pass/Fail
-   [ ] **Cart Functionality**: Pass/Fail
-   [ ] **Responsive Design**: Pass/Fail

### Số lượng bugs tìm thấy:

-   **Critical**: \_\_\_ bugs
-   **High**: \_\_\_ bugs
-   **Medium**: \_\_\_ bugs
-   **Low**: \_\_\_ bugs

### Đánh giá tổng thể:

-   **Functionality**: \_\_\_/10
-   **Usability**: \_\_\_/10
-   **Performance**: \_\_\_/10
-   **Security**: \_\_\_/10

---

## 📝 NOTES FOR TESTER

### Lưu ý quan trọng:

1. **Backup Data**: Luôn backup dữ liệu trước khi test
2. **Test Environment**: Đảm bảo test trên môi trường riêng biệt
3. **Documentation**: Ghi chép chi tiết mọi bug và issue
4. **Regression Testing**: Test lại các chức năng đã fix
5. **Cross-browser**: Test trên nhiều trình duyệt khác nhau

### Tools hỗ trợ:

-   **Browser DevTools**: F12 để debug
-   **Screenshot Tools**: Chụp ảnh màn hình khi có lỗi
-   **Test Data**: Chuẩn bị sẵn dữ liệu test
-   **Bug Tracking**: Sử dụng Excel hoặc tool quản lý bug

---

**Tài liệu này được tạo để hỗ trợ quá trình kiểm thử hệ thống một cách có hệ thống và chuyên nghiệp.**

**Ngày tạo**: 19/10/2025  
**Phiên bản**: 1.0  
**Người tạo**: AI Assistant

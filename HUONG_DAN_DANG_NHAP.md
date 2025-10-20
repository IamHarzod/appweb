# Hướng dẫn sử dụng chức năng đăng nhập và quên mật khẩu

## Các chức năng đã được cải thiện

### 1. Validation đăng nhập
- **Bắt buộc nhập email**: Thông báo lỗi nếu không nhập email
- **Bắt buộc nhập mật khẩu**: Thông báo lỗi nếu không nhập mật khẩu
- **Validation email**: Kiểm tra định dạng email hợp lệ
- **Validation mật khẩu**: Mật khẩu phải có ít nhất 6 ký tự
- **Thông báo lỗi chi tiết**: Hiển thị lỗi cụ thể cho từng trường

### 2. Xử lý lỗi đăng nhập
- **Thông báo sai mật khẩu**: Hiển thị thông báo "Email hoặc mật khẩu không đúng" khi đăng nhập sai
- **Giữ lại email**: Email được giữ lại trong form khi có lỗi
- **Hiển thị lỗi trong form**: Lỗi hiển thị ngay trong form với styling Bootstrap

### 3. Chức năng quên mật khẩu
- **Form quên mật khẩu**: Truy cập tại `/password/reset`
- **Gửi email reset**: Nhập email để nhận liên kết khôi phục
- **Token bảo mật**: Sử dụng token ngẫu nhiên 64 ký tự
- **Thời hạn token**: Token có hiệu lực trong 24 giờ
- **Form reset mật khẩu**: Nhập mật khẩu mới và xác nhận

## Cách sử dụng

### Đăng nhập
1. Truy cập `/admin`
2. Nhập email và mật khẩu
3. Có thể tick "Ghi nhớ tôi" để đăng nhập tự động
4. Nhấn "Đăng nhập"

### Quên mật khẩu
1. Tại trang đăng nhập, nhấn "Quên mật khẩu?"
2. Nhập email đã đăng ký
3. Nhấn "Gửi liên kết khôi phục"
4. Kiểm tra log để lấy link reset (hiện tại chưa cấu hình email)
5. Truy cập link reset và nhập mật khẩu mới

### Test chức năng
- Để test token reset: truy cập `/test-password-reset/{email}`
- Kiểm tra log Laravel để xem link reset password

## Cấu trúc file đã tạo/cập nhật

### Controllers
- `app/Http/Controllers/AdminController.php` - Cập nhật logic đăng nhập
- `app/Http/Controllers/Auth/PasswordResetController.php` - Controller mới cho reset password

### Views
- `resources/views/admin/auth/login_admin.blade.php` - Cập nhật form đăng nhập
- `resources/views/admin/auth/forgot_password.blade.php` - Form quên mật khẩu
- `resources/views/admin/auth/reset_password.blade.php` - Form reset mật khẩu

### Models
- `app/Models/User.php` - Thêm interface CanResetPassword

### Routes
- `routes/web.php` - Thêm routes cho password reset

### Database
- Sử dụng bảng `password_reset_tokens` có sẵn

## Lưu ý quan trọng

1. **Email service**: Hiện tại chức năng gửi email chưa được cấu hình, link reset được ghi vào log
2. **Bảo mật**: Token được hash trước khi lưu vào database
3. **Thời hạn**: Token có hiệu lực 24 giờ
4. **Validation**: Tất cả input đều được validate kỹ lưỡng

## Cần cấu hình thêm

Để hoàn thiện chức năng, cần cấu hình:
1. Mail service trong `.env`
2. Tạo email template cho password reset
3. Cấu hình queue nếu muốn gửi email bất đồng bộ

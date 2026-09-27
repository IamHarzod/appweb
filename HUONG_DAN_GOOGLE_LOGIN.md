# Hướng Dẫn Cấu Hình Và Sử Dụng Đăng Nhập Bằng Google (Google OAuth 2.0)

## 📌 Tổng Quan Tính Năng

Tính năng **Đăng nhập bằng Google** đã được tích hợp hoàn chỉnh vào hệ thống thông qua `laravel/socialite` với các ưu điểm:
1. **Đăng nhập 1-click**: Khách hàng và Quản trị viên có thể đăng nhập tức thì không cần nhớ mật khẩu.
2. **Tự động liên kết tài khoản**: Nếu email Google đã tồn tại trong hệ thống (từng đăng ký trước đó), hệ thống sẽ tự động liên kết `google_id` và cập nhật ảnh đại diện (`avatar`), giữ nguyên quyền hạn và lịch sử đơn hàng.
3. **Tự động tạo tài khoản mới**: Nếu chưa có tài khoản, hệ thống sẽ tự động tạo người dùng mới với vai trò `user`, kích hoạt tài khoản (`IsActive = 1`) và tạo mật khẩu ngẫu nhiên bảo mật cao.
4. **Phân quyền điều hướng thông minh**:
   - Tài khoản có vai trò `admin`: Tự động chuyển hướng vào trang Quản trị (`/admin/dashboard`).
   - Tài khoản người dùng `user`: Tự động chuyển hướng về Trang chủ (`/`).
5. **Xử lý an toàn**: Bắt lỗi thân thiện nếu người dùng hủy quyền hoặc chưa cấu hình API credentials trong `.env`.

---

## 🛠️ Các Bước Cấu Hình Google OAuth 2.0

Để tính năng hoạt động trên môi trường thật hoặc localhost, bạn cần tạo thông tin xác thực trên Google Cloud Platform theo các bước sau:

### Bước 1: Truy cập Google Cloud Console
1. Truy cập vào [Google Cloud Console](https://console.cloud.google.com/).
2. Đăng nhập bằng tài khoản Google của bạn.
3. Chọn hoặc tạo một Project mới (Ví dụ: `Shop Online` hoặc `AppWeb`).

### Bước 2: Cấu hình Màn hình đồng ý OAuth (OAuth consent screen)
1. Ở thanh menu bên trái, chọn **APIs & Services** > **OAuth consent screen** (Màn hình đồng ý OAuth).
2. Chọn **User Type**:
   - Chọn **External** (Bên ngoài) để bất kỳ ai có Gmail đều đăng nhập được.
   - Nhấn **Create**.
3. Điền các thông tin ứng dụng cơ bản:
   - **App name**: Tên ứng dụng (Ví dụ: `AppWeb Shop`).
   - **User support email**: Email của bạn.
   - **Developer contact information**: Email của bạn.
4. Nhấn **Save and Continue** qua các bước Scopes (mặc định đã có `email`, `profile`, `openid`).
5. Ở mục **Test users** (nếu ứng dụng đang ở chế độ Testing), bạn hãy thêm các địa chỉ email bạn dùng để đăng nhập thử nghiệm.
6. Nhấn **Save and Continue** để hoàn tất.

### Bước 3: Tạo Khóa Thông Tin Xác Thực (OAuth 2.0 Client ID)
1. Ở menu bên trái, chọn **Credentials** (Thông tin xác thực).
2. Nhấn vào **+ CREATE CREDENTIALS** ở trên cùng > chọn **OAuth client ID**.
3. **Application type**: Chọn **Web application**.
4. **Name**: Đặt tên (Ví dụ: `AppWeb Client`).
5. **Authorized JavaScript origins** (Nguồn gốc JavaScript được phép):
   - Thêm: `http://localhost:8000`
   - Thêm: `http://127.0.0.1:8000`
   - Thêm: `http://localhost`
6. **Authorized redirect URIs** (URI chuyển hướng được phép - *CỰC KỲ QUAN TRỌNG*):
   - Nếu chạy qua `php artisan serve`:
     - `http://localhost:8000/auth/google/callback`
     - `http://127.0.0.1:8000/auth/google/callback`
   - Nếu chạy qua Apache/XAMPP (`http://localhost/appweb`):
     - `http://localhost/appweb/auth/google/callback`
     - `http://localhost/auth/google/callback`
7. Nhấn **Create**. Một popup sẽ xuất hiện cung cấp cho bạn:
   - **Client ID** (dạng `xxxxxx.apps.googleusercontent.com`)
   - **Client Secret** (dạng `GOCSPX-xxxxxx`)

---

## ⚙️ Cấu Hình Vào File `.env`

Mở file `.env` trong thư mục dự án và dán thông tin bạn vừa nhận được:

```env
# Google OAuth 2.0 Configuration
GOOGLE_CLIENT_ID=your-google-client-id-here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-your-client-secret-here
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

> **Lưu ý**: Đảm bảo biến `APP_URL` trong file `.env` khớp với đường dẫn bạn đang chạy (Ví dụ `APP_URL=http://localhost:8000` hoặc `http://127.0.0.1:8000` hoặc `http://localhost/appweb`).

Sau khi cập nhật file `.env`, bạn nên chạy lệnh xóa cache config:
```powershell
php artisan config:clear
```

---

## 📁 Cấu Trúc Các File Đã Triển Khai

1. **Controller xử lý**:
   - `app/Http/Controllers/Auth/GoogleAuthController.php`: Quản lý chuyển hướng sang Google và tiếp nhận callback xác thực, tạo user hoặc liên kết user cũ.
2. **Model User**:
   - `app/Models/User.php`: Đã bổ sung `google_id` và `avatar` vào `$fillable`.
3. **Database Migration**:
   - `database/migrations/2026_09_20_000000_add_google_auth_to_users_table.php`: Quản lý cột `google_id` (unique, nullable) và `avatar` (nullable) trong bảng `users`.
4. **Cấu hình dịch vụ**:
   - `config/services.php`: Đã cấu hình mảng `'google'` sử dụng biến môi trường.
   - `.env` & `.env.example`: Đã thêm các khóa cấu hình Google.
5. **Giao diện người dùng (Blade Views)**:
   - `resources/views/admin/auth/login_admin.blade.php`: Đã bổ sung nút bấm Google login chuẩn thiết kế Google và hiển thị thông báo flash.
   - `resources/views/admin/auth/register_admin.blade.php`: Đã bổ sung nút bấm đăng ký nhanh bằng Google.
6. **Routes**:
   - `routes/web.php`:
     - `GET /auth/google` (name: `auth.google`)
     - `GET /auth/google/callback` (name: `auth.google.callback`)
7. **Kiểm thử tự động**:
   - `tests/Feature/GoogleAuthTest.php`: 6 bài kiểm thử toàn diện cho các kịch bản Google Auth.

---

## 🧪 Cách Kiểm Tra Tính Năng

### 1. Chạy bài kiểm tra tự động (Feature Tests)
Chạy lệnh sau tại terminal:
```powershell
php artisan test --filter=GoogleAuthTest
```
Kết quả: Đạt 100% (6 tests passed, 22 assertions).

### 2. Kiểm tra trên trình duyệt
1. Chạy server:
   ```powershell
   php artisan serve
   ```
2. Mở trình duyệt truy cập: `http://127.0.0.1:8000/login` hoặc `http://127.0.0.1:8000/admin`.
3. Bạn sẽ thấy form đăng nhập có nút **"Đăng nhập bằng Google"** với biểu tượng đa sắc chuẩn Google.
4. Nhấn nút để bắt đầu trải nghiệm đăng nhập nhanh.

# Original User Request

## 2026-09-18T14:33:01Z

Rà soát, kiểm tra và đánh giá toàn diện hệ thống website Laravel (cả khu vực Client và Admin) bắt đầu từ các thành phần giao diện người dùng (Blade views, UI/UX, Forms, Assets), kiểm tra sự liên kết với Routes/Controllers, và tổng hợp báo cáo chi tiết kèm giải pháp khắc phục các lỗi phát hiện được.

Working directory: c:\xampp\htdocs\appweb
Integrity mode: development

## Requirements

### R1. Rà soát giao diện phía Client (Client-side UI & Views)
- Kiểm tra toàn bộ các view tại `resources/views/client/`, `resources/views/auth/`, và `resources/views/layout/`.
- Đánh giá tính hoàn chỉnh của giao diện: header, footer, navigation, trang chủ, danh sách sản phẩm, chi tiết sản phẩm, giỏ hàng, thanh toán (checkout), đăng nhập/đăng ký, trang cá nhân.
- Kiểm tra các form nhập liệu, đường dẫn liên kết tĩnh/động, tính responsive và các lỗi vỡ layout hoặc thiếu CSS/JS.

### R2. Rà soát giao diện phía Admin (Admin Dashboard & Management Views)
- Kiểm tra toàn bộ các view quản trị tại `resources/views/admin/` (Dashboard, Quản lý sản phẩm, Danh mục, Đơn hàng, Người dùng, Cài đặt).
- Kiểm tra bảng dữ liệu (tables), form thêm/sửa/xóa (CRUD), modal, thông báo (flash messages), quyền truy cập và hiển thị trạng thái dữ liệu.

### R3. Rà soát liên kết Giao diện - Backend (Routes, Controllers, Validation, Data flow)
- Kiểm tra tính tương thích giữa action trên Form/View với các Route định nghĩa trong `routes/web.php` và `routes/api.php`.
- Kiểm tra logic xử lý trong `app/Http/Controllers`, dữ liệu truyền vào view, và xử lý validate dữ liệu đầu vào.
- Phát hiện các liên kết hỏng (broken links), route không tồn tại, thiếu controller method hoặc lỗi hiển thị biến không xác định (`undefined variable`).

### R4. Lập báo cáo kiểm tra toàn diện và lộ trình khắc phục (Audit & Recommendations Report)
- Tổng hợp tài liệu báo cáo phân loại rõ ràng: Lỗi nghiêm trọng (Critical), Lỗi giao diện/hiển thị (UI/UX), Lỗi luồng nghiệp vụ (Functional/Logic), Đề xuất tối ưu (Improvements).
- Kèm theo chỉ dẫn file/dòng code cụ thể và hướng dẫn sửa lỗi rõ ràng.

## Acceptance Criteria

### Giao diện & Trải nghiệm (UI/UX)
- [ ] Kiểm tra 100% các file view trong `resources/views/client/`, `resources/views/admin/`, `resources/views/auth/`.
- [ ] Báo cáo ghi nhận đầy đủ các lỗi giao diện, vỡ layout, thiếu link hoặc asset không tải được.

### Luồng nghiệp vụ & Kết nối Backend (Functional & Integration)
- [ ] Kiểm tra tính khớp nối giữa Form action / AJAX calls với Controller tương ứng.
- [ ] Xác định các trường hợp xử lý ngoại lệ bị thiếu hoặc có nguy cơ crash trang khi người dùng thao tác trên giao diện.

### Báo cáo kiểm tra (Audit Deliverable)
- [ ] Xuất bản báo cáo kiểm toán hệ thống chi tiết (Markdown) lưu tại thư mục dự án với cấu trúc phân loại rõ ràng và phương án khắc phục cho từng mục.

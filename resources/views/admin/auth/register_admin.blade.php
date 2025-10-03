<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Focus - Bootstrap Admin Dashboard </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('public/admin/images/favicon.png') }}">
    <link href="{{ asset('public/admin/css/style.css') }}" rel="stylesheet">

</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container-fluid h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <h4 class="text-center mb-4">Đăng kí tài khoản</h4>
                                    <form id="register-form" action="{{ url('/submit-register-admin') }}"
                                        method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label><strong>Họ và tên</strong></label>
                                            <input type="text" name="name" class="form-control"
                                                placeholder="Họ và tên" required>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Email</strong></label>
                                            <input type="email" name="email" class="form-control"
                                                placeholder="Email" required>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Số điện thoại</strong></label>
                                            <input type="text" name="phone" class="form-control"
                                                placeholder="Số điện thoại" required>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Mật khẩu</strong></label>
                                            <input type="password" name="password" class="form-control"
                                                placeholder="Mật khẩu" minlength="6" required>
                                        </div>

                                        <div class="form-group">
                                            <label><strong>Xác nhận mật khẩu</strong></label>
                                            <input type="password" name="check-password" class="form-control"
                                                placeholder="Xác nhận mật khẩu" minlength="6" required>
                                            <small id="pw-error" class="text-danger d-none"></small>
                                        </div>

                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary btn-block">Đăng ký</button>
                                        </div>
                                    </form>

                                    <div class="new-account mt-3">
                                        <p>Nếu đã có tài khoản <a class="text-primary" href="{{ url('/admin') }}">Đăng
                                                nhập</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ asset('public/admin/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('public/admin/js/quixnav-init.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('register-form');
            const pass = form.querySelector('input[name="password"]');
            const cpass = form.querySelector('input[name="check-password"]');
            const errEl = document.getElementById('pw-error');

            function validateMatch() {
                // Xoá lỗi cũ
                errEl.textContent = '';
                errEl.classList.add('d-none');

                // Kiểm tra độ dài tối thiểu (tuỳ chọn)
                if (pass.value && pass.value.length < 6) {
                    cpass.setCustomValidity(''); // không báo lỗi “không khớp” khi đang lỗi độ dài
                    return;
                }

                // Kiểm tra khớp
                if (cpass.value && pass.value !== cpass.value) {
                    cpass.setCustomValidity('Mật khẩu và xác nhận mật khẩu không khớp');
                    errEl.textContent = 'Mật khẩu và xác nhận mật khẩu không khớp';
                    errEl.classList.remove('d-none');
                } else {
                    cpass.setCustomValidity('');
                }
            }

            // Kiểm tra theo thời gian thực
            pass.addEventListener('input', validateMatch);
            cpass.addEventListener('input', validateMatch);

            // Chặn submit nếu có lỗi
            form.addEventListener('submit', function(e) {
             

                validateMatch();
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Gợi ý focus vào ô xác nhận nếu lỗi do không khớp
                    if (cpass.validationMessage) cpass.focus();
                }
            });
        });
    </script>

    <!--endRemoveIf(production)-->
</body>

</html>

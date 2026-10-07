<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Đăng kí tài khoản </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/images/favicon.png') }}">
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

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

                                    @if (session('status'))
                                        <div class="alert alert-success">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    @if (session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    @if (session('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0 pl-3">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form id="register-form" action="{{ url('/submit-register-admin') }}"
                                        method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label><strong>Họ và tên</strong></label>
                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}" placeholder="Họ và tên" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Email</strong></label>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" placeholder="Email" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Số điện thoại</strong></label>
                                            <input type="text" name="phoneNumber" class="form-control @error('phoneNumber') is-invalid @enderror"
                                                value="{{ old('phoneNumber') }}" placeholder="Số điện thoại" required>
                                            @error('phoneNumber')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Mật khẩu</strong></label>
                                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                                placeholder="Mật khẩu" minlength="6" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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

                                    <div class="text-center my-3 position-relative">
                                        <hr style="border-top: 1px solid #e5e7eb;">
                                        <span class="position-absolute px-2 bg-white text-muted" style="top: -12px; left: 50%; transform: translateX(-50%); font-size: 13px;">HOẶC</span>
                                    </div>

                                    <div class="text-center">
                                        <a href="{{ route('auth.google') }}" class="btn btn-light btn-block d-flex align-items-center justify-content-center py-2 text-dark font-weight-bold" style="background-color: #fff; border: 1px solid #dadce0; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); text-decoration: none; font-size: 14px; transition: all 0.2s ease;">
                                            <svg class="mr-2" width="18" height="18" viewBox="0 0 24 24">
                                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                            </svg>
                                            <span>Đăng ký nhanh bằng Google</span>
                                        </a>
                                    </div>

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
    <script src="{{ asset('admin/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('admin/js/quixnav-init.js') }}"></script>

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

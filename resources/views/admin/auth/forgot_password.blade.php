<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Quên mật khẩu</title>
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
                                    <h4 class="text-center mb-4">Quên mật khẩu</h4>
                                    <p class="text-center mb-4">Nhập địa chỉ email của bạn để nhận liên kết khôi phục mật khẩu.</p>
                                    
                                    <form action="{{ route('password.email') }}" method="POST">
                                        @csrf
                                        
                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="form-group">
                                            <label><strong>Email</strong></label>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                                value="{{ old('email') }}" placeholder="Nhập địa chỉ email của bạn">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">Gửi liên kết khôi phục</button>
                                        </div>
                                    </form>
                                    
                                    <div class="new-account mt-3">
                                        <p class="text-center">
                                            <a class="text-primary" href="{{ route('admin') }}">← Quay lại đăng nhập</a>
                                        </p>
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
    <script src="{{ asset('public/admin/js/custom.min.js') }}"></script>

</body>

</html>

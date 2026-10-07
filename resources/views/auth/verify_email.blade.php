@extends('layout.home_layout')

@section('home-content')
<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4 p-md-5 text-center">
                    
                    {{-- Icon Email --}}
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 80px; height: 80px; background-color: #fff3ea; color: #f37021;">
                            <i class="fas fa-envelope-open-text fa-3x"></i>
                        </div>
                    </div>

                    <h3 class="fw-bold text-dark mb-3">Xác thực địa chỉ Email</h3>

                    <p class="text-muted mb-3" style="font-size: 15px; line-height: 1.6;">
                        Cảm ơn bạn đã đăng ký tài khoản tại <strong>36Shop</strong>! Trước khi bắt đầu mua sắm, vui lòng kiểm tra hộp thư và nhấn vào liên kết xác thực chúng tôi vừa gửi đến:
                    </p>

                    <div class="p-3 mb-4 rounded-3 d-inline-block w-100" style="background-color: #f8f9fa; border: 1px dashed #dee2e6;">
                        <span class="text-primary fw-bold fs-6">
                            <i class="fas fa-at me-1"></i> {{ auth()->user()->email ?? 'Email của bạn' }}
                        </span>
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success d-flex align-items-center mb-4 text-start" role="alert" style="border-radius: 10px;">
                            <i class="fas fa-check-circle fa-lg me-2 flex-shrink-0"></i>
                            <div>
                                <strong>Đã gửi thành công!</strong> Một liên kết xác thực mới đã được gửi tới hộp thư của bạn. Vui lòng kiểm tra lại (cả trong mục <em>Spam / Thư rác / Quảng cáo</em>).
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger d-flex align-items-center mb-4 text-start" role="alert" style="border-radius: 10px;">
                            <i class="fas fa-exclamation-triangle fa-lg me-2 flex-shrink-0"></i>
                            <div>
                                <strong>Gặp sự cố khi gửi mail:</strong> {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    <p class="text-muted small mb-4">
                        Không nhận được thư? Nhấn nút bên dưới để hệ thống gửi lại mã kích hoạt mới.
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center mb-3">
                        <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 w-100 fw-bold">
                                <i class="fas fa-paper-plane me-1"></i> Gửi lại email xác thực
                            </button>
                        </form>

                        <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                            <i class="fas fa-home me-1"></i> Về trang chủ
                        </a>
                    </div>

                    <div class="border-top pt-3 mt-4 text-center">
                        <small class="text-muted">Đăng nhập tài khoản khác? 
                            <a href="{{ route('logout') }}" class="text-danger text-decoration-none fw-bold">
                                <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                            </a>
                        </small>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

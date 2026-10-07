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

                    @if (session('success'))
                        <div class="alert alert-success d-flex align-items-center mb-4 text-start" role="alert" style="border-radius: 10px;">
                            <i class="fas fa-check-circle fa-lg me-2 flex-shrink-0"></i>
                            <div>
                                <strong>Thành công!</strong> {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning d-flex align-items-center mb-4 text-start" role="alert" style="border-radius: 10px;">
                            <i class="fas fa-exclamation-circle fa-lg me-2 flex-shrink-0"></i>
                            <div>
                                <strong>Yêu cầu xác thực:</strong> {{ session('warning') }}
                            </div>
                        </div>
                    @endif

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success d-flex align-items-center mb-4 text-start" role="alert" style="border-radius: 10px;">
                            <i class="fas fa-paper-plane fa-lg me-2 flex-shrink-0"></i>
                            <div>
                                <strong>Đã gửi lại thành công!</strong> Một liên kết xác thực mới đã được gửi tới hộp thư của bạn. Vui lòng kiểm tra lại (cả trong mục <em>Spam / Thư rác / Quảng cáo</em>).
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

                    @if (session('fallback_verify_url'))
                        <div class="alert alert-info text-start mb-4 shadow-sm" style="border-radius: 12px; border-left: 5px solid #0dcaf0;">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-key text-info fa-lg me-2"></i>
                                <strong class="text-dark">Liên kết kích hoạt tài khoản trực tiếp:</strong>
                            </div>
                            <p class="mb-3 small text-muted">
                                Do máy chủ Render (gói Free) chặn cổng gửi mail SMTP (587/465) ra Internet, bạn có thể nhấn ngay nút bên dưới để xác thực và kích hoạt tài khoản thành công ngay lập tức:
                            </p>
                            <div class="text-center">
                                <a href="{{ session('fallback_verify_url') }}" class="btn btn-success fw-bold rounded-pill px-4 py-2 shadow-sm">
                                    <i class="fas fa-check-circle me-1"></i> Kích hoạt tài khoản ngay
                                </a>
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

                        <a href="{{ route('logout') }}" class="btn btn-outline-danger rounded-pill px-4 py-2">
                            <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                        </a>
                    </div>

                    <div class="alert alert-light border small text-muted text-start mt-4 mb-0" style="border-radius: 10px;">
                        <i class="fas fa-shield-alt text-warning me-1"></i>
                        <strong>Lưu ý bảo mật:</strong> Để đảm bảo an toàn giao dịch và bảo vệ quyền lợi khách hàng, bạn cần mở email và nhấn vào liên kết xác nhận để mở khóa toàn bộ quyền mua sắm, đặt hàng và sử dụng dịch vụ trên 36Shop.
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

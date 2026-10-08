@extends('layout.home_layout')

@section('home-content')
<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: #ffffff;">
                
                {{-- Header banner --}}
                <div class="text-center pt-5 pb-3 px-4" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm mb-3" 
                         style="width: 80px; height: 80px; background-color: #ffffff; color: #0d6efd;">
                        <i class="fas fa-shield-alt fa-3x"></i>
                    </div>
                    <h3 class="fw-bold text-white mb-1">Xác thực mã OTP</h3>
                    <p class="text-white-50 small mb-0">Bảo mật tài khoản 36Shop qua giao thức HTTPS</p>
                </div>

                <div class="card-body p-4 p-md-5 text-center">

                    {{-- Thông báo gửi tới email nào --}}
                    <p class="text-muted mb-2" style="font-size: 15px; line-height: 1.6;">
                        Mã xác thực bảo mật <strong>OTP (6 chữ số)</strong> đã được gửi tới địa chỉ:
                    </p>
                    <div class="p-2 mb-4 rounded-3 d-inline-block w-100" style="background-color: #f1f5f9; border: 1px dashed #cbd5e1;">
                        <span class="text-primary fw-bold fs-6">
                            <i class="fas fa-envelope me-1"></i> {{ $user->email }}
                        </span>
                    </div>

                    {{-- Thông báo Alerts --}}
                    @if (session('success'))
                        <div class="alert alert-success d-flex align-items-center mb-4 text-start shadow-sm" role="alert" style="border-radius: 12px;">
                            <i class="fas fa-check-circle fa-lg me-2 flex-shrink-0 text-success"></i>
                            <div class="small">
                                <strong>Thành công:</strong> {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning d-flex align-items-center mb-4 text-start shadow-sm" role="alert" style="border-radius: 12px;">
                            <i class="fas fa-exclamation-triangle fa-lg me-2 flex-shrink-0 text-warning"></i>
                            <div class="small">
                                {{ session('warning') }}
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger d-flex align-items-center mb-4 text-start shadow-sm" role="alert" style="border-radius: 12px;">
                            <i class="fas fa-times-circle fa-lg me-2 flex-shrink-0 text-danger"></i>
                            <div class="small">
                                <strong>Lỗi:</strong> {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    {{-- Thông báo debug OTP (nếu chưa cấu hình Email API hoặc môi trường local) --}}
                    @if (!empty($debugOtp))
                        <div class="alert alert-info text-start mb-4 shadow-sm" style="border-radius: 12px; border-left: 5px solid #0dcaf0; background-color: #f0f9ff;">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-key text-info fa-lg me-2"></i>
                                <strong class="text-dark small">Mã OTP thử nghiệm (Demo Mode):</strong>
                            </div>
                            <p class="mb-2 small text-muted">
                                Do chưa gắn API Key Email hoặc đang ở chế độ kiểm thử, bạn có thể nhập trực tiếp mã OTP sau:
                            </p>
                            <div class="text-center py-2 bg-white rounded border">
                                <span class="fs-4 fw-bold text-primary letter-spacing-lg" style="letter-spacing: 6px;">{{ $debugOtp }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- FORM NHẬP MÃ OTP --}}
                    <form method="POST" action="{{ route('verification.verify_otp') }}" id="otpForm" class="mb-4">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold small mb-3">NHẬP 6 CHỮ SỐ MÃ OTP</label>
                            
                            {{-- 6 ô số trực quan --}}
                            <div class="d-flex justify-content-between gap-2 mb-2 px-1">
                                @for ($i = 0; $i < 6; $i++)
                                    <input type="text" 
                                           class="form-control text-center fs-3 fw-bold otp-digit" 
                                           maxlength="1" 
                                           inputmode="numeric" 
                                           pattern="[0-9]*" 
                                           autocomplete="off"
                                           data-index="{{ $i }}"
                                           style="width: 48px; height: 58px; border-radius: 12px; border: 2px solid #ced4da; transition: all 0.2s;"
                                           required>
                                @endfor
                            </div>

                            {{-- Input ẩn chứa chuỗi 6 ký tự thực tế submit lên server --}}
                            <input type="hidden" name="otp" id="fullOtpInput" value="">
                            
                            @error('otp')
                                <div class="text-danger small mt-2 fw-bold">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Bộ đếm ngược thời gian hết hạn --}}
                        <div class="d-flex align-items-center justify-content-center text-muted small mb-4">
                            <i class="far fa-clock me-1 text-danger"></i>
                            Mã có hiệu lực trong: 
                            <span id="countdownTimer" class="fw-bold text-danger ms-1">
                                {{ sprintf('%02d:%02d', floor($secondsRemaining / 60), $secondsRemaining % 60) }}
                            </span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow-sm" id="btnSubmitOtp">
                            <i class="fas fa-check-circle me-1"></i> Xác thực tài khoản
                        </button>
                    </form>

                    {{-- GỬI LẠI MÃ & ĐĂNG XUẤT --}}
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 align-items-center pt-3 border-top">
                        <form method="POST" action="{{ route('verification.resend_otp') }}" class="d-inline w-100 w-sm-auto">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary rounded-pill px-3 py-2 w-100 btn-sm" id="btnResendOtp"
                                    {{ $cooldownSeconds > 0 ? 'disabled' : '' }}>
                                <i class="fas fa-redo-alt me-1"></i> 
                                <span id="resendText">
                                    {{ $cooldownSeconds > 0 ? "Gửi lại sau ({$cooldownSeconds}s)" : "Gửi lại mã OTP" }}
                                </span>
                            </button>
                        </form>

                        <a href="{{ route('logout') }}" class="btn btn-link text-danger text-decoration-none small py-2">
                            <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                        </a>
                    </div>

                    {{-- Cam kết bảo mật --}}
                    <div class="alert alert-light border small text-muted text-start mt-4 mb-0" style="border-radius: 10px; font-size: 12px;">
                        <i class="fas fa-lock text-success me-1"></i>
                        <strong>Bảo mật cấp cao:</strong> Mã OTP được mã hóa và bảo vệ chống dò mã Brute-force (tối đa 5 lần thử). Đường truyền gửi qua giao thức HTTPS Port 443 không bị nghẽn hay chặn bởi tường lửa.
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const digits = document.querySelectorAll('.otp-digit');
    const fullOtpInput = document.getElementById('fullOtpInput');
    const otpForm = document.getElementById('otpForm');
    const btnSubmit = document.getElementById('btnSubmitOtp');

    // Tự động focus vào ô đầu tiên
    if (digits.length > 0) {
        digits[0].focus();
    }

    // Cập nhật giá trị vào hidden input
    function updateFullOtp() {
        let otp = '';
        digits.forEach(d => otp += d.value);
        fullOtpInput.value = otp;
        return otp;
    }

    digits.forEach((digit, idx) => {
        // Highlight khi focus
        digit.addEventListener('focus', function() {
            this.style.borderColor = '#0d6efd';
            this.style.boxShadow = '0 0 0 0.2rem rgba(13, 110, 253, 0.25)';
            this.select();
        });

        digit.addEventListener('blur', function() {
            this.style.borderColor = '#ced4da';
            this.style.boxShadow = 'none';
        });

        // Xử lý khi gõ phím
        digit.addEventListener('input', function(e) {
            // Chỉ nhận chữ số
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length >= 1) {
                this.value = this.value.charAt(0);
                if (idx < digits.length - 1) {
                    digits[idx + 1].focus();
                }
            }
            updateFullOtp();
        });

        // Xử lý phím Backspace để lùi ô
        digit.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) {
                digits[idx - 1].focus();
            }
        });

        // Hỗ trợ dán (Paste) cả 6 chữ số
        digit.addEventListener('paste', function(e) {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
            const numbers = pasteData.replace(/[^0-9]/g, '').slice(0, 6);
            if (numbers.length > 0) {
                numbers.split('').forEach((num, i) => {
                    if (digits[i]) {
                        digits[i].value = num;
                    }
                });
                const nextIndex = Math.min(numbers.length, digits.length - 1);
                digits[nextIndex].focus();
                updateFullOtp();
            }
        });
    });

    otpForm.addEventListener('submit', function(e) {
        const otp = updateFullOtp();
        if (otp.length !== 6) {
            e.preventDefault();
            alert('Vui lòng nhập đủ 6 chữ số mã OTP!');
            const emptyIdx = Array.from(digits).findIndex(d => !d.value);
            if (emptyIdx !== -1) {
                digits[emptyIdx].focus();
            }
        }
    });

    // Countdown timer cho OTP còn hiệu lực
    let secondsRemaining = {{ $secondsRemaining }};
    const timerElem = document.getElementById('countdownTimer');
    if (timerElem && secondsRemaining > 0) {
        const timerInterval = setInterval(function() {
            secondsRemaining--;
            if (secondsRemaining <= 0) {
                clearInterval(timerInterval);
                timerElem.textContent = '00:00 (Hết hạn)';
                btnSubmit.disabled = true;
            } else {
                const m = Math.floor(secondsRemaining / 60).toString().padStart(2, '0');
                const s = (secondsRemaining % 60).toString().padStart(2, '0');
                timerElem.textContent = `${m}:${s}`;
            }
        }, 1000);
    }

    // Cooldown timer cho nút gửi lại
    let cooldownSeconds = {{ $cooldownSeconds }};
    const btnResend = document.getElementById('btnResendOtp');
    const resendText = document.getElementById('resendText');
    if (btnResend && cooldownSeconds > 0) {
        const cooldownInterval = setInterval(function() {
            cooldownSeconds--;
            if (cooldownSeconds <= 0) {
                clearInterval(cooldownInterval);
                btnResend.disabled = false;
                resendText.textContent = 'Gửi lại mã OTP';
            } else {
                resendText.textContent = `Gửi lại sau (${cooldownSeconds}s)`;
            }
        }, 1000);
    }
});
</script>
@endsection

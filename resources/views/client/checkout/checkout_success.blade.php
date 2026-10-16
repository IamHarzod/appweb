@extends('layout.home_layout')
@section('home-content')
@php
    $isFailed = ($order->payment_status === 'failed') 
        || session()->has('payment_failed')
        || session()->has('error') 
        || ($order->status === 'cancelled');

    $reason = session('error') 
        ?? session('order_failure_reason_' . $order->id) 
        ?? ($order->payment_status === 'failed' ? ('Giao dịch thanh toán ' . (strtoupper($order->payment_method ?? '') === 'VNPAY' ? 'qua VNPAY' : (strtoupper($order->payment_method ?? '') === 'MOMO' ? 'qua Ví MoMo' : '')) . ' không thành công hoặc đã bị hủy.') : null);
@endphp
    <!-- Order Success / Failure Start -->
    <div class="container-fluid py-5">
        <div class="container py-5 text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded">
                        <div class="card-body p-5">
                            @if ($isFailed)
                                <!-- Icon Thất bại -->
                                <div class="mb-4">
                                    <i class="fa fa-times-circle text-danger display-1"></i>
                                </div>

                                <h1 class="mb-3 text-danger">Đặt hàng không thành công!</h1>
                                <p class="lead text-muted mb-4">Giao dịch thanh toán chưa hoàn tất. Đơn hàng của bạn chưa thể tiếp nhận xử lý.</p>

                                <!-- Thông tin Mã đơn hàng -->
                                <div class="alert alert-light border mb-4">
                                    <p class="mb-0">Mã đơn hàng của bạn: <strong
                                            class="text-danger">#{{ $order->id ?? 'DH0000' }}</strong></p>
                                </div>

                                <!-- Lý do không thành công -->
                                <div class="alert alert-danger mb-4 text-start">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-exclamation-triangle fs-4 me-3 text-danger flex-shrink-0"></i>
                                        <div>
                                            <strong class="d-block mb-1">Lý do không thành công:</strong>
                                            <span>{{ $reason ?? 'Thanh toán không thành công hoặc giao dịch đã bị hủy.' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Icon Thành công -->
                                <div class="mb-4">
                                    <i class="fa fa-check-circle text-success display-1"></i>
                                </div>

                                <h1 class="mb-3 text-success">Đặt hàng thành công!</h1>
                                <p class="lead text-muted mb-4">Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đã được tiếp nhận và
                                    đang trong quá trình xử lý.</p>

                                <!-- Thông tin Mã đơn hàng -->
                                <div class="alert alert-light border mb-4">
                                    <p class="mb-0">Mã đơn hàng của bạn: <strong
                                            class="text-primary">#{{ $order->id ?? 'DH0000' }}</strong></p>
                                </div>

                                @if (session('warning'))
                                    <div class="alert alert-warning mb-4">
                                        {{ session('warning') }}
                                    </div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success mb-4">
                                        {{ session('success') }}
                                    </div>
                                @endif
                            @endif

                            <!-- Chi tiết đơn hàng -->
                            <div class="text-start">
                                <h4 class="mb-3 border-bottom pb-2">Thông tin đơn hàng</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-uppercase text-muted small">Người nhận</h6>
                                        <p class="fw-bold mb-1">{{ $order->shipping_name ?? 'Nguyễn Văn A' }}</p>
                                        <p class="mb-1">{{ $order->shipping_phone ?? '0123456789' }}</p>
                                        <p class="mb-1">{{ $order->shipping_email ?? 'email@example.com' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-uppercase text-muted small">Địa chỉ giao hàng</h6>
                                        <p class="mb-0">
                                            {{ $order->shipping_address ?? 'Số 1, Đường ABC, Quận XYZ, TP.HCM' }}</p>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-uppercase text-muted small">Phương thức thanh toán</h6>
                                        <p class="fw-bold">
                                            @if (strtoupper($order->payment_method ?? '') == 'COD')
                                                Thanh toán khi nhận hàng (COD)
                                            @elseif(strtoupper($order->payment_method ?? '') == 'VNPAY')
                                                Thanh toán qua VNPAY
                                            @elseif(strtoupper($order->payment_method ?? '') == 'MOMO')
                                                Thanh toán qua Ví MoMo
                                            @else
                                                {{ $order->payment_method ?? 'Khác' }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-uppercase text-muted small">Trạng thái thanh toán</h6>
                                        <p>
                                            @if (($order->payment_status ?? 'unpaid') == 'paid')
                                                <span class="badge bg-success text-white fs-6">Đã thanh toán</span>
                                            @elseif(($order->payment_status ?? '') == 'failed')
                                                <span class="badge bg-danger text-white fs-6">Thanh toán thất bại</span>
                                            @else
                                                <span class="badge bg-warning text-dark fs-6">Chưa thanh toán</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tóm tắt số tiền -->
                            <div class="bg-light p-3 rounded mt-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <?php
                                    // 1. Tính tổng tiền hàng
                                    $subtotal = $order->orderItems->sum(function ($item) {
                                        return $item->price * $item->quantity;
                                    });
                                    
                                    // 2. Lấy phí ship từ DB (thay vì hardcode)
                                    $shippingFee = $order->shipping_fee ?? 50000;
                                    
                                    // 3. Lấy tiền giảm giá từ DB
                                    $discount = $order->discount_amount ?? 0;
                                    
                                    $displayTotal = $subtotal + $shippingFee - $discount;
                                    if ($displayTotal < 0) {
                                        $displayTotal = 0;
                                    }
                                    ?>
                                    <span>Tổng tiền hàng:</span>
                                    <strong>{{ number_format($subtotal ?? 0, 0, ',', '.') }} VNĐ</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    @if (isset($shippingFee) && $shippingFee == 0)
                                        <p class="mb-0 text-success">Miễn phí</p>
                                    @else
                                        <p class="mb-0 text-dark">
                                            {{ number_format($shippingFee ?? 50000, 0, ',', '.') }} VNĐ</p>
                                    @endif
                                </div>
                                @if ($discount > 0)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span>Giảm giá (Voucher):</span>
                                        <strong>-{{ number_format($discount, 0, ',', '.') }} VNĐ</strong>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                    <span class="h5 mb-0">Tổng thanh toán:</span>
                                    <span class="h5 mb-0 text-primary">{{ number_format($displayTotal, 0, ',', '.') }} VNĐ</span>
                                </div>
                            </div>

                            @if (!$isFailed && $order->orderItems && $order->orderItems->isNotEmpty())
                                <!-- PHẦN ĐÁNH GIÁ SẢN PHẨM TRỰC TIẾP SAU KHI MUA HÀNG -->
                                <div class="mt-5 text-start" id="post-purchase-reviews">
                                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                                        <div>
                                            <h4 class="mb-1 text-primary fw-bold">
                                                <i class="fas fa-star text-warning me-2"></i>Đánh giá sản phẩm đã mua
                                            </h4>
                                            <p class="text-muted small mb-0">
                                                Đánh giá trực tiếp ngay tại đây để nhận xét chất lượng sản phẩm và trải nghiệm mua sắm của bạn.
                                            </p>
                                        </div>
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold">
                                            {{ $order->orderItems->count() }} sản phẩm
                                        </span>
                                    </div>

                                    <div class="row g-4">
                                        @foreach ($order->orderItems as $item)
                                            @php
                                                $prod = $item->product;
                                                $existingReview = isset($existingReviews) && $prod ? $existingReviews->get($prod->id) : null;
                                                $itemId = $item->id;
                                                $prodId = $item->product_id;
                                            @endphp
                                            <div class="col-12" id="review-card-wrapper-{{ $prodId }}">
                                                <div class="card border border-light-subtle shadow-sm rounded-4 overflow-hidden">
                                                    <div class="card-body p-4">
                                                        <div class="row align-items-start g-3">
                                                            <!-- Thông tin sản phẩm -->
                                                            <div class="col-md-5 col-lg-4 border-end-md pb-3 pb-md-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="position-relative me-3 flex-shrink-0">
                                                                        @if ($prod && $prod->imageURL)
                                                                            <img src="{{ asset('uploads/products/' . $prod->imageURL) }}"
                                                                                 alt="{{ $item->product_name ?? $prod->name }}"
                                                                                 class="rounded-3 border object-fit-cover shadow-sm"
                                                                                 style="width: 75px; height: 75px;">
                                                                        @else
                                                                            <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center"
                                                                                 style="width: 75px; height: 75px;">
                                                                                <i class="fas fa-box text-muted fa-2x"></i>
                                                                            </div>
                                                                        @endif
                                                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary" style="font-size: 10px;">
                                                                            x{{ $item->quantity }}
                                                                        </span>
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="fw-bold mb-1 text-dark line-clamp-2">
                                                                            {{ $item->product_name ?? ($prod ? $prod->name : 'Sản phẩm #' . $prodId) }}
                                                                        </h6>
                                                                        <div class="text-danger fw-bold small">
                                                                            {{ number_format($item->price, 0, ',', '.') }} VNĐ
                                                                        </div>
                                                                        <div class="text-muted small">
                                                                            Số lượng: {{ $item->quantity }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Khối đánh giá hoặc kết quả đã đánh giá -->
                                                            <div class="col-md-7 col-lg-8 ps-md-4">
                                                                <!-- Trạng thái đã đánh giá -->
                                                                <div id="reviewed-display-{{ $prodId }}" class="{{ $existingReview ? '' : 'd-none' }}">
                                                                    <div class="alert alert-success-subtle border border-success-subtle rounded-3 p-3 mb-2">
                                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <span class="badge bg-success text-white rounded-pill px-2.5 py-1 small">
                                                                                    <i class="fas fa-check-circle me-1"></i>Đã đánh giá
                                                                                </span>
                                                                                <div class="text-warning fs-5" id="reviewed-stars-{{ $prodId }}">
                                                                                    @for ($s = 1; $s <= 5; $s++)
                                                                                        <i class="fas fa-star {{ $existingReview && $existingReview->rating >= $s ? 'text-warning' : 'text-muted' }}" style="font-size: 15px;"></i>
                                                                                    @endfor
                                                                                </div>
                                                                            </div>
                                                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 btn-edit-review" data-product-id="{{ $prodId }}">
                                                                                <i class="fas fa-edit me-1"></i>Chỉnh sửa
                                                                            </button>
                                                                        </div>
                                                                        <p class="mb-1 text-dark fst-italic" id="reviewed-comment-{{ $prodId }}">
                                                                            "{{ $existingReview->comment ?? '' }}"
                                                                        </p>
                                                                        <small class="text-muted" id="reviewed-date-{{ $prodId }}">
                                                                            {{ $existingReview && $existingReview->created_at ? $existingReview->created_at->diffForHumans() : '' }}
                                                                        </small>
                                                                    </div>
                                                                </div>

                                                                <!-- Form gửi đánh giá trực tiếp -->
                                                                <div id="review-form-box-{{ $prodId }}" class="{{ $existingReview ? 'd-none' : '' }}">
                                                                    <form class="ajax-order-review-form" data-product-id="{{ $prodId }}" action="{{ route('product.review.store', $prodId) }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                        <input type="hidden" name="rating" id="rating-input-{{ $prodId }}" value="{{ $existingReview->rating ?? 5 }}">

                                                                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
                                                                            <label class="form-label fw-bold small text-dark mb-1">
                                                                                Mức độ hài lòng:
                                                                                <span id="star-desc-{{ $prodId }}" class="text-warning fw-bold ms-1">Tuyệt vời (5 sao)</span>
                                                                            </label>

                                                                            <div class="star-rating-box d-flex align-items-center gap-1 fs-4 text-warning" style="cursor: pointer;" data-product-id="{{ $prodId }}">
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    <i class="fas fa-star star-item {{ ($existingReview ? $existingReview->rating : 5) >= $i ? 'text-warning' : 'text-muted' }}"
                                                                                       data-value="{{ $i }}"
                                                                                       title="{{ $i }} sao"
                                                                                       style="transition: transform 0.15s ease;"></i>
                                                                                @endfor
                                                                            </div>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <textarea name="comment" class="form-control rounded-3" rows="2"
                                                                                      placeholder="Chia sẻ cảm nhận của bạn về sản phẩm này (chất âm, thiết kế, đóng gói, trải nghiệm thực tế...)"
                                                                                      required>{{ $existingReview->comment ?? '' }}</textarea>
                                                                        </div>

                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <small class="text-muted">Đánh giá được xác thực từ đơn hàng #{{ $order->id }}</small>
                                                                            <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2 shadow-sm submit-review-btn">
                                                                                <i class="fas fa-paper-plane me-1"></i> Gửi đánh giá ngay
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Nút điều hướng -->
                            <div class="mt-5 d-flex justify-content-center flex-wrap gap-3">
                                @if ($isFailed && in_array(strtoupper($order->payment_method ?? ''), ['VNPAY', 'MOMO']))
                                    <a href="{{ route('order.repay', ['id' => $order->id]) }}" class="btn btn-danger rounded-pill px-4 py-2 shadow-sm">
                                        <i class="fa fa-credit-card me-2"></i>Thử thanh toán lại
                                    </a>
                                @endif
                                <a href="{{ route('orders.show', ['id' => $order->id]) }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                    <i class="fas fa-file-invoice me-2"></i>Xem chi tiết đơn hàng #{{ $order->id }}
                                </a>
                                @auth
                                    <a href="{{ route('orders.my') }}" class="btn btn-outline-success rounded-pill px-4 py-2">
                                        <i class="fas fa-box-open me-2"></i>Đơn hàng đã đặt của tôi
                                    </a>
                                @else
                                    <a href="{{ route('orders.lookup') }}" class="btn btn-outline-primary rounded-pill px-4 py-2">
                                        <i class="fa fa-search me-2"></i>Tra cứu đơn hàng
                                    </a>
                                @endauth
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                                    <i class="fa fa-home me-2"></i>Về trang chủ
                                </a>
                                <a href="{{ url('/') }}" class="btn btn-outline-dark rounded-pill px-4 py-2">
                                    <i class="fa fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Order Success End -->

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ratingLabels = {
            1: 'Rất tệ (1 sao)',
            2: 'Tệ (2 sao)',
            3: 'Bình thường (3 sao)',
            4: 'Hài lòng (4 sao)',
            5: 'Tuyệt vời (5 sao)'
        };

        // Xử lý chọn sao tương tác
        document.querySelectorAll('.star-rating-box').forEach(box => {
            const prodId = box.getAttribute('data-product-id');
            const stars = box.querySelectorAll('.star-item');
            const ratingInput = document.getElementById('rating-input-' + prodId);
            const label = document.getElementById('star-desc-' + prodId);

            stars.forEach(star => {
                star.addEventListener('mouseenter', function () {
                    const val = parseInt(this.getAttribute('data-value'));
                    stars.forEach((s, idx) => {
                        s.classList.toggle('text-warning', idx < val);
                        s.classList.toggle('text-muted', idx >= val);
                    });
                    if (label) label.textContent = ratingLabels[val] || (val + ' sao');
                });

                star.addEventListener('click', function () {
                    const val = parseInt(this.getAttribute('data-value'));
                    if (ratingInput) ratingInput.value = val;
                    stars.forEach((s, idx) => {
                        s.classList.toggle('text-warning', idx < val);
                        s.classList.toggle('text-muted', idx >= val);
                    });
                    if (label) label.textContent = ratingLabels[val] || (val + ' sao');
                });
            });

            box.addEventListener('mouseleave', function () {
                const currentVal = parseInt(ratingInput ? ratingInput.value : 5);
                stars.forEach((s, idx) => {
                    s.classList.toggle('text-warning', idx < currentVal);
                    s.classList.toggle('text-muted', idx >= currentVal);
                });
                if (label) label.textContent = ratingLabels[currentVal] || (currentVal + ' sao');
            });
        });

        // Xử lý chuyển đổi Chỉnh sửa đánh giá
        document.querySelectorAll('.btn-edit-review').forEach(btn => {
            btn.addEventListener('click', function () {
                const prodId = this.getAttribute('data-product-id');
                const formBox = document.getElementById('review-form-box-' + prodId);
                const reviewedDisplay = document.getElementById('reviewed-display-' + prodId);
                if (formBox) formBox.classList.remove('d-none');
                if (reviewedDisplay) reviewedDisplay.classList.add('d-none');
            });
        });

        // Xử lý gửi Form Đánh Giá AJAX không cần rời trang
        document.querySelectorAll('.ajax-order-review-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const prodId = this.getAttribute('data-product-id');
                const submitBtn = this.querySelector('.submit-review-btn');
                const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang gửi...';
                }

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(errData => { throw new Error(errData.message || 'Lỗi gửi đánh giá'); });
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        const formBox = document.getElementById('review-form-box-' + prodId);
                        const reviewedDisplay = document.getElementById('reviewed-display-' + prodId);
                        const commentEl = document.getElementById('reviewed-comment-' + prodId);
                        const dateEl = document.getElementById('reviewed-date-' + prodId);
                        const starsEl = document.getElementById('reviewed-stars-' + prodId);

                        if (commentEl && data.review) commentEl.textContent = '"' + data.review.comment + '"';
                        if (dateEl && data.review) dateEl.textContent = data.review.created_at || 'Vừa xong';

                        if (starsEl && data.review) {
                            let starsHtml = '';
                            for (let s = 1; s <= 5; s++) {
                                starsHtml += `<i class="fas fa-star ${s <= data.review.rating ? 'text-warning' : 'text-muted'}" style="font-size: 15px;"></i> `;
                            }
                            starsEl.innerHTML = starsHtml;
                        }

                        if (formBox) formBox.classList.add('d-none');
                        if (reviewedDisplay) reviewedDisplay.classList.remove('d-none');

                        // Hiển thị thông báo nhỏ
                        const alertSuccess = document.createElement('div');
                        alertSuccess.className = 'alert alert-success alert-dismissible fade show mt-2 py-2 px-3 small';
                        alertSuccess.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${data.message} <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>`;
                        reviewedDisplay.prepend(alertSuccess);
                    } else {
                        alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                    }
                })
                .catch(err => {
                    alert(err.message || 'Có lỗi xảy ra khi gửi đánh giá.');
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnHtml;
                    }
                });
            });
        });
    });
    </script>
@endsection

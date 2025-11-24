@extends('layout.home_layout')
@section('home-content')
    <!-- Order Success Start -->
    <div class="container-fluid py-5">
        <div class="container py-5 text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded">
                        <div class="card-body p-5">
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

                                <h6 class="text-uppercase text-muted small mt-4">Phương thức thanh toán</h6>
                                <p>
                                    @if (($order->payment_method ?? 'cod') == 'cod')
                                        Thanh toán khi nhận hàng (COD)
                                    @elseif(($order->payment_method ?? '') == 'vnpay')
                                        Thanh toán qua VNPAY
                                    @else
                                        Thanh toán qua Ngân hàng
                                    @endif
                                </p>
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
                                    <strong>{{ number_format($subtotal ?? 0, 0, ',', '.') }}
                                        VNĐ</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    @if (isset($shippingFee) && $shippingFee == 0)
                                        <p class="mb-0 text-success">Miễn phí</p>
                                    @else
                                        <p class="mb-0 text-dark">
                                            {{ number_format($shippingFee ?? 50000, 0, ',', '.') }} VNĐ</p>
                                    @endif
                                    {{-- <strong>{{ number_format($shippingFee, 0, ',', '.') }} VNĐ</strong> --}}
                                </div>
                                @if ($discount > 0)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span>Giảm giá (Voucher):</span>
                                        <strong>-{{ number_format($discount, 0, ',', '.') }} VNĐ</strong>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                    <span class="h5 mb-0">Tổng thanh toán:</span>
                                    <span class="h5 mb-0 text-primary">{{ number_format($displayTotal, 0, ',', '.') }}
                                        VNĐ</span>
                                </div>
                            </div>

                            <!-- Nút điều hướng -->
                            <div class="mt-5 d-flex justify-content-center gap-3">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                                    <i class="fa fa-home me-2"></i>Về trang chủ
                                </a>
                                <a href="{{ url('/shop') }}" class="btn btn-primary rounded-pill px-4 py-2">
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
@endsection

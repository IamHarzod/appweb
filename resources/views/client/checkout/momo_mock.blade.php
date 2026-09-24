@extends('layout.home_layout')
@section('home-content')
    <div class="container py-5" style="max-width: 600px;">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header MoMo Header -->
            <div class="p-4 text-center text-white" style="background-color: #a50064;">
                <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="MoMo Logo" style="height: 50px; background: white; padding: 5px; border-radius: 10px;" class="mb-3">
                <h4 class="fw-bold mb-1 text-white">CỔNG THANH TOÁN MÔ PHỎNG MOMO</h4>
                <p class="mb-0 text-white-50 fs-7">(Môi trường thử nghiệm cho Lập trình viên)</p>
            </div>

            <div class="card-body p-4 text-center">
                <div class="alert alert-info py-2 px-3 small rounded-3 mb-4">
                    <i class="fas fa-info-circle me-1"></i> Trang này giả lập giao diện Ví MoMo để kiểm thử luồng gạch nợ tự động trong ứng dụng.
                </div>

                <div class="border rounded-3 p-3 bg-light mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Mã đơn hàng:</span>
                        <span class="fw-bold text-dark">#{{ $order->id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Người đặt hàng:</span>
                        <span class="fw-bold text-dark">{{ $order->shipping_name }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mt-2">
                        <span class="fw-bold text-dark fs-5">Số tiền thanh toán:</span>
                        <span class="fw-bold fs-4" style="color: #a50064;">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</span>
                    </div>
                </div>

                <!-- Mã QR giả lập -->
                <div class="mb-4">
                    <p class="text-muted small mb-2">Quét mã QR bằng ứng dụng MoMo (Mô phỏng)</p>
                    <div class="d-inline-block p-3 bg-white border rounded-3 shadow-sm">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=MOMO_MOCK_ORDER_{{ $order->id }}" alt="MoMo QR" class="img-fluid">
                    </div>
                </div>

                <!-- Thao tác thử nghiệm -->
                <div class="d-grid gap-2">
                    <a href="{{ route('momo.return', [
                        'partnerCode' => 'MOMO_MOCK',
                        'orderId' => $order->id . '_' . time(),
                        'requestId' => time(),
                        'amount' => (string)round($order->total_amount),
                        'orderInfo' => 'Thanh toán đơn hàng #' . $order->id,
                        'orderTypeId' => 'momo_wallet',
                        'transId' => 'MOMO_TRANS_' . rand(10000000, 99999999),
                        'resultCode' => 0,
                        'message' => 'Successful.',
                        'responseTime' => time(),
                        'payType' => 'qr',
                        'signature' => 'mock_signature'
                    ]) }}" class="btn py-3 fw-bold text-white shadow-sm" style="background-color: #a50064; border-radius: 10px;">
                        <i class="fas fa-check-circle me-2"></i> XÁC NHẬN THANH TOÁN THÀNH CÔNG
                    </a>

                    <a href="{{ route('momo.return', [
                        'partnerCode' => 'MOMO_MOCK',
                        'orderId' => $order->id . '_' . time(),
                        'requestId' => time(),
                        'amount' => (string)round($order->total_amount),
                        'orderInfo' => 'Thanh toán đơn hàng #' . $order->id,
                        'orderTypeId' => 'momo_wallet',
                        'transId' => 'MOMO_TRANS_' . rand(10000000, 99999999),
                        'resultCode' => 1006,
                        'message' => 'Người dùng hủy giao dịch.',
                        'responseTime' => time(),
                        'payType' => 'qr',
                        'signature' => 'mock_signature'
                    ]) }}" class="btn btn-outline-secondary py-2 fw-semibold" style="border-radius: 10px;">
                        <i class="fas fa-times-circle me-2"></i> Hủy giao dịch (Thử nghiệm thất bại)
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

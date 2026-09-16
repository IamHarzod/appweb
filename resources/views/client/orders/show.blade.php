@extends('layout.home_layout')
@section('home-content')
<div class="container-fluid py-5">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h2 class="text-primary m-0"><i class="fas fa-file-invoice me-2"></i>Chi tiết đơn hàng #{{ $order->id }}</h2>
                <small class="text-muted">Ngày đặt: {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '---' }}</small>
            </div>
            <a href="{{ route('orders.my') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-1"></i> Danh sách đơn hàng
            </a>
        </div>

        <div class="row g-4">
            <!-- Thông tin người nhận và vận chuyển -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded h-100">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title m-0 text-primary"><i class="fas fa-user-check me-2"></i>Thông tin nhận hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="text-muted small d-block">Họ và tên:</span>
                            <strong class="fs-6">{{ $order->shipping_name }}</strong>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted small d-block">Số điện thoại:</span>
                            <strong>{{ $order->shipping_phone }}</strong>
                        </div>
                        @if($order->shipping_email)
                            <div class="mb-3">
                                <span class="text-muted small d-block">Email:</span>
                                <span>{{ $order->shipping_email }}</span>
                            </div>
                        @endif
                        <div class="mb-3">
                            <span class="text-muted small d-block">Địa chỉ giao hàng:</span>
                            <span>{{ $order->shipping_address }}</span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted small d-block">Phương thức thanh toán:</span>
                            <span class="badge bg-secondary px-2 py-1">{{ strtoupper($order->payment_method ?? 'COD') }}</span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted small d-block">Trạng thái đơn:</span>
                            @if($order->status == 'pending')
                                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">Chờ xử lý</span>
                            @elseif($order->status == 'completed' || $order->status == 'success')
                                <span class="badge bg-success px-3 py-1 rounded-pill">Hoàn thành</span>
                            @elseif($order->status == 'cancelled')
                                <span class="badge bg-danger px-3 py-1 rounded-pill">Đã hủy</span>
                            @else
                                <span class="badge bg-info px-3 py-1 rounded-pill">{{ ucfirst($order->status ?? 'Đang xử lý') }}</span>
                            @endif
                        </div>
                        @if($order->notes)
                            <div class="mb-0">
                                <span class="text-muted small d-block">Ghi chú:</span>
                                <em>{{ $order->notes }}</em>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title m-0 text-primary"><i class="fas fa-shopping-basket me-2"></i>Sản phẩm đã đặt</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 px-4">Sản phẩm</th>
                                        <th class="py-3 text-center">Đơn giá</th>
                                        <th class="py-3 text-center">Số lượng</th>
                                        <th class="py-3 px-4 text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $subtotal = 0; @endphp
                                    @foreach($order->orderItems as $item)
                                        @php
                                            $itemTotal = $item->price * $item->quantity;
                                            $subtotal += $itemTotal;
                                        @endphp
                                        <tr>
                                            <td class="py-3 px-4">
                                                <div class="d-flex align-items-center">
                                                    @if($item->product && $item->product->imageURL)
                                                        <img src="{{ asset('public/uploads/products/' . $item->product->imageURL) }}"
                                                             alt="{{ $item->product_name ?? ($item->product->name ?? 'SP') }}"
                                                             style="width: 50px; height: 50px; object-fit: cover;" class="rounded me-3 border">
                                                    @endif
                                                    <div>
                                                        <a href="{{ $item->product_id ? route('product.detail', $item->product_id) : '#' }}" class="fw-bold text-dark text-decoration-none">
                                                            {{ $item->product_name ?? ($item->product->name ?? 'Sản phẩm #' . $item->product_id) }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 text-center">{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                                            <td class="py-3 text-center">{{ $item->quantity }}</td>
                                            <td class="py-3 px-4 text-end fw-bold">{{ number_format($itemTotal, 0, ',', '.') }} VNĐ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính tiền hàng:</span>
                            <span class="fw-bold">{{ number_format($subtotal, 0, ',', '.') }} VNĐ</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Giảm giá:</span>
                                <span class="fw-bold">-{{ number_format($order->discount_amount, 0, ',', '.') }} VNĐ</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span>{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee, 0, ',', '.') . ' VNĐ' : 'Miễn phí' }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold">Tổng thanh toán:</span>
                            <span class="fs-4 fw-bold text-danger">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layout.home_layout')
@section('home-content')
<div class="container-fluid py-5">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h2 class="text-primary m-0"><i class="fas fa-file-invoice me-2"></i>Chi tiết đơn hàng #{{ $order->id }}</h2>
                <small class="text-muted">Ngày đặt: {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '---' }}</small>
            </div>
            <div>
                @if($order->status === 'pending')
                    <form action="{{ route('orders.user_cancel', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng #{{ $order->id }}? Số lượng sản phẩm sẽ được hoàn lại kho.');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger rounded-pill me-2">
                            <i class="fas fa-times-circle me-1"></i> Hủy đơn hàng
                        </button>
                    </form>
                @endif
                <a href="{{ route('orders.my') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i> Danh sách đơn hàng
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Order Stepper Timeline -->
        <div class="card border-0 shadow-sm rounded mb-4">
            <div class="card-body py-4 px-3 px-md-5">
                @if($order->status === 'cancelled')
                    <div class="alert alert-danger d-flex align-items-center mb-0 border-0 rounded-3 p-3" style="background-color: #fdf2f2; border-left: 5px solid #dc3545 !important;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 48px; height: 48px; background-color: #fde8e8; color: #dc3545;">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading mb-1 fw-bold text-danger">Đơn hàng đã bị hủy</h5>
                            <p class="mb-0 small text-secondary">{{ $order->notes ?? 'Đơn hàng này đã được hủy và hoàn trả tồn kho sản phẩm.' }}</p>
                        </div>
                    </div>
                @else
                    @php
                        $step = 1;
                        if ($order->status == 'processing') $step = 2;
                        elseif ($order->status == 'shipping') $step = 3;
                        elseif ($order->status == 'completed' || $order->status == 'success') $step = 4;
                    @endphp
                    <div class="order-stepper position-relative py-2">
                        <div class="position-absolute top-50 start-0 translate-middle-y w-100" style="height: 4px; background-color: #e9ecef; z-index: 1;">
                            <div class="bg-success h-100" style="width: {{ ($step - 1) * 33.33 }}%; transition: width 0.3s ease;"></div>
                        </div>
                        <div class="d-flex justify-content-between position-relative" style="z-index: 2;">
                            <!-- Step 1 -->
                            <div class="text-center bg-white px-2">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center {{ $step >= 1 ? 'bg-success text-white shadow-sm' : 'bg-light text-muted border' }}" style="width: 44px; height: 44px; font-size: 18px;">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div class="mt-2 fw-bold small {{ $step >= 1 ? 'text-success' : 'text-muted' }}">Đặt hàng thành công</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '' }}</div>
                            </div>
                            <!-- Step 2 -->
                            <div class="text-center bg-white px-2">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center {{ $step >= 2 ? 'bg-success text-white shadow-sm' : 'bg-light text-muted border' }}" style="width: 44px; height: 44px; font-size: 18px;">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <div class="mt-2 fw-bold small {{ $step >= 2 ? 'text-success' : 'text-muted' }}">Đã xác nhận</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $step >= 2 ? 'Đang đóng gói hàng' : 'Chờ xử lý' }}</div>
                            </div>
                            <!-- Step 3 -->
                            <div class="text-center bg-white px-2">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center {{ $step >= 3 ? 'bg-success text-white shadow-sm' : 'bg-light text-muted border' }}" style="width: 44px; height: 44px; font-size: 18px;">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <div class="mt-2 fw-bold small {{ $step >= 3 ? 'text-success' : 'text-muted' }}">Đang vận chuyển</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $order->ghn_order_code ? 'Bàn giao GHN' : ($step >= 3 ? 'Đang giao' : 'Chưa giao') }}</div>
                            </div>
                            <!-- Step 4 -->
                            <div class="text-center bg-white px-2">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center {{ $step >= 4 ? 'bg-success text-white shadow-sm' : 'bg-light text-muted border' }}" style="width: 44px; height: 44px; font-size: 18px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="mt-2 fw-bold small {{ $step >= 4 ? 'text-success' : 'text-muted' }}">Giao thành công</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $step >= 4 ? 'Đã hoàn tất' : 'Chờ nhận hàng' }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- GHN Tracking Box if available -->
        @if($order->ghn_order_code)
            <div class="card border-0 shadow-sm rounded mb-4 bg-light border-start border-4 border-warning">
                <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-dark rounded-circle p-3 me-3 d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-shipping-fast fa-lg text-dark"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Đơn vị vận chuyển: <strong class="text-primary">Giao Hàng Nhanh (GHN)</strong></div>
                            <div>Mã vận đơn: <span class="font-monospace fw-bold text-dark fs-5">{{ $order->ghn_order_code }}</span></div>
                            <div class="small text-muted">Trạng thái GHN: <span class="badge bg-secondary">{{ $order->shipping_status ?? 'ready_to_pick' }}</span></div>
                        </div>
                    </div>
                    <div>
                        <a href="https://tracking.ghn.vn/?order_code={{ $order->ghn_order_code }}" target="_blank" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm">
                            <i class="fas fa-external-link-alt me-1"></i> Tra cứu hành trình bưu kiện GHN
                        </a>
                    </div>
                </div>
            </div>
        @endif

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
                            @if($order->latitude && $order->longitude)
                                <div class="mt-2">
                                    <span class="badge bg-light text-primary border me-1">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $order->latitude }}, {{ $order->longitude }}
                                    </span>
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $order->latitude }},{{ $order->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0" style="font-size: 11px;">
                                        <i class="fas fa-external-link-alt me-1"></i> Xem bản đồ
                                    </a>
                                </div>
                            @endif
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

@extends('layout.home_layout')
@section('home-content')
<div class="container-fluid py-5">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h2 class="text-primary m-0"><i class="fas fa-box-open me-2"></i>Đơn hàng của tôi</h2>
            <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill">
                <i class="fas fa-arrow-left me-1"></i> Tiếp tục mua sắm
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if($orders->isEmpty())
            <div class="card border-0 shadow-sm rounded text-center py-5">
                <div class="card-body">
                    <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                    <h4>Bạn chưa có đơn hàng nào</h4>
                    <p class="text-muted">Hãy dạo quanh cửa hàng và chọn cho mình sản phẩm ưng ý nhé!</p>
                    <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4 mt-2">Khám phá sản phẩm</a>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm rounded">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3 px-3">Mã đơn</th>
                                    <th class="py-3">Ngày đặt</th>
                                    <th class="py-3">Sản phẩm đã đặt</th>
                                    <th class="py-3">Tổng tiền</th>
                                    <th class="py-3">Trạng thái</th>
                                    <th class="py-3 px-3 text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="py-3 px-3 font-monospace fw-bold text-primary">#{{ $order->id }}</td>
                                        <td class="py-3 text-muted small">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '---' }}</td>
                                        <td class="py-3">
                                            <div class="d-flex flex-column gap-1.5">
                                                @foreach($order->orderItems->take(2) as $it)
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($it->product && $it->product->imageURL)
                                                            <img src="{{ asset('uploads/products/' . $it->product->imageURL) }}"
                                                                 class="rounded border object-fit-cover shadow-2xs"
                                                                 style="width: 34px; height: 34px;">
                                                        @else
                                                            <div class="rounded border bg-light d-flex align-items-center justify-content-center"
                                                                 style="width: 34px; height: 34px;">
                                                                <i class="fas fa-box text-muted" style="font-size: 13px;"></i>
                                                            </div>
                                                        @endif
                                                        <span class="small fw-semibold text-truncate" style="max-width: 220px;" title="{{ $it->product_name ?? ($it->product->name ?? '') }}">
                                                            {{ $it->product_name ?? ($it->product->name ?? 'Sản phẩm #' . $it->product_id) }}
                                                        </span>
                                                        <span class="badge bg-light text-secondary border" style="font-size: 10px;">x{{ $it->quantity }}</span>
                                                    </div>
                                                @endforeach
                                                @if($order->orderItems->count() > 2)
                                                    <small class="text-primary fst-italic ps-1" style="font-size: 11px;">
                                                        +{{ $order->orderItems->count() - 2 }} sản phẩm khác...
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3 fw-bold text-danger">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                                        <td class="py-3">
                                            @if($order->status == 'pending')
                                                <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill">Chờ xử lý</span>
                                            @elseif(in_array($order->status, ['completed', 'success', 'delivered']) || $order->shipping_status === 'delivered')
                                                <span class="badge bg-success px-2.5 py-1.5 rounded-pill">Giao thành công</span>
                                            @elseif($order->status == 'cancelled' || $order->shipping_status === 'cancelled')
                                                <span class="badge bg-danger px-2.5 py-1.5 rounded-pill">Đã hủy</span>
                                            @elseif($order->status == 'shipping' || in_array($order->shipping_status, ['delivering', 'picked', 'storing', 'transporting', 'sorting']))
                                                <span class="badge bg-info px-2.5 py-1.5 rounded-pill">Đang giao hàng</span>
                                            @else
                                                <span class="badge bg-primary px-2.5 py-1.5 rounded-pill">{{ ucfirst($order->status ?? 'Đang xử lý') }}</span>
                                            @endif

                                            @if($order->ghn_order_code)
                                                <div class="mt-1">
                                                    <a href="https://tracking.ghn.vn/?order_code={{ $order->ghn_order_code }}" target="_blank" class="badge bg-light text-primary border text-decoration-none" title="Tra cứu đơn hàng GHN">
                                                        <i class="fas fa-shipping-fast text-warning me-1"></i>GHN: {{ $order->ghn_order_code }}
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-end">
                                            <div class="d-inline-flex gap-1 align-items-center">
                                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" title="Xem chi tiết đơn hàng">
                                                    <i class="fas fa-eye me-1"></i> Chi tiết
                                                </a>
                                                @if($order->status !== 'cancelled')
                                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-2.5 py-1 shadow-2xs" title="Đánh giá sản phẩm trong đơn này">
                                                        <i class="fas fa-star me-1"></i> Đánh giá
                                                    </a>
                                                @endif
                                                @if($order->status === 'pending')
                                                    <form action="{{ route('orders.user_cancel', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn hủy đơn #{{ $order->id }} không?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" title="Hủy đơn hàng">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

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
                                    <th class="py-3 px-4">Mã đơn</th>
                                    <th class="py-3">Ngày đặt</th>
                                    <th class="py-3">Người nhận</th>
                                    <th class="py-3">Số lượng SP</th>
                                    <th class="py-3">Tổng tiền</th>
                                    <th class="py-3">Trạng thái</th>
                                    <th class="py-3 px-4 text-end">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="py-3 px-4 font-monospace fw-bold text-primary">#{{ $order->id }}</td>
                                        <td class="py-3 text-muted">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '---' }}</td>
                                        <td class="py-3">
                                            <strong>{{ $order->shipping_name }}</strong>
                                            <div class="small text-muted">{{ $order->shipping_phone }}</div>
                                        </td>
                                        <td class="py-3">{{ $order->orderItems ? $order->orderItems->sum('quantity') : 0 }}</td>
                                        <td class="py-3 fw-bold text-danger">{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                                        <td class="py-3">
                                            @if($order->status == 'pending')
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Chờ xử lý</span>
                                            @elseif($order->status == 'completed' || $order->status == 'success')
                                                <span class="badge bg-success px-3 py-2 rounded-pill">Hoàn thành</span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge bg-danger px-3 py-2 rounded-pill">Đã hủy</span>
                                            @else
                                                <span class="badge bg-info px-3 py-2 rounded-pill">{{ ucfirst($order->status ?? 'Đang xử lý') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-end">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fas fa-eye me-1"></i> Xem
                                            </a>
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

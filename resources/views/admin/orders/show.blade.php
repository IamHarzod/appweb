@extends('layout.admin_layout')

@section('view-content')
    <div class="container-fluid">
        <h4 class="mb-3">Chi tiết đơn hàng #{{ $order->id }}</h4>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Thông tin đơn</div>
                    <div class="card-body">
                        <p><strong>Người dùng:</strong> {{ optional($order->user)->email ?? 'N/A' }}</p>
                        <p><strong>Số lượng SP:</strong> {{ $order->quantity }}</p>
                        <p><strong>Giá TB:</strong> {{ number_format($order->unitPrice,0,',','.') }} ₫</p>
                        <p><strong>Tổng tiền:</strong> <span class="text-primary fw-bold">{{ number_format($order->totalPrice,0,',','.') }} ₫</span></p>
                        <p><strong>Ngày tạo:</strong> {{ $order->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Sản phẩm</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên SP</th>
                                    <th>SL</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($order->oderItems as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ optional($item->product)->name ?? 'SP #' . $item->product_id }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->UnitPrice,0,',','.') }} ₫</td>
                                        <td class="fw-semibold">{{ number_format($item->totalPrice,0,',','.') }} ₫</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Không có sản phẩm.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">← Quay lại danh sách</a>
    </div>
@endsection

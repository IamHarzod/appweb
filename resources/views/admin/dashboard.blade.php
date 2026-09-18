@extends('layout.admin_layout')
@section('view-content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Xin chào, {{ Auth::user()->name }}!</h4>
                <p class="mb-0">Tổng quan hệ thống quản trị bán hàng</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard</a></li>
            </ol>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="stat-widget-two card-body">
                    <div class="stat-content">
                        <div class="stat-text">Tổng doanh thu hoàn tất</div>
                        <div class="stat-digit text-success">{{ number_format($totalRevenue, 0, ',', '.') }} đ</div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-success w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="stat-widget-two card-body">
                    <div class="stat-content">
                        <div class="stat-text">Tổng đơn hàng</div>
                        <div class="stat-digit text-primary">{{ number_format($totalOrders) }}</div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-primary w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="stat-widget-two card-body">
                    <div class="stat-content">
                        <div class="stat-text">Sản phẩm đang có</div>
                        <div class="stat-digit text-warning">{{ number_format($totalProducts) }}</div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-warning w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="stat-widget-two card-body">
                    <div class="stat-content">
                        <div class="stat-text">Khách hàng thành viên</div>
                        <div class="stat-digit text-info">{{ number_format($totalUsers) }}</div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-info w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Đơn hàng mới nhận</h4>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">Xem tất cả đơn hàng</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped verticle-middle table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Khách hàng</th>
                                    <th>Số điện thoại</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td><strong>#{{ $order->id }}</strong></td>
                                        <td>{{ $order->shipping_name ?? $order->user?->name ?? 'Khách vãng lai' }}</td>
                                        <td>{{ $order->shipping_phone ?? $order->user?->phoneNumber ?? '—' }}</td>
                                        <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }} đ</strong></td>
                                        <td>
                                            @if($order->status === 'completed')
                                                <span class="badge badge-success">Hoàn thành</span>
                                            @elseif($order->status === 'shipping')
                                                <span class="badge badge-info">Đang giao</span>
                                            @elseif($order->status === 'processing')
                                                <span class="badge badge-primary">Đang xử lý</span>
                                            @elseif($order->status === 'cancelled')
                                                <span class="badge badge-danger">Đã hủy</span>
                                            @else
                                                <span class="badge badge-warning">Chờ xử lý</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '—' }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                            Chưa có đơn hàng nào trong hệ thống.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
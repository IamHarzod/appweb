@extends('layout.admin_layout')
@section('view-content')
<div class="container-fluid">
    <h3 class="mb-3">Thống kê & báo cáo đơn hàng</h3>
    <p>Thống kê theo ngày đặt đơn. Doanh thu chỉ tính đơn hoàn tất hoặc giao thành công, không gồm đơn hủy.</p>
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <form method="GET" action="{{ route('admin.dashboard') }}" class="card card-body mb-4">
        <div class="row align-items-end">
            <div class="col-md-3"><label for="date_from">Từ ngày</label><input class="form-control" type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></div>
            <div class="col-md-3"><label for="date_to">Đến ngày</label><input class="form-control" type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></div>
            <div class="col-md-3"><label for="status">Trạng thái đơn</label><select class="form-control" id="status" name="status"><option value="">Tất cả</option>@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-3 mt-3"><button class="btn btn-primary">Áp dụng</button> <a href="{{ route('admin.dashboard') }}" class="btn btn-light">Xóa lọc</a></div>
        </div>
    </form>
    <div class="d-flex flex-wrap mb-3" style="gap:10px">
        <a class="btn btn-success" href="{{ route('admin.reports.export', array_merge($filters, ['format' => 'xlsx'])) }}">Xuất Excel (.xlsx)</a>
        <a class="btn btn-danger" href="{{ route('admin.reports.export', array_merge($filters, ['format' => 'pdf'])) }}">Xuất PDF</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.reviews.index') }}">Quản lý đánh giá</a>
    </div>
    <p class="text-muted">Báo cáo sử dụng bộ lọc đã áp dụng, gồm tất cả trang kết quả. PDF tối đa 1.000 đơn.</p>
    <div class="row">
        @foreach(['Doanh thu hoàn tất' => number_format($totalRevenue, 0, ',', '.').' đ', 'Đơn trong bộ lọc' => $totalOrders, 'Tổng sản phẩm' => $totalProducts, 'Tổng khách hàng' => $totalUsers] as $label => $value)
        <div class="col-xl-3 col-sm-6"><div class="card card-body"><p>{{ $label }}</p><h3>{{ $value }}</h3></div></div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-lg-8"><div class="card card-body"><h4>Doanh thu theo ngày đặt</h4><p class="text-muted">Tối đa 30 ngày có doanh thu gần nhất trong bộ lọc.</p>
            @forelse($daily as $day)
            <div class="mb-3"><div class="d-flex justify-content-between"><span>{{ \Carbon\Carbon::parse($day->day)->format('d/m/Y') }}</span><strong>{{ number_format($day->total, 0, ',', '.') }} đ</strong></div><div class="progress" style="height:12px"><div class="progress-bar bg-primary" style="width:{{ $daily->max('total') > 0 ? round($day->total / $daily->max('total') * 100, 2) : 0 }}%"></div></div></div>
            @empty<p>Chưa có doanh thu trong khoảng thời gian này.</p>@endforelse
        </div></div>
        <div class="col-lg-4"><div class="card card-body"><h4>Đơn theo trạng thái</h4>@forelse($statusCounts as $status => $count)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $statuses[$status] ?? $status }}</span><strong>{{ $count }}</strong></div>@empty<p>Chưa có đơn hàng.</p>@endforelse</div></div>
    </div>
    <div class="card card-body"><h4>Đơn hàng trong báo cáo</h4><div class="table-responsive"><table class="table"><thead><tr><th>Mã đơn</th><th>Ngày đặt</th><th>Khách hàng</th><th>Trạng thái</th><th>Tổng tiền</th></tr></thead><tbody>
    @forelse($recentOrders as $order)<tr><td><a href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->id }}</a></td><td>{{ $order->created_at?->format('d/m/Y H:i') }}</td><td>{{ $order->shipping_name }}</td><td>{{ $statuses[$order->status] ?? $order->status }}</td><td>{{ number_format($order->total_amount, 0, ',', '.') }} đ</td></tr>@empty<tr><td colspan="5">Không có đơn hàng phù hợp.</td></tr>@endforelse
    </tbody></table></div>{{ $recentOrders->links() }}</div>
</div>
@endsection

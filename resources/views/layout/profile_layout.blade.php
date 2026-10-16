@extends('layout.home_layout')
@section('home-content')
    <div class="container-fluid py-5">
        <div class="container py-4">
            <!-- Header tiêu đề -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-2">
                <div>
                    <h2 class="text-primary fw-bold m-0">
                        <i class="fas fa-user-circle me-2"></i>Tài khoản của tôi
                    </h2>
                    <p class="text-muted small mb-0">Quản lý thông tin cá nhân và theo dõi các đơn hàng bạn đã đặt.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('orders.my') }}" class="btn btn-outline-primary rounded-pill px-3 py-2">
                        <i class="fas fa-box-open me-1"></i> Tất cả đơn hàng đã đặt
                    </a>
                    <a href="{{ url('/logout-admin') }}" class="btn btn-outline-danger rounded-pill px-3 py-2">
                        <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <!-- Cột trái: Thông tin cá nhân -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-primary text-white text-center py-4 position-relative">
                            <div class="rounded-circle bg-white text-primary d-inline-flex align-items-center justify-content-center shadow mb-2" style="width: 76px; height: 76px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                            <h5 class="fw-bold text-white mb-0">{{ $client->name ?? 'Khách hàng' }}</h5>
                            <span class="badge bg-light text-primary rounded-pill px-3 py-1 mt-1 small">
                                {{ ucfirst($client->role ?? 'Thành viên') }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <h6 class="text-muted text-uppercase small fw-bold mb-3 border-bottom pb-2">Thông tin liên hệ</h6>
                            <div class="mb-3">
                                <span class="text-muted small d-block"><i class="fas fa-envelope text-primary me-2"></i>Email</span>
                                <strong class="text-dark">{{ $client->email }}</strong>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block"><i class="fas fa-phone text-primary me-2"></i>Số điện thoại</span>
                                <strong class="text-dark">{{ $client->phoneNumber ?? 'Chưa cập nhật' }}</strong>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted small d-block"><i class="fas fa-calendar-alt text-primary me-2"></i>Ngày gia nhập</span>
                                <span>{{ $client->created_at ? $client->created_at->format('d/m/Y') : 'Chưa cập nhật' }}</span>
                            </div>

                            <div class="d-grid gap-2 pt-2 border-top">
                                <a href="{{ route('password.request') }}" class="btn btn-light border rounded-pill py-2 text-start px-3 small">
                                    <i class="fas fa-key text-muted me-2"></i> Đổi mật khẩu
                                </a>
                                <a href="{{ route('orders.lookup') }}" class="btn btn-light border rounded-pill py-2 text-start px-3 small">
                                    <i class="fas fa-search-location text-muted me-2"></i> Tra cứu đơn hàng khác
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Danh sách đơn hàng đã đặt -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                            <h5 class="m-0 text-primary fw-bold">
                                <i class="fas fa-clipboard-list me-2"></i>Đơn hàng đã đặt gần đây
                            </h5>
                            @if(isset($orders) && $orders->isNotEmpty())
                                <a href="{{ route('orders.my') }}" class="text-primary small fw-bold text-decoration-none">
                                    Xem tất cả ({{ $orders->total() }}) <i class="fas fa-chevron-right ms-1"></i>
                                </a>
                            @endif
                        </div>

                        <div class="card-body p-0">
                            @if(!isset($orders) || $orders->isEmpty())
                                <div class="text-center py-5 px-3">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3 opacity-50"></i>
                                    <h5>Bạn chưa có đơn hàng nào</h5>
                                    <p class="text-muted small mb-3">Hãy dạo quanh cửa hàng và lựa chọn những sản phẩm công nghệ ưng ý nhất nhé!</p>
                                    <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4 py-2">
                                        <i class="fas fa-shopping-bag me-1"></i> Khám phá mua sắm ngay
                                    </a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="py-3 px-3">Mã đơn</th>
                                                <th class="py-3">Ngày đặt</th>
                                                <th class="py-3">Sản phẩm</th>
                                                <th class="py-3">Tổng tiền</th>
                                                <th class="py-3">Trạng thái</th>
                                                <th class="py-3 px-3 text-end">Chi tiết</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($orders as $ord)
                                                <tr>
                                                    <td class="py-3 px-3 font-monospace fw-bold text-primary">#{{ $ord->id }}</td>
                                                    <td class="py-3 text-muted small">{{ $ord->created_at ? $ord->created_at->format('d/m/Y') : '---' }}</td>
                                                    <td class="py-3">
                                                        <div class="d-flex flex-column gap-1">
                                                            @foreach($ord->orderItems->take(2) as $it)
                                                                <div class="d-flex align-items-center gap-2">
                                                                    @if($it->product && $it->product->imageURL)
                                                                        <img src="{{ asset('uploads/products/' . $it->product->imageURL) }}"
                                                                             class="rounded border object-fit-cover"
                                                                             style="width: 28px; height: 28px;">
                                                                    @endif
                                                                    <span class="small text-truncate" style="max-width: 170px;" title="{{ $it->product_name ?? ($it->product->name ?? '') }}">
                                                                        {{ $it->product_name ?? ($it->product->name ?? 'SP #' . $it->product_id) }}
                                                                    </span>
                                                                    <span class="badge bg-light text-muted border" style="font-size: 9px;">x{{ $it->quantity }}</span>
                                                                </div>
                                                            @endforeach
                                                            @if($ord->orderItems->count() > 2)
                                                                <small class="text-primary fst-italic ps-1" style="font-size: 10px;">
                                                                    +{{ $ord->orderItems->count() - 2 }} sản phẩm khác
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="py-3 fw-bold text-danger">{{ number_format($ord->total_amount, 0, ',', '.') }} đ</td>
                                                    <td class="py-3">
                                                        @if($ord->status == 'pending')
                                                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill small">Chờ xử lý</span>
                                                        @elseif(in_array($ord->status, ['completed', 'success', 'delivered']) || $ord->shipping_status === 'delivered')
                                                            <span class="badge bg-success px-2 py-1 rounded-pill small">Hoàn thành</span>
                                                        @elseif($ord->status == 'cancelled' || $ord->shipping_status === 'cancelled')
                                                            <span class="badge bg-danger px-2 py-1 rounded-pill small">Đã hủy</span>
                                                        @elseif($ord->status == 'shipping' || in_array($ord->shipping_status, ['delivering', 'picked', 'storing', 'transporting', 'sorting']))
                                                            <span class="badge bg-info px-2 py-1 rounded-pill small">Đang giao</span>
                                                        @else
                                                            <span class="badge bg-primary px-2 py-1 rounded-pill small">{{ ucfirst($ord->status ?? 'Đang xử lý') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-3 text-end">
                                                        <div class="d-inline-flex gap-1">
                                                            <a href="{{ route('orders.show', $ord->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" title="Xem chi tiết đơn hàng">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if($ord->status !== 'cancelled')
                                                                <a href="{{ route('orders.show', $ord->id) }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-2.5 py-1" title="Đánh giá sản phẩm">
                                                                    <i class="fas fa-star"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3 border-top text-center bg-light">
                                    <a href="{{ route('orders.my') }}" class="btn btn-sm btn-primary rounded-pill px-4">
                                        <i class="fas fa-box-open me-1"></i> Xem toàn bộ lịch sử đơn hàng đã đặt
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<!-- Single Page Header start -->

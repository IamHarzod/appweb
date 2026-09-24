@extends('layout.admin_layout')

@section('view-content')
<div class="container-fluid">
    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Breadcrumb & Title --}}
    <div class="row page-titles mx-0 mb-3 align-items-center">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="font-weight-bold mb-0 text-dark">
                    <i class="fa fa-shopping-bag mr-2 text-primary"></i>Quản lý đơn hàng
                </h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Quản trị</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Đơn hàng</a></li>
            </ol>
        </div>
    </div>

    {{-- Khung Lọc & Tìm Kiếm --}}
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.orders.index') }}" id="filterForm">
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                <div class="row align-items-center">
                    {{-- Tiêu đề & Thông tin phân trang --}}
                    <div class="col-md-3 col-12 mb-2 mb-md-0">
                        <span class="font-weight-bold text-dark" style="font-size: 16px;">Đơn hàng</span>
                        <span class="text-muted ml-2 small">
                            ({{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }} / {{ $orders->total() }})
                        </span>
                    </div>

                    {{-- Bộ điều khiển nhanh: Per Page, Trạng thái TT, Tìm kiếm, Xuất file --}}
                    <div class="col-md-9 col-12 d-flex flex-wrap align-items-center justify-content-md-end" style="gap: 8px;">
                        {{-- Hiển thị số bản ghi --}}
                        <div class="d-inline-flex align-items-center">
                            <select name="per_page" class="form-control form-control-sm" style="width: 110px;" onchange="this.form.submit()">
                                <option value="25" {{ ($filters['per_page'] ?? 25) == 25 ? 'selected' : '' }}>Hiển thị 25</option>
                                <option value="50" {{ ($filters['per_page'] ?? 25) == 50 ? 'selected' : '' }}>Hiển thị 50</option>
                                <option value="100" {{ ($filters['per_page'] ?? 25) == 100 ? 'selected' : '' }}>Hiển thị 100</option>
                            </select>
                        </div>

                        {{-- Lọc thanh toán --}}
                        <div class="d-inline-flex align-items-center">
                            <select name="payment_status" class="form-control form-control-sm" style="min-width: 140px;" onchange="this.form.submit()">
                                <option value="">Tất cả thanh toán</option>
                                @foreach ($paymentLabels as $val => $label)
                                    <option value="{{ $val }}" {{ ($filters['payment_status'] ?? '') == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ô tìm kiếm --}}
                        <div class="input-group input-group-sm" style="max-width: 280px;">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Mã đơn, khách hàng, SĐT, SP..."
                                   value="{{ $filters['search'] ?? '' }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                                @if(!empty($filters['search']))
                                    <a href="{{ route('admin.orders.index', ['tab' => $activeTab]) }}" class="btn btn-light" title="Xóa tìm kiếm">
                                        <i class="fa fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Nút Xuất file CSV --}}
                        <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
                           class="btn btn-sm btn-success text-white d-inline-flex align-items-center">
                            <i class="fa fa-download mr-1"></i> Xuất trang này
                        </a>

                        {{-- Nút toggle bộ lọc nâng cao --}}
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="collapse" data-target="#advancedFilterCollapse" aria-expanded="false">
                            <i class="fa fa-filter mr-1"></i> Bộ lọc nâng cao
                        </button>
                    </div>
                </div>

                {{-- Khối Bộ Lọc Nâng Cao (Collapse) --}}
                <div class="collapse {{ (request()->filled('date_from') || request()->filled('date_to') || request()->filled('gateway') || request()->filled('sort') || request()->filled('shipping_status')) ? 'show' : '' }} mt-3 pt-3 border-top" id="advancedFilterCollapse">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">Từ ngày:</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $filters['date_from'] ?? '' }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="small text-muted mb-1">Đến ngày:</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $filters['date_to'] ?? '' }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="small text-muted mb-1">Cổng thanh toán:</label>
                            <select name="gateway" class="form-control form-control-sm">
                                <option value="">Tất cả cổng</option>
                                <option value="cod" {{ ($filters['gateway'] ?? '') == 'cod' ? 'selected' : '' }}>COD</option>
                                <option value="momo" {{ ($filters['gateway'] ?? '') == 'momo' ? 'selected' : '' }}>MoMo</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="small text-muted mb-1">Trạng thái VC:</label>
                            <select name="shipping_status" class="form-control form-control-sm">
                                <option value="">Tất cả trạng thái VC</option>
                                @foreach ($shippingLabels as $key => $lbl)
                                    <option value="{{ $key }}" {{ ($filters['shipping_status'] ?? '') == $key ? 'selected' : '' }}>
                                        {{ $lbl }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="small text-muted mb-1">Sắp xếp theo:</label>
                            <select name="sort" class="form-control form-control-sm">
                                <option value="newest" {{ ($filters['sort'] ?? '') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="oldest" {{ ($filters['sort'] ?? '') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                                <option value="amount_desc" {{ ($filters['sort'] ?? '') == 'amount_desc' ? 'selected' : '' }}>Giá trị giảm dần</option>
                                <option value="amount_asc" {{ ($filters['sort'] ?? '') == 'amount_asc' ? 'selected' : '' }}>Giá trị tăng dần</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-2" style="gap: 6px;">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-danger">
                            <i class="fa fa-undo mr-1"></i> Đặt lại bộ lọc
                        </a>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa fa-check mr-1"></i> Áp dụng bộ lọc
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabs Trạng Thái Đơn Hàng (Theo tài liệu PDF Trang 1 & 6) --}}
    <div class="order-status-tabs mb-3 overflow-auto">
        <ul class="nav nav-pills flex-nowrap bg-white p-2 rounded shadow-sm border" style="min-width: max-content;">
            @php
                $tabColors = [
                    'all'        => 'primary',
                    'pending'    => 'secondary',
                    'ready'      => 'info',
                    'picking'    => 'info',
                    'delivering' => 'warning',
                    'delivered'  => 'success',
                    'return'     => 'dark',
                    'cancelled'  => 'danger',
                ];
            @endphp
            @foreach ($tabs as $key => $tab)
                @php
                    $isActive = ($activeTab === $key);
                    $btnColor = $tabColors[$key] ?? 'secondary';
                    $queryParams = array_merge(request()->query(), ['tab' => $key, 'page' => 1]);
                @endphp
                <li class="nav-item mr-2">
                    <a class="nav-link py-2 px-3 text-uppercase font-weight-bold {{ $isActive ? 'active bg-' . $btnColor . ' text-white' : 'text-dark bg-light' }}"
                       href="{{ route('admin.orders.index', $queryParams) }}"
                       style="border-radius: 6px; font-size: 13px; letter-spacing: 0.3px; transition: all 0.2s;">
                        {{ $tab['label'] }}
                        <span class="badge ml-1 {{ $isActive ? 'badge-light text-' . $btnColor : 'badge-' . $btnColor . ' text-white' }}">
                            {{ $tab['count'] }}
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Header thống kê nhỏ --}}
    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <div class="small text-muted font-weight-bold">
            <i class="fa fa-list-ul mr-1"></i> {{ $orders->total() }} đơn hàng trong danh sách
        </div>
        <div class="small text-muted">
            <i class="fa fa-truck mr-1 text-primary"></i> Trạng thái vận chuyển cập nhật theo hệ thống vận chuyển (GHN)
        </div>
    </div>

    {{-- Bảng Đơn Hàng --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="bg-light text-muted" style="border-top: none;">
                        <tr>
                            <th style="width: 30px;" class="text-center">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th style="min-width: 120px;">Mã đơn hàng</th>
                            <th style="min-width: 120px;">Ngày tạo đơn</th>
                            <th style="min-width: 220px;">Sản phẩm</th>
                            <th style="min-width: 110px;" class="text-right">Tổng tiền</th>
                            <th style="min-width: 90px;" class="text-right">COD cần thu</th>
                            <th style="min-width: 150px;">Tên khách hàng</th>
                            <th style="min-width: 110px;">Mã vận đơn</th>
                            <th style="min-width: 150px;">Trạng thái giao hàng</th>
                            <th style="min-width: 80px;" class="text-center">Đơn vị VC</th>
                            <th style="min-width: 120px;" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $isDelivering = in_array($order->shipping_status, ['delivering', 'picked', 'storing', 'transporting', 'sorting']);
                                $isCancelled = ($order->shipping_status === 'cancelled' || $order->status === 'cancelled');
                                $isDelivered = ($order->shipping_status === 'delivered' || $order->status === 'delivered');

                                // Trạng thái thanh toán
                                $payStatus = $order->payment_status ?? ($order->status === 'paid' ? 'paid' : 'pending');
                                $payBadgeClass = match($payStatus) {
                                    'paid' => 'badge-success',
                                    'failed' => 'badge-danger',
                                    'cancelled' => 'badge-secondary',
                                    'refunded' => 'badge-dark',
                                    default => 'badge-warning text-dark',
                                };
                                $payBadgeText = match($payStatus) {
                                    'paid' => 'ĐÃ THANH TOÁN',
                                    'failed' => 'TT THẤT BẠI',
                                    'cancelled' => 'ĐÃ HỦY TT',
                                    'refunded' => 'ĐÃ HOÀN TIỀN',
                                    default => 'CHỜ THANH TOÁN',
                                };

                                // Badge trạng thái giao hàng
                                $shipBadgeClass = match($order->shipping_status) {
                                    'delivered' => 'badge-success',
                                    'cancelled' => 'badge-danger',
                                    'delivering', 'transporting', 'sorting', 'storing', 'picked' => 'badge-warning text-dark',
                                    'ready_to_pick', 'picking' => 'badge-info',
                                    default => 'badge-secondary',
                                };
                                $shipLabelText = $shippingLabels[$order->shipping_status] ?? $order->shipping_status;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="order-check-item" value="{{ $order->id }}">
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="font-weight-bold text-primary">
                                        #DH{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <div>
                                        <span class="badge {{ $payBadgeClass }} font-weight-bold" style="font-size: 10px; padding: 3px 6px;">
                                            {{ $payBadgeText }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark">{{ $order->created_at?->format('d/m/Y') }}</div>
                                    <div class="small text-muted">{{ $order->created_at?->format('H:i') }}</div>
                                </td>
                                <td>
                                    @php
                                        $items = $order->items ?? $order->orderItems ?? collect();
                                    @endphp
                                    @if ($items->count() > 0)
                                        <div class="text-dark font-weight-500" style="line-height: 1.3;">
                                            {{ $items->first()->product_name ?? optional($items->first()->product)->name ?? 'Sản phẩm' }}
                                            <span class="text-muted">x {{ $items->first()->quantity }}</span>
                                        </div>
                                        @if ($items->count() > 1)
                                            <div class="small text-muted mt-1">
                                                <i class="fa fa-plus-circle mr-1"></i>+{{ $items->count() - 1 }} sản phẩm khác
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted italic">Không có sản phẩm</span>
                                    @endif
                                </td>
                                <td class="text-right font-weight-bold text-dark">
                                    {{ number_format($order->total_price ?: $order->total_amount, 0, ',', '.') }} đ
                                </td>
                                <td class="text-right font-weight-bold text-muted">
                                    {{ number_format($order->cod_amount ?: 0, 0, ',', '.') }} đ
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">
                                        {{ $order->shipping_name ?: $order->name ?: (optional($order->user)->name ?? 'Khách vãng lai') }}
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa fa-phone mr-1"></i>{{ $order->shipping_phone ?: $order->phone ?: '—' }}
                                    </div>
                                    @if ($order->latitude && $order->longitude)
                                        <div class="mt-1">
                                            <a href="{{ route('admin.orders.show', $order->id) }}#order-map-card" class="badge badge-light border text-danger" title="Khách có ghim định vị GPS: {{ $order->latitude }}, {{ $order->longitude }}">
                                                <i class="fa fa-map-marker-alt mr-1"></i>Đã ghim GPS
                                            </a>
                                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->latitude }},{{ $order->longitude }}" target="_blank" class="text-primary ml-1" title="Mở chỉ đường Google Maps">
                                                <i class="fa fa-directions"></i>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($order->ghn_order_code))
                                        <span class="badge badge-light border font-weight-bold text-primary px-2 py-1">
                                            {{ $order->ghn_order_code }}
                                        </span>
                                    @else
                                        <span class="small text-muted italic">Chưa có vận đơn</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Dropdown chuyển trạng thái giao hàng nhanh --}}
                                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline update-status-form">
                                        @csrf
                                        <select name="shipping_status" class="form-control form-control-sm font-weight-bold"
                                                style="border-radius: 4px; font-size: 12px; height: 32px;"
                                                onchange="confirmChangeStatus(this, '{{ $order->shipping_status }}')"
                                                {{ $isCancelled || $isDelivered ? 'disabled' : '' }}>
                                            @foreach ($shippingLabels as $statusKey => $statusName)
                                                <option value="{{ $statusKey }}" {{ $order->shipping_status === $statusKey ? 'selected' : '' }}>
                                                    {{ $statusName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="text-center font-weight-bold text-secondary">
                                    {{ $order->shipping_carrier ?? 'GHN' }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        {{-- Nút Xem chi tiết --}}
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info text-white" title="Xem chi tiết đơn hàng">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        {{-- Nút Hủy đơn: Tuân thủ quy tắc PDF "Nếu đang giao -> KHÔNG cho Hủy" --}}
                                        @if ($isCancelled)
                                            <button class="btn btn-light text-muted" disabled title="Đơn đã hủy">
                                                <i class="fa fa-ban"></i>
                                            </button>
                                        @elseif ($isDelivering)
                                            <button class="btn btn-secondary" disabled title="Đơn hàng đang giao, KHÔNG ĐƯỢC HỦY theo quy định!">
                                                <i class="fa fa-ban"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-danger"
                                                    onclick="confirmCancelOrder('{{ route('admin.orders.cancel', $order->id) }}', '{{ $order->id }}')"
                                                    title="Hủy đơn hàng">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="fa fa-inbox fa-3x mb-2 text-secondary d-block"></i>
                                    Không tìm thấy đơn hàng nào phù hợp với bộ lọc hiện tại.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap">
                <div class="text-muted small">
                    Đang xem từ {{ $orders->firstItem() ?? 0 }} đến {{ $orders->lastItem() ?? 0 }} trong tổng số {{ $orders->total() }} đơn
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form ẩn để submit Hủy đơn --}}
<form id="cancelOrderForm" method="POST" style="display: none;">
    @csrf
</form>

@endsection

@section('scripts')
<script>
    // Xử lý Checkbox All
    document.getElementById('checkAll')?.addEventListener('change', function () {
        document.querySelectorAll('.order-check-item').forEach(cb => cb.checked = this.checked);
    });

    // Xác nhận chuyển trạng thái giao hàng
    function confirmChangeStatus(selectElem, oldStatus) {
        const newStatus = selectElem.value;
        const deliveringStatuses = ['delivering', 'picked', 'storing', 'transporting', 'sorting'];

        // Kiểm tra chặn nếu đang giao mà chọn hủy
        if (newStatus === 'cancelled' && deliveringStatuses.includes(oldStatus)) {
            alert('CẢNH BÁO: Đơn hàng đang ở trạng thái vận chuyển / đang giao, KHÔNG THỂ HỦY đơn hàng!');
            selectElem.value = oldStatus;
            return false;
        }

        if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái đơn hàng sang: "' + selectElem.options[selectElem.selectedIndex].text + '"?')) {
            selectElem.form.submit();
        } else {
            selectElem.value = oldStatus;
        }
    }

    // Xác nhận hủy đơn hàng
    function confirmCancelOrder(url, orderId) {
        if (confirm('Bạn có chắc chắn muốn HỦY đơn hàng #' + orderId + ' không? Thao tác này sẽ cập nhật trạng thái đơn thành ĐÃ HỦY.')) {
            const form = document.getElementById('cancelOrderForm');
            form.action = url;
            form.submit();
        }
    }
</script>
@endsection

@extends('layout.admin_layout')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #admin_order_map {
        border-radius: 0 0 8px 8px;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.15);
    }
</style>
@endsection

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
            <div class="welcome-text d-flex align-items-center">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary mr-3">
                    <i class="fa fa-arrow-left mr-1"></i> Quay lại
                </a>
                <h4 class="font-weight-bold mb-0 text-dark">
                    Chi tiết đơn hàng #DH{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                </h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Quản trị</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">#{{ $order->id }}</a></li>
            </ol>
        </div>
    </div>

    @php
        $deliveringStatuses = ['delivering', 'picked', 'storing', 'transporting', 'sorting'];
        $isDelivering = in_array($order->shipping_status, $deliveringStatuses);
        $isCancelled = ($order->shipping_status === 'cancelled' || $order->status === 'cancelled');
        $isDelivered = ($order->shipping_status === 'delivered' || $order->status === 'delivered');

        $shippingLabels = [
            'pending' => 'Chờ tạo vận đơn',
            'not_shipped' => 'Chưa giao hàng',
            'processing' => 'Đang tạo vận đơn',
            'ready_to_pick' => 'Chờ lấy hàng',
            'picking' => 'Đang lấy hàng',
            'picked' => 'Đã lấy hàng',
            'storing' => 'Đang lưu kho',
            'transporting' => 'Đang trung chuyển',
            'sorting' => 'Đang phân loại',
            'delivering' => 'Đang giao hàng',
            'delivered' => 'Giao hàng thành công',
            'return' => 'Chờ hoàn hàng',
            'returning' => 'Đang hoàn hàng',
            'returned' => 'Đã hoàn hàng',
            'return_transporting' => 'Đang chuyển hoàn',
            'return_sorting' => 'Đang phân loại hoàn',
            'cancelled' => 'Đã hủy',
        ];
    @endphp

    <div class="row">
        {{-- Cột trái: Thông tin sản phẩm & Giao dịch --}}
        <div class="col-lg-8">
            {{-- Thẻ Danh sách sản phẩm --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-boxes mr-2 text-primary"></i> Danh sách sản phẩm đã đặt</span>
                    <span class="badge badge-light border">{{ $order->items->count() }} mặt hàng</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-right">Đơn giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $productSubtotal = 0; @endphp
                                @forelse ($order->items as $item)
                                    @php
                                        $itemTotal = $item->price * $item->quantity;
                                        $productSubtotal += $itemTotal;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark">
                                                {{ $item->product_name ?? optional($item->product)->name ?? ('Sản phẩm #' . $item->product_id) }}
                                            </div>
                                            @if ($item->product)
                                                <small class="text-muted">Mã SP: #{{ $item->product->id }}</small>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($item->price, 0, ',', '.') }} đ
                                        </td>
                                        <td class="text-center font-weight-bold">
                                            x {{ $item->quantity }}
                                        </td>
                                        <td class="text-right font-weight-bold text-primary">
                                            {{ number_format($itemTotal, 0, ',', '.') }} đ
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Không có dữ liệu sản phẩm.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bold text-muted">Tiền hàng:</td>
                                    <td class="text-right font-weight-bold">{{ number_format($productSubtotal, 0, ',', '.') }} đ</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bold text-muted">Phí vận chuyển:</td>
                                    <td class="text-right font-weight-bold text-dark">+{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }} đ</td>
                                </tr>
                                @if (($order->discount_amount ?? 0) > 0)
                                    <tr>
                                        <td colspan="3" class="text-right font-weight-bold text-success">Giảm giá voucher:</td>
                                        <td class="text-right font-weight-bold text-success">-{{ number_format($order->discount_amount, 0, ',', '.') }} đ</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bold text-danger" style="font-size: 16px;">TỔNG THANH TOÁN:</td>
                                    <td class="text-right font-weight-bold text-danger" style="font-size: 18px;">
                                        {{ number_format($order->total_price ?: $order->total_amount, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Thẻ Lịch sử giao dịch thanh toán --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white font-weight-bold">
                    <i class="fa fa-credit-card mr-2 text-info"></i> Lịch sử giao dịch thanh toán
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th>Cổng</th>
                                    <th>Mã GD</th>
                                    <th>Số tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order->paymentTransactions as $trans)
                                    <tr>
                                        <td class="text-uppercase font-weight-bold">{{ $trans->gateway }}</td>
                                        <td>{{ $trans->transaction_code ?: '—' }}</td>
                                        <td class="font-weight-bold">{{ number_format($trans->amount, 0, ',', '.') }} đ</td>
                                        <td>
                                            <span class="badge badge-{{ $trans->status === 'paid' ? 'success' : 'warning' }}">
                                                {{ $trans->status }}
                                            </span>
                                        </td>
                                        <td>{{ $trans->created_at?->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted small">
                                            Chưa có giao dịch trực tuyến ghi nhận. Phương thức đặt: <strong>{{ $order->payment_method ?? 'COD' }}</strong>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Thẻ Bản đồ vị trí giao hàng trực tiếp (GPS) --}}
            <div class="card shadow-sm border-0 mb-4" id="order-map-card">
                <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-map-marked-alt mr-2 text-danger"></i> Bản đồ vị trí giao hàng trực tiếp (GPS)</span>
                    @if ($order->latitude && $order->longitude)
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->latitude }},{{ $order->longitude }}"
                           target="_blank" class="btn btn-sm btn-primary text-white font-weight-bold shadow-sm">
                            <i class="fa fa-directions mr-1"></i> Mở Google Maps chỉ đường
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if ($order->latitude && $order->longitude)
                        <div id="admin_order_map" style="height: 360px; width: 100%; z-index: 1;"></div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fa fa-map-marker-alt fa-3x text-muted mb-3 d-block" style="opacity: 0.35;"></i>
                            <h6 class="font-weight-bold text-dark">Khách hàng chưa ghim tọa độ GPS</h6>
                            <p class="mb-3 small">Đơn hàng này được đặt không kèm tọa độ ghim trên bản đồ. Giao hàng theo địa chỉ văn bản:</p>
                            <div class="alert alert-light border d-inline-block text-dark font-weight-bold px-3 py-2 mb-3">
                                {{ $order->shipping_address ?: $order->address ?: 'Chưa cập nhật địa chỉ' }}
                            </div>
                            @if ($order->shipping_address || $order->address)
                                <div>
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($order->shipping_address ?: $order->address) }}"
                                       target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-search-location mr-1"></i> Tra cứu địa chỉ trên Google Maps
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                @if ($order->latitude && $order->longitude)
                    <div class="card-footer bg-light py-2 px-3 d-flex justify-content-between align-items-center flex-wrap">
                        <span class="small text-muted">
                            <i class="fa fa-crosshairs mr-1 text-primary"></i> Vị trí được khách hàng ghim trực tiếp khi đặt hàng.
                        </span>
                        <div class="small font-weight-bold text-dark">
                            <span>Vĩ độ (Lat): <strong class="text-primary font-monospace">{{ $order->latitude }}</strong></span> |
                            <span>Kinh độ (Lng): <strong class="text-primary font-monospace">{{ $order->longitude }}</strong></span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Cột phải: Xử lý trạng thái, Khách hàng & Vận chuyển --}}
        <div class="col-lg-4">
            {{-- Thao tác trạng thái đơn hàng --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white font-weight-bold">
                    <i class="fa fa-cogs mr-2 text-primary"></i> Quản lý trạng thái
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted">Cập nhật trạng thái giao hàng:</label>
                            <select name="shipping_status" class="form-control" {{ $isCancelled || $isDelivered ? 'disabled' : '' }}>
                                @foreach ($shippingLabels as $statusKey => $statusName)
                                    <option value="{{ $statusKey }}" {{ $order->shipping_status === $statusKey ? 'selected' : '' }}>
                                        {{ $statusName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" {{ $isCancelled || $isDelivered ? 'disabled' : '' }}>
                            <i class="fa fa-save mr-1"></i> Cập nhật trạng thái
                        </button>
                    </form>

                    <hr>

                    {{-- Nút Hủy đơn theo quy tắc --}}
                    @if ($isCancelled)
                        <div class="alert alert-danger py-2 mb-0 text-center font-weight-bold">
                            <i class="fa fa-ban mr-1"></i> Đơn hàng này đã bị hủy
                        </div>
                    @elseif ($isDelivering)
                        <div class="alert alert-warning py-2 mb-0 small">
                            <i class="fa fa-info-circle mr-1"></i>
                            Đơn hàng đang ở trạng thái vận chuyển (<strong>{{ $shippingLabels[$order->shipping_status] ?? $order->shipping_status }}</strong>). Theo quy định, <strong>KHÔNG CHO PHÉP HỦY</strong> đơn hàng khi đang giao.
                        </div>
                    @else
                        <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST"
                              onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-block">
                                <i class="fa fa-times-circle mr-1"></i> Hủy đơn hàng này
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Thông tin khách hàng & Giao nhận --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white font-weight-bold">
                    <i class="fa fa-user mr-2 text-primary"></i> Thông tin khách hàng
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Họ tên:</strong> {{ $order->shipping_name ?: $order->name ?: (optional($order->user)->name ?? 'Khách vãng lai') }}</p>
                    <p class="mb-1"><strong>Số điện thoại:</strong> {{ $order->shipping_phone ?: $order->phone ?: '—' }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $order->shipping_email ?: optional($order->user)->email ?: '—' }}</p>
                    <p class="mb-1"><strong>Địa chỉ giao:</strong> {{ $order->shipping_address ?: '—' }}</p>
                    <p class="mb-1"><strong>Tọa độ GPS:</strong>
                        @if ($order->latitude && $order->longitude)
                            <a href="#order-map-card" class="badge badge-success text-white" title="Cuộn tới bản đồ">
                                <i class="fa fa-map-marker-alt mr-1"></i> {{ $order->latitude }}, {{ $order->longitude }}
                            </a>
                        @else
                            <span class="badge badge-secondary">Chưa có tọa độ</span>
                        @endif
                    </p>
                    @if ($order->notes)
                        <div class="alert alert-light border mt-2 py-2 small mb-0">
                            <strong>Ghi chú đơn:</strong> {{ $order->notes }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Thông tin vận chuyển GHN --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white font-weight-bold">
                    <i class="fa fa-truck mr-2 text-primary"></i> Thông tin vận chuyển (GHN)
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Đơn vị VC:</strong> {{ $order->shipping_carrier ?? 'GHN' }}</p>
                    <p class="mb-1"><strong>Mã vận đơn:</strong>
                        @if ($order->ghn_order_code)
                            <a href="https://tracking.ghn.vn/?order_code={{ $order->ghn_order_code }}" target="_blank"
                               class="badge badge-primary px-2 py-1 font-weight-bold" title="Tra cứu trên GHN">
                                {{ $order->ghn_order_code }} <i class="fa fa-external-link-alt ml-1"></i>
                            </a>
                        @else
                            <span class="text-muted italic">Chưa phát hành mã</span>
                        @endif
                    </p>
                    <p class="mb-1"><strong>Cước phí GHN:</strong> {{ number_format($order->ghn_total_fee ?: $order->shipping_fee ?: 0, 0, ',', '.') }} đ</p>
                    <p class="mb-1"><strong>COD cần thu:</strong> {{ number_format($order->cod_amount ?: 0, 0, ',', '.') }} đ</p>
                    <p class="mb-2"><strong>Trạng thái:</strong>
                        <span class="badge badge-info">{{ $shippingLabels[$order->shipping_status] ?? $order->shipping_status }}</span>
                    </p>

                    @if (empty($order->ghn_order_code) && !$isCancelled && !$isDelivered)
                        <form action="{{ route('admin.orders.push_ghn', $order->id) }}" method="POST" class="mt-3 pt-2 border-top">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary btn-block">
                                <i class="fa fa-paper-plane mr-1"></i> Đẩy sang GHN (Tạo vận đơn)
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if ($order->latitude && $order->longitude)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var lat = {{ (float)$order->latitude }};
    var lng = {{ (float)$order->longitude }};
    
    var map = L.map('admin_order_map').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    var redIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    var marker = L.marker([lat, lng], { icon: redIcon }).addTo(map);
    var popupContent = `
        <div style="font-size: 13px; line-height: 1.5; min-width: 220px;">
            <div style="font-weight: 700; color: #dc3545; margin-bottom: 4px;">
                📍 Điểm giao hàng #{{ $order->id }}
            </div>
            <div><strong>Người nhận:</strong> {{ addslashes($order->shipping_name ?: $order->name ?: 'Khách hàng') }}</div>
            <div><strong>Số điện thoại:</strong> {{ addslashes($order->shipping_phone ?: $order->phone ?: '—') }}</div>
            <div><strong>Địa chỉ:</strong> {{ addslashes($order->shipping_address ?: '—') }}</div>
            <div style="margin-top: 8px; padding-top: 6px; border-top: 1px dashed #ddd;">
                <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank"
                   style="padding: 4px 10px; font-size: 12px; text-decoration: none; display: inline-block; border-radius: 4px; background-color: #007bff; color: #fff; font-weight: 600;">
                    🗺️ Mở Google Maps chỉ đường
                </a>
            </div>
        </div>
    `;
    marker.bindPopup(popupContent).openPopup();

    setTimeout(function() {
        map.invalidateSize();
    }, 350);
});
</script>
@endif
@endsection

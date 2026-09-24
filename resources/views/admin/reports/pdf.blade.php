<!doctype html><html lang="vi"><head><meta charset="utf-8"><style>
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color:#222; } h1 {font-size:20px} table {width:100%; border-collapse:collapse; table-layout:fixed} th,td {padding:7px; border:1px solid #ddd; word-wrap:break-word} th {background:#edf1f7} .money {text-align:right} tr {page-break-inside:avoid} thead {display:table-header-group}
</style></head><body>
<h1>36SHOP — Báo cáo đơn hàng</h1>
<p>Ngày xuất: {{ now()->format('d/m/Y H:i') }} | Từ: {{ $filters['date_from'] ?? 'Tất cả' }} | Đến: {{ $filters['date_to'] ?? 'Tất cả' }} | Trạng thái: {{ $statuses[$filters['status'] ?? ''] ?? 'Tất cả' }}</p>
<p>Số đơn: {{ $orders->count() }} | Tổng giá trị đơn: {{ number_format($orders->sum('total_amount'), 0, ',', '.') }} VND (bao gồm mọi trạng thái trong bộ lọc).</p>
<table><thead><tr><th style="width:6%">Mã</th><th style="width:13%">Ngày đặt</th><th style="width:21%">Khách hàng</th><th>Điện thoại</th><th>Trạng thái</th><th>Thanh toán</th><th>Giảm giá</th><th>Phí giao</th><th style="width:13%">Tổng (VND)</th></tr></thead><tbody>
@forelse($orders as $order)<tr><td>#{{ $order->id }}</td><td>{{ $order->created_at?->format('d/m/Y H:i') }}</td><td>{{ $order->shipping_name }}</td><td>{{ $order->shipping_phone }}</td><td>{{ $statuses[$order->status] ?? $order->status }}</td><td>{{ $order->payment_method }}</td><td class="money">{{ number_format($order->discount_amount) }}</td><td class="money">{{ number_format($order->shipping_fee) }}</td><td class="money">{{ number_format($order->total_amount) }}</td></tr>@empty<tr><td colspan="9">Không có đơn hàng phù hợp.</td></tr>@endforelse
</tbody></table></body></html>

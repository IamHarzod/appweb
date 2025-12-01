<div class="row">
    <div class="col-md-6">
        <h6><strong>Thông tin người nhận:</strong></h6>
        <p>Họ tên: {{ $order->shipping_name }}</p>
        <p>Email: {{ $order->shipping_email }}</p>
        <p>SĐT: {{ $order->shipping_phone }}</p>
        <p>Địa chỉ: {{ $order->shipping_address }}</p>
    </div>
    <div class="col-md-6">
        <h6><strong>Thông tin đơn hàng:</strong></h6>
        <p>Mã đơn: #{{ $order->id }}</p>
        <p>Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
        <p>Phương thức: {{ $order->payment_method }}</p>
        <p>Trạng thái:
            <span class="badge badge-info">{{ $order->status }}</span>
        </p>
    </div>
</div>

<hr>

<h6><strong>Danh sách sản phẩm:</strong></h6>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->orderItems as $item)
            <tr>
                <td>{{ $item->product_name ?? 'Sản phẩm ID: ' . $item->product_id }}</td>
                <td>{{ number_format($item->price) }} đ</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price * $item->quantity) }} đ</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-right"><strong>Phí ship:</strong></td>
            <td>{{ number_format($order->shipping_fee) }} đ</td>
        </tr>
        <tr>
            <td colspan="3" class="text-right"><strong>Giảm giá:</strong></td>
            <td>-{{ number_format($order->discount_amount) }} đ</td>
        </tr>
        <tr>
            <td colspan="3" class="text-right text-danger"><strong>TỔNG TIỀN:</strong></td>
            <td class="text-danger font-weight-bold">{{ number_format($order->total_amount) }} đ</td>
        </tr>
    </tfoot>
</table>

@if ($order->notes)
    <div class="alert alert-secondary mt-3">
        <strong>Ghi chú:</strong> {{ $order->notes }}
    </div>
@endif

@extends('layout.admin_layout')

@if (session('success'))
    <div class="alert alert-primary alert-dismissible alert-alt fade show">
        <button type="button" class="close h-100" data-dismiss="alert" aria-label="Close">
            <span><i class="mdi mdi-close"></i></span>
        </button>
        <strong>Success!</strong> {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible alert-alt fade show">
        <button type="button" class="close h-100" data-dismiss="alert" aria-label="Close">
            <span><i class="mdi mdi-close"></i></span>
        </button>
        <strong>Error!</strong> {{ session('error') }}
    </div>
@endif

@section('view-content')
    <div class="container-fluid">
        <h4 class="card-title mb-3">Danh sách đơn hàng</h4>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="myTable" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Người dùng</th>
                                <th>Số điện thoại</th>
                                <th>Tổng tiền</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ optional($order->user)->email ?? 'N/A' }}</td>
                                    <td>{{ $order->shipping_phone }}</td>
                                    <td class="text-primary fw-bold">{{ number_format($order->total_amount, 0, ',', '.') }}
                                        ₫
                                    </td>
                                    <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-primary dropdown-toggle"
                                                data-toggle="dropdown" aria-expanded="true">
                                                Thao tác
                                            </button>
                                            <div class="dropdown-menu">
                                                <button type="button" class="dropdown-item"
                                                    onclick="ShowOrderDetails('{{ route('admin.orders.detail', $order->id) }}')">
                                                    Chi tiết
                                                </button>
                                                <button type="button" class="dropdown-item text-danger btn-open-delete"
                                                    onclick="DeleteData('{{ route('admin.orders.destroy', $order->id) }}')">
                                                    Xoá
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
<div class="modal fade" id="modalOrderDetails" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết đơn hàng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="order-detail-content">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<script>
    function ShowOrderDetails(url) {
        // 1. Hiện modal lên trước và hiện icon loading
        $('#modalOrderDetails').modal('show');
        $('#order-detail-content').html(
            '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>'
        );

        // 2. Gọi Ajax lấy dữ liệu
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                // 3. Chèn HTML nhận được vào modal body
                $('#order-detail-content').html(response);
            },
            error: function(xhr) {
                console.log(xhr);
                $('#order-detail-content').html(
                    '<p class="text-danger text-center">Lỗi không tải được dữ liệu!</p>');
            }
        });
    }
</script>

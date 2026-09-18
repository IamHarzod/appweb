@extends('layout.admin_layout')

@section('view-content')
    <div class="container-fluid">
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

        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Quản lý đơn hàng</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Đơn hàng</a></li>
                </ol>
            </div>
        </div>

        <div class="card">
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
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->shipping_name ?? optional($order->user)->name ?? 'Khách vãng lai' }}</td>
                                    <td>{{ $order->shipping_phone ?? '—' }}</td>
                                    <td class="text-primary font-weight-bold">
                                        {{ number_format($order->total_amount, 0, ',', '.') }} đ
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm order-status-select" 
                                                data-url="{{ route('admin.orders.status', $order->id) }}"
                                                style="width: 140px; font-weight: bold; border-radius: 4px;
                                                color: {{ $order->status === 'completed' ? '#28a745' : ($order->status === 'cancelled' ? '#dc3545' : ($order->status === 'shipping' ? '#17a2b8' : '#ffc107')) }};">
                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                            <option value="shipping" {{ $order->status === 'shipping' ? 'selected' : '' }}>Đang giao</option>
                                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                        </select>
                                    </td>
                                    <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info"
                                            onclick="ShowOrderDetails('{{ route('admin.orders.detail', $order->id) }}')">
                                            <i class="fa fa-eye mr-1"></i>Chi tiết
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger ml-1"
                                            onclick="DeleteData('{{ route('admin.orders.destroy', $order->id) }}')">
                                            <i class="fa fa-trash mr-1"></i>Xoá
                                        </button>
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
                <div class="mt-3 d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chi tiết Đơn hàng -->
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
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Đang tải...</span>
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
            $('#modalOrderDetails').modal('show');
            $('#order-detail-content').html(
                '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Đang tải...</span></div></div>'
            );

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    $('#order-detail-content').html(response);
                },
                error: function(xhr) {
                    $('#order-detail-content').html(
                        '<p class="text-danger text-center py-3">Lỗi không thể tải dữ liệu đơn hàng!</p>'
                    );
                }
            });
        }

        $(document).on('change', '.order-status-select', function() {
            var select = $(this);
            var url = select.data('url');
            var newStatus = select.val();
            var token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    status: newStatus,
                    _token: token
                },
                success: function(res) {
                    toastr.success('Cập nhật trạng thái đơn hàng thành công!', 'Thành công');
                    if (newStatus === 'completed') select.css('color', '#28a745');
                    else if (newStatus === 'cancelled') select.css('color', '#dc3545');
                    else if (newStatus === 'shipping') select.css('color', '#17a2b8');
                    else select.css('color', '#ffc107');
                },
                error: function(xhr) {
                    toastr.error('Lỗi khi cập nhật trạng thái đơn hàng!', 'Lỗi');
                }
            });
        });
    </script>
@endsection

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
                            <th>Số lượng SP</th>
                            <th>Giá TB</th>
                            <th>Tổng tiền</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ optional($order->user)->email ?? 'N/A' }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>{{ number_format($order->unitPrice, 0, ',', '.') }} ₫</td>
                                <td class="text-primary fw-bold">{{ number_format($order->totalPrice, 0, ',', '.') }} ₫</td>
                                <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                            Thao tác
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('orders.show',$order->id) }}">Chi tiết</a>
                                            <form action="{{ route('admin.orders.destroy',$order->id) }}" method="POST" onsubmit="return confirm('Xóa đơn hàng #{{ $order->id }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Xoá</button>
                                            </form>
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

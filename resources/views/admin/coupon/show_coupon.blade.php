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
        <button type="button" class="btn btn-primary" onclick="OpenModal(null, '{{ url('/show-create-coupon') }}')">
            Thêm mã giảm giá
        </button>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Quản lý Mã Khuyến Mãi</h4>
                    </div>
                    @if (session('message'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('message') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="myTable" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã Code</th>
                                        <th>Loại mã</th>
                                        <th>Giá trị giảm</th>
                                        <th>Số lượng</th>
                                        <th>Hạn sử dụng</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($coupons as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>

                                            <td>
                                                <span class="badge badge-primary"
                                                    style="font-size: 14px;">{{ $item->code }}</span>
                                            </td>

                                            <td>
                                                @if ($item->type == 'percent')
                                                    <span class="text-info">Giảm theo %</span>
                                                @elseif($item->type == 'fixed')
                                                    <span class="text-success">Giảm tiền mặt</span>
                                                @else
                                                    <span class="text-warning font-weight-bold">Free Ship</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($item->type == 'percent')
                                                    {{ $item->value }}%
                                                @elseif($item->type == 'fixed')
                                                    {{ number_format($item->value, 0, ',', '.') }} VNĐ
                                                @elseif($item->type == 'free_ship')
                                                    {{ number_format($item->value, 0, ',', '.') }} đ
                                                @else
                                                    {{ number_format($item->value, 0, ',', '.') }}
                                                @endif
                                            </td>

                                            <td>{{ $item->quantity }}</td>

                                            <td>{{ date('d/m/Y', strtotime($item->expiry_date)) }}</td>

                                            <td>
                                                @if ($item->quantity <= 0)
                                                    <span class="badge badge-danger">Hết lượt dùng</span>
                                                @elseif(now() > $item->expiry_date)
                                                    <span class="badge badge-dark">Đã hết hạn</span>
                                                @else
                                                    <span class="badge badge-success">Đang hoạt động</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-toggle="dropdown" aria-expanded="true">
                                                        Thao tác
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <button class="dropdown-item" type="button"
                                                            onclick="OpenModal(null, '{{ url('/edit-coupon/' . $item->id) }}')">Sửa</button>
                                                        <button type="button"
                                                            class="dropdown-item text-danger btn-open-delete"
                                                            onclick="DeleteData('{{ url('/delete-product/' . $item->id) }}')">
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
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

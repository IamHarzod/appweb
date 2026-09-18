@extends('layout.admin_layout')
@section('view-content')
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

    @if (isset($keyword))
        <div class="alert alert-info d-flex align-items-center justify-content-between">
            <span>
                Kết quả tìm kiếm cho: <strong>"{{ $keyword }}"</strong>
                ({{ count($products) }} sản phẩm)
            </span>
            <a href="{{ url('/show-product') }}" class="btn btn-sm btn-outline-secondary">Xóa tìm kiếm</a>
        </div>

        @if (count($products) == 0)
            <div class="text-center py-5">
                <h4>Không tìm thấy sản phẩm nào!</h4>
                <p>Vui lòng thử lại với từ khóa khác.</p>
            </div>
        @endif
    @endif
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">Danh sách sản phẩm</h4>
            <button type="button" class="btn btn-primary" onclick="OpenModal(null, '{{ url('/show-create-product') }}')">
                <i class="fa fa-plus mr-1"></i> Thêm mới
            </button>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="myTable" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Giảm giá</th>
                                        <th>Thương hiệu</th>
                                        <th>Danh mục</th>
                                        <th>Kiểu</th>
                                        <th>Trạng thái</th>
                                        <th>Hoạt động</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <img src="{{ asset('public/uploads/products/' . $item->imageURL) }}"
                                                    width="50" height="50" class="rounded" alt="Ảnh sản phẩm">
                                            </td>
                                            <td><strong>{{ $item->name }}</strong></td>
                                            <td class="text-primary font-weight-bold">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                            <td>
                                                @if ($item->stockQuantity > 0)
                                                    <span class="badge badge-success">Còn hàng ({{ $item->stockQuantity }})</span>
                                                @else
                                                    <span class="badge badge-danger">Hết hàng</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->discountPercent ? $item->discountPercent . '%' : '0%' }}</td>
                                            <td>{{ $item->brand->TenThuongHieu ?? 'Chưa có' }}</td>
                                            <td>{{ $item->category?->name ?? 'Chưa phân loại' }}</td>
                                            <td>{{ $item->style }}</td>
                                            <td>
                                                @if ($item->Status)
                                                    <span class="badge badge-success">Đang kinh doanh</span>
                                                @else
                                                    <span class="badge badge-warning">Ngừng kinh doanh</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->IsActive)
                                                    <span class="badge badge-primary">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                                        data-toggle="dropdown" aria-expanded="true">
                                                        Thao tác
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <button class="dropdown-item" type="button"
                                                            onclick="OpenModal(null, '{{ url('/show-edit-product/' . $item->id) }}')">
                                                            <i class="fa fa-pencil mr-1"></i> Sửa
                                                        </button>
                                                        <button type="button"
                                                            class="dropdown-item text-danger btn-open-delete"
                                                            onclick="DeleteData('{{ url('/delete-product/' . $item->id) }}')">
                                                            <i class="fa fa-trash mr-1"></i> Xoá
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
    </div>

    {{-- @include('admin.product.add_product') --}}
@endsection

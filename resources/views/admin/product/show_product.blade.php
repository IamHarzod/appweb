@extends('layout.admin_layout')
@section('view-content')
    @if (isset($keyword))
        <div class="alert alert-info d-flex align-items-center justify-content-between">
            <span>
                Kết quả tìm kiếm cho: <strong>"{{ $keyword }}"</strong>
                ({{ count($products) }} sản phẩm)
            </span>
            <a href="{{ url('/shop') }}" class="btn btn-sm btn-outline-secondary">Xóa tìm kiếm</a>
        </div>

        @if (count($products) == 0)
            <div class="text-center py-5">
                <h4>Không tìm thấy sản phẩm nào!</h4>
                <p>Vui lòng thử lại với từ khóa khác.</p>
            </div>
        @endif
    @endif
    <div class="container-fluid">
        <button type="button" class="btn btn-primary" onclick="OpenModal(null, '{{ url('/show-create-product') }}')">Thêm
            mới</button>
        <div class="row">
            <div class="col-12">



                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Danh sách sản phẩm</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="myTable" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <!-- <th>Category ID</th> -->
                                        <th>Hình ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Giảm giá (%)</th>
                                        <th>Thương hiệu</th>
                                        <th>Danh mục</th>
                                        <th>Kiểu</th>
                                        <th>Mô tả</th>
                                        <th>Hoạt động</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            {{-- <td>{{$item->category_id}} </td> --}}
                                            <td>
                                                <img src="{{ asset('public/uploads/products/' . $item->imageURL) }}"
                                                    width="50" height="50" alt="Ảnh sản phẩm">
                                            </td>
                                            <td>{{ $item->name }} </td>
                                            <td>{{ $item->price }}</td>
                                            <td>{{ $item->stockQuantity > 0 ? 'Còn hàng (' . $item->stockQuantity . ')' : 'Hết hàng' }}
                                            </td>
                                            <td>{{ $item->discountPercent }}%</td>
                                            <td>{{ $item->brand->TenThuongHieu ?? 'Chưa có thương hiệu' }}</td>
                                            <td>{{ $item->category->name }}</td>
                                            <td>{{ $item->style }}</td>
                                            <td>{{ $item->description }} </td>
                                            <td>{{ $item->Status ? 'Đang kinh doan' : 'Ngừng kinh doanh' }}</td>
                                            <td>{{ $item->IsActive ? 'Active' : 'Inactive' }}</td>


                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-toggle="dropdown" aria-expanded="true">
                                                        Thao tác
                                                    </button>
                                                    <div class="dropdown-menu" x-placement="bottom-start"
                                                        style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                        <button class="dropdown-item" type="button"
                                                            onclick="OpenModal(null, '{{ url('/show-edit-product/' . $item->id) }}')">Sửa</button>
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
    </div>

    {{-- @include('admin.product.add_product') --}}
@endsection

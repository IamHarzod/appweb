@extends('layout.admin_layout')
@section('view-content')
    <div class="container-fluid">
        <button type="button" class="btn btn-primary" onclick="OpenModal('ModalCreateProduct')">Thêm mới</button>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Basic Datatable</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="myTable" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <!-- <th>Category ID</th> -->
                                    <th>Tên sản phẩm</th>
                                    <th>Hình ảnh</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Giảm giá (%)</th>
                                    <th>Dòng sản phẩm</th>
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
                                        <td>{{ $item->name }} </td>
                                        <td>
                                            <img src="{{ asset('public/uploads/products/' . $item->imageURL) }}"
                                                width="50" height="50" alt="Ảnh sản phẩm">
                                        </td>
                                        <td>{{ $item->price }}</td>
                                        <td>{{ $item->stockQuantity > 0 ? 'Còn hàng (' . $item->stockQuantity . ')' : 'Hết hàng' }}
                                        </td>
                                        <td>{{ $item->discountPercent }}%</td>
                                        <td>{{ $item->line }}</td>
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
                                                    <button type="button" class="dropdown-item text-danger btn-open-delete"
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

    @include('admin.product.add_product')
@endsection

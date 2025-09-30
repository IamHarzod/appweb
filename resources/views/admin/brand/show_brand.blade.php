@extends('layout.admin_layout')
{{-- Alert sau thao tác --}}
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
        <button type="button" class="btn btn-primary" onclick="OpenModal('ModalCreateBrand')">Thêm mới</button>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Basic Datatable</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="myTable" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Logo</th>
                                        <th>Tên thương hiệu</th>
                                        <th>Mô tả</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($brands as $item)
                                        <tr>
                                            <td>{{ $loop->iteration++ }}</td>
                                            <td>
                                                <img src="{{ asset('public/uploads/brands/' . $item->Logo) }}"
                                                    width="50" alt="">
                                            </td>
                                            <td>{{ $item->TenThuongHieu }}</td>
                                            <td>{{ $item->MoTa }}</td>
                                            <td>{{ $item->TrangThai ? 'Hiển thị' : 'Ẩn' }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-toggle="dropdown" aria-expanded="true">
                                                        Thao tác
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#">Sửa</a>

                                                        <button type="button"
                                                            class="dropdown-item text-danger btn-open-delete"
                                                            onclick="DeleteData('{{ url('/delete-brand/' . $item->id) }}')">
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

    @include('admin.brand.add_brand')
@endsection

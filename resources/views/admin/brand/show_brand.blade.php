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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">Danh sách thương hiệu</h4>
            <button type="button" class="btn btn-primary" onclick="OpenModal('ModalCreateBrand')">
                <i class="fa fa-plus mr-1"></i> Thêm mới
            </button>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Danh sách thương hiệu</h4>
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
                                                <img src="{{ asset('uploads/brands/' . $item->Logo) }}"
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
                                                        <button class="dropdown-item" type="button" href="#"
                                                            onclick="OpenModal(null, '{{ url('/show-edit-brand/' . $item->id) }}')">
                                                            Sửa
                                                        </button>

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

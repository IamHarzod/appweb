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
            <h4 class="card-title mb-0">Danh sách danh mục</h4>
            <button type="button" class="btn btn-primary" onclick="OpenModal('ModalCreateCategory')">
                <i class="fa fa-plus mr-1"></i> Thêm mới
            </button>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Danh sách danh mục</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="myTable" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Ảnh</th>
                                        <th>Tên danh mục</th>
                                        <th>Mô tả</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($item->ImageURL)
                                                    <img src="{{ asset('uploads/categories/' . $item->ImageURL) }}"
                                                        width="50" alt="">
                                                @else
                                                    <span class="text-muted">Không có ảnh</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-toggle="dropdown" aria-expanded="true">
                                                        Thao tác
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        {{-- Sửa (modal) --}}
                                                        <button class="dropdown-item" type="button"
                                                            onclick="OpenModal(null, '{{ route('category.show_edit_modal', $item->id) }}')">
                                                            Sửa
                                                        </button>

                                                        {{-- Xoá (AJAX giống Brand) --}}
                                                        <button type="button" class="dropdown-item text-danger btn-open-delete"
                                                            onclick="DeleteData('{{ url('/delete-category/' . $item->id) }}')">
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

    {{-- Modal thêm mới --}}
    @include('admin.category.add_category')
@endsection

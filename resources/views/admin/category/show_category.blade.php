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
        <button type="button" class="btn btn-primary" onclick="OpenModal('ModalCreateCategory')">Thêm mới</button>

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
                                                    <img src="{{ asset('public/uploads/categories/' . $item->ImageURL) }}"
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
<<<<<<< Updated upstream
                                                        {{-- Sửa --}}
                                                        <a class="dropdown-item"
                                                            href="{{ route('category.edit', $item->id) }}">
=======
                                                        {{-- Sửa (modal) --}}
                                                        <button class="dropdown-item" type="button"
                                                            onclick="OpenModal(null, '{{ route('category.show_edit_modal', $item->id) }}')">
>>>>>>> Stashed changes
                                                            Sửa
                                                        </button>

                                                        {{-- Xoá --}}
                                                        <form action="{{ route('delete-category', ['id' => $item->id]) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Bạn có chắc chắn muốn xoá không?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="dropdown-item text-danger">Xoá</button>
                                                        </form>
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

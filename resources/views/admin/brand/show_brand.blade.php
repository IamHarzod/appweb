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

                                                        {{-- form xoá ẩn --}}
                                                        <form id="delete-form-{{ $item->id }}"
                                                            action="{{ route('brand.destroy', $item->id) }}" method="POST"
                                                            class="d-none">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

                                                        {{-- nút xoá: chỉ bật notification --}}
                                                        <button type="button"
                                                            class="dropdown-item text-danger btn-open-delete"
                                                            data-id="{{ $item->id }}"
                                                            data-name="{{ $item->TenThuongHieu }}">
                                                            Xoá
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- xoá logo --}}
                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                            <script>
                                document.addEventListener('click', function(e) {
                                    const btn = e.target.closest('.btn-open-delete');
                                    if (!btn) return;

                                    const id = btn.dataset.id;
                                    const name = btn.dataset.name || '';

                                    // Đóng dropdown (nếu đang mở) cho gọn gàng
                                    const dd = btn.closest('.dropdown-menu');
                                    if (dd) {
                                        const toggle = dd.parentElement.querySelector('[data-toggle="dropdown"]');
                                        if (toggle) toggle.click();
                                    }

                                    Swal.fire({
                                        title: name ? `Xoá: ${name}` : 'Xác nhận xoá',
                                        text: 'Bạn có chắc muốn xoá thương hiệu này?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Confirm',
                                        cancelButtonText: 'Cancel',
                                        reverseButtons: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: true
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            const form = document.getElementById('delete-form-' + id);
                                            if (form) form.submit();
                                        }
                                        // else: Cancel -> không làm gì
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.brand.add_brand')
    {{-- Notification Confirm/Cancel --}}
    <div id="delete-notify" class="alert alert-primary notification d-none">
        <p class="notification-title mb-1">
            <strong>Success!</strong> <span id="notify-title">Xác nhận xoá</span>
        </p>
        <p class="mb-2" id="notify-desc">Bạn có chắc muốn xoá thương hiệu này?</p>
        <button id="btn-confirm-delete" class="btn btn-primary btn-sm rounded-0">Confirm</button>
        <button id="btn-cancel-delete" class="btn btn-link btn-sm">Cancel</button>
    </div>
@endsection

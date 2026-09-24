@extends('layout.admin_layout')
@section('view-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fa fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Quản lý tài khoản</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="myTable" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Điện thoại</th>
                                        <th>Role</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $u)
                                        <tr>
                                            <td>{{ $u->id }}</td>
                                            <td>{{ $u->name }}</td>
                                            <td>{{ $u->email }}</td>
                                            <td>{{ $u->phoneNumber }}</td>
                                            <td>
                                                <form action="{{ route('admin.users.role', $u->id) }}" method="POST" class="d-flex align-items-center">
                                                    @csrf
                                                    <select name="role" class="form-control mr-2" style="max-width: 180px;">
                                                        <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>User</option>
                                                        <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-primary">Gán quyền</button>
                                                </form>
                                            </td>
                                            <td>
                                                @if ($u->id === auth()->id())
                                                    <span class="badge badge-info">Đang đăng nhập</span>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="DeleteData('{{ route('admin.users.destroy', $u->id) }}')">
                                                        <i class="fa fa-trash"></i> Xoá
                                                    </button>
                                                @endif
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

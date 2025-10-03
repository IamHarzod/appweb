@extends('layout.admin_layout')
@section('view-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
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
                                                {{-- future actions here --}}
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

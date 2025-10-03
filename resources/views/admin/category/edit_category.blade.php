@extends('layout.admin_layout')

@section('view-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-8 col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Chỉnh sửa danh mục</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('category.update', ['id' => $category->id]) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>Ảnh danh mục</label>
                                @if (!empty($category->ImageURL))
                                    <div class="mb-2">
                                        <img src="{{ asset('public/uploads/categories/' . $category->ImageURL) }}" alt=""
                                            width="60">
                                    </div>
                                @endif
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-primary" type="button">Chọn tệp</button>
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" accept=".jpg, .png, .jpeg, .webp"
                                            id="ImageURL" name="ImageURL">
                                        <label class="custom-file-label">Choose file</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Tên danh mục</label>
                                <input type="text" name="name" value="{{ old('name', $category->name) }}"
                                    class="form-control" placeholder="Tên danh mục">
                            </div>

                            <div class="form-group">
                                <label>Mô tả</label>
                                <textarea class="form-control" name="description" rows="4" id="comment" style="height: 119px;">{{ old('description', $category->description) }}</textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ url('/show-category') }}" class="btn btn-secondary">Đóng</a>
                                <button type="submit" class="btn btn-primary">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

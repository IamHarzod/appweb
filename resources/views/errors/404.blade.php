@extends('layout.home_layout')

@section('home-content')
<div class="container-fluid py-5">
    <div class="container py-5 text-center">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <i class="bi bi-exclamation-triangle display-1 text-primary"></i>
                <h1 class="display-1">404</h1>
                <h1 class="mb-4">Không tìm thấy trang</h1>
                <p class="mb-4">Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển. Vui lòng quay lại trang chủ hoặc tìm kiếm sản phẩm khác.</p>
                <a class="btn btn-primary rounded-pill py-3 px-5" href="{{ route('home') }}">Trở về trang chủ</a>
            </div>
        </div>
    </div>
</div>
@endsection
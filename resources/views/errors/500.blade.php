@extends('layout.home_layout')

@section('home-content')
<div class="container-fluid py-5">
    <div class="container py-5 text-center">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <i class="bi bi-shield-exclamation display-1 text-danger"></i>
                <h1 class="display-1 text-danger">500</h1>
                <h1 class="mb-4">Lỗi máy chủ nội bộ</h1>
                <p class="mb-4">Hệ thống đang gặp sự cố tạm thời khi xử lý yêu cầu của bạn. Đội ngũ kỹ thuật đã ghi nhận và đang khắc phục.</p>
                <a class="btn btn-primary rounded-pill py-3 px-5" href="{{ route('home') }}">Trở về trang chủ</a>
            </div>
        </div>
    </div>
</div>
@endsection
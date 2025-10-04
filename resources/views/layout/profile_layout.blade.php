@extends('layout.home_layout')
@section('home-content')
    <div class="container-fluid contact py-5">
        <div class="container py-5">
            <div class="p-5 bg-light rounded">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block pb-2">Thông tin cá
                                nhân</h4>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <h1 class="display-5 mb-4 wow fadeInUp" data-wow-delay="0.3s">36Member Profile</h1>
                        <div class="row g-4 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="col-lg-12 col-xl-6">
                                <div class="form-floating">
                                    <p><strong>Họ và tên:</strong> {{ $client->name }}</p>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-6">
                                <div class="form-floating">
                                    <p><strong>Email:</strong> {{ $client->email }}</p>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-6">
                                <div class="form-floating">
                                    <p><strong>Số điện thoại:</strong> {{ $client->phoneNumber ?? 'Chưa cập nhật' }}</p>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-6">
                                <div class="form-floating">
                                    <p><strong>Địa chỉ:</strong> {{ $client->address ?? 'Chưa cập nhật' }}</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <p><strong>Ngày khởi tạo :</strong>{{ $client->create_at }} </p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Leave a message here" id="message" style="height: 160px"></textarea>
                                    <label for="message">Message</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3">Send Message</button>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="h-100 rounded">
                            <iframe class="rounded w-100" style="height: 100%;"
                                src="https://www.google.com/maps/place/Hanoi+University+of+Natural+Resources+and+Environment/@21.0470536,105.7598622,17z/data=!4m14!1m7!3m6!1s0x313454c3ce577141:0xb1a1ac92701777bc!2sHanoi+University+of+Natural+Resources+and+Environment!8m2!3d21.0470486!4d105.7624371!16s%2Fg%2F11b6dylw9c!3m5!1s0x313454c3ce577141:0xb1a1ac92701777bc!8m2!3d21.0470486!4d105.7624371!16s%2Fg%2F11b6dylw9c?hl=en&entry=ttu&g_ep=EgoyMDI1MTAwMS4wIKXMDSoASAFQAw%3D%3D"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<!-- Single Page Header start -->

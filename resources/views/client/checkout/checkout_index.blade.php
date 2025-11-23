@extends('layout.home_layout')
@section('home-content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Checkout Page</h1>
    </div>
    <!-- Single Page Header End -->

    <!-- Searvices Start -->
    <div class="container-fluid px-0">
        <div class="row g-0">
            <div class="col-6 col-md-4 col-lg-2 border-start border-end wow fadeInUp" data-wow-delay="0.1s">
                <div class="p-4">
                    <div class="d-inline-flex align-items-center">
                        <i class="fa fa-sync-alt fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Miễn phí hoàn trả !!</h6>
                            <p class="mb-0">Trong vòng 30 ngày sau khi mua hàng!</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.2s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fab fa-telegram-plane fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Miễn phí ship</h6>
                            <p class="mb-0">Miễn phí ship các đơn hàng của bạn !</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.3s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-life-ring fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Hỗ trợ 24/7</h6>
                            <p class="mb-0">Chúng tôi sẽ luôn hỗ trợ bạn trong 24h </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.4s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-credit-card fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Nhận về nhiều mã giảm giá !</h6>
                            <p class="mb-0">Có thể nhận mã giảm giá lên đến 50% cho mỗi đơn hàng áp dụng !</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.5s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lock fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Thanh toán bảo mật</h6>
                            <p class="mb-0">Chúc tôi đảm bảo bạn luôn được bảo mật</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="0.6s">
                <div class="p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-blog fa-2x text-primary"></i>
                        <div class="ms-4">
                            <h6 class="text-uppercase mb-2">Dịch vụ online</h6>
                            <p class="mb-0">Được hoàn trả hàng trong vòng 30 ngày !!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Searvices End -->


    <!-- Checkout Page Start -->
    <div class="container-fluid bg-light overflow-hidden py-5">
        <div class="container py-5">
            <h1 class="mb-4 wow fadeInUp" data-wow-delay="0.1s">Thông tin giao hàng</h1>
            <form action="#">
                <div class="row g-5">
                    <div class="col-md-12 col-lg-6 col-xl-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="form-item">
                            <label class="form-label my-3">Họ và tên<sup>*</sup></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Số điện thoại<sup>*</sup></label>
                            <input type="tel" class="form-control">
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Địa chỉ email<sup>*</sup></label>
                            <input type="email" class="form-control">
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Tỉnh / Thành phố<sup>*</sup></label>
                            <select name="tinh" id="tinh" class="form-select" title="Chọn tỉnh / thành phố">
                                <option value="0">--Chọn--</option>
                            </select>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Quận / Huyện<sup>*</sup></label>
                            <select name="quan" id="quan" class="form-select" title="Chọn quận / huyện">
                                <option value="0">--Chọn--</option>
                            </select>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Phường / Xã<sup>*</sup></label>
                            <select name="phuong" id="phuong" class="form-select" title="Chọn phường / xã">
                                <option value="0">--Chọn--</option>
                            </select>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Địa chỉ(số nhà, đường,...)<sup>*</sup></label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-item">
                            <textarea name="text" class="form-control mt-4" spellcheck="false" cols="30" rows="11"
                                placeholder="Ghi chú"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6 col-xl-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col" class="text-start">Tên sản phẩm</th>
                                        <th scope="col">Đơn giá</th>
                                        <th scope="col">Số lượng</th>
                                        <th scope="col">Tổng tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cartItems as $item)
                                        <tr class="text-center">
                                            <th scope="row" class="text-start py-4">
                                                {{ $item->product->name }}
                                            </th>
                                            <td class="py-4">
                                                {{ number_format($item->product->price, 0, ',', '.') }} VNĐ
                                            </td>
                                            <td class="py-4 text-center">{{ $item->quantity }}</td>
                                            <td class="py-4">
                                                {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                                                VNĐ
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th scope="row">
                                        </th>
                                        <td class="py-4"></td>
                                        <td class="py-4"></td>
                                        <td class="py-4">
                                            <p class="mb-0 text-dark py-2">Tạm tính</p>
                                        </td>
                                        <td class="py-4">
                                            <div class="py-2 text-center border-bottom border-top">
                                                <p class="mb-0 text-dark">{{ number_format($subtotal ?? 0, 0, ',', '.') }}
                                                    VNĐ</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"></th>
                                        <td class="py-4" colspan="2">
                                            <p class="mb-0 text-dark py-2">Phí vận chuyển</p>
                                        </td>
                                        <td class="py-4">
                                            <div class="py-2 text-center border-bottom">
                                                @if (isset($shippingFee) && $shippingFee == 0)
                                                    <p class="mb-0 text-success">Miễn phí</p>
                                                @else
                                                    <p class="mb-0 text-dark">
                                                        {{ number_format($shippingFee ?? 50000, 0, ',', '.') }} VNĐ</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th scope="row"></th>
                                        <td class="py-4" colspan="3">
                                            <div class="d-flex flex-column align-items-end">
                                                @if (Session::has('coupon'))
                                                    <div
                                                        class="d-flex justify-content-between w-100 mb-2 align-items-center">
                                                        <span class="text-success">
                                                            <i class="fa fa-tag"></i> Mã:
                                                            <strong>{{ Session::get('coupon')['code'] }}</strong>
                                                        </span>
                                                        <span class="text-success">
                                                            - {{ number_format($discountAmount ?? 0, 0, ',', '.') }} VNĐ
                                                        </span>
                                                    </div>
                                                    <a href="{{ route('remove_coupon') }}"
                                                        class="btn btn-sm btn-outline-danger w-100"
                                                        onclick="saveScrollPosition()">
                                                        Gỡ bỏ mã
                                                    </a>
                                                @else
                                                    <div class="input-group">
                                                        <input type="text" form="coupon-form" name="code_input"
                                                            class="form-control" placeholder="Nhập mã giảm giá">
                                                        <button onclick="saveScrollPosition()" class="btn btn-dark"
                                                            type="submit" form="coupon-form">Áp
                                                            dụng</button>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                        </th>
                                        <td class="py-4">
                                            <p class="mb-0 text-dark text-uppercase py-2">TỔNG CỘNG</p>
                                        </td>
                                        <td class="py-4"></td>
                                        <td class="py-4"></td>
                                        <td class="py-4">
                                            <div class="py-2 text-center border-bottom border-top">
                                                <p class="mb-0 text-dark">
                                                    {{ number_format($totalPrice ?? 0, 0, ',', '.') }} VNĐ</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                            <div class="col-12">
                                <h2 class="text-start mb-4">Phương thức thanh toán</h2>

                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input bg-primary border-0" id="payment_cod"
                                        name="payment_method" value="cod" checked>
                                    <label class="form-check-label" for="payment_cod">
                                        Thanh toán khi nhận hàng (COD)
                                    </label>
                                </div>

                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input bg-primary border-0" id="payment_vnpay"
                                        name="payment_method" value="vnpay">
                                    <label class="form-check-label d-flex align-items-center" for="payment_vnpay">
                                        <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Icon-VNPAY-QR.png"
                                            alt="VNPAY" style="height: 30px; margin-right: 10px; object-fit: contain;">
                                        Thanh toán qua VNPAY
                                    </label>
                                </div>

                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input bg-primary border-0" id="payment_momo"
                                        name="payment_method" value="momo">
                                    <label class="form-check-label d-flex align-items-center" for="payment_momo">
                                        <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png"
                                            alt="MoMo" style="height: 30px; margin-right: 10px; object-fit: contain;">
                                        Thanh toán qua Ví MoMo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                            <button type="submit"
                                class="btn btn-primary border-secondary py-3 px-4 text-uppercase w-100 text-white">
                                Đặt hàng ngay
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <form id="coupon-form" action="{{ route('check_coupon') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Lấy danh sách Tỉnh/Thành phố
            $.getJSON('https://esgoo.net/api-tinhthanh/1/0.htm', function(data_tinh) {
                if (data_tinh.error == 0) {
                    $.each(data_tinh.data, function(key_tinh, val_tinh) {
                        $("#tinh").append('<option value="' + val_tinh.id + '">' + val_tinh
                            .full_name + '</option>');
                    });

                    // Sự kiện khi chọn Tỉnh -> Load Quận
                    $("#tinh").change(function(e) {
                        var idtinh = $(this).val();
                        // Lấy danh sách Quận/Huyện
                        $.getJSON('https://esgoo.net/api-tinhthanh/2/' + idtinh + '.htm', function(
                            data_quan) {
                            if (data_quan.error == 0) {
                                $("#quan").html('<option value="0">Quận Huyện</option>');
                                $("#phuong").html('<option value="0">Phường Xã</option>');
                                $.each(data_quan.data, function(key_quan, val_quan) {
                                    $("#quan").append('<option value="' + val_quan
                                        .id + '">' + val_quan.full_name +
                                        '</option>');
                                });

                                // Sự kiện khi chọn Quận -> Load Phường
                                $("#quan").change(function(e) {
                                    var idquan = $(this).val();
                                    // Lấy danh sách Phường/Xã
                                    $.getJSON('https://esgoo.net/api-tinhthanh/3/' +
                                        idquan + '.htm',
                                        function(data_phuong) {
                                            if (data_phuong.error == 0) {
                                                $("#phuong").html(
                                                    '<option value="0">Phường Xã</option>'
                                                );
                                                $.each(data_phuong.data,
                                                    function(key_phuong,
                                                        val_phuong) {
                                                        $("#phuong").append(
                                                            '<option value="' +
                                                            val_phuong
                                                            .id + '">' +
                                                            val_phuong
                                                            .full_name +
                                                            '</option>');
                                                    });
                                            }
                                        });
                                });
                            }
                        });
                    });
                }
            });
        });
    </script>
@endsection
</body>

</html>

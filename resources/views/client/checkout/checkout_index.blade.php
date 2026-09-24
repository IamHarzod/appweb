@extends('layout.home_layout')
@section('home-content')
    <!-- Leaflet CSS & JS cho bản đồ định vị giao hàng -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Checkout Page</h1>
    </div>
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
    <div class="container-fluid bg-light overflow-hidden py-5">
        <div class="container py-5">
            <h1 class="mb-4 wow fadeInUp" data-wow-delay="0.1s">Thông tin giao hàng</h1>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('dathang') }}" method="POST">
                @csrf
                <div class="row g-5">
                    <div class="col-md-12 col-lg-6 col-xl-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="form-item">
                            <label class="form-label my-3">Họ và tên<sup>*</sup></label>
                            <input type="text" class="form-control" name="shipping_name"
                                value="{{ auth()->user()->name ?? '' }}" required>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Số điện thoại<sup>*</sup></label>
                            <input type="text" class="form-control" name="shipping_phone" required>
                        </div>
                        <div class="form-item">
                            <label class="form-label my-3">Địa chỉ email<sup>*</sup></label>
                            <input type="email" class="form-control" name="shipping_email"
                                value="{{ auth()->user()->email ?? '' }}" required>
                        </div>

                        <input type="hidden" name="to_district_id" id="to_district_id" value="">
                        <input type="hidden" name="to_ward_code" id="to_ward_code" value="">
                        <input type="hidden" name="tinh_thanh_name" id="tinh_thanh_name" value="">
                        <input type="hidden" name="quan_huyen_name" id="quan_huyen_name" value="">
                        <input type="hidden" name="phuong_xa_name" id="phuong_xa_name" value="">
                        <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="{{ $shippingFee ?? 50000 }}">
                        <input type="hidden" id="total_price_input" value="{{ $subtotal ?? 0 }}">
                        <input type="hidden" id="discount_amount_input" value="{{ $discountAmount ?? 0 }}">

                        <label class="form-label font-weight-bold text-dark text-uppercase mt-2 mb-1" style="font-size: 13px; letter-spacing: 0.3px;">
                            KHU VỰC NHẬN HÀNG (GIAO HÀNG NHANH - GHN) <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4 col-12">
                                <label class="form-label small text-muted mb-1">Tỉnh / Thành phố</label>
                                <select name="tinh_thanh" id="province_select" class="form-select" title="Chọn tỉnh / thành phố" required>
                                    <option value="">-- Chọn Tỉnh/Thành --</option>
                                    @if (isset($provinces) && count($provinces) > 0)
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province['ProvinceName'] }}" data-id="{{ $province['ProvinceID'] }}">
                                                {{ $province['ProvinceName'] }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label small text-muted mb-1">Quận / Huyện</label>
                                <select name="quan_huyen" id="district_select" class="form-select" title="Chọn quận / huyện" required disabled>
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label small text-muted mb-1">Phường / Xã</label>
                                <select name="phuong_xa" id="ward_select" class="form-select" title="Chọn phường / xã" required disabled>
                                    <option value="">-- Chọn Phường/Xã --</option>
                                </select>
                            </div>
                        </div>
                        </div>

                        <div class="form-item mb-3">
                            <label class="form-label font-weight-bold text-dark text-uppercase mb-1" style="font-size: 13px; letter-spacing: 0.3px;">
                                SỐ NHÀ, TÊN ĐƯỜNG CHI TIẾT <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="shipping_address" id="shipping_address_detail" required
                                placeholder="Ví dụ: Số 123 Đường Lê Duẩn, Căn hộ 402...">
                        </div>

                        {{-- Card Bản đồ Ghim Vị Trí Giao Hàng Chi Tiết --}}
                        <div class="card border mb-3 shadow-sm" style="border-radius: 12px; overflow: hidden; background-color: #f8fafc;">
                            <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                                <div class="font-weight-bold text-dark d-flex align-items-center" style="font-size: 14px;">
                                    <span class="mr-2" style="font-size: 16px;">📍</span>
                                    <span>Bản đồ vị trí giao hàng chi tiết (Ghim vị trí)</span>
                                </div>
                                <button type="button" id="btn_get_gps" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 d-inline-flex align-items-center" style="font-size: 12px; font-weight: 500;">
                                    <i class="fa fa-crosshairs mr-1"></i> Lấy vị trí GPS của tôi
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div id="delivery_map" style="height: 310px; width: 100%; z-index: 1;"></div>
                            </div>
                            <div class="card-footer bg-white py-2 px-3 d-flex justify-content-between align-items-center flex-wrap" style="font-size: 12px;">
                                <span class="text-muted">
                                    <i class="fa fa-info-circle mr-1 text-primary"></i> Click hoặc kéo ghim đỏ để chọn vị trí chính xác của bạn.
                                </span>
                                <span class="font-weight-bold text-dark">
                                    Tọa độ: <span id="coords_text" class="text-primary font-monospace">21.02850, 105.85420</span>
                                </span>
                            </div>
                        </div>

                        {{-- Hidden inputs lưu tọa độ --}}
                        <input type="hidden" name="latitude" id="latitude" value="21.028511">
                        <input type="hidden" name="longitude" id="longitude" value="105.854444">

                        <div class="form-item mb-3">
                            <label class="form-label font-weight-bold text-dark text-uppercase mb-1" style="font-size: 13px; letter-spacing: 0.3px;">
                                ĐỊA CHỈ GIAO HÀNG HOÀN CHỈNH <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="complete_address_preview" rows="2" readonly
                                style="background-color: #f8f9fa; font-weight: 500; font-size: 13.5px; line-height: 1.5;"
                                placeholder="Địa chỉ sẽ tự động tổng hợp từ thông tin bên trên..."></textarea>
                        </div>

                        <div class="form-item mb-3">
                            <label class="form-label text-muted small mb-1">Ghi chú đơn hàng (tuỳ chọn):</label>
                            <textarea name="ghichu" class="form-control" spellcheck="false" cols="30" rows="3"
                                placeholder="Ghi chú thêm cho người giao hàng..."></textarea>
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
                                        <td colspan="3" class="py-3 text-end fw-bold">Tạm tính:</td>
                                        <td class="py-3 text-center fw-bold">
                                            {{ number_format($subtotal ?? 0, 0, ',', '.') }} VNĐ
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="py-3 text-end">Phí vận chuyển (GHN):</td>
                                        <td class="py-3 text-center">
                                            <span id="shipping_fee_text" class="text-dark font-weight-bold">
                                                <span id="shipping-fee-display">{{ isset($shippingFee) && $shippingFee == 0 ? 'Miễn phí' : number_format($shippingFee ?? 50000, 0, ',', '.') . ' VNĐ' }}</span>
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="4" class="py-3">
                                            <div class="d-flex flex-column align-items-end">
                                                @if (Session::has('coupon'))
                                                    <div
                                                        class="d-flex justify-content-between w-100 mb-2 align-items-center">
                                                        <span class="text-success">
                                                            <i class="fa fa-tag"></i> Mã:
                                                            <strong>{{ Session::get('coupon')['code'] }}</strong>
                                                        </span>
                                                        <span class="text-success fw-bold">
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
                                        <td colspan="3" class="py-3 text-end text-uppercase fw-bold h5 mb-0 text-primary">
                                            TỔNG CỘNG:
                                        </td>
                                        <td class="py-3 text-center fw-bold h5 mb-0 text-primary border-top">
                                            <span id="final_total_text"><span id="total-price-display">{{ number_format($totalPrice ?? ($subtotal + ($shippingFee ?? 50000) - ($discountAmount ?? 0)), 0, ',', '.') }} VNĐ</span></span>
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
                                        name="payment_method" value="COD" checked>
                                    <label class="form-check-label" for="payment_cod">
                                        Thanh toán khi nhận hàng (COD)
                                    </label>
                                </div>

                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input bg-primary border-0" id="payment_vnpay"
                                        name="payment_method" value="VNPAY">
                                    <label class="form-check-label d-flex align-items-center" for="payment_vnpay">
                                        <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Icon-VNPAY-QR.png"
                                            alt="VNPAY" style="height: 30px; margin-right: 10px; object-fit: contain;">
                                        Thanh toán qua VNPAY
                                    </label>
                                </div>

                                <div class="form-check text-start my-3">
                                    <input type="radio" class="form-check-input bg-primary border-0" id="payment_momo"
                                        name="payment_method" value="MOMO">
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

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const provinceSelect = document.getElementById('province_select') || document.getElementById('tinh');
            const districtSelect = document.getElementById('district_select') || document.getElementById('quan');
            const wardSelect = document.getElementById('ward_select') || document.getElementById('phuong');
            const toDistrictInput = document.getElementById('to_district_id');
            const toWardInput = document.getElementById('to_ward_code');
            const tinhThanhName = document.getElementById('tinh_thanh_name');
            const quanHuyenName = document.getElementById('quan_huyen_name');
            const phuongXaName = document.getElementById('phuong_xa_name');
            const shippingFeeText = document.getElementById('shipping_fee_text') || document.getElementById('shipping-fee-display');
            const shippingFeeInput = document.getElementById('shipping_fee_input');
            const finalTotalText = document.getElementById('final_total_text') || document.getElementById('total-price-display');
            const totalPriceInput = document.getElementById('total_price_input');
            const discountAmountInput = document.getElementById('discount_amount_input');

            const subtotal = parseInt(totalPriceInput ? totalPriceInput.value : "{{ $subtotal ?? 0 }}") || 0;
            const discount = parseInt(discountAmountInput ? discountAmountInput.value : "{{ $discountAmount ?? 0 }}") || 0;

            function updateTotals(fee) {
                const formattedFee = new Intl.NumberFormat('vi-VN').format(fee) + ' VNĐ';
                if (shippingFeeText) {
                    shippingFeeText.innerText = fee === 0 ? 'Miễn phí' : formattedFee;
                }
                const feeDisplay = document.getElementById('shipping-fee-display');
                if (feeDisplay && feeDisplay !== shippingFeeText) {
                    feeDisplay.innerText = fee === 0 ? 'Miễn phí' : formattedFee;
                }
                if (shippingFeeInput) {
                    shippingFeeInput.value = fee;
                }
                const finalAmount = Math.max(0, subtotal + fee - discount);
                const formattedTotal = new Intl.NumberFormat('vi-VN').format(finalAmount) + ' VNĐ';
                if (finalTotalText) {
                    finalTotalText.innerText = formattedTotal;
                }
                const totalDisplay = document.getElementById('total-price-display');
                if (totalDisplay && totalDisplay !== finalTotalText) {
                    totalDisplay.innerText = formattedTotal;
                }
            }

            // Tải Tỉnh/Thành phố từ GHN nếu chưa render sẵn từ server
            if (provinceSelect && provinceSelect.options.length <= 1) {
                fetch("{{ route('ghn.provinces') }}")
                    .then(res => res.json())
                    .then(data => {
                        let provinces = Array.isArray(data) ? data : (data.data || []);
                        if (provinces.length > 0) {
                            let options = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                            provinces.forEach(p => {
                                options += `<option value="${p.ProvinceName}" data-id="${p.ProvinceID}" data-name="${p.ProvinceName}">${p.ProvinceName}</option>`;
                            });
                            provinceSelect.innerHTML = options;
                        }
                    })
                    .catch(err => console.error("Lỗi load tỉnh thành:", err));
            }

            // Khi chọn Tỉnh -> Tải Quận/Huyện
            if (provinceSelect) {
                provinceSelect.addEventListener('change', function () {
                    if (!districtSelect) return;
                    districtSelect.innerHTML = '<option value="">-- Đang tải Quận/Huyện... --</option>';
                    districtSelect.disabled = false;
                    if (wardSelect) {
                        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                        wardSelect.disabled = true;
                    }

                    const selectedOpt = this.options[this.selectedIndex];
                    const provinceId = selectedOpt ? selectedOpt.getAttribute('data-id') : null;
                    if (tinhThanhName) tinhThanhName.value = selectedOpt ? selectedOpt.text : '';

                    if (!provinceId) return;

                    fetch("{{ url('/ghn/districts') }}/" + provinceId)
                        .then(res => res.json())
                        .then(data => {
                            let districts = Array.isArray(data) ? data : (data.data || []);
                            if (districts.length > 0) {
                                let options = '<option value="">-- Chọn Quận/Huyện --</option>';
                                districts.forEach(d => {
                                    options += `<option value="${d.DistrictName}" data-id="${d.DistrictID}" data-name="${d.DistrictName}">${d.DistrictName}</option>`;
                                });
                                districtSelect.innerHTML = options;
                                districtSelect.disabled = false;
                            }
                        })
                        .catch(err => console.error("Lỗi load quận huyện:", err));
                });
            }

            // Khi chọn Quận -> Tải Phường/Xã
            if (districtSelect) {
                districtSelect.addEventListener('change', function () {
                    if (!wardSelect) return;
                    wardSelect.innerHTML = '<option value="">-- Đang tải Phường/Xã... --</option>';
                    wardSelect.disabled = false;

                    const selectedOpt = this.options[this.selectedIndex];
                    const districtId = selectedOpt ? selectedOpt.getAttribute('data-id') : null;
                    if (toDistrictInput && districtId) toDistrictInput.value = districtId;
                    if (quanHuyenName) quanHuyenName.value = selectedOpt ? selectedOpt.text : '';

                    if (!districtId) return;

                    fetch("{{ url('/ghn/wards') }}/" + districtId)
                        .then(res => res.json())
                        .then(data => {
                            let wards = Array.isArray(data) ? data : (data.data || []);
                            if (wards.length > 0) {
                                let options = '<option value="">-- Chọn Phường/Xã --</option>';
                                wards.forEach(w => {
                                    options += `<option value="${w.WardName}" data-code="${w.WardCode}" data-name="${w.WardName}">${w.WardName}</option>`;
                                });
                                wardSelect.innerHTML = options;
                                wardSelect.disabled = false;
                            }
                        })
                        .catch(err => console.error("Lỗi load phường xã:", err));
                });
            }

            // Tính cước phí giao hàng GHN
            function calculateShippingFee(districtId, wardCode) {
                if (!districtId || !wardCode) return;
                if (shippingFeeText) shippingFeeText.innerText = 'Đang tính cước...';

                fetch("{{ route('ghn.calculate_fee') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        to_district_id: districtId,
                        to_ward_code: wardCode,
                        subtotal: subtotal
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success && res.fee !== undefined) {
                        updateTotals(res.fee);
                    } else {
                        updateTotals(30000);
                    }
                })
                .catch(err => {
                    console.error("Lỗi tính phí:", err);
                    updateTotals(50000);
                });
            }

            // Khi chọn Phường/Xã -> Tính cước vận chuyển GHN
            if (wardSelect) {
                wardSelect.addEventListener('change', function () {
                    const selectedWardOpt = this.options[this.selectedIndex];
                    const wardCode = selectedWardOpt ? selectedWardOpt.getAttribute('data-code') : null;
                    const selectedDistrictOpt = districtSelect ? districtSelect.options[districtSelect.selectedIndex] : null;
                    const districtId = selectedDistrictOpt ? selectedDistrictOpt.getAttribute('data-id') : null;

                    if (toWardInput && wardCode) toWardInput.value = wardCode;
                    if (phuongXaName) phuongXaName.value = selectedWardOpt ? selectedWardOpt.text : '';

                    if (districtId && wardCode) {
                        calculateShippingFee(districtId, wardCode);
                    }
                });
            }

            // Leaflet Map & GPS
            const mapElement = document.getElementById('delivery_map');
            const btnGetGps = document.getElementById('btn_get_gps');
            const coordsText = document.getElementById('coords_text');
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const addressDetailInput = document.getElementById('shipping_address_detail');
            const completeAddressPreview = document.getElementById('complete_address_preview');

            let defaultLat = 21.028511;
            let defaultLng = 105.854444;

            function updateCompleteAddress() {
                if (!completeAddressPreview) return;
                const parts = [];

                const house = addressDetailInput ? addressDetailInput.value.trim() : '';
                if (house) parts.push(house);

                const wardText = phuongXaName?.value?.trim() || (wardSelect && wardSelect.selectedIndex > 0 ? wardSelect.options[wardSelect.selectedIndex].text : '');
                if (wardText && !wardText.includes('--')) parts.push(wardText);

                const districtText = quanHuyenName?.value?.trim() || (districtSelect && districtSelect.selectedIndex > 0 ? districtSelect.options[districtSelect.selectedIndex].text : '');
                if (districtText && !districtText.includes('--')) parts.push(districtText);

                const provinceText = tinhThanhName?.value?.trim() || (provinceSelect && provinceSelect.selectedIndex > 0 ? provinceSelect.options[provinceSelect.selectedIndex].text : '');
                if (provinceText && !provinceText.includes('--')) parts.push(provinceText);

                completeAddressPreview.value = parts.length > 0 ? parts.join(', ') : '';
            }

            if (addressDetailInput) {
                addressDetailInput.addEventListener('input', updateCompleteAddress);
            }
            if (provinceSelect) {
                provinceSelect.addEventListener('change', () => setTimeout(updateCompleteAddress, 100));
            }
            if (districtSelect) {
                districtSelect.addEventListener('change', () => setTimeout(updateCompleteAddress, 100));
            }
            if (wardSelect) {
                wardSelect.addEventListener('change', () => setTimeout(updateCompleteAddress, 100));
            }

            if (mapElement && typeof L !== 'undefined') {
                const map = L.map('delivery_map', {
                    center: [defaultLat, defaultLng],
                    zoom: 14,
                    zoomControl: true,
                });

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                const redIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                const marker = L.marker([defaultLat, defaultLng], {
                    draggable: true,
                    icon: redIcon
                }).addTo(map);

                function setCoordinates(lat, lng) {
                    if (latInput) latInput.value = parseFloat(lat).toFixed(6);
                    if (lngInput) lngInput.value = parseFloat(lng).toFixed(6);
                    if (coordsText) coordsText.innerText = parseFloat(lat).toFixed(5) + ', ' + parseFloat(lng).toFixed(5);
                }

                setCoordinates(defaultLat, defaultLng);

                marker.on('dragend', function () {
                    const pos = marker.getLatLng();
                    setCoordinates(pos.lat, pos.lng);
                });

                map.on('click', function (e) {
                    marker.setLatLng(e.latlng);
                    setCoordinates(e.latlng.lat, e.latlng.lng);
                });

                if (btnGetGps) {
                    btnGetGps.addEventListener('click', function () {
                        if (!navigator.geolocation) {
                            alert('Trình duyệt của bạn không hỗ trợ GPS.');
                            return;
                        }

                        btnGetGps.disabled = true;
                        btnGetGps.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Đang định vị...';

                        navigator.geolocation.getCurrentPosition(
                            function (position) {
                                const userLat = position.coords.latitude;
                                const userLng = position.coords.longitude;
                                map.flyTo([userLat, userLng], 16, { animate: true });
                                marker.setLatLng([userLat, userLng]);
                                setCoordinates(userLat, userLng);
                                btnGetGps.disabled = false;
                                btnGetGps.innerHTML = '<i class="fa fa-crosshairs mr-1"></i> Lấy vị trí GPS của tôi';
                            },
                            function (error) {
                                btnGetGps.disabled = false;
                                btnGetGps.innerHTML = '<i class="fa fa-crosshairs mr-1"></i> Lấy vị trí GPS của tôi';
                                alert('Không thể lấy vị trí GPS từ thiết bị.');
                            },
                            { enableHighAccuracy: true, timeout: 10000 }
                        );
                    });
                }
                setTimeout(() => { map.invalidateSize(); }, 500);
            }
        });
    </script>elect.options[districtSelect.selectedIndex].text : '');
                if (districtText && !districtText.includes('--')) parts.push(districtText);

                const provinceText = tinhThanhName?.value?.trim() || (provinceSelect && provinceSelect.selectedIndex > 0 ? provinceSelect.options[provinceSelect.selectedIndex].text : '');
                if (provinceText && !provinceText.includes('--')) parts.push(provinceText);

                completeAddressPreview.value = parts.length > 0 ? parts.join(', ') : '';
            }

            if (addressDetailInput) {
                addressDetailInput.addEventListener('input', updateCompleteAddress);
            }
            if (provinceSelect) {
                provinceSelect.addEventListener('change', () => setTimeout(updateCompleteAddress, 100));
            }
            if (districtSelect) {
                districtSelect.addEventListener('change', () => setTimeout(updateCompleteAddress, 100));
            }
            if (wardSelect) {
                wardSelect.addEventListener('change', () => setTimeout(updateCompleteAddress, 100));
>>>>>>> ab21131689b3ae97f2f43842b98d7019802230a8
            }
        });
    </script>
@endsection

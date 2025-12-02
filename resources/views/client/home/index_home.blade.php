@extends('layout.home_layout')
@section('home-content')
    <style>
        /* Quy định chiều cao chung cho cả slide và banner phụ */
        :root {
            --slide-height: 450px;
            /* <--- CHỈNH ĐỘ CAO MONG MUỐN TẠI ĐÂY */
        }

        .header-carousel .carousel-img img {
            height: var(--slide-height) !important;
            object-fit: contain;
            width: auto !important;
            max-width: 100%;
        }

        /* Banner Vision Pro bên phải */
        .carousel-header-banner {
            height: var(--slide-height) !important;
        }

        .carousel-header-banner img {
            height: 100% !important;
            object-fit: cover;
        }

        /* Căn chỉnh lại nội dung chữ cho cân đối */
        .carousel-content {
            padding: 1rem !important;
            /* Giảm padding của chữ */
        }

        .carousel-content h1.display-3 {
            font-size: 2.5rem;
            /* Giảm cỡ chữ tiêu đề */
        }
    </style>
    <!-- Carousel Start -->
    <div class="container-fluid carousel bg-light px-0">
        <div class="row g-0 justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="header-carousel owl-carousel bg-light">

                    <div class="row g-0 header-carousel-item align-items-center">
                        <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                            <img src="{{ asset('public/client/img/macbook-banner.png') }}" class="img-fluid" alt="Image">
                        </div>
                        <div class="col-xl-6 carousel-content p-4">
                            <h4 class="text-uppercase fw-bold mb-3 wow fadeInRight" data-wow-delay="0.1s"
                                style="letter-spacing: 3px; font-size: 1rem;">
                                Phụ kiện giảm tới 50%
                            </h4>
                            <h1 class="display-3 text-capitalize mb-3 wow fadeInRight" data-wow-delay="0.3s">
                                Thu cũ đổi mới <br> Lên đời giá tốt
                            </h1>
                            <p class="text-dark wow fadeInRight mb-3" data-wow-delay="0.5s">
                                Thu cũ lên đời | Trợ giá lên đến 3 triệu
                            </p>
                            <a class="btn btn-primary rounded-pill py-2 px-4 wow fadeInRight" data-wow-delay="0.7s"
                                href="#">Shop Now</a>
                        </div>
                    </div>

                    <div class="row g-0 header-carousel-item align-items-center">
                        <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                            <img src="{{ asset('public/client/img/Mac-studio-banner.png') }}" class="img-fluid"
                                alt="Image">
                        </div>
                        <div class="col-xl-6 carousel-content p-4">
                            <h4 class="text-uppercase fw-bold mb-3 wow fadeInRight" data-wow-delay="0.1s"
                                style="letter-spacing: 3px; font-size: 1rem;">
                                Ưu đã tới 50%
                            </h4>
                            <h1 class="display-3 text-capitalize mb-3 wow fadeInRight" data-wow-delay="0.3s">
                                Giải pháp đột phá <br> Ưu đãi doanh nghiệp
                            </h1>
                            <p class="text-dark wow fadeInRight mb-3" data-wow-delay="0.5s">
                                Trả góp 0% lên đến 12 tháng
                            </p>
                            <a class="btn btn-primary rounded-pill py-2 px-4 wow fadeInRight" data-wow-delay="0.7s"
                                href="#">Mua ngay</a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-12 col-lg-4 wow fadeInRight" data-wow-delay="0.1s">
                <div class="carousel-header-banner position-relative"
                    style="height: var(--slide-height) !important; overflow: hidden; background: #000;">

                    <img src="{{ asset('public/client/img/Vision-Pro-Banner.jpg') }}" class="img-fluid w-100 h-100"
                        style="object-fit: cover; object-position: center;" alt="Image">

                    <div class="carousel-banner-offer" style="position: absolute; top: 20px; right: 20px; z-index: 2;">
                        <p class="bg-primary text-white rounded fs-6 py-1 px-3 mb-0 d-inline-block">Giảm 10%</p>
                        <p class="text-primary fs-6 fw-bold mb-0 text-end mt-1">Hot Deal</p>
                    </div>

                    <div class="carousel-banner"
                        style="position: absolute; bottom: 0; left: 0; width: 100%; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); padding: 20px;">
                        <div class="carousel-banner-content text-start">
                            <h3 class="text-white fw-bold mb-1">Vision Pro</h3>
                            <p class="text-warning fs-4 fw-bold mb-0">85.399.000đ</p>
                            <del class="text-white-50 fs-6">86.990.000đ</del>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Carousel End -->

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

    <!-- Products Offer Start -->
    <div class="container-fluid bg-light py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.2s">
                    <a href="#"
                        class="d-flex align-items-center justify-content-between border bg-white rounded p-4">
                        <div>
                            <p class="text-muted mb-3">Khám phá máy ảnh theo phong cách của bạn !</p>
                            <h3 class="text-primary">Camera </h3>
                            <h1 class="display-3 text-secondary mb-0">40% <span class="text-primary fw-normal">Off</span>
                            </h1>
                        </div>
                        <img src="{{ asset('public/client/img/product-1.png') }}" class="img-fluid" alt="">
                    </a>
                </div>
                <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                    <a href="#"
                        class="d-flex align-items-center justify-content-between border bg-white rounded p-4">
                        <div>
                            <p class="text-muted mb-3">Phụ kiện đeo tay ư ? Không thể thiếu !</p>
                            <h3 class="text-primary">Smart Watch</h3>
                            <h1 class="display-3 text-secondary mb-0">20% <span class="text-primary fw-normal">Off</span>
                            </h1>
                        </div>
                        <img src="{{ asset('public/client/img/product-2.png') }}" class="img-fluid" alt="">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Products Offer End -->


    <!-- Our Products Start -->
    <div id="san-pham-moi" class="container-fluid product py-5">
        <div class="container py-5">
            <div class="tab-class">
                <div class="row g-4">
                    <div class="col-lg-4 text-start wow fadeInLeft" data-wow-delay="0.1s">
                        <h2>Sản phẩm của chúng tôi</h2>
                    </div>
                    <div class="col-lg-8 text-end wow fadeInRight" data-wow-delay="0.1s">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <li class="nav-item mb-4">
                                <a class="d-flex mx-2 py-2 bg-light rounded-pill active text-dark scroll-link"
                                    href="#san-pham-moi">
                                    <span class="" style="width: 130px;">Sản phẩm mới</span>
                                </a>
                            </li>
                            <li class="nav-item mb-4">
                                <a class="d-flex py-2 mx-2 bg-light rounded-pill scroll-link" href="#tat-ca-san-pham">
                                    <span class="text-dark" style="width: 130px;">Tất cả sản phẩm</span>
                                </a>
                            </li>
                            <li class="nav-item mb-4">
                                <a class="d-flex mx-2 py-2 bg-light rounded-pill scroll-link" href="#top-ban-chay">
                                    <span class="text-dark" style="width: 130px;">Top bán chạy</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp"
                                data-wow-delay="0.1s">Products</h4>
                            <h1 class="mb-0 display-3 wow fadeInUp" data-wow-delay="0.3s">Sản phẩm mới</h1>
                        </div>
                        <div class="row g-4">
                            @foreach ($our_product as $product)
                                <div class="col-md-6 col-lg-4 col-xl-3">
                                    <div class="product-item rounded wow fadeInUp" data-wow-delay="0.1s">
                                        <div class="product-item-inner border rounded">
                                            <div class="product-item-inner-item">
                                                <img src="{{ asset('public/uploads/products/' . $product->imageURL) }}"
                                                    class="img-fluid w-100 rounded-top" alt="">
                                                <div class="product-new">Mới</div>
                                                <div class="product-details">
                                                    <a href="{{ route('product.detail', $product->id) }}"><i
                                                            class="fa fa-eye fa-1x"></i></a>
                                                </div>
                                            </div>
                                            <div class="text-center rounded-bottom p-4">
                                                <a href="{{ route('product.detail', $product->id) }}"
                                                    class="d-block mb-2">{{ $product->category->name }}</a>
                                                <a href="{{ route('product.detail', $product->id) }}" class="d-block h4">
                                                    {{ $product->name }} <br> </a>
                                                @php
                                                    $price = (float) ($product->price ?? 0);
                                                    $percent = (int) ($product->discountPercent ?? 0);
                                                    $percent = max(0, min(100, $percent)); // chặn ngoài 0–100

                                                    // giá sau giảm (làm tròn đến đơn vị đồng; nếu muốn tới nghìn dùng round($..., -3))
                                                    $discounted = ($price * (100 - $percent)) / 100;

                                                    // format VND: 1.234.567đ
                                                    $fmt = fn($n) => number_format($n, 0, ',', '.') . 'đ';
                                                @endphp

                                                @if ($percent > 0)
                                                    <del class="me-2 fs-5">{{ $fmt($price) }}</del>
                                                    <span class="text-primary fs-5">{{ $fmt($discounted) }}</span>
                                                @else
                                                    <span class="fs-5">{{ $fmt($price) }}</span>
                                                @endif

                                            </div>
                                        </div>
                                        <div
                                            class="product-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                                            <button
                                                class="btn btn-primary border-secondary rounded-pill py-2 px-4 mb-4 add-to-cart-btn"
                                                data-product-id="{{ $product->id }}"
                                                data-authenticated="{{ Auth::check() ? 'true' : 'false' }}">
                                                <i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ hàng
                                            </button>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex">
                                                    <i class="fas fa-star text-primary"></i>
                                                    <i class="fas fa-star text-primary"></i>
                                                    <i class="fas fa-star text-primary"></i>
                                                    <i class="fas fa-star text-primary"></i>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <div class="d-flex">
                                                    <a href="#"
                                                        class="text-primary d-flex align-items-center justify-content-center me-3"><span
                                                            class="rounded-circle btn-sm-square border"><i
                                                                class="fas fa-random"></i></i></a>
                                                    <a href="#"
                                                        class="text-primary d-flex align-items-center justify-content-center me-0"><span
                                                            class="rounded-circle btn-sm-square border"><i
                                                                class="fas fa-heart"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Our Products End -->


    <!-- Product List Satrt -->
    <div id="tat-ca-san-pham" class="container-fluid products productList overflow-hidden mb-5">
        <div class="container products-mini py-5">
            <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp"
                    data-wow-delay="0.1s">Products</h4>
                <h1 class="mb-0 display-3 wow fadeInUp" data-wow-delay="0.3s">All Product Items</h1>
            </div>
            <div class="productList-carousel owl-carousel pt-4 wow fadeInUp" data-wow-delay="0.3s">
                @foreach ($all_product as $product)
                    <div class="productImg-carousel owl-carousel productList-item">
                        <div class="productImg-item products-mini-item border">
                            <div class="row g-0">
                                <div class="col-5">
                                    <div class="products-mini-img border-end h-100">
                                        <img src="{{ asset('public/uploads/products/' . $product->imageURL) }}"
                                            class="img-fluid w-100 h-100" alt="Image">
                                        <div class="products-mini-icon rounded-circle bg-primary">
                                            <a href="{{ route('product.detail', $product->id) }}"><i
                                                    class="fa fa-eye fa-1x text-white"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="products-mini-content p-3">
                                        <a href="{{ route('product.detail', $product->id) }}"
                                            class="d-block mb-2">{{ $product->category->name }}</a>
                                        <a href="{{ route('product.detail', $product->id) }}"
                                            class="d-block h4">{{ $product->name }} <br></a>

                                        @php
                                            $price = (float) ($product->price ?? 0);
                                            $percent = (int) ($product->discountPercent ?? 0);
                                            $percent = max(0, min(100, $percent)); // chặn ngoài 0–100

                                            // giá sau giảm (làm tròn đến đơn vị đồng; nếu muốn tới nghìn dùng round($..., -3))
                                            $discounted = ($price * (100 - $percent)) / 100;

                                            // format VND: 1.234.567đ
                                            $fmt = fn($n) => number_format($n, 0, ',', '.') . 'đ';
                                        @endphp

                                        @if ($percent > 0)
                                            <del class="me-2 fs-5">{{ $fmt($price) }}</del>
                                            <span class="text-primary fs-5">{{ $fmt($discounted) }}</span>
                                        @else
                                            <span class="text-primary fs-5">{{ $fmt($price) }}</span>
                                        @endif


                                    </div>
                                </div>
                            </div>
                            <div class="products-mini-add border p-3">
                                <button class="btn btn-primary border-secondary rounded-pill py-2 px-4 add-to-cart-btn"
                                    data-product-id="{{ $product->id }}"
                                    data-authenticated="{{ Auth::check() ? 'true' : 'false' }}">
                                    <i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ hàng
                                </button>
                                <div class="d-flex">
                                    <a href="#"
                                        class="text-primary d-flex align-items-center justify-content-center me-3"><span
                                            class="rounded-circle btn-sm-square border"><i
                                                class="fas fa-random"></i></i></a>
                                    <a href="#"
                                        class="text-primary d-flex align-items-center justify-content-center me-0"><span
                                            class="rounded-circle btn-sm-square border"><i class="fas fa-heart"></i></a>
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
            <!-- Product List End -->

            <!-- Bestseller Products Start -->
            <div id="top-ban-chay" class="container-fluid products pb-5 mt-5">
                <div class="container products-mini py-5">
                    <div class="mx-auto text-center mb-5" style="max-width: 900px;">
                        <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp"
                            data-wow-delay="0.1s">Products</h4>
                        <h1 class="mb-0 display-3 wow fadeInUp" data-wow-delay="0.3s">Top bán chạy</h1>
                    </div>
                    <div class="row g-4">
                        @foreach ($best_seller_product as $product)
                            <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="products-mini-item border">
                                    <div class="row g-0">
                                        <div class="col-5">
                                            <div class="products-mini-img border-end h-100">
                                                <img src="{{ asset('public/uploads/products/' . $product->imageURL) }}"
                                                    class="img-fluid w-100 h-100" alt="Image">
                                                <div class="products-mini-icon rounded-circle bg-primary">
                                                    <a href="{{ route('product.detail', $product->id) }}"><i
                                                            class="fa fa-eye fa-1x text-white"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7">
                                            <div class="products-mini-content p-3">
                                                <a href="{{ route('product.detail', $product->id) }}"
                                                    class="d-block mb-2">{{ $product->category->name }}</a>
                                                <a href="{{ route('product.detail', $product->id) }}" class="d-block h4">
                                                    {{ $product->name }} <br></a>
                                                @if ($percent > 0)
                                                    <del class="me-2 fs-5">{{ $fmt($price) }}</del>
                                                    <span class="text-primary fs-5">{{ $fmt($discounted) }}</span>
                                                @else
                                                    <span class="text-primary fs-5">{{ $fmt($price) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="products-mini-add border p-3">
                                        <button
                                            class="btn btn-primary border-secondx  ary rounded-pill py-2 px-4 add-to-cart-btn"
                                            data-product-id="{{ $product->id ?? 1 }}"
                                            data-authenticated="{{ Auth::check() ? 'true' : 'false' }}">
                                            <i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ hàng
                                        </button>
                                        <div class="d-flex">
                                            <a href="#"
                                                class="text-primary d-flex align-items-center justify-content-center me-3"><span
                                                    class="rounded-circle btn-sm-square border"><i
                                                        class="fas fa-random"></i></i></a>
                                            <a href="#"
                                                class="text-primary d-flex align-items-center justify-content-center me-0"><span
                                                    class="rounded-circle btn-sm-square border"><i
                                                        class="fas fa-heart"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Bestseller Products End -->
        @endsection
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const links = document.querySelectorAll('.scroll-link');

                links.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault(); // Ngăn chặn hành động nhảy trang mặc định

                        // 1. Xóa class active ở tất cả các nút
                        links.forEach(l => l.classList.remove('active'));

                        // 2. Thêm class active cho nút vừa click
                        this.classList.add('active');

                        // 3. Lấy ID mục tiêu
                        const targetId = this.getAttribute('href');
                        const targetSection = document.querySelector(targetId);

                        if (targetSection) {
                            // 4. Cuộn mượt xuống vị trí đó
                            // offsetTop - 100 để trừ hao thanh menu header nếu có (tùy chỉnh số 100 này)
                            const offsetTop = targetSection.offsetTop - 100;

                            window.scrollTo({
                                top: offsetTop,
                                behavior: "smooth"
                            });
                        }
                    });
                });
            });
        </script>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>36 Shop</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="route-cart-add" content="{{ route('cart.add') }}">
    <meta name="route-cart-update" content="{{ route('cart.update', ['cartItemId' => 0]) }}">
    <meta name="route-cart-remove" content="{{ route('cart.remove', ['cartItemId' => 0]) }}">
    <meta name="route-cart-clear" content="{{ route('cart.clear') }}">
    <meta name="route-cart-summary" content="{{ route('cart.summary') }}">
    <meta name="route-cart-api" content="{{ route('cart.api') }}">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('public/client/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/client/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('public/client/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('public/client/css/style.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('public/client/img/favicon.png') }}">
    <style>
        /* Hiệu ứng khi di chuột vào sản phẩm gợi ý */
        .search-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        /* Thanh cuộn nếu danh sách quá dài */
        .dropdown-menu {
            max-height: 400px;
            overflow-y: auto;
            border: none !important;
            padding: 0;
            margin: 0;
        }

        .navbar-nav .nav-link.active {
            color: #ffffff !important;
            /* Bắt buộc màu trắng */
            font-weight: bold;
            /* In đậm cho rõ */
            text-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
            /* Thêm chút bóng nhẹ cho đẹp */
        }

        .navbar-nav .nav-link:hover {
            color: #ffffff !important;
        }
    </style>
</head>

<body>

    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


    <!-- Topbar Start -->
    <div class="container-fluid px-5 d-none border-bottom d-lg-block">
        <div class="row gx-0 align-items-center">
            <div class="col-lg-4 text-center text-lg-start mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <a href="#" class="text-muted me-2"> Help</a><small> / </small>
                    <a href="#" class="text-muted mx-2"> Hỗ trợ</a><small> / </small>
                    <a href="#" class="text-muted ms-2"> Liên hệ</a>

                </div>
            </div>
            <div class="col-lg-4 text-center d-flex align-items-center justify-content-center">
                <small class="text-dark">Liên hệ chúng tôi:</small>
                <a href="#" class="text-muted">(+84)373033510</a>
            </div>

            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle text-muted ms-2" data-bs-toggle="dropdown"><small><i
                                    class="fa fa-home me-2"></i> My Dashboard</small></a>
                        <div class="dropdown-menu rounded">
                            @auth
                                @if (auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item"> Admin Dashboard</a>
                                    <div class="dropdown-divider"></div>
                                @endif
                                <a href="{{ url('/show-profile') }}" class="dropdown-item"> Thông tin cá nhân</a>
                                <a href="{{ url('/dat-hang-thanh-cong/{id}') }}" class="dropdown-item"> Thông tin đơn
                                    hàng</a>
                                <a href="{{ url('/logout-admin') }}" class="dropdown-item"> Đăng xuất</a>
                            @else
                                <a href="{{ route('admin') }}" class="dropdown-item"> Đăng nhập</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid px-5 py-4 d-none d-lg-block">
        <div class="row gx-0 align-items-center text-center">
            <div class="col-md-4 col-lg-3 text-center text-lg-start">
                <div class="d-inline-flex align-items-center">
                    <a href="{{ url('/') }}" class="navbar-brand p-0">
                        <h1 class="display-5 text-primary m-0"><i
                                class="fas fa-shopping-bag text-secondary me-2"></i>36Shop</h1>
                    </a>
                </div>
            </div>
            <div class="col-md-4 col-lg-6 text-center">
                <div class="position-relative ps-4">
                    <form action="{{ route('search') }}" method="GET" style="position: relative;">
                        <div class="d-flex border rounded-pill">
                            <input value="{{ request()->keyword }}"
                                class="form-control border-0 rounded-pill w-100 py-3" type="text" name="keyword"
                                id="keywords" placeholder="Tìm kiếm sản phẩm?" autocomplete="off">
                            <button type="submit" class="btn btn-primary rounded-pill py-3 px-5" style="border: 0;">
                                <i class="fas fa-search"></i>
                            </button>
                            <div id="search_ajax"
                                style="position: absolute; top: 100%; left: 0; width: 100%; z-index: 1021; background: white; border-radius: 0 0 15px 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden;">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4 col-lg-3 text-center text-lg-end">
                <div class="d-inline-flex align-items-center">
                    <a href="{{ route('cart') }}"
                        class="text-muted d-flex align-items-center justify-content-center position-relative">
                        <span class="rounded-circle btn-md-square border">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                        <span class="text-dark ms-2">Giỏ Hàng</span>
                        <span
                            class="cart-counter position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="display: none;">0</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar & Hero Start -->
    <div class="container-fluid nav-bar p-0">
        <div class="row gx-0 bg-primary px-5 align-items-center">
            <div class="col-12 col-lg-12">
                <nav class="navbar navbar-expand-lg navbar-light bg-primary">
                    <a href="{{ url('/') }}" class="navbar-brand d-block d-lg-none">
                        <h1 class="display-5 text-secondary m-0">
                            <i class="fas fa-shopping-bag text-white me-2"></i>36Shop
                        </h1>
                    </a>

                    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars fa-1x"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <div class="navbar-nav me-auto py-0"> <a href="{{ url(path: '/') }}"
                                class="nav-item nav-link {{ request()->is('/') ? 'active text-white font-weight-bold' : '' }}">Trang
                                chủ</a>

                            @foreach ($categories as $cat)
                                <a href="{{ url('/show-product-category-home/' . $cat->id) }}"
                                    class="nav-item nav-link {{ request()->is('show-product-category-home/' . $cat->id) || request()->id == $cat->id ? 'active text-white font-weight-bold' : '' }}">
                                    {{ $cat->name }}
                                </a>
                            @endforeach

                            {{-- <a href="shop.html" class="nav-item nav-link">Shop</a> --}}
                        </div>

                        <a href=""
                            class="btn btn-secondary rounded-pill py-2 px-4 px-lg-3 mb-3 mb-md-3 mb-lg-0">
                            <i class="fa fa-mobile-alt me-2"></i> +0123 456 7890
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    </div>
    <!-- Navbar & Hero End -->
    <main class="flex-grow-1">
        @yield('home-content')
    </main>

    <!-- Footer Start -->
    @include('layout.footer_home')
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('public/client/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('public/client/lib/owlcarousel/owl.carousel.min.js') }}"></script>


    <!-- Template Javascript -->
    <script src="{{ asset('public/client/js/main.js') }}"></script>
    <script src="{{ asset('public/client/js/cart.js') }}"></script>
    <!-- Script giữ vị trí cuộn trang -->
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            // 1. Khôi phục vị trí cuộn nếu có trong bộ nhớ
            var scrollPos = sessionStorage.getItem('scrollPos');
            if (scrollPos) {
                window.scrollTo(0, scrollPos);
                sessionStorage.removeItem('scrollPos'); // Xóa đi để không ảnh hưởng các trang khác
            }
        });

        // 2. Hàm lưu vị trí cuộn hiện tại (Gọi hàm này trước khi reload)
        function saveScrollPosition() {
            sessionStorage.setItem('scrollPos', window.scrollY);
        }
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Bắt sự kiện khi gõ phím vào ô input #keywords
            $('#keywords').keyup(function() {
                var query = $(this).val();

                if (query != '') {
                    // Lấy CSRF Token (Laravel yêu cầu bảo mật)
                    var _token = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: "{{ route('product.autocomplete_ajax') }}", // Gọi đến route ở Bước 1
                        method: "GET",
                        data: {
                            query: query,
                            _token: _token
                        },
                        success: function(data) {
                            // Khi thành công, đổ dữ liệu HTML vào div #search_ajax
                            $('#search_ajax').fadeIn();
                            $('#search_ajax').html(data);
                        }
                    });
                } else {
                    $('#search_ajax').fadeOut(); // Nếu xóa hết chữ thì ẩn đi
                }
            });

            // Khi click ra ngoài thì ẩn bảng gợi ý đi cho đỡ vướng
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#keywords, #search_ajax').length) {
                    $('#search_ajax').fadeOut();
                }
            });

            // Khi click vào 1 item trong danh sách -> Gán tên vào ô input (tùy chọn)
            $(document).on('click', '.search-item', function() {
                var text = $(this).find('span').first().text();
                $('#keywords').val(text);
                $('#search_ajax').fadeOut();
                // Link thẻ <a> đã xử lý việc chuyển trang rồi
            });
        });
    </script>

</html>

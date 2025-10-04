<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Focus - Bootstrap Admin Dashboard </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('public/admin/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/vendor/owl-carousel/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/vendor/owl-carousel/css/owl.theme.default.min.css') }}">
    <link href="{{ asset('public/admin/vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/admin/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/admin/vendor/datatables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/vendor/toastr/css/toastr.min.css') }}">


</head>

<body>
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <div id="main-wrapper">
        <div class="nav-header">
            <a href="{{ url('/dashboard') }}" class="brand-logo">
                <img class="logo-abbr" src="{{ asset('public/admin/images/logo.png') }}" alt="">
                <img class="logo-compact" src="{{ asset('public/admin/images/logo-text.png') }}" alt="">
                <img class="brand-title" src="{{ asset('public/admin/images/logo-text.png') }}" alt="">
            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="search_bar dropdown">
                                <span class="search_icon p-3 c-pointer" data-toggle="dropdown">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                                <div class="dropdown-menu p-0 m-0">
                                    <form>
                                        <input class="form-control" type="search" placeholder="Search"
                                            aria-label="Search">
                                    </form>
                                </div>
                            </div>
                        </div>

                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown header-profile">
                                @auth
                                    <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                        <i class="mdi mdi-account"></i>
                                        <p class="mb-0" style="font-size: 14px; display: inline-block;">
                                            {{ auth()->user()->name }}</p>
                                    </a>
                                @endauth
                                <div class="dropdown-menu dropdown-menu-right">
                                    <!-- <a href="./app-profile.html" class="dropdown-item">
                                        <i class="icon-user"></i>
                                        <span class="ml-2">Profile </span>
                                    </a> -->
                                    <a href="{{url('/show-home')}} " class="dropdown-item">
                                        <i class="icon-envelope-open"></i>
                                        <span class="ml-2"> Quay lại trang chủ </span>
                                    </a>
                                    <a href="{{ url('/logout-admin') }}" class="dropdown-item">
                                        <i class="icon-logout"></i>
                                        <span class="ml-2">Đăng xuất </span>
                                    </a>
                                    <a href="{{ url('/show-home') }}" class="dropdown-item">
                                        <i class="icon-compass"></i>
                                        <span class="ml-2">Quay về trang mua sắm </span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <div class="quixnav">
            <div class="quixnav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="nav-label first">Menu quản lý</li>

                    <li>
                        <a href="{{ url('/show-brand') }}" aria-expanded="false">
                            <i class="icon icon-globe-2"></i>
                            <span class="nav-text">Quản lý thương hiệu</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/show-category') }}" aria-expanded="false">
                            <i class="icon icon-home"></i>
                            <span class="nav-text">Quản lý danh mục</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/show-product') }}" aria-expanded="false">
                            <i class="icon icon-basket"></i>
                            <span class="nav-text">Quản lý sản phẩm</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users') }}" aria-expanded="false">
                            <i class="icon icon-people"></i>
                            <span class="nav-text">Quản lý tài khoản</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="content-body">
            @yield('view-content')
        </div>
    </div>
    <div class="modal fade" id="ModalEdit" tabindex="-1" aria-labelledby="ModalEditLabel" aria-hidden="true"
        role="dialog">
    </div>

    <div class="footer">
        <div class="copyright">
            <p>Copyright © Designed &amp; Developed by <a href="#" target="_blank">Quixkit</a> 2019</p>
            <p>Distributed by <a href="https://themewagon.com/" target="_blank">Themewagon</a></p>
        </div>
    </div>



    <!-- Required vendors -->
    <script src="{{ asset('public/admin/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('public/admin/js/quixnav-init.js') }}"></script>
    <script src="{{ asset('public/admin/js/custom.min.js') }}"></script>


    <!-- Vectormap -->
    <script src="{{ asset('public/admin/vendor/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('public/admin/vendor/morris/morris.min.js') }}"></script>


    <script src="{{ asset('public/admin/vendor/circle-progress/circle-progress.min.js') }}"></script>
    <script src="{{ asset('public/admin/vendor/chart.js/Chart.bundle.min.js') }}"></script>

    <script src="{{ asset('public/admin/vendor/gaugeJS/dist/gauge.min.js') }}"></script>

    <!--  flot-chart js -->
    <script src="{{ asset('public/admin/vendor/flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('public/admin/vendor/flot/jquery.flot.resize.js') }}"></script>

    <!-- Owl Carousel -->
    <script src="{{ asset('public/admin/vendor/owl-carousel/js/owl.carousel.min.js') }}"></script>

    <!-- Counter Up -->
    <script src="{{ asset('public/admin/vendor/jqvmap/js/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('public/admin/vendor/jqvmap/js/jquery.vmap.usa.js') }}"></script>
    <script src="{{ asset('public/admin/vendor/jquery.counterup/jquery.counterup.min.js') }}"></script>


    <script src="{{ asset('public/admin/js/dashboard/dashboard-1.js') }}"></script>
    <script src="{{ asset('public/admin/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/admin/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('public/admin/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('public/admin/js/main.js') }}"></script>
    {{-- <script>
        function CloseModal(id) {
            debugger;
            const $m = $("#" + id);
            // DÙNG plugin của Bootstrap, KHÔNG dùng .hide()
            $m.modal("hide");
        }
    </script> --}}
</body>

</html>

@extends('layout.home_layout')

@section('home-content')
@php
    $price = (float) ($product->price ?? 0);
    $percent = (int) ($product->discountPercent ?? 0);
    $percent = max(0, min(100, $percent));
    $discounted = $percent > 0 ? ($price * (100 - $percent)) / 100 : $price;
    $savings = $percent > 0 ? ($price - $discounted) : 0;
    $fmt = fn($n) => number_format($n, 0, ',', '.') . ' VNĐ';
    $fmtShort = fn($n) => number_format($n, 0, ',', '.') . 'đ';

    // Xử lý hình ảnh chính & thư viện ảnh gallery
    $mainImgUrl = null;
    if (!empty($product->imageURL)) {
        if (file_exists(public_path('uploads/products/' . $product->imageURL))) {
            $mainImgUrl = asset('uploads/products/' . $product->imageURL);
        } elseif (file_exists(public_path('client/img/' . $product->imageURL))) {
            $mainImgUrl = asset('client/img/' . $product->imageURL);
        }
    }
    if (!$mainImgUrl) {
        $mainImgUrl = asset('client/img/product-1.png');
    }

    $isAirPods = str_contains(strtolower($product->name), 'airpods') || str_contains(strtolower($product->name), 'tai nghe');

    $gallery = [
        ['type' => 'image', 'url' => $mainImgUrl, 'alt' => $product->name . ' - Hình hộp sạc mở']
    ];

    if ($isAirPods) {
        if (file_exists(public_path('uploads/products/airpods-pro.jpg'))) {
            $gallery[] = ['type' => 'image', 'url' => asset('uploads/products/airpods-pro.jpg'), 'alt' => 'Tai nghe AirPods Pro'];
        } elseif (file_exists(public_path('client/img/airpods-pro.jpg'))) {
            $gallery[] = ['type' => 'image', 'url' => asset('client/img/airpods-pro.jpg'), 'alt' => 'Tai nghe AirPods Pro'];
        }
        if (file_exists(public_path('uploads/products/airpods-pro-2.jpg')) && $product->imageURL !== 'airpods-pro-2.jpg') {
            $gallery[] = ['type' => 'image', 'url' => asset('uploads/products/airpods-pro-2.jpg'), 'alt' => 'AirPods Pro 2 góc nghiêng'];
        }
        $gallery[] = ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=800&auto=format&fit=crop&q=80', 'alt' => 'Chi tiết dock sạc'];
        $gallery[] = ['type' => 'video', 'url' => 'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?w=800&auto=format&fit=crop&q=80', 'video_url' => 'https://www.youtube.com/embed/fWL8k3nN1x8', 'alt' => 'Video mở hộp & trải nghiệm'];
    } else {
        $gallery[] = ['type' => 'image', 'url' => $mainImgUrl, 'alt' => $product->name . ' - Góc nhìn 2'];
        $gallery[] = ['type' => 'image', 'url' => $mainImgUrl, 'alt' => $product->name . ' - Chi tiết sản phẩm'];
        $gallery[] = ['type' => 'video', 'url' => $mainImgUrl, 'video_url' => 'https://www.youtube.com/embed/fWL8k3nN1x8', 'alt' => 'Video giới thiệu'];
    }

    // Xử lý danh sách Phiên bản (Variants)
    $variantsRaw = $product->variants;
    if (empty($variantsRaw) && $isAirPods) {
        $variantsRaw = 'USB-C, MagSafe Qi';
    }
    $variantList = !empty($variantsRaw) ? array_filter(array_map('trim', explode(',', $variantsRaw))) : [];

    // Xử lý danh sách Màu sắc (Colors)
    $colorsRaw = $product->colors;
    if (empty($colorsRaw) && $isAirPods) {
        $colorsRaw = 'Trắng, Xám không gian';
    }
    $colorList = !empty($colorsRaw) ? array_filter(array_map('trim', explode(',', $colorsRaw))) : [];

    // Hàm xác định màu sắc hiển thị cho chấm tròn (color dot)
    $getColorDotStyle = function($colorName) {
        $lower = mb_strtolower($colorName, 'UTF-8');
        if (str_contains($lower, 'trắng') || str_contains($lower, 'white')) {
            return 'background-color: #ffffff; border: 1.5px solid #cbd5e1;';
        } elseif (str_contains($lower, 'đen') || str_contains($lower, 'black') || str_contains($lower, 'tối')) {
            return 'background-color: #18181b;';
        } elseif (str_contains($lower, 'xám') || str_contains($lower, 'gray') || str_contains($lower, 'grey') || str_contains($lower, 'titan')) {
            return 'background-color: #4b5563;';
        } elseif (str_contains($lower, 'vàng') || str_contains($lower, 'gold') || str_contains($lower, 'yellow')) {
            return 'background-color: #eab308;';
        } elseif (str_contains($lower, 'hồng') || str_contains($lower, 'pink')) {
            return 'background-color: #f472b6;';
        } elseif (str_contains($lower, 'đỏ') || str_contains($lower, 'red')) {
            return 'background-color: #ef4444;';
        } elseif (str_contains($lower, 'xanh lá') || str_contains($lower, 'green')) {
            return 'background-color: #22c55e;';
        } elseif (str_contains($lower, 'xanh') || str_contains($lower, 'blue')) {
            return 'background-color: #3b82f6;';
        } elseif (str_contains($lower, 'tím') || str_contains($lower, 'purple')) {
            return 'background-color: #a855f7;';
        } elseif (str_contains($lower, 'cam') || str_contains($lower, 'orange')) {
            return 'background-color: #f97316;';
        }
        return 'background-color: #94a3b8;';
    };

    // Xử lý dữ liệu Đánh giá
    $reviewsList = $product->reviews ?? collect();
    $realReviewsCount = $reviewsList->count();
    $reviewsCount = $realReviewsCount;
    $avgRating = $realReviewsCount > 0 ? round($reviewsList->avg('rating'), 1) : 0;
@endphp

<div class="product-detail-page py-4">
    <div class="container">
        <!-- 1. BREADCRUMBS -->
        <nav aria-label="breadcrumb" class="custom-breadcrumb-nav mb-4">
            <ol class="breadcrumb align-items-center mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="breadcrumb-link" title="Trang chủ">
                        <i class="fas fa-home text-muted"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="breadcrumb-link">Trang chủ</a>
                </li>
                @if ($product->category && ($product->category->id ?? $product->category_id))
                    <li class="breadcrumb-item">
                        <a href="{{ route('home.category.product', ['id' => $product->category->id ?? $product->category_id]) }}" class="breadcrumb-link">
                            {{ $product->category->name }}
                        </a>
                    </li>
                @else
                    <li class="breadcrumb-item">
                        <span class="text-muted">{{ $product->category->name ?? 'Tai nghe & Âm thanh' }}</span>
                    </li>
                @endif
                <li class="breadcrumb-item">
                    <span class="text-muted">{{ $product->style ?? 'Tai nghe' }}</span>
                </li>
                @if ($product->brand)
                    <li class="breadcrumb-item">
                        <span class="text-muted">{{ $product->brand->TenThuongHieu }}</span>
                    </li>
                @endif
                <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 280px;">
                    {{ $product->name }}
                </li>
            </ol>
        </nav>

        <!-- 2. MAIN PRODUCT SECTION -->
        <div class="product-hero-card bg-white rounded-4 p-4 p-lg-5 shadow-sm mb-5">
            <div class="row g-4 g-xl-5">
                <!-- CỘT TRÁI: MEDIA & THƯ VIỆN ẢNH -->
                <div class="col-lg-6">
                    <div class="product-gallery-wrapper">
                        <!-- Khung ảnh chính -->
                        <div class="main-image-viewport position-relative rounded-4 d-flex align-items-center justify-content-center">
                            <!-- Huy hiệu "Mới" -->
                            <span class="badge-tag-new position-absolute top-0 start-0 m-3">
                                Mới
                            </span>

                            <!-- Nút phóng to toàn màn hình -->
                            <button type="button" class="btn-zoom-expand position-absolute bottom-0 end-0 m-3" id="btn-zoom-image" title="Phóng to ảnh">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </button>

                            <!-- Nút điều hướng ảnh Trước/Sau -->
                            @if (count($gallery) > 1)
                                <button type="button" class="gallery-nav-arrow prev" id="gallery-prev-btn" title="Ảnh trước">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="gallery-nav-arrow next" id="gallery-next-btn" title="Ảnh tiếp theo">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif

                            <!-- Ảnh chính -->
                            <div class="image-inner-container">
                                <img src="{{ $gallery[0]['url'] }}" alt="{{ $product->name }}" id="main-product-image" class="img-fluid main-img-element">
                            </div>
                        </div>

                        <!-- Danh sách Thumbnail -->
                        <div class="gallery-thumbnails-strip mt-3 d-flex align-items-center gap-3">
                            @foreach ($gallery as $index => $item)
                                <div class="thumb-item {{ $index === 0 ? 'active' : '' }} {{ $item['type'] === 'video' ? 'video-thumb' : '' }}"
                                     data-index="{{ $index }}"
                                     data-type="{{ $item['type'] }}"
                                     data-src="{{ $item['url'] }}"
                                     data-video="{{ $item['video_url'] ?? '' }}">
                                    <img src="{{ $item['url'] }}" alt="{{ $item['alt'] }}" class="thumb-img">
                                    @if ($item['type'] === 'video')
                                        <div class="video-overlay-play">
                                            <span class="play-icon-circle">
                                                <i class="fas fa-play"></i>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI: THÔNG TIN CHI TIẾT & ĐẶT HÀNG -->
                <div class="col-lg-6">
                    <div class="product-info-panel ps-lg-3">
                        <!-- Thương hiệu -->
                        <div class="brand-row mb-2 d-flex align-items-center">
                            @if (($product->brand && strtolower($product->brand->TenThuongHieu) === 'apple') || str_contains(strtolower($product->name), 'apple'))
                                <span class="brand-badge-item d-inline-flex align-items-center fw-semibold text-dark">
                                    <i class="fab fa-apple fa-lg me-1 text-dark"></i> Apple
                                </span>
                            @elseif ($product->brand)
                                <span class="brand-badge-item d-inline-flex align-items-center fw-semibold text-dark">
                                    <i class="fas fa-certificate text-primary me-1"></i> {{ $product->brand->TenThuongHieu }}
                                </span>
                            @else
                                <span class="brand-badge-item d-inline-flex align-items-center fw-semibold text-dark">
                                    <i class="fas fa-check-circle text-success me-1"></i> Chính Hãng
                                </span>
                            @endif
                        </div>

                        <!-- Tên sản phẩm -->
                        <h1 class="product-title-heading fw-bold text-dark mb-2">
                            {{ $product->name }}
                        </h1>

                        <!-- Slogan / Giới thiệu ngắn -->
                        <p class="product-subtitle-tagline text-muted mb-3">
                            {{ $product->style ? $product->style . ' - ' : '' }}Âm thanh đỉnh cao. Chống ồn chủ động. Mọi trải nghiệm đều vượt trội.
                        </p>

                        <!-- Đánh giá sao & Lượt bán -->
                        <div class="product-rating-sold-row d-flex align-items-center flex-wrap gap-2 mb-3 pb-2">
                            <div class="stars-group text-warning d-flex align-items-center">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($avgRating))
                                        <i class="fas fa-star"></i>
                                    @elseif ($i - $avgRating < 1 && $i - $avgRating > 0)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="rating-score fw-bold text-dark ms-1">{{ $avgRating }}</span>
                            <a href="#pills-reviews" class="rating-count-link text-muted" onclick="document.getElementById('pills-reviews-tab').click(); return true;">
                                ({{ $reviewsCount }} đánh giá)
                            </a>
                            <span class="meta-divider text-muted opacity-50">|</span>
                            <span class="sold-count text-muted">
                                Đã bán <strong class="text-dark">1.2K+</strong>
                            </span>
                        </div>

                        <!-- Khối Giá Bán -->
                        <div class="product-pricing-card p-3 rounded-3 mb-4">
                            <div class="d-flex align-items-baseline flex-wrap gap-2 mb-1">
                                <span class="current-price-val fw-bolder">
                                    {{ $fmt($discounted) }}
                                </span>

                                @if ($percent > 0)
                                    <del class="original-price-val text-muted ms-2">
                                        {{ $fmt($price) }}
                                    </del>
                                    <span class="discount-badge-pill badge bg-danger rounded-pill px-2.5 py-1">
                                        -{{ $percent }}%
                                    </span>
                                @endif
                            </div>

                            @if ($percent > 0)
                                <div class="saving-badge-container mt-2">
                                    <span class="saving-badge-item">
                                        <i class="fas fa-fire me-1 text-danger"></i> Tiết kiệm {{ $fmtShort($savings) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Tùy chọn: Phiên bản -->
                        @if (count($variantList) > 0)
                            <div class="variant-option-group mb-3">
                                <label class="option-title-label fw-bold text-dark d-block mb-2">
                                    Phiên bản
                                </label>
                                <div class="variant-pills-list d-flex flex-wrap gap-2">
                                    @foreach ($variantList as $vIndex => $vName)
                                        <button type="button" class="variant-btn {{ $vIndex === 0 ? 'active' : '' }}" data-variant="{{ $vName }}">
                                            <i class="fas fa-check me-1.5 check-icon {{ $vIndex === 0 ? '' : 'd-none' }}"></i> {{ $vName }}
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" id="selected-variant" name="variant" value="{{ $variantList[0] ?? '' }}">
                            </div>
                        @endif

                        <!-- Tùy chọn: Màu sắc -->
                        @if (count($colorList) > 0)
                            <div class="color-option-group mb-4">
                                <label class="option-title-label fw-bold text-dark d-block mb-2">
                                    Màu sắc
                                </label>
                                <div class="color-pills-list d-flex flex-wrap gap-2">
                                    @foreach ($colorList as $cIndex => $cName)
                                        <button type="button" class="color-btn {{ $cIndex === 0 ? 'active' : '' }}" data-color="{{ $cName }}">
                                            <span class="color-dot me-2" style="{{ $getColorDotStyle($cName) }}"></span> {{ $cName }}
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" id="selected-color" name="color" value="{{ $colorList[0] ?? '' }}">
                            </div>
                        @endif

                        <!-- Bộ điều khiển số lượng & Trạng thái tồn kho -->
                        @if ($product->IsActive && $product->stockQuantity > 0)
                            <div class="quantity-picker-group mb-4">
                                <label class="option-title-label fw-bold text-dark d-block mb-2">
                                    Số lượng
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stepper-counter-box d-flex align-items-center">
                                        <button type="button" class="stepper-btn minus" id="btn-decrement-qty" aria-label="Giảm">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="stepper-input" id="product-quantity" value="1" min="1" max="{{ $product->stockQuantity }}">
                                        <button type="button" class="stepper-btn plus" id="btn-increment-qty" aria-label="Tăng">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <span class="stock-hint-label text-muted small">
                                        <i class="fas fa-boxes me-1 text-success"></i> Còn lại {{ $product->stockQuantity }} sản phẩm
                                    </span>
                                </div>
                            </div>

                            <!-- CỤM NÚT HÀNH ĐỘNG CHÍNH -->
                            <div class="main-cta-buttons-row d-flex flex-wrap gap-3 mb-4">
                                <!-- Nút: Thêm vào giỏ hàng -->
                                <button type="button" class="btn btn-outline-orange add-to-cart-btn flex-fill py-3 px-4 fw-bold"
                                        data-product-id="{{ $product->id }}"
                                        data-quantity-selector="#product-quantity"
                                        onclick="if(window.addToCartDirect){window.addToCartDirect({{ $product->id }}, this);}else if(window.cartManager){window.cartManager.addToCart({{ $product->id }}, '#product-quantity');}">
                                    <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ hàng
                                </button>

                                <!-- Nút: Mua ngay -->
                                <button type="button" class="btn btn-solid-orange buy-now-btn flex-fill py-3 px-4 fw-bold text-white shadow-sm"
                                        data-product-id="{{ $product->id }}"
                                        data-quantity-selector="#product-quantity"
                                        onclick="var q=document.getElementById('product-quantity')?.value||1; if(window.cartManager){window.cartManager.buyNow({{ $product->id }}, q, this);}else{window.location.href='{{ route('checkout.index') }}';}">
                                    <i class="fas fa-bolt me-2"></i> Mua ngay
                                </button>
                            </div>
                        @else
                            <div class="alert alert-warning rounded-3 mb-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Sản phẩm hiện tại đang tạm hết hàng hoặc ngừng kinh doanh.
                            </div>
                        @endif

                        <!-- TIỆN ÍCH PHỤ (Yêu thích, So sánh, Chia sẻ) -->
                        <div class="secondary-utilities-row d-flex align-items-center gap-4 pt-2 border-top">
                            <button type="button" class="util-action-btn" id="btn-toggle-wishlist">
                                <i class="far fa-heart me-1.5 icon-heart"></i>
                                <span>Thêm vào yêu thích</span>
                            </button>
                            <button type="button" class="util-action-btn" id="btn-toggle-compare">
                                <i class="fas fa-balance-scale me-1.5"></i>
                                <span>So sánh</span>
                            </button>
                            <button type="button" class="util-action-btn" id="btn-share-product">
                                <i class="fas fa-share-alt me-1.5"></i>
                                <span>Chia sẻ</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. TABS SECTION (Mô tả sản phẩm, Thông số kỹ thuật, Đánh giá, Hỏi đáp) -->
        <div class="product-tabs-container bg-white rounded-4 p-4 p-lg-5 shadow-sm mb-5">
            <!-- Tab Headers -->
            <ul class="nav nav-tabs custom-underline-tabs mb-4" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-desc-tab" data-bs-toggle="tab" data-bs-target="#pills-desc" type="button" role="tab" aria-controls="pills-desc" aria-selected="true">
                        Mô tả sản phẩm
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-specs-tab" data-bs-toggle="tab" data-bs-target="#pills-specs" type="button" role="tab" aria-controls="pills-specs" aria-selected="false">
                        Thông số kỹ thuật
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-reviews-tab" data-bs-toggle="tab" data-bs-target="#pills-reviews" type="button" role="tab" aria-controls="pills-reviews" aria-selected="false">
                        Đánh giá ({{ $reviewsCount }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-faq-tab" data-bs-toggle="tab" data-bs-target="#pills-faq" type="button" role="tab" aria-controls="pills-faq" aria-selected="false">
                        Hỏi & đáp
                    </button>
                </li>
            </ul>

            <!-- Tab Content Panes -->
            <div class="tab-content" id="productTabsContent">
                <!-- TAB 1: MÔ TẢ SẢN PHẨM -->
                <div class="tab-pane fade show active" id="pills-desc" role="tabpanel" aria-labelledby="pills-desc-tab">
                    <div class="row g-4 g-lg-5">
                        <!-- Cột Trái: Trải nghiệm âm thanh & 4 Thẻ tính năng -->
                        <div class="col-lg-8">
                            <div class="desc-main-content">
                                <h3 class="feature-headline fw-bold text-dark mb-3">
                                    Trải nghiệm âm thanh đột phá
                                </h3>
                                <p class="desc-intro-text text-muted mb-4 line-height-lg">
                                    {{ $product->description ? $product->description : 'AirPods Pro 2 (USB-C) mang đến chất lượng âm thanh vượt trội với chip H2 mạnh mẽ, khả năng chống ồn chủ động (ANC) tối ưu và âm thanh không gian cá nhân hóa. Thiết kế in-ear thoải mái, điều khiển cảm ứng thông minh và thời lượng pin ấn tượng giúp bạn tận hưởng âm nhạc mọi lúc, mọi nơi.' }}
                                </p>

                                <!-- 4 Thẻ tính năng nổi bật (2x2 grid hoặc 4 hàng đẹp mắt) -->
                                <div class="row g-3 feature-highlights-grid mt-2">
                                    <div class="col-md-6">
                                        <div class="feature-card-item d-flex align-items-center p-3 rounded-4">
                                            <div class="feature-icon-circle me-3">
                                                <i class="fas fa-wave-square"></i>
                                            </div>
                                            <div class="feature-text-block">
                                                <div class="fw-bold text-dark feature-title">Chống ồn chủ động (ANC)</div>
                                                <div class="small text-muted">thế hệ mới</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-card-item d-flex align-items-center p-3 rounded-4">
                                            <div class="feature-icon-circle me-3">
                                                <i class="fas fa-headphones-alt"></i>
                                            </div>
                                            <div class="feature-text-block">
                                                <div class="fw-bold text-dark feature-title">Âm thanh không gian</div>
                                                <div class="small text-muted">cá nhân hóa</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-card-item d-flex align-items-center p-3 rounded-4">
                                            <div class="feature-icon-circle me-3">
                                                <i class="fas fa-suitcase"></i>
                                            </div>
                                            <div class="feature-text-block">
                                                <div class="fw-bold text-dark feature-title">Thời lượng pin</div>
                                                <div class="small text-muted">lên đến 30 giờ</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="feature-card-item d-flex align-items-center p-3 rounded-4">
                                            <div class="feature-icon-circle me-3">
                                                <i class="fas fa-tint"></i>
                                            </div>
                                            <div class="feature-text-block">
                                                <div class="fw-bold text-dark feature-title">Kháng nước, mồ hôi</div>
                                                <div class="small text-muted">chuẩn IP54</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cột Phải: Khung Thông số kỹ thuật nổi bật -->
                        <div class="col-lg-4">
                            <div class="specs-highlight-card border rounded-4 p-4 bg-light-subtle">
                                <h5 class="specs-card-title fw-bold text-dark mb-3 pb-2 border-bottom">
                                    Thông số kỹ thuật nổi bật
                                </h5>
                                <div class="specs-list-table">
                                    <div class="spec-row d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Thương hiệu</span>
                                        <span class="fw-semibold text-dark">{{ $product->brand->TenThuongHieu ?? 'Apple' }}</span>
                                    </div>
                                    <div class="spec-row d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Model</span>
                                        <span class="fw-semibold text-dark">{{ $product->name }}</span>
                                    </div>
                                    <div class="spec-row d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Chip</span>
                                        <span class="fw-semibold text-dark">{{ $product->style ?? 'Apple H2' }}</span>
                                    </div>
                                    <div class="spec-row d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Chống ồn</span>
                                        <span class="fw-semibold text-dark">Chủ động (ANC)</span>
                                    </div>
                                    <div class="spec-row d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Thời lượng pin</span>
                                        <span class="fw-semibold text-dark">Lên đến 30 giờ (kèm hộp sạc)</span>
                                    </div>
                                    <div class="spec-row d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Kết nối</span>
                                        <span class="fw-semibold text-dark">Bluetooth 5.3</span>
                                    </div>
                                    <div class="spec-row d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Kháng nước</span>
                                        <span class="fw-semibold text-dark">IP54</span>
                                    </div>
                                    <div class="spec-row d-flex justify-content-between py-2">
                                        <span class="text-muted">Cổng sạc</span>
                                        <span class="fw-semibold text-dark">USB-C</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: THÔNG SỐ KỸ THUẬT ĐẦY ĐỦ -->
                <div class="tab-pane fade" id="pills-specs" role="tabpanel" aria-labelledby="pills-specs-tab">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle table-hover rounded-3 overflow-hidden">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;">Tên sản phẩm</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th>Danh mục</th>
                                    <td>{{ $product->category->name ?? 'Phụ kiện công nghệ' }}</td>
                                </tr>
                                <tr>
                                    <th>Thương hiệu</th>
                                    <td>{{ $product->brand->TenThuongHieu ?? 'Apple' }}</td>
                                </tr>
                                <tr>
                                    <th>Kiểu dáng / Dòng</th>
                                    <td>{{ $product->style ?? 'In-ear / True Wireless' }}</td>
                                </tr>
                                <tr>
                                    <th>Trọng lượng</th>
                                    <td>{{ $product->weight ? $product->weight . ' gram' : '50.8 gram' }}</td>
                                </tr>
                                <tr>
                                    <th>Thời gian bảo hành</th>
                                    <td>12 tháng chính hãng tại tất cả TTBH ủy quyền</td>
                                </tr>
                                <tr>
                                    <th>Hộp sản phẩm gồm</th>
                                    <td>Tai nghe, Hộp sạc MagSafe (USB-C), Đệm tai silicon 4 kích cỡ (XS, S, M, L), Cáp sạc USB-C, Sách hướng dẫn</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: ĐÁNH GIÁ & NHẬN XÉT -->
                <div class="tab-pane fade" id="pills-reviews" role="tabpanel" aria-labelledby="pills-reviews-tab">
                    <div class="row g-4 g-lg-5">
                        <!-- Cột Trái: Điểm đánh giá & Biểu đồ sao -->
                        <div class="col-lg-4">
                            <div class="rating-overview-card p-4 rounded-4 border bg-light-subtle text-center mb-4">
                                <h1 class="display-3 fw-bolder text-warning mb-0">{{ $avgRating }}</h1>
                                <div class="text-warning fs-5 my-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= floor($avgRating))
                                            <i class="fas fa-star"></i>
                                        @elseif ($i - $avgRating < 1 && $i - $avgRating > 0)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="text-muted small fw-medium mb-3">
                                    Dựa trên {{ $reviewsCount }} nhận xét từ khách hàng
                                </div>

                                <!-- Biểu đồ thanh tỉ lệ sao -->
                                <div class="rating-bars-list text-start">
@for ($star = 5; $star >= 1; $star--)
@php($percentage = $reviewsCount ? round($reviewsList->where('rating', $star)->count() / $reviewsCount * 100) : 0)
<div class="d-flex align-items-center gap-2 mb-2 small"><span>{{ $star }} ★</span><div class="progress flex-grow-1" style="height:6px"><div class="progress-bar bg-warning" style="width:{{ $percentage }}%"></div></div><span>{{ $percentage }}%</span></div>
@endfor
</div>
                            </div>
                        </div>

                        <!-- Cột Phải: Form viết đánh giá & Danh sách nhận xét -->
                        <div class="col-lg-8">
                            <!-- FORM GỬI ĐÁNH GIÁ MỚI -->
                            <div class="card border rounded-4 p-4 mb-4 shadow-sm bg-white" id="review-form-card">
                                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                    <i class="fas fa-pen-alt me-2 text-warning"></i> Viết đánh giá của bạn
                                </h5>

                                <form id="formSubmitReview" action="{{ route('product.review.store', $product->id) }}" method="POST">
                                    @csrf
                                    <!-- Chọn số sao tương tác -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-dark small mb-1">
                                            Mức độ hài lòng: <span id="star-rating-label" class="text-warning fw-bold">Tuyệt vời (5 sao)</span>
                                        </label>
                                        <div class="star-rating-selector d-flex align-items-center gap-2 fs-3 text-warning" id="interactive-star-picker" style="cursor: pointer;">
                                            <i class="fas fa-star star-pick" data-val="1" title="Rất tệ (1 sao)"></i>
                                            <i class="fas fa-star star-pick" data-val="2" title="Tệ (2 sao)"></i>
                                            <i class="fas fa-star star-pick" data-val="3" title="Bình thường (3 sao)"></i>
                                            <i class="fas fa-star star-pick" data-val="4" title="Hài lòng (4 sao)"></i>
                                            <i class="fas fa-star star-pick" data-val="5" title="Tuyệt vời (5 sao)"></i>
                                        </div>
                                        <input type="hidden" name="rating" id="review-rating-input" value="5">
                                    </div>

                                    <!-- Thông tin tài khoản đánh giá (Tự động lấy tên tài khoản, không cần nhập) -->
                                    @if (Auth::check())
                                        <div class="mb-3 p-2.5 px-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-user-circle text-primary fs-5"></i>
                                                <span class="small text-muted">Đánh giá với tài khoản: <strong class="text-dark">{{ Auth::user()->name ?? Auth::user()->email }}</strong></span>
                                            </div>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill small">
                                                <i class="fas fa-check-circle me-1"></i>Đã đăng nhập
                                            </span>
                                        </div>
                                    @else
                                        <div class="mb-3 p-2.5 px-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-user-circle text-secondary fs-5"></i>
                                                <span class="small text-muted">Đánh giá với tư cách: <strong class="text-dark">Khách hàng</strong></span>
                                            </div>
                                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-orange py-0 px-2 fw-semibold" style="font-size: 12px;">
                                                <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
                                            </a>
                                        </div>
                                    @endif

                                    <!-- Nội dung nhận xét -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-dark small mb-1">Cảm nhận chi tiết</label>
                                        <textarea class="form-control" name="comment" id="review-comment-input" rows="3" placeholder="Chia sẻ cảm nhận của bạn về chất âm, thiết kế, thời lượng pin, độ thoải mái khi đeo..." required></textarea>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Đánh giá của bạn sẽ giúp người mua khác có trải nghiệm tốt hơn.</small>
                                        <button type="submit" class="btn btn-solid-orange text-white px-4 py-2 fw-bold" id="btn-send-review">
                                            <i class="fas fa-paper-plane me-1.5"></i> Gửi đánh giá
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- DANH SÁCH NHẬN XÉT THỰC TẾ & MẪU -->
                            <div class="reviews-feed-container">
                                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                                    Nhận xét từ khách hàng (<span id="reviews-feed-counter">{{ $reviewsCount }}</span>)
                                </h6>

                                <div id="reviews-feed-list">
                                    <!-- Đánh giá từ Database -->
                                    @foreach ($reviewsList as $rev)
                                        <div class="review-item pb-3 mb-3 border-bottom">
                                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                                <div class="d-flex align-items-center gap-2">
                                                    <strong class="text-dark">{{ $rev->author_name }}</strong>
                                                    @if ($rev->is_verified_purchase)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 small">
                                                            <i class="fas fa-check-circle me-1"></i>Đã mua hàng
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-muted small">{{ $rev->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="text-warning small mb-1">
                                                @for ($s = 1; $s <= 5; $s++)
                                                    <i class="{{ $s <= $rev->rating ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>
                                            <p class="text-muted mb-0 small">{{ $rev->comment }}</p>
                                        </div>
                                    @endforeach

                                    @if ($reviewsList->isEmpty())<p class="text-muted">Chưa có đánh giá. Hãy chia sẻ trải nghiệm của bạn!</p>@endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: HỎI & ĐÁP -->
                <div class="tab-pane fade" id="pills-faq" role="tabpanel" aria-labelledby="pills-faq-tab">
                    <div class="row g-4 g-lg-5">
                        <div class="col-lg-8">
                            <h5 class="fw-bold text-dark mb-3">Câu hỏi thường gặp về sản phẩm</h5>
                            <div class="accordion custom-faq-accordion mb-4" id="faqAccordion">
                                <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                                    <h2 class="accordion-header" id="faqHeadingOne">
                                        <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne">
                                            Sản phẩm này có được bảo hành chính hãng không?
                                        </button>
                                    </h2>
                                    <div id="faqCollapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted small">
                                            Tất cả các sản phẩm bán tại cửa hàng đều là hàng chính hãng 100%, được bảo hành 12 tháng tại các trung tâm bảo hành được ủy quyền trên toàn quốc. Khách hàng chỉ cần đọc số điện thoại mua hàng để được bảo hành điện tử.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                                    <h2 class="accordion-header" id="faqHeadingTwo">
                                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo">
                                            Thời gian giao hàng tiêu chuẩn là bao lâu?
                                        </button>
                                    </h2>
                                    <div id="faqCollapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted small">
                                            Thời gian giao hàng từ 1-2 ngày đối với khu vực nội thành Hà Nội & TP. Hồ Chí Minh, và từ 2-4 ngày đối với các tỉnh thành khác qua đơn vị vận chuyển Giao Hàng Nhanh (GHN). Hỗ trợ giao hỏa tốc 2 giờ tại nội thành.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border rounded-3 mb-2 overflow-hidden">
                                    <h2 class="accordion-header" id="faqHeadingThree">
                                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree">
                                            Chính sách đổi trả trong trường hợp sản phẩm có lỗi?
                                        </button>
                                    </h2>
                                    <div id="faqCollapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted small">
                                            Cửa hàng hỗ trợ đổi mới 1-1 miễn phí trong vòng 7 ngày đầu tiên nếu sản phẩm phát sinh lỗi kỹ thuật từ nhà sản xuất. Sản phẩm đổi trả cần giữ nguyên hộp và đầy đủ phụ kiện.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border rounded-3 overflow-hidden">
                                    <h2 class="accordion-header" id="faqHeadingFour">
                                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFour">
                                            Sản phẩm có tương thích với thiết bị Android hoặc máy tính Windows không?
                                        </button>
                                    </h2>
                                    <div id="faqCollapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted small">
                                            Hoàn toàn tương thích! Sản phẩm kết nối chuẩn Bluetooth 5.3 chuẩn quốc tế, hoạt động ổn định với mọi điện thoại Android, máy tính Windows cũng như toàn bộ hệ sinh thái Apple.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Đặt câu hỏi nhanh -->
                            <div class="p-4 rounded-4 border bg-light-subtle">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-question-circle text-primary me-1.5"></i> Bạn có câu hỏi khác?</h6>
                                <p class="text-muted small mb-3">Nhập thắc mắc của bạn bên dưới, đội ngũ tư vấn sẽ phản hồi giải đáp sớm nhất.</p>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="faq-quick-input" placeholder="Nhập câu hỏi của bạn về sản phẩm này...">
                                    <button class="btn btn-outline-orange px-3 fw-semibold" type="button" id="faq-quick-submit">
                                        Gửi câu hỏi
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Cột Phải: Khung tư vấn trực tiếp 24/7 -->
                        <div class="col-lg-4">
                            <div class="support-contact-card border rounded-4 p-4 bg-white shadow-sm">
                                <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                                    <i class="fas fa-headset text-primary me-2"></i> Tư vấn & Hỗ trợ kỹ thuật
                                </h6>
                                <ul class="list-unstyled mb-4 small">
                                    <li class="mb-3 d-flex align-items-start gap-2">
                                        <i class="fas fa-phone-alt text-success mt-1"></i>
                                        <div>
                                            <div class="fw-bold text-dark">Hotline Miễn Cước</div>
                                            <span class="text-danger fw-bold fs-6">1900 1234</span>
                                            <div class="text-muted small">(8:00 - 21:30 hàng ngày)</div>
                                        </div>
                                    </li>
                                    <li class="mb-3 d-flex align-items-start gap-2">
                                        <i class="fas fa-comment-dots text-primary mt-1"></i>
                                        <div>
                                            <div class="fw-bold text-dark">Chat Zalo / Messenger</div>
                                            <span class="text-muted">Hỗ trợ tư vấn phản hồi dưới 5 phút</span>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-start gap-2">
                                        <i class="fas fa-shield-alt text-warning mt-1"></i>
                                        <div>
                                            <div class="fw-bold text-dark">Bảo hành chính hãng</div>
                                            <span class="text-muted">Tra cứu bảo hành theo SĐT</span>
                                        </div>
                                    </li>
                                </ul>
                                <a href="tel:19001234" class="btn btn-solid-orange text-white w-100 py-2 fw-bold rounded-pill text-center d-block">
                                    <i class="fas fa-phone-volume me-1.5"></i> Gọi tư vấn ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL LIGHTBOX PHÓNG TO ẢNH -->
<div class="modal fade" id="imageZoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">{{ $product->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img src="" id="modal-zoomed-img" class="img-fluid rounded-3" style="max-height: 70vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<!-- MODAL VIDEO TRẢI NGHIỆM -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow overflow-hidden">
            <div class="modal-header border-0 bg-dark text-white pb-2">
                <h5 class="modal-title fw-semibold">Video trải nghiệm: {{ $product->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body p-0 bg-black">
                <div class="ratio ratio-16x9">
                    <iframe id="videoIframe" src="" title="Video sản phẩm" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* =========================================================
       CSS TÙY CHỈNH THEO THIẾT KẾ MẪU (ORANGE E-COMMERCE THEME)
       ========================================================= */
    :root {
        --app-orange: #ff5b00;
        --app-orange-hover: #e04f00;
        --app-orange-light: #fff3eb;
        --app-text-dark: #1e293b;
        --app-text-muted: #64748b;
        --app-border-color: #f1f5f9;
    }

    .product-detail-page {
        background-color: #f8fafc;
        min-height: 80vh;
    }

    /* Breadcrumbs */
    .custom-breadcrumb-nav .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
        font-size: 11px;
        color: #94a3b8;
        padding: 0 8px;
    }
    .custom-breadcrumb-nav .breadcrumb-link {
        color: #64748b;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.2s ease;
    }
    .custom-breadcrumb-nav .breadcrumb-link:hover {
        color: var(--app-orange);
    }
    .custom-breadcrumb-nav .breadcrumb-item.active {
        color: #1e293b;
        font-weight: 500;
        font-size: 14px;
    }

    /* Hero Card */
    .product-hero-card {
        border: 1px solid #eef2f6;
    }

    /* Gallery Main Viewport */
    .main-image-viewport {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        min-height: 420px;
        max-height: 480px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .image-inner-container {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .main-img-element {
        max-height: 380px;
        width: auto;
        object-fit: contain;
        transition: transform 0.3s ease, opacity 0.25s ease;
    }
    .main-image-viewport:hover .main-img-element {
        transform: scale(1.03);
    }

    /* Badge Mới */
    .badge-tag-new {
        background-color: var(--app-orange-light);
        color: var(--app-orange);
        font-size: 13px;
        font-weight: 700;
        padding: 4px 14px;
        border-radius: 50rem;
        letter-spacing: 0.3px;
        z-index: 5;
    }

    /* Zoom button */
    .btn-zoom-expand {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        cursor: pointer;
        z-index: 5;
        transition: all 0.2s ease;
    }
    .btn-zoom-expand:hover {
        background-color: var(--app-orange);
        color: #ffffff;
        border-color: var(--app-orange);
    }

    /* Gallery Navigation Arrows */
    .gallery-nav-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        cursor: pointer;
        z-index: 5;
        transition: all 0.2s ease;
        opacity: 0.85;
    }
    .gallery-nav-arrow:hover {
        opacity: 1;
        background-color: var(--app-orange);
        color: #ffffff;
        border-color: var(--app-orange);
    }
    .gallery-nav-arrow.prev { left: 14px; }
    .gallery-nav-arrow.next { right: 14px; }

    /* Thumbnails */
    .gallery-thumbnails-strip {
        overflow-x: auto;
        padding-bottom: 6px;
    }
    .thumb-item {
        position: relative;
        width: 76px;
        height: 76px;
        flex-shrink: 0;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        background-color: #ffffff;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.25s ease;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .thumb-item:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }
    .thumb-item.active {
        border: 2px solid var(--app-orange) !important;
        box-shadow: 0 0 0 2px rgba(255, 91, 0, 0.15);
    }
    .thumb-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* Video overlay thumbnail */
    .video-thumb {
        background-color: #0f172a;
    }
    .video-thumb .thumb-img {
        opacity: 0.6;
    }
    .video-overlay-play {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,0.3);
    }
    .play-icon-circle {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.9);
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        padding-left: 2px;
    }

    /* Right Info Panel */
    .product-title-heading {
        font-size: 26px;
        line-height: 1.35;
        letter-spacing: -0.3px;
    }
    .product-subtitle-tagline {
        font-size: 14.5px;
        line-height: 1.5;
    }

    /* Pricing Card */
    .product-pricing-card {
        background-color: #fffaf5;
        border: 1px dashed #ffd8bf;
    }
    .current-price-val {
        color: #ff4d00;
        font-size: 30px;
        letter-spacing: -0.5px;
    }
    .original-price-val {
        font-size: 16px;
        text-decoration: line-through;
    }
    .saving-badge-item {
        background-color: #fee2e2;
        color: #dc2626;
        font-weight: 700;
        font-size: 13px;
        padding: 5px 14px;
        border-radius: 50rem;
        display: inline-flex;
        align-items: center;
    }

    /* Variant & Color Buttons */
    .variant-btn, .color-btn {
        background-color: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 7px 18px;
        font-size: 14px;
        font-weight: 500;
        color: #334155;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    .variant-btn:hover, .color-btn:hover {
        border-color: #94a3b8;
    }
    .variant-btn.active {
        border-color: var(--app-orange);
        color: var(--app-orange);
        background-color: var(--app-orange-light);
        font-weight: 600;
    }
    .color-btn.active {
        border-color: var(--app-orange);
        color: var(--app-orange);
        background-color: var(--app-orange-light);
        font-weight: 600;
    }
    .color-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
    }
    .white-dot {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
    }
    .gray-dot {
        background-color: #475569;
    }

    /* Interactive Star Picker */
    .star-rating-selector {
        user-select: none;
    }
    .star-rating-selector .star-pick {
        cursor: pointer;
        padding: 4px 6px;
        transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-block;
    }
    .star-rating-selector .star-pick:hover {
        transform: scale(1.3);
    }

    /* Stepper Counter */
    .stepper-counter-box {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        background-color: #ffffff;
        width: 130px;
    }
    .stepper-btn {
        width: 38px;
        height: 38px;
        border: none;
        background: #f8fafc;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .stepper-btn:hover {
        background-color: #e2e8f0;
        color: #0f172a;
    }
    .stepper-input {
        width: 54px;
        height: 38px;
        border: none;
        text-align: center;
        font-weight: 600;
        color: #1e293b;
        outline: none;
    }
    /* Hide number input spinners */
    .stepper-input::-webkit-outer-spin-button,
    .stepper-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Main CTA Buttons */
    .btn-outline-orange {
        border: 2px solid var(--app-orange);
        color: var(--app-orange);
        background-color: #ffffff;
        border-radius: 10px;
        font-size: 16px;
        transition: all 0.25s ease;
    }
    .btn-outline-orange:hover {
        background-color: var(--app-orange-light);
        color: var(--app-orange-hover);
        border-color: var(--app-orange-hover);
        transform: translateY(-1px);
    }
    .btn-solid-orange {
        background: linear-gradient(135deg, #ff6a00 0%, #ff4500 100%);
        border: none;
        border-radius: 10px;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(255, 91, 0, 0.3);
        transition: all 0.25s ease;
    }
    .btn-solid-orange:hover {
        background: linear-gradient(135deg, #ff5b00 0%, #e03e00 100%);
        box-shadow: 0 6px 20px rgba(255, 91, 0, 0.4);
        transform: translateY(-1px);
    }

    /* Secondary Utility Buttons */
    .util-action-btn {
        background: none;
        border: none;
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        padding: 6px 0;
        display: inline-flex;
        align-items: center;
        transition: color 0.2s ease;
    }
    .util-action-btn:hover {
        color: var(--app-orange);
    }

    /* Custom Underline Tabs */
    .custom-underline-tabs {
        border-bottom: 2px solid #f1f5f9;
        gap: 24px;
    }
    .custom-underline-tabs .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        font-size: 16px;
        padding: 12px 4px;
        position: relative;
        background: transparent;
        transition: color 0.2s ease;
    }
    .custom-underline-tabs .nav-link:hover {
        color: var(--app-orange);
    }
    .custom-underline-tabs .nav-link.active {
        color: var(--app-orange);
        background: transparent;
    }
    .custom-underline-tabs .nav-link.active::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background-color: var(--app-orange);
        border-radius: 3px 3px 0 0;
    }

    /* 4 Feature highlight cards */
    .feature-card-item {
        background-color: #ffffff;
        border: 1px solid #edf2f7;
        transition: all 0.25s ease;
    }
    .feature-card-item:hover {
        border-color: #fed7aa;
        background-color: #fffaf5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 91, 0, 0.06);
    }
    .feature-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background-color: var(--app-orange-light);
        color: var(--app-orange);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .feature-title {
        font-size: 14px;
        line-height: 1.3;
    }

    /* Specs highlight card */
    .specs-highlight-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
    }
    .specs-card-title {
        font-size: 16px;
    }
    .spec-row {
        font-size: 13.5px;
    }

    @media (max-width: 991px) {
        .product-title-heading { font-size: 22px; }
        .current-price-val { font-size: 24px; }
        .main-image-viewport { min-height: 320px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. XỬ LÝ MEDIA GALLERY & THUMBNAILS ---
        const mainImg = document.getElementById('main-product-image');
        const thumbs = document.querySelectorAll('.thumb-item');
        const prevBtn = document.getElementById('gallery-prev-btn');
        const nextBtn = document.getElementById('gallery-next-btn');
        const zoomBtn = document.getElementById('btn-zoom-image');
        const modalZoomedImg = document.getElementById('modal-zoomed-img');
        const videoModalEl = document.getElementById('videoModal');
        const videoIframe = document.getElementById('videoIframe');

        let currentIndex = 0;

        function setActiveThumbnail(index) {
            if (index < 0 || index >= thumbs.length) return;
            currentIndex = index;

            thumbs.forEach(t => t.classList.remove('active'));
            const targetThumb = thumbs[index];
            if (!targetThumb) return;

            targetThumb.classList.add('active');

            const itemType = targetThumb.getAttribute('data-type');
            const imgSrc = targetThumb.getAttribute('data-src');
            const videoSrc = targetThumb.getAttribute('data-video');

            if (itemType === 'video' && videoSrc) {
                // Mở modal video nếu click vào thumb video
                if (window.bootstrap && window.bootstrap.Modal) {
                    const vModal = new bootstrap.Modal(videoModalEl);
                    videoIframe.src = videoSrc + "?autoplay=1";
                    vModal.show();
                }
            } else if (mainImg && imgSrc) {
                mainImg.style.opacity = '0.3';
                setTimeout(() => {
                    mainImg.src = imgSrc;
                    mainImg.style.opacity = '1';
                }, 150);
            }
        }

        thumbs.forEach((thumb, idx) => {
            thumb.addEventListener('click', function () {
                setActiveThumbnail(idx);
            });
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                let nextIdx = currentIndex - 1;
                if (nextIdx < 0) nextIdx = thumbs.length - 1;
                setActiveThumbnail(nextIdx);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                let nextIdx = currentIndex + 1;
                if (nextIdx >= thumbs.length) nextIdx = 0;
                setActiveThumbnail(nextIdx);
            });
        }

        // Dừng video khi đóng modal video
        if (videoModalEl) {
            videoModalEl.addEventListener('hidden.bs.modal', function () {
                if (videoIframe) videoIframe.src = '';
            });
        }

        // Lightbox phóng to ảnh
        if (zoomBtn && modalZoomedImg) {
            zoomBtn.addEventListener('click', function () {
                modalZoomedImg.src = mainImg.src;
                if (window.bootstrap && window.bootstrap.Modal) {
                    const zoomModal = new bootstrap.Modal(document.getElementById('imageZoomModal'));
                    zoomModal.show();
                }
            });
        }

        // --- 2. BỘ ĐẾM SỐ LƯỢNG (STEPPER) ---
        const qtyInput = document.getElementById('product-quantity');
        const btnMinus = document.getElementById('btn-decrement-qty');
        const btnPlus = document.getElementById('btn-increment-qty');

        if (qtyInput) {
            const minQty = 1;
            const maxQty = parseInt(qtyInput.getAttribute('max')) || 9999;

            if (btnMinus) {
                btnMinus.addEventListener('click', function () {
                    let val = parseInt(qtyInput.value) || 1;
                    if (val > minQty) {
                        qtyInput.value = val - 1;
                        qtyInput.dispatchEvent(new Event('change'));
                    }
                });
            }

            if (btnPlus) {
                btnPlus.addEventListener('click', function () {
                    let val = parseInt(qtyInput.value) || 1;
                    if (val < maxQty) {
                        qtyInput.value = val + 1;
                        qtyInput.dispatchEvent(new Event('change'));
                    } else if (window.cartManager) {
                        window.cartManager.showMessage(`Số lượng tối đa trong kho là ${maxQty}`, 'warning');
                    }
                });
            }

            qtyInput.addEventListener('change', function () {
                let val = parseInt(this.value) || 1;
                if (val < minQty) this.value = minQty;
                if (val > maxQty) {
                    this.value = maxQty;
                    if (window.cartManager) {
                        window.cartManager.showMessage(`Số lượng tối đa trong kho là ${maxQty}`, 'warning');
                    }
                }
            });
        }

        // --- 3. LỰA CHỌN PHIÊN BẢN & MÀU SẮC ---
        document.querySelectorAll('.variant-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.variant-btn').forEach(b => {
                    b.classList.remove('active');
                    const icon = b.querySelector('.check-icon');
                    if (icon) icon.classList.add('d-none');
                });
                this.classList.add('active');
                const activeIcon = this.querySelector('.check-icon');
                if (activeIcon) activeIcon.classList.remove('d-none');

                const hiddenVariant = document.getElementById('selected-variant');
                if (hiddenVariant) {
                    hiddenVariant.value = this.getAttribute('data-variant') || '';
                }
            });
        });

        document.querySelectorAll('.color-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const hiddenColor = document.getElementById('selected-color');
                if (hiddenColor) {
                    hiddenColor.value = this.getAttribute('data-color') || '';
                }
            });
        });

        // --- 4. CÁC HÀNH ĐỘNG PHỤ (YÊU THÍCH, SO SÁNH, CHIA SẺ) ---
        const btnWishlist = document.getElementById('btn-toggle-wishlist');
        if (btnWishlist) {
            btnWishlist.addEventListener('click', function () {
                const heart = this.querySelector('.icon-heart');
                if (heart.classList.contains('far')) {
                    heart.classList.remove('far');
                    heart.classList.add('fas');
                    if (window.cartManager) window.cartManager.showMessage('Đã thêm sản phẩm vào danh sách yêu thích!', 'success');
                } else {
                    heart.classList.remove('fas');
                    heart.classList.add('far');
                    if (window.cartManager) window.cartManager.showMessage('Đã xóa khỏi danh sách yêu thích!', 'info');
                }
            });
        }

        const btnCompare = document.getElementById('btn-toggle-compare');
        if (btnCompare) {
            btnCompare.addEventListener('click', function () {
                if (window.cartManager) {
                    window.cartManager.showMessage('Đã thêm vào danh sách so sánh sản phẩm!', 'info');
                } else {
                    alert('Đã thêm vào danh sách so sánh sản phẩm!');
                }
            });
        }

        const btnShare = document.getElementById('btn-share-product');
        if (btnShare) {
            btnShare.addEventListener('click', function () {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(window.location.href).then(() => {
                        if (window.cartManager) {
                            window.cartManager.showMessage('Đã sao chép liên kết sản phẩm vào bộ nhớ tạm!', 'success');
                        } else {
                            alert('Đã sao chép liên kết sản phẩm!');
                        }
                    }).catch(() => {
                        alert('Liên kết sản phẩm: ' + window.location.href);
                    });
                } else {
                    alert('Liên kết sản phẩm: ' + window.location.href);
                }
            });
        }

        // --- 5. ĐÁNH GIÁ SẢN PHẨM (STAR PICKER & AJAX FORM) ---
        const starContainer = document.getElementById('interactive-star-picker');
        const starInput = document.getElementById('review-rating-input');
        const starLabel = document.getElementById('star-rating-label');
        const ratingTexts = {
            1: 'Rất tệ (1 sao)',
            2: 'Tệ (2 sao)',
            3: 'Bình thường (3 sao)',
            4: 'Hài lòng (4 sao)',
            5: 'Tuyệt vời (5 sao)'
        };

        if (starContainer && starInput) {
            const stars = starContainer.querySelectorAll('.star-pick');

            function renderStars(rating) {
                stars.forEach(s => {
                    const val = parseInt(s.getAttribute('data-val'), 10);
                    if (val <= rating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
                if (starLabel) {
                    starLabel.textContent = ratingTexts[rating] || (rating + ' sao');
                }
            }

            // Khởi tạo hiển thị ban đầu (5 sao)
            const initialRating = parseInt(starInput.value, 10) || 5;
            renderStars(initialRating);

            // Bắt sự kiện click vào sao
            starContainer.addEventListener('click', function (e) {
                const star = e.target.closest('.star-pick');
                if (!star) return;
                e.preventDefault();
                e.stopPropagation();
                const val = parseInt(star.getAttribute('data-val'), 10);
                if (val >= 1 && val <= 5) {
                    starInput.value = val;
                    renderStars(val);
                }
            });

            // Rê chuột xem trước số sao
            starContainer.addEventListener('mouseover', function (e) {
                const star = e.target.closest('.star-pick');
                if (!star) return;
                const val = parseInt(star.getAttribute('data-val'), 10);
                if (val >= 1 && val <= 5) {
                    renderStars(val);
                }
            });

            // Rời chuột khôi phục số sao đã chọn
            starContainer.addEventListener('mouseleave', function () {
                const currentVal = parseInt(starInput.value, 10) || 5;
                renderStars(currentVal);
            });
        }

        // Xử lý gửi Form Đánh giá qua AJAX
        const formReview = document.getElementById('formSubmitReview');
        if (window.location.hash === '#pills-reviews') document.getElementById('pills-reviews-tab')?.click();
        const btnSendReview = document.getElementById('btn-send-review');
        const reviewsList = document.getElementById('reviews-feed-list');
        const reviewsCounter = document.getElementById('reviews-feed-counter');

        if (formReview) {
            formReview.addEventListener('submit', function (e) {
                e.preventDefault();

                if (btnSendReview) {
                    btnSendReview.disabled = true;
                    btnSendReview.innerHTML = '<i class="fas fa-spinner fa-spin me-1.5"></i> Đang gửi...';
                }

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        return res.json().then(errData => { throw errData; });
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        window.location.hash = 'pills-reviews';
                        window.location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi gửi đánh giá!');
                    }
                })
                .catch(err => {
                    console.error('Review submit error:', err);
                    let msg = 'Không thể gửi đánh giá, vui lòng thử lại sau!';
                    if (err && err.errors) {
                        const errList = Object.values(err.errors).flat();
                        if (errList.length > 0) msg = errList.join('\n');
                    } else if (err && err.message) {
                        msg = err.message;
                    }
                    if (window.cartManager && typeof window.cartManager.showMessage === 'function') {
                        window.cartManager.showMessage(msg, 'danger');
                    } else {
                        alert(msg);
                    }
                })
                .finally(() => {
                    if (btnSendReview) {
                        btnSendReview.disabled = false;
                        btnSendReview.innerHTML = '<i class="fas fa-paper-plane me-1.5"></i> Gửi đánh giá';
                    }
                });
            });
        }

        // --- 6. HỎI ĐÁP: GỬI CÂU HỎI NHANH ---
        const btnFaqQuick = document.getElementById('faq-quick-submit');
        const inputFaqQuick = document.getElementById('faq-quick-input');

        if (btnFaqQuick && inputFaqQuick) {
            function handleFaqQuickSubmit() {
                const questionText = inputFaqQuick.value.trim();
                if (!questionText) {
                    if (window.cartManager && typeof window.cartManager.showMessage === 'function') {
                        window.cartManager.showMessage('Vui lòng nhập nội dung câu hỏi!', 'warning');
                    } else {
                        alert('Vui lòng nhập nội dung câu hỏi!');
                    }
                    inputFaqQuick.focus();
                    return;
                }

                inputFaqQuick.value = '';
                const successMsg = 'Cảm ơn bạn! Câu hỏi đã được gửi, nhân viên tư vấn sẽ phản hồi giải đáp trong ít phút.';
                if (window.cartManager && typeof window.cartManager.showMessage === 'function') {
                    window.cartManager.showMessage(successMsg, 'success');
                } else {
                    alert(successMsg);
                }
            }

            btnFaqQuick.addEventListener('click', handleFaqQuickSubmit);
            inputFaqQuick.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleFaqQuickSubmit();
                }
            });
        }
    });
</script>
@endsection

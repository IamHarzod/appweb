@extends('layout.home_layout')
@section('home-content')

@php
    $catTitle = $currentCategory?->name ?? ($keyword ? 'Kết quả tìm kiếm cho: "' . $keyword . '"' : 'Tất cả sản phẩm');
    $catSubtitle = $currentCategory
        ? 'Khám phá các dòng ' . mb_strtolower($currentCategory->name) . ' chính hãng với giá tốt nhất'
        : 'Khám phá các sản phẩm công nghệ chính hãng hàng đầu với giá tốt nhất';

    // Tính toán danh sách thương hiệu và số lượng sản phẩm tương ứng
    $brandStats = [];
    foreach ($product as $p) {
        $bName = $p->brand?->TenThuongHieu;
        if ($bName) {
            $brandStats[$bName] = ($brandStats[$bName] ?? 0) + 1;
        }
    }
    // Nếu chưa có brand từ sản phẩm hiện tại, lấy từ danh sách $brands
    if (empty($brandStats) && isset($brands)) {
        foreach ($brands as $b) {
            $brandStats[$b->TenThuongHieu] = 0;
        }
    }

    // Tính toán các loại sản phẩm (style)
    $styleStats = [];
    foreach ($product as $p) {
        if (!empty($p->style)) {
            $styleStats[$p->style] = ($styleStats[$p->style] ?? 0) + 1;
        }
    }
    // Nếu danh mục là Máy ảnh mà chưa có style, cung cấp các gợi ý mặc định
    if (empty($styleStats) && $currentCategory && str_contains(mb_strtolower($currentCategory->name), 'máy ảnh')) {
        $styleStats = [
            'Máy ảnh Mirrorless' => 0,
            'Máy ảnh DSLR' => 0,
            'Máy quay phim' => 0,
            'Ống kính' => 0,
            'Phụ kiện' => 0,
        ];
    }

    // Khoảng giá lớn nhất và nhỏ nhất
    $minDbPrice = $product->min('price') ?? 0;
    $maxDbPrice = $product->max('price') ?? 100000000;
    if ($maxDbPrice <= 0) $maxDbPrice = 100000000;
    $maxSliderPrice = ceil($maxDbPrice / 1000000) * 1000000;
    if ($maxSliderPrice < 50000000) $maxSliderPrice = 100000000;
@endphp

<style>
    /* Tổng thể trang */
    .category-page-wrapper {
        background-color: #f8f9fa;
        min-height: 80vh;
        padding-top: 24px;
        padding-bottom: 60px;
    }

    /* Breadcrumb */
    .custom-breadcrumb {
        font-size: 13.5px;
        color: #71717a;
    }
    .custom-breadcrumb a {
        color: #71717a;
        text-decoration: none;
        transition: color 0.2s;
    }
    .custom-breadcrumb a:hover {
        color: #f37021;
    }
    .custom-breadcrumb .separator {
        margin: 0 8px;
        color: #d4d4d8;
    }

    /* Tiêu đề danh mục */
    .category-header-title {
        font-size: 28px;
        font-weight: 700;
        color: #18181b;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
    }
    .category-header-subtitle {
        font-size: 14px;
        color: #71717a;
        margin-bottom: 0;
    }
    .category-total-badge {
        font-size: 13px;
        color: #71717a;
    }

    /* Thanh Toolbar */
    .filter-toolbar {
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 14px;
        padding: 12px 18px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .toolbar-search-box {
        position: relative;
        flex-grow: 1;
        max-width: 420px;
        min-width: 240px;
    }
    .toolbar-search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #a1a1aa;
        font-size: 14px;
    }
    .toolbar-search-input {
        width: 100%;
        border-radius: 10px;
        border: 1px solid #e4e4e7;
        background: #fbfbfb;
        padding: 9px 14px 9px 38px;
        font-size: 13.5px;
        outline: none;
        transition: all 0.2s;
    }
    .toolbar-search-input:focus {
        background: #fff;
        border-color: #f37021;
        box-shadow: 0 0 0 3px rgba(243, 112, 33, 0.12);
    }

    .toolbar-sort-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .toolbar-sort-label {
        font-size: 13.5px;
        color: #71717a;
        white-space: nowrap;
    }
    .toolbar-sort-select {
        border-radius: 10px;
        border: 1px solid #e4e4e7;
        background: #fbfbfb;
        padding: 8px 14px;
        font-size: 13.5px;
        font-weight: 500;
        color: #27272a;
        cursor: pointer;
        outline: none;
    }
    .toolbar-sort-select:focus {
        border-color: #f37021;
    }

    /* Nút đổi dạng xem Grid / List */
    .view-toggle-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid #e4e4e7;
        background: #ffffff;
        color: #71717a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        transition: all 0.2s;
        cursor: pointer;
    }
    .view-toggle-btn:hover {
        color: #f37021;
        border-color: #f37021;
    }
    .view-toggle-btn.active {
        background: #f37021;
        border-color: #f37021;
        color: #ffffff;
    }

    /* Sidebar Bộ Lọc */
    .filter-sidebar-card {
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 16px;
        padding: 22px 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.02);
    }
    .filter-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        margin-bottom: 14px;
    }
    .filter-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #18181b;
        margin: 0;
    }
    .filter-section-header i {
        font-size: 12px;
        color: #71717a;
        transition: transform 0.25s;
    }
    .filter-section-header.collapsed i {
        transform: rotate(180deg);
    }

    /* Range slider màu cam */
    .price-range-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 6px;
        border-radius: 5px;
        background: #ffdec7;
        outline: none;
        margin: 12px 0 16px;
    }
    .price-range-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #f37021;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(243, 112, 33, 0.4);
        border: 2px solid #ffffff;
    }
    .price-range-slider::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #f37021;
        cursor: pointer;
        border: 2px solid #ffffff;
    }

    .price-inputs-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
    }
    .price-input-badge {
        flex: 1;
        background: #f4f4f5;
        border: 1px solid #e4e4e7;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: 13px;
        color: #27272a;
        font-weight: 600;
        text-align: center;
    }

    .price-preset-pill {
        display: inline-block;
        padding: 5px 10px;
        font-size: 12px;
        border-radius: 20px;
        background: #f4f4f5;
        color: #52525b;
        margin: 2px 2px 4px 0;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .price-preset-pill:hover,
    .price-preset-pill.active {
        background: #fff4eb;
        color: #f37021;
        border-color: #f37021;
        font-weight: 600;
    }

    /* Checkbox lọc */
    .filter-check-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        cursor: pointer;
    }
    .filter-check-item label {
        cursor: pointer;
        font-size: 13.5px;
        color: #3f3f46;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        user-select: none;
    }
    .filter-checkbox {
        width: 17px;
        height: 17px;
        border-radius: 4px;
        accent-color: #f37021;
        cursor: pointer;
    }
    .filter-count {
        font-size: 12.5px;
        color: #a1a1aa;
    }
    .filter-more-link {
        font-size: 13px;
        color: #f37021;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin-top: 4px;
    }
    .filter-more-link:hover {
        text-decoration: underline;
    }

    /* THẺ SẢN PHẨM HIỆN ĐẠI (Style theo ảnh mẫu) */
    .modern-product-card {
        background: #ffffff;
        border: 1px solid #edf0f5;
        border-radius: 16px;
        padding: 16px;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .modern-product-card:hover {
        border-color: #ffc49e;
        box-shadow: 0 10px 25px rgba(243, 112, 33, 0.12);
        transform: translateY(-3px);
    }

    /* Huy hiệu New / Discount & Trái tim */
    .card-top-badges {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
        z-index: 2;
    }
    .badge-pill-new {
        background: #f37021;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-pill-discount {
        background: #ef4444;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }
    .card-wishlist-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid #f4f4f5;
        background: #ffffff;
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .card-wishlist-btn:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        transform: scale(1.1);
    }
    .card-wishlist-btn.active i {
        font-weight: 900;
    }

    /* Khung ảnh sản phẩm */
    .modern-card-img-wrap {
        height: 175px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px 4px;
        margin-bottom: 12px;
        overflow: hidden;
        background-color: #ffffff;
    }
    .modern-card-img-wrap img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        transition: transform 0.4s ease;
    }
    .modern-product-card:hover .modern-card-img-wrap img {
        transform: scale(1.06);
    }

    /* Thông tin chữ trong card */
    .product-category-name {
        font-size: 12.5px;
        color: #a1a1aa;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .product-title-text {
        font-size: 14.5px;
        font-weight: 700;
        color: #18181b;
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 42px;
        line-height: 21px;
        margin-bottom: 6px;
        transition: color 0.2s;
    }
    .product-title-text:hover {
        color: #f37021;
    }

    /* Đánh giá sao */
    .product-rating-row {
        display: flex;
        align-items: center;
        gap: 3px;
        font-size: 12px;
        color: #f59e0b;
        margin-bottom: 6px;
    }
    .product-rating-count {
        color: #a1a1aa;
        font-size: 12px;
        margin-left: 3px;
    }

    /* Giá sản phẩm */
    .product-price-row {
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-bottom: 8px;
    }
    .product-old-price {
        font-size: 13px;
        color: #a1a1aa;
        text-decoration: line-through;
    }
    .product-sale-price {
        font-size: 17px;
        font-weight: 800;
        color: #ea580c;
        letter-spacing: -0.3px;
    }

    /* Tiện ích (Còn hàng, Freeship) */
    .product-perks-row {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 12px;
        margin-bottom: 14px;
    }
    .perk-in-stock {
        color: #16a34a;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .perk-free-ship {
        color: #71717a;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Nút hành động Mua hàng */
    .card-actions-row {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-card-cart {
        flex: 1;
        border: 1px solid #f37021;
        background: #ffffff;
        color: #f37021;
        border-radius: 8px;
        padding: 7px 6px;
        font-size: 12.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        white-space: nowrap;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-card-cart:hover {
        background: #fff4eb;
        color: #ea580c;
        border-color: #ea580c;
    }

    .btn-card-buy {
        flex: 1;
        background: #f37021;
        border: 1px solid #f37021;
        color: #ffffff;
        border-radius: 8px;
        padding: 7px 6px;
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        white-space: nowrap;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-card-buy:hover {
        background: #ea580c;
        border-color: #ea580c;
        color: #ffffff;
    }

    /* CHẾ ĐỘ XEM DANH SÁCH (LIST VIEW) */
    .list-view-mode .product-col-item {
        width: 100% !important;
        max-width: 100% !important;
    }
    .list-view-mode .modern-product-card {
        flex-direction: row;
        align-items: center;
        gap: 20px;
        padding: 16px 20px;
    }
    .list-view-mode .modern-card-img-wrap {
        width: 160px;
        height: 140px;
        margin-bottom: 0;
        flex-shrink: 0;
    }
    .list-view-mode .card-top-badges {
        position: absolute;
        top: 14px;
        left: 14px;
        right: 14px;
    }
    .list-view-mode .card-body-content {
        flex-grow: 1;
    }
    .list-view-mode .card-actions-wrapper {
        min-width: 200px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }
    .list-view-mode .card-actions-row {
        width: 100%;
    }

    /* Trạng thái trống (Không tìm thấy) */
    .empty-product-state {
        background: #ffffff;
        border: 1px solid #e4e4e7;
        border-radius: 16px;
        padding: 50px 20px;
        text-align: center;
    }
    .empty-state-icon {
        width: 70px;
        height: 70px;
        background: #fff4eb;
        color: #f37021;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 16px;
    }
</style>

<div class="category-page-wrapper">
    <div class="container-fluid px-3 px-lg-5">
        
        <!-- Breadcrumb & Tiêu đề danh mục -->
        <div class="mb-3">
            <div class="custom-breadcrumb mb-2">
                <a href="{{ url('/') }}"><i class="fas fa-home me-1"></i> Trang chủ</a>
                <span class="separator">/</span>
                <span class="text-dark fw-semibold">{{ $catTitle }}</span>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
                <div>
                    <h1 class="category-header-title">{{ $catTitle }}</h1>
                    <p class="category-header-subtitle">{{ $catSubtitle }}</p>
                </div>
                <div class="category-total-badge">
                    Hiển thị <span id="displayed-count" class="fw-bold text-dark">{{ count($product) }}</span> trong tổng số {{ count($product) }} sản phẩm
                </div>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- SIDEBAR BỘ LỌC BÊN TRÁI -->
            <div class="col-lg-3 col-xl-3">
                <div class="filter-sidebar-card">
                    
                    <!-- 1. Bộ lọc khoảng giá -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="filter-section-header" onclick="toggleFilterSection('price-filter-body', this)">
                            <h3 class="filter-section-title">Khoảng giá</h3>
                            <i class="fas fa-chevron-up"></i>
                        </div>
                        <div id="price-filter-body">
                            <!-- Slider màu cam -->
                            <input type="range" class="price-range-slider" id="priceRange" 
                                min="0" max="{{ $maxSliderPrice }}" step="500000" value="{{ $maxSliderPrice }}"
                                oninput="onPriceSliderChange(this.value)">

                            <!-- 2 ô hiển thị giá -->
                            <div class="price-inputs-row">
                                <div class="price-input-badge">0 đ</div>
                                <div class="price-input-badge" id="maxPriceBadge">{{ number_format($maxSliderPrice, 0, ',', '.') }} đ</div>
                            </div>

                            <!-- Mức giá nhanh -->
                            <div class="d-flex flex-wrap">
                                <span class="price-preset-pill active" onclick="setPricePreset(0, {{ $maxSliderPrice }}, this)">Tất cả</span>
                                <span class="price-preset-pill" onclick="setPricePreset(0, 15000000, this)">Dưới 15 triệu</span>
                                <span class="price-preset-pill" onclick="setPricePreset(15000000, 40000000, this)">15 - 40 triệu</span>
                                <span class="price-preset-pill" onclick="setPricePreset(40000000, 70000000, this)">40 - 70 triệu</span>
                                <span class="price-preset-pill" onclick="setPricePreset(70000000, {{ $maxSliderPrice }}, this)">Trên 70 triệu</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Bộ lọc thương hiệu -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="filter-section-header" onclick="toggleFilterSection('brand-filter-body', this)">
                            <h3 class="filter-section-title">Thương hiệu</h3>
                            <i class="fas fa-chevron-up"></i>
                        </div>
                        <div id="brand-filter-body">
                            <div id="brand-list-wrapper">
                                @php $bIdx = 0; @endphp
                                @foreach ($brandStats as $brandName => $count)
                                    @php $bIdx++; @endphp
                                    <div class="filter-check-item {{ $bIdx > 5 ? 'brand-extra-item d-none' : '' }}">
                                        <label for="brand-{{ Str::slug($brandName) }}">
                                            <input type="checkbox" class="filter-checkbox brand-filter-checkbox" 
                                                id="brand-{{ Str::slug($brandName) }}" 
                                                value="{{ $brandName }}"
                                                onchange="applyAllFilters()">
                                            <span>{{ $brandName }}</span>
                                        </label>
                                        <span class="filter-count">({{ $count }})</span>
                                    </div>
                                @endforeach
                            </div>
                            @if (count($brandStats) > 5)
                                <a href="javascript:void(0)" class="filter-more-link" id="btn-toggle-brands" onclick="toggleMoreBrands()">Xem thêm <i class="fas fa-chevron-down ms-1" style="font-size: 11px;"></i></a>
                            @endif
                        </div>
                    </div>

                    <!-- 3. Bộ lọc loại sản phẩm -->
                    <div class="mb-3">
                        <div class="filter-section-header" onclick="toggleFilterSection('style-filter-body', this)">
                            <h3 class="filter-section-title">Loại sản phẩm</h3>
                            <i class="fas fa-chevron-up"></i>
                        </div>
                        <div id="style-filter-body">
                            @foreach ($styleStats as $styleName => $count)
                                <div class="filter-check-item">
                                    <label for="style-{{ Str::slug($styleName) }}">
                                        <input type="checkbox" class="filter-checkbox style-filter-checkbox" 
                                            id="style-{{ Str::slug($styleName) }}" 
                                            value="{{ $styleName }}"
                                            onchange="applyAllFilters()">
                                        <span>{{ $styleName }}</span>
                                    </label>
                                    <span class="filter-count">({{ $count }})</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Nút đặt lại bộ lọc -->
                    <button type="button" class="btn btn-outline-secondary w-100 rounded-pill py-2 mt-2" style="font-size: 13px;" onclick="resetAllFilters()">
                        <i class="fas fa-redo-alt me-1"></i> Đặt lại tất cả bộ lọc
                    </button>
                </div>
            </div>

            <!-- NỘI DUNG SẢN PHẨM BÊN PHẢI -->
            <div class="col-lg-9 col-xl-9">
                
                <!-- THANH CÔNG CỤ TOOLBAR -->
                <div class="filter-toolbar">
                    <!-- Ô tìm kiếm trong danh mục -->
                    <div class="toolbar-search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="categorySearchInput" class="toolbar-search-input" 
                            placeholder="Tìm kiếm trong danh mục này..." 
                            oninput="applyAllFilters()">
                    </div>

                    <!-- Sắp xếp & Chuyển dạng xem -->
                    <div class="d-flex align-items-center gap-3 ms-auto">
                        <div class="toolbar-sort-group">
                            <span class="toolbar-sort-label">Sắp xếp theo:</span>
                            <select id="sortSelect" class="toolbar-sort-select" onchange="applyAllFilters()">
                                <option value="popular">Phổ biến nhất</option>
                                <option value="price_asc">Giá: Thấp đến Cao</option>
                                <option value="price_desc">Giá: Cao đến Thấp</option>
                                <option value="newest">Mới nhất</option>
                                <option value="discount">Giảm giá nhiều</option>
                            </select>
                        </div>

                        <!-- Chuyển dạng Lưới / Danh sách -->
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="view-toggle-btn active" id="btnGridView" onclick="setViewMode('grid')" title="Dạng lưới">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button type="button" class="view-toggle-btn" id="btnListView" onclick="setViewMode('list')" title="Dạng danh sách">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- LƯỚI SẢN PHẨM -->
                <div class="row g-3 g-md-4" id="productsContainer">
                    @forelse ($product as $item)
                        @php
                            $price = (float) ($item->price ?? 0);
                            $percent = (int) ($item->discountPercent ?? 0);
                            $percent = max(0, min(100, $percent));
                            $discounted = $percent > 0 ? ($price * (100 - $percent)) / 100 : $price;
                            $fmt = fn($n) => number_format($n, 0, ',', '.') . 'đ';
                            
                            $brandName = $item->brand?->TenThuongHieu ?? '';
                            $styleName = $item->style ?? '';
                            $reviewsCount = $item->reviews ? $item->reviews->count() : 0;
                            if ($reviewsCount == 0) {
                                // Random review count nhỏ sinh động cho demo
                                $reviewsCount = (($item->id * 7) % 25) + 12;
                            }
                        @endphp

                        <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3 product-col-item"
                            data-id="{{ $item->id }}"
                            data-name="{{ mb_strtolower($item->name) }}"
                            data-brand="{{ $brandName }}"
                            data-style="{{ $styleName }}"
                            data-price="{{ $discounted }}"
                            data-discount="{{ $percent }}"
                            data-reviews="{{ $reviewsCount }}">
                            
                            <div class="modern-product-card">
                                
                                <!-- Badge & Wishlist -->
                                <div class="card-top-badges">
                                    <div>
                                        @if ($percent > 0)
                                            <span class="badge-pill-discount">-{{ $percent }}%</span>
                                        @else
                                            <span class="badge-pill-new">New</span>
                                        @endif
                                    </div>
                                    <button type="button" class="card-wishlist-btn" onclick="toggleWishlist(this)" title="Thêm vào yêu thích">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>

                                <!-- Hình ảnh sản phẩm -->
                                <div class="modern-card-img-wrap">
                                    <a href="{{ route('product.detail', $item->id) }}" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                        <img src="{{ asset('uploads/products/' . $item->imageURL) }}" alt="{{ $item->name }}" loading="lazy">
                                    </a>
                                </div>

                                <!-- Nội dung thông tin sản phẩm -->
                                <div class="card-body-content">
                                    <div class="product-category-name">
                                        {{ $item->category?->name ?? 'Máy ảnh & Quay phim' }}
                                    </div>

                                    <a href="{{ route('product.detail', $item->id) }}" class="product-title-text" title="{{ $item->name }}">
                                        {{ $item->name }}
                                    </a>

                                    <!-- Đánh giá sao -->
                                    <div class="product-rating-row">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                        <span class="product-rating-count">({{ $reviewsCount }})</span>
                                    </div>

                                    <!-- Giá bán -->
                                    <div class="product-price-row">
                                        @if ($percent > 0)
                                            <span class="product-old-price">{{ $fmt($price) }}</span>
                                            <span class="product-sale-price">{{ $fmt($discounted) }}</span>
                                        @else
                                            <span class="product-sale-price">{{ $fmt($price) }}</span>
                                        @endif
                                    </div>

                                    <!-- Tiện ích (Còn hàng, Freeship) -->
                                    <div class="product-perks-row">
                                        <span class="perk-in-stock">
                                            <i class="fas fa-check-circle"></i> Còn hàng
                                        </span>
                                        <span class="perk-free-ship">
                                            <i class="fas fa-truck"></i> Miễn phí ship
                                        </span>
                                    </div>
                                </div>

                                <!-- Nút Thêm vào giỏ & Mua ngay -->
                                <div class="card-actions-wrapper">
                                    <div class="card-actions-row">
                                        <button type="button" class="btn-card-cart add-to-cart-btn"
                                            data-product-id="{{ $item->id }}"
                                            data-authenticated="{{ Auth::check() ? 'true' : 'false' }}"
                                            onclick="if(window.addToCartDirect){window.addToCartDirect({{ $item->id }}, this);}"
                                            title="Thêm vào giỏ hàng">
                                            <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                        </button>
                                        
                                        <button type="button" class="btn-card-buy buy-now-btn"
                                            data-product-id="{{ $item->id }}"
                                            data-authenticated="{{ Auth::check() ? 'true' : 'false' }}"
                                            onclick="if(window.buyNowDirect){window.buyNowDirect({{ $item->id }}, this);}else{window.location.href='{{ route('checkout.index') }}';}"
                                            title="Mua ngay">
                                            <i class="fas fa-bolt"></i> Mua ngay
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-product-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <h4 class="fw-bold mb-2">Chưa có sản phẩm nào trong danh mục này</h4>
                                <p class="text-muted mb-4">Vui lòng quay lại sau hoặc tham khảo các danh mục sản phẩm khác.</p>
                                <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4 py-2">
                                    <i class="fas fa-arrow-left me-1"></i> Về trang chủ
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Trạng thái khi lọc không ra kết quả -->
                <div id="noResultsBox" class="empty-product-state d-none mt-3">
                    <div class="empty-state-icon">
                        <i class="fas fa-filter"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Không tìm thấy sản phẩm phù hợp</h4>
                    <p class="text-muted mb-4">Hãy thử nới rộng khoảng giá hoặc bỏ bớt các tiêu chí lọc thương hiệu.</p>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2" onclick="resetAllFilters()">
                        <i class="fas fa-redo-alt me-1"></i> Xóa tất cả bộ lọc
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // Trạng thái lọc
    let currentMinPrice = 0;
    let currentMaxPrice = {{ $maxSliderPrice }};

    function toggleFilterSection(sectionId, headerEl) {
        const body = document.getElementById(sectionId);
        if (!body) return;
        if (body.style.display === 'none') {
            body.style.display = 'block';
            headerEl.classList.remove('collapsed');
        } else {
            body.style.display = 'none';
            headerEl.classList.add('collapsed');
        }
    }

    function toggleMoreBrands() {
        const extraItems = document.querySelectorAll('.brand-extra-item');
        const btn = document.getElementById('btn-toggle-brands');
        const isHidden = extraItems[0]?.classList.contains('d-none');
        extraItems.forEach(el => {
            if (isHidden) {
                el.classList.remove('d-none');
            } else {
                el.classList.add('d-none');
            }
        });
        if (btn) {
            btn.innerHTML = isHidden 
                ? 'Thu gọn <i class="fas fa-chevron-up ms-1" style="font-size: 11px;"></i>' 
                : 'Xem thêm <i class="fas fa-chevron-down ms-1" style="font-size: 11px;"></i>';
        }
    }

    function onPriceSliderChange(val) {
        currentMaxPrice = parseFloat(val);
        const badge = document.getElementById('maxPriceBadge');
        if (badge) {
            badge.innerText = new Intl.NumberFormat('vi-VN').format(currentMaxPrice) + ' đ';
        }
        applyAllFilters();
    }

    function setPricePreset(min, max, pillEl) {
        document.querySelectorAll('.price-preset-pill').forEach(el => el.classList.remove('active'));
        if (pillEl) pillEl.classList.add('active');

        currentMinPrice = min;
        currentMaxPrice = max;

        const slider = document.getElementById('priceRange');
        if (slider) slider.value = max;
        
        const badge = document.getElementById('maxPriceBadge');
        if (badge) {
            badge.innerText = new Intl.NumberFormat('vi-VN').format(max) + ' đ';
        }
        applyAllFilters();
    }

    function toggleWishlist(btn) {
        btn.classList.toggle('active');
        const icon = btn.querySelector('i');
        if (btn.classList.contains('active')) {
            icon.classList.remove('far');
            icon.classList.add('fas');
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
        }
    }

    function setViewMode(mode) {
        const container = document.getElementById('productsContainer');
        const btnGrid = document.getElementById('btnGridView');
        const btnList = document.getElementById('btnListView');

        if (mode === 'list') {
            container.classList.add('list-view-mode');
            btnList.classList.add('active');
            btnGrid.classList.remove('active');
        } else {
            container.classList.remove('list-view-mode');
            btnGrid.classList.add('active');
            btnList.classList.remove('active');
        }
    }

    function applyAllFilters() {
        const searchVal = (document.getElementById('categorySearchInput')?.value || '').trim().toLowerCase();
        const sortVal = document.getElementById('sortSelect')?.value || 'popular';

        // Lấy danh sách thương hiệu được check
        const selectedBrands = [];
        document.querySelectorAll('.brand-filter-checkbox:checked').forEach(cb => {
            selectedBrands.push(cb.value.toLowerCase());
        });

        // Lấy danh sách loại sản phẩm được check
        const selectedStyles = [];
        document.querySelectorAll('.style-filter-checkbox:checked').forEach(cb => {
            selectedStyles.push(cb.value.toLowerCase());
        });

        const items = Array.from(document.querySelectorAll('.product-col-item'));
        let visibleCount = 0;

        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const brand = (item.getAttribute('data-brand') || '').toLowerCase();
            const style = (item.getAttribute('data-style') || '').toLowerCase();
            const price = parseFloat(item.getAttribute('data-price') || 0);

            // Kiểm tra tìm kiếm
            const matchSearch = !searchVal || name.includes(searchVal);

            // Kiểm tra giá
            const matchPrice = price >= currentMinPrice && price <= currentMaxPrice;

            // Kiểm tra thương hiệu
            const matchBrand = selectedBrands.length === 0 || selectedBrands.includes(brand);

            // Kiểm tra loại sản phẩm
            const matchStyle = selectedStyles.length === 0 || selectedStyles.includes(style);

            if (matchSearch && matchPrice && matchBrand && matchStyle) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Cập nhật số lượng hiển thị
        const countBadge = document.getElementById('displayed-count');
        if (countBadge) countBadge.innerText = visibleCount;

        // Trạng thái trống
        const noResults = document.getElementById('noResultsBox');
        if (noResults) {
            if (visibleCount === 0 && items.length > 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }

        // Sắp xếp các sản phẩm đang hiển thị
        sortVisibleProducts(sortVal);
    }

    function sortVisibleProducts(sortMode) {
        const container = document.getElementById('productsContainer');
        if (!container) return;

        const items = Array.from(container.querySelectorAll('.product-col-item'));
        items.sort((a, b) => {
            const priceA = parseFloat(a.getAttribute('data-price') || 0);
            const priceB = parseFloat(b.getAttribute('data-price') || 0);
            const idA = parseInt(a.getAttribute('data-id') || 0);
            const idB = parseInt(b.getAttribute('data-id') || 0);
            const discountA = parseInt(a.getAttribute('data-discount') || 0);
            const discountB = parseInt(b.getAttribute('data-discount') || 0);
            const reviewsA = parseInt(a.getAttribute('data-reviews') || 0);
            const reviewsB = parseInt(b.getAttribute('data-reviews') || 0);

            if (sortMode === 'price_asc') {
                return priceA - priceB;
            } else if (sortMode === 'price_desc') {
                return priceB - priceA;
            } else if (sortMode === 'newest') {
                return idB - idA;
            } else if (sortMode === 'discount') {
                return discountB - discountA;
            } else {
                // Phổ biến nhất (theo số review / id)
                return reviewsB - reviewsA;
            }
        });

        items.forEach(el => container.appendChild(el));
    }

    function resetAllFilters() {
        const searchInput = document.getElementById('categorySearchInput');
        if (searchInput) searchInput.value = '';

        document.querySelectorAll('.filter-checkbox').forEach(cb => cb.checked = false);

        setPricePreset(0, {{ $maxSliderPrice }}, document.querySelector('.price-preset-pill'));
    }
</script>

@endsection

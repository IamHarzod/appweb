@extends('layout.home_layout')
@section('home-content')
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="product-image">
                    <img src="{{ asset('uploads/products/' . $product->imageURL) }}" 
                         alt="{{ $product->name }}" 
                         class="img-fluid rounded">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="product-details">
                    <h1 class="mb-3">{{ $product->name }}</h1>
                    
                    @if($product->category)
                        <p class="text-muted mb-3">
                            <i class="fas fa-tag me-2"></i>Danh mục: {{ $product->category->name }}
                        </p>
                    @endif
                    
                    @if($product->brand)
                        <p class="text-muted mb-3">
                            <i class="fas fa-store me-2"></i>Thương hiệu: {{ $product->brand->name }}
                        </p>
                    @endif

                    <div class="mb-4">
                        @php
                            $price = (float) ($product->price ?? 0);
                            $percent = (int) ($product->discountPercent ?? 0);
                            $percent = max(0, min(100, $percent));
                            $discounted = ($price * (100 - $percent)) / 100;
                            $fmt = fn($n) => number_format($n, 0, ',', '.') . ' VNĐ';
                        @endphp
                        
                        @if ($percent > 0)
                            <h3 class="text-primary mb-2">{{ $fmt($discounted) }}</h3>
                            <del class="text-muted me-2">{{ $fmt($price) }}</del>
                            <span class="badge bg-danger">-{{ $percent }}%</span>
                        @else
                            <h3 class="text-primary">{{ $fmt($price) }}</h3>
                        @endif
                    </div>

                    @if($product->description)
                        <div class="mb-4">
                            <h5>Mô tả sản phẩm:</h5>
                            <p class="text-muted">{{ $product->description }}</p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5>Thông tin sản phẩm:</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Tồn kho: {{ $product->stockQuantity }} sản phẩm</li>
                            <li><i class="fas fa-check text-success me-2"></i>Trạng thái: 
                                @if($product->IsActive)
                                    <span class="text-success">Còn hàng</span>
                                @else
                                    <span class="text-danger">Hết hàng</span>
                                @endif
                            </li>
                        </ul>
                    </div>

                    <!-- Form thêm vào giỏ hàng -->
                    @if($product->IsActive && $product->stockQuantity > 0)
                        <div class="add-to-cart-form mb-4">
                            <h5>Thêm vào giỏ hàng:</h5>
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label for="product-quantity" class="form-label">Số lượng:</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="product-quantity" 
                                           value="1" 
                                           min="1" 
                                           max="{{ $product->stockQuantity }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">&nbsp;</label><br>
                                    @if(Auth::check())
                                        <button class="btn btn-primary btn-lg add-to-cart-btn" 
                                                data-product-id="{{ $product->id }}" 
                                                data-quantity-selector="#product-quantity"
                                                data-authenticated="true">
                                            <i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ hàng
                                        </button>
                                    @else
                                        <button class="btn btn-warning btn-lg" onclick="alert('Vui lòng đăng nhập để mua hàng')">
                                            <i class="fas fa-lock me-2"></i> Đăng nhập để mua
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Sản phẩm hiện tại không có sẵn để mua.
                        </div>
                    @endif

                    <!-- Thông tin bổ sung -->
                    <div class="product-features mt-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="feature-item text-center p-3 border rounded">
                                    <i class="fas fa-shipping-fast fa-2x text-primary mb-2"></i>
                                    <h6>Miễn phí vận chuyển</h6>
                                    <small class="text-muted">Cho đơn hàng từ 500.000 VNĐ</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="feature-item text-center p-3 border rounded">
                                    <i class="fas fa-undo fa-2x text-primary mb-2"></i>
                                    <h6>Đổi trả dễ dàng</h6>
                                    <small class="text-muted">Trong vòng 7 ngày</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="feature-item text-center p-3 border rounded">
                                    <i class="fas fa-headset fa-2x text-primary mb-2"></i>
                                    <h6>Hỗ trợ 24/7</h6>
                                    <small class="text-muted">Hotline: 1900 1234</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tùy chỉnh thêm cho trang chi tiết sản phẩm
document.addEventListener('DOMContentLoaded', function() {
    // Tự động cập nhật nút khi thay đổi số lượng
    const quantityInput = document.getElementById('product-quantity');
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    
    if (quantityInput && addToCartBtn) {
        quantityInput.addEventListener('change', function() {
            const quantity = this.value;
            const maxQuantity = parseInt(this.getAttribute('max'));
            
            if (quantity > maxQuantity) {
                this.value = maxQuantity;
                cartManager.showMessage(`Số lượng tối đa là ${maxQuantity}`, 'warning');
                return;
            }
            
            if (quantity < 1) {
                this.value = 1;
                return;
            }
            
            // Cập nhật text nút
            addToCartBtn.innerHTML = `<i class="fas fa-shopping-cart me-2"></i> Thêm ${quantity} vào giỏ hàng`;
        });
    }
});
</script>

<style>
.product-image img {
    max-height: 500px;
    object-fit: cover;
}

.feature-item {
    transition: transform 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.add-to-cart-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
}
</style>
@endsection

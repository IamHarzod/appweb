# Hướng dẫn sử dụng JavaScript cho chức năng giỏ hàng

## Các file đã được cập nhật:

### 1. Layout chính (`resources/views/layout/home_layout.blade.php`)
- ✅ Thêm CSRF token meta tag
- ✅ Include file `cart.js`
- ✅ Thêm bộ đếm giỏ hàng vào header

### 2. Trang sản phẩm (`resources/views/client/home/index_home.blade.php`)
- ✅ Cập nhật tất cả nút "Thêm vào giỏ hàng" với class `add-to-cart-btn`
- ✅ Thêm data attributes: `data-product-id`, `data-authenticated`
- ✅ Cập nhật link đến trang chi tiết sản phẩm

### 3. Trang chi tiết sản phẩm (`resources/views/client/product/detail.blade.php`)
- ✅ Tạo trang chi tiết sản phẩm hoàn chỉnh
- ✅ Form thêm vào giỏ hàng với số lượng tùy chỉnh
- ✅ JavaScript tùy chỉnh cho trang chi tiết

### 4. Controllers và Routes
- ✅ Thêm route `/product/{id}` cho trang chi tiết
- ✅ Thêm method `show_product_detail()` trong HomeController

## Cách sử dụng JavaScript:

### 1. Tự động (Đã được thiết lập sẵn)
```html
<!-- Tất cả nút có class "add-to-cart-btn" sẽ tự động hoạt động -->
<button class="btn btn-primary add-to-cart-btn" 
        data-product-id="1" 
        data-authenticated="{{ Auth::check() ? 'true' : 'false' }}">
    Thêm vào giỏ hàng
</button>
```

### 2. Sử dụng thủ công
```javascript
// Thêm sản phẩm vào giỏ hàng
cartManager.addToCart(productId, quantity).then(success => {
    if (success) {
        console.log('Đã thêm thành công');
    }
});

// Cập nhật số lượng
cartManager.updateCartCounter();

// Hiển thị thông báo
cartManager.showMessage('Thông báo', 'success');
```

### 3. Với form số lượng
```html
<!-- Form với số lượng tùy chỉnh -->
<div class="input-group">
    <input type="number" id="quantity" value="1" min="1" max="10">
    <button class="btn btn-primary add-to-cart-btn" 
            data-product-id="1" 
            data-quantity-selector="#quantity"
            data-authenticated="{{ Auth::check() ? 'true' : 'false' }}">
        Thêm vào giỏ hàng
    </button>
</div>
```

## Các tính năng đã hoạt động:

### ✅ Thêm vào giỏ hàng
- Yêu cầu đăng nhập
- Kiểm tra tồn kho
- Tự động cập nhật số lượng nếu sản phẩm đã có trong giỏ

### ✅ Hiển thị giỏ hàng
- Trang `/show-cart` hiển thị tất cả sản phẩm
- Cập nhật số lượng realtime
- Xóa sản phẩm individual
- Xóa tất cả giỏ hàng

### ✅ Bộ đếm giỏ hàng
- Tự động cập nhật trong header
- Hiển thị số lượng sản phẩm trong giỏ

### ✅ Thông báo
- Thông báo thành công/lỗi
- Tự động ẩn sau 5 giây

## Cách test:

1. **Đăng nhập** vào hệ thống
2. **Truy cập trang chủ** - sẽ thấy các nút "Thêm vào giỏ hàng"
3. **Click nút thêm vào giỏ** - sẽ có thông báo thành công
4. **Kiểm tra bộ đếm** trong header (góc trên bên phải)
5. **Truy cập `/show-cart`** để xem giỏ hàng
6. **Thử cập nhật số lượng** và **xóa sản phẩm**

## Lưu ý quan trọng:

- **Phải đăng nhập** để sử dụng giỏ hàng
- **Kiểm tra tồn kho** trước khi thêm
- **CSRF token** được thêm tự động
- **Responsive** trên mobile và desktop
- **AJAX** không cần reload trang

## Troubleshooting:

### Nút không hoạt động:
```javascript
// Kiểm tra console để xem lỗi
console.log('Cart manager:', window.cartManager);
```

### CSRF token lỗi:
```html
<!-- Đảm bảo có meta tag này trong <head> -->
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Không cập nhật được giỏ hàng:
```javascript
// Kiểm tra authentication
console.log('User authenticated:', {{ Auth::check() ? 'true' : 'false' }});
```

## Mở rộng:

### Thêm validation tùy chỉnh:
```javascript
// Trong cart.js, thêm vào event listener
document.querySelectorAll('.add-to-cart-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        // Validation tùy chỉnh ở đây
        if (someCondition) {
            e.preventDefault();
            cartManager.showMessage('Không thể thêm sản phẩm', 'error');
            return;
        }
        // ... rest of the code
    });
});
```

### Thêm animation:
```css
.add-to-cart-btn {
    transition: all 0.3s ease;
}

.add-to-cart-btn:hover {
    transform: scale(1.05);
}
```

Hệ thống giỏ hàng đã sẵn sàng sử dụng! 🛒✨

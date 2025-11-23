@extends('layout.home_layout')
@section('home-content')
    <!-- Cart Page Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            @if (!Auth::check())
                <div class="alert alert-warning text-center">
                    <h4>Vui lòng đăng nhập để xem giỏ hàng</h4>
                    <p>Bạn cần đăng nhập để có thể thêm sản phẩm vào giỏ hàng và tiến hành thanh toán.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary">Đăng nhập ngay</a>
                </div>
            @elseif($cartItems->isEmpty())
                <div class="alert alert-info text-center">
                    <h4>Giỏ hàng trống</h4>
                    <p>Bạn chưa có sản phẩm nào trong giỏ hàng.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Hình ảnh</th>
                                <th scope="col">Tên sản phẩm</th>
                                <th scope="col">Giá</th>
                                <th scope="col">Số lượng</th>
                                <th scope="col">Tổng tiền</th>
                                <th scope="col">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            @foreach ($cartItems as $item)
                                <tr id="cart-item-{{ $item->id }}">
                                    <td>
                                        <img src="{{ asset('public/uploads/products/' . $item->product->imageURL) }}"
                                            alt="{{ $item->product->name }}"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <p class="mb-0 py-4">{{ $item->product->name }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0 py-4">{{ number_format($item->product->price, 0, ',', '.') }} VNĐ</p>
                                    </td>
                                    <td>
                                        <div class="input-group quantity py-4" style="width: 100px;">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-minus rounded-circle bg-light border"
                                                    onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                            <input type="number"
                                                class="form-control form-control-sm text-center border-0 quantity-input"
                                                value="{{ $item->quantity }}" min="1"
                                                max="{{ $item->product->stockQuantity }}"
                                                onchange="updateQuantity({{ $item->id }}, this.value)">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-plus rounded-circle bg-light border"
                                                    onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0 py-4" id="subtotal-{{ $item->id }}">
                                            {{ number_format($item->quantity * $item->product->price, 0, ',', '.') }} VNĐ
                                        </p>
                                    </td>
                                    <td class="py-4">
                                        <button class="btn btn-md rounded-circle bg-light border"
                                            onclick="removeFromCart({{ $item->id }})">
                                            <i class="fa fa-times text-danger"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Action buttons -->
                <div class="mt-4 mb-4">
                    <button class="btn btn-danger" onclick="clearCart()">
                        <i class="fa fa-trash"></i> Xóa tất cả giỏ hàng
                    </button>
                </div>

                <!-- Coupon section -->
                <div class="mt-5">
                    <form action="{{ route('check_coupon') }}" method="POST">
                        @csrf
                        <div class="d-flex align-items-center">
                            <input type="text" name="code_input" class="border-0 border-bottom rounded me-5 py-3 mb-4"
                                placeholder="Mã giảm giá" required>
                            <button onclick="saveScrollPosition()" class="btn btn-primary rounded-pill px-4 py-3"
                                type="submit">
                                Nhập mã giảm giá
                            </button>
                        </div>
                    </form>
                </div>
                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @elseif(Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif

                <!-- Order summary -->
                <div class="row g-4 justify-content-end">
                    <div class="col-8"></div>
                    <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                        <div class="bg-light rounded">
                            <div class="p-4">
                                <h1 class="display-6 mb-4">Tổng đơn hàng<span class="fw-normal"></span></h1>
                                <div class="d-flex justify-content-between mb-4">
                                    <h5 class="mb-0 me-4">Tổng phụ:</h5>
                                    <p class="mb-0" id="cart-subtotal">
                                        {{ number_format($subtotal, 0, ',', '.') }} VNĐ</p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-0 me-4">Phí vận chuyển:</h5>
                                    <div>
                                        <p class="mb-0">
                                            @if ($shippingFee == 0)
                                                <span class="text-success">Miễn phí</span>
                                            @else
                                                <span>{{ number_format($shippingFee, 0, ',', '.') }} VNĐ</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @if (Session::has('coupon') && Session::get('coupon')['type'] != 'free_ship')
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Giảm giá ({{ Session::get('coupon')['code'] }}):</span>
                                    <span>- {{ number_format($discountAmount, 0, ',', '.') }} VNĐ</span>
                                </div>
                            @endif

                            @if (Session::has('coupon'))
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                                    <div>
                                        <i class="fa fa-ticket text-primary me-2"></i>
                                        <span class="text-muted">Đang dùng:</span>
                                        <span class="fw-bold text-dark">{{ Session::get('coupon')['code'] }}</span>
                                    </div>
                                    <a onclick="saveScrollPosition()" href="{{ route('remove_coupon') }}"
                                        class="btn btn-sm btn-outline-danger rounded-pill" title="Gỡ bỏ mã này">
                                        <i class="fa fa-times"></i> Gỡ
                                    </a>
                                </div>
                            @endif
                            <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                                <h5 class="mb-0 ps-4 me-4">Tổng tiền</h5>
                                <p class="mb-0 pe-4" id="cart-total">{{ number_format($totalPrice, 0, ',', '.') }} VNĐ</p>
                            </div>
                            <a class="btn btn-primary rounded-pill px-4 py-3 text-uppercase mb-4 ms-4"
                                href="{{ route('checkout.index') }}">
                                Tiến hành thanh toán
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- Cart Page End -->

    <!-- JavaScript -->
    <script>
        // Function to update quantity
        function updateQuantity(cartItemId, newQuantity) {
            if (newQuantity < 1) {
                return;
            }

            const updateUrlTemplate = document.querySelector('meta[name="route-cart-update"]').getAttribute('content');
            const updateUrl = updateUrlTemplate.replace(/0$/, String(cartItemId));

            fetch(updateUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        quantity: newQuantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the quantity input
                        document.querySelector(`#cart-item-${cartItemId} .quantity-input`).value = newQuantity;
                        location.reload();

                        // Update subtotal
                        const productPrice = parseFloat(document.querySelector(
                            `#cart-item-${cartItemId} td:nth-child(3) p`).textContent.replace(/[^\d]/g, ''));
                        const subtotal = productPrice * newQuantity;
                        document.querySelector(`#subtotal-${cartItemId}`).textContent = subtotal.toLocaleString(
                            'vi-VN') + ' VNĐ';

                        // Update cart total
                        document.querySelector('#cart-subtotal').textContent = data.cart_total.toLocaleString('vi-VN') +
                            ' VNĐ';
                        document.querySelector('#cart-total').textContent = data.cart_total.toLocaleString('vi-VN') +
                            ' VNĐ';

                        // Show success message
                        showMessage(data.message, 'success');
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Có lỗi xảy ra khi cập nhật giỏ hàng', 'error');
                });
        }

        // Function to remove item from cart
        function removeFromCart(cartItemId) {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                fetch(document.querySelector('meta[name="route-cart-remove"]').getAttribute('content').replace(/0$/, String(
                        cartItemId)), {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the row from table
                            document.querySelector(`#cart-item-${cartItemId}`).remove();
                            location.reload();

                            // Update cart total
                            document.querySelector('#cart-subtotal').textContent = data.cart_total.toLocaleString(
                                'vi-VN') + ' VNĐ';
                            document.querySelector('#cart-total').textContent = data.cart_total.toLocaleString(
                                'vi-VN') + ' VNĐ';

                            // Check if cart is empty
                            const cartItems = document.querySelectorAll('#cart-items tr');
                            if (cartItems.length === 0) {
                                location.reload(); // Reload to show empty cart message
                            }

                            showMessage(data.message, 'success');
                        } else {
                            showMessage(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMessage('Có lỗi xảy ra khi xóa sản phẩm', 'error');
                    });
            }
        }

        // Function to clear cart
        function clearCart() {
            if (confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm trong giỏ hàng?')) {
                fetch(document.querySelector('meta[name="route-cart-clear"]').getAttribute('content'), {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload(); // Reload to show empty cart message
                        } else {
                            showMessage(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMessage('Có lỗi xảy ra khi xóa giỏ hàng', 'error');
                    });
            }
        }

        // Function to show messages
        function showMessage(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(alertDiv);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 3000);
        }
    </script>
@endsection

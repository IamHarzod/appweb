// Cart functionality JavaScript
class CartManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        // Resolve routes from meta tags (safer với base url)
        this.routes = {
            add: document.querySelector('meta[name="route-cart-add"]')?.getAttribute('content') || '/cart/add',
            update: (id) => {
                const tmpl = document.querySelector('meta[name="route-cart-update"]')?.getAttribute('content');
                return tmpl ? tmpl.replace(/0$/, String(id)) : `/cart/update/${id}`;
            },
            remove: (id) => {
                const tmpl = document.querySelector('meta[name="route-cart-remove"]')?.getAttribute('content');
                return tmpl ? tmpl.replace(/0$/, String(id)) : `/cart/remove/${id}`;
            },
            clear: document.querySelector('meta[name="route-cart-clear"]')?.getAttribute('content') || '/cart/clear',
            summary: document.querySelector('meta[name="route-cart-summary"]')?.getAttribute('content') || '/cart/summary',
            api: document.querySelector('meta[name="route-cart-api"]')?.getAttribute('content') || '/cart/api',
        };
    }

    // Add product to cart
    async addToCart(productId, quantity = 1) {
        // Support passing a selector string for quantity
        if (typeof quantity === 'string' && quantity.startsWith('#')) {
            const el = document.querySelector(quantity);
            if (el) {
                const parsed = parseInt(el.value, 10);
                quantity = Number.isNaN(parsed) ? 1 : Math.max(1, parsed);
            }
        }
        if (!this.csrfToken) {
            this.showMessage('CSRF token not found', 'error');
            return false;
        }

        try {
            const response = await fetch(this.routes.add, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            // Handle unauthenticated -> redirect to login page
            if (response.status === 401) {
                this.showMessage('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng', 'warning');
                // Try to redirect to login route if available
                try { window.location.href = '/admin'; } catch (_) {}
                return false;
            }

            let data;
            try {
                data = await response.json();
            } catch (e) {
                // Non-JSON (e.g., HTML error page)
                this.showMessage('Không thể xử lý phản hồi từ máy chủ', 'error');
                return false;
            }
            
            if (data.success) {
                this.showMessage(data.message, 'success');
                this.updateCartCounter();
                return true;
            } else {
                this.showMessage(data.message, 'error');
                return false;
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showMessage('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng', 'error');
            return false;
        }
    }

    // Update cart counter in header/navigation
    async updateCartCounter() {
        try {
            const response = await fetch(this.routes.summary, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            const data = await response.json();
            
            if (data.success) {
                const counter = document.querySelector('.cart-counter');
                if (counter) {
                    counter.textContent = data.data.total_items;
                    counter.style.display = data.data.total_items > 0 ? 'inline' : 'none';
                }
            }
        } catch (error) {
            console.error('Error updating cart counter:', error);
        }
    }

    // Show message to user
    showMessage(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }
}

// Initialize cart manager
const cartManager = new CartManager();

// Add to cart button handler
document.addEventListener('DOMContentLoaded', function() {
    // Handle add to cart buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            let quantity = this.dataset.quantitySelector || this.dataset.quantity || 1;
            
            if (!productId) {
                cartManager.showMessage('Product ID not found', 'error');
                return;
            }

            // Check if user is authenticated
            const isAuthenticated = this.dataset.authenticated === 'true';
            if (!isAuthenticated) {
                cartManager.showMessage('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng', 'warning');
                return;
            }

            // Add to cart
            cartManager.addToCart(productId, quantity);
        });
    });

    // Update cart counter on page load
    cartManager.updateCartCounter();
});

// Export for use in other scripts
window.cartManager = cartManager;

// Cart functionality JavaScript

class CartManager {
    constructor() {
        this.activeAlertTimer = null;

        this.csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        // Resolve routes from meta tags (safer với base url)
        this.routes = {
            add:
                document
                    .querySelector('meta[name="route-cart-add"]')
                    ?.getAttribute("content") || "/cart/add",
            update: (id) => {
                const tmpl = document
                    .querySelector('meta[name="route-cart-update"]')
                    ?.getAttribute("content");
                return tmpl
                    ? tmpl.replace(/0$/, String(id))
                    : `/cart/update/${id}`;
            },
            remove: (id) => {
                const tmpl = document
                    .querySelector('meta[name="route-cart-remove"]')
                    ?.getAttribute("content");
                return tmpl
                    ? tmpl.replace(/0$/, String(id))
                    : `/cart/remove/${id}`;
            },
            clear:
                document
                    .querySelector('meta[name="route-cart-clear"]')
                    ?.getAttribute("content") || "/cart/clear",
            summary:
                document
                    .querySelector('meta[name="route-cart-summary"]')
                    ?.getAttribute("content") || "/cart/summary",
            api:
                document
                    .querySelector('meta[name="route-cart-api"]')
                    ?.getAttribute("content") || "/cart/api",
            checkout:
                document
                    .querySelector('meta[name="route-checkout"]')
                    ?.getAttribute("content") || "/show-checkout",
            login:
                document
                    .querySelector('meta[name="route-login"]')
                    ?.getAttribute("content") || "/login",
        };
    }

    // Add product to cart
    async addToCart(productId, quantity = 1) {
        // Support passing a selector string for quantity
        if (typeof quantity === "string" && quantity.startsWith("#")) {
            const el = document.querySelector(quantity);
            if (el) {
                const parsed = parseInt(el.value, 10);
                quantity = Number.isNaN(parsed) ? 1 : Math.max(1, parsed);
            }
        }
        if (!this.csrfToken) {
            this.showMessage("CSRF token not found", "error");
            return false;
        }

        try {
            const response = await fetch(this.routes.add, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": this.csrfToken,
                },
                credentials: "same-origin",
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity,
                }),
            });

            // Handle unauthenticated -> redirect to login page
            if (response.status === 401) {
                this.showMessage(
                    "Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng",
                    "warning"
                );
                // Try to redirect to login route if available
                try {
                    window.location.href = "/login";
                } catch (_) {}
                return false;
            }

            let data;
            try {
                data = await response.json();
            } catch (e) {
                // Non-JSON (e.g., HTML error page)
                this.showMessage(
                    "Không thể xử lý phản hồi từ máy chủ",
                    "error"
                );
                return false;
            }

            this.lastAddResponse = data;

            if (data.success) {
                this.showMessage(data.message, "success");
                this.updateCartCounter();
                return true;
            } else {
                this.showMessage(data.message, "error");
                return false;
            }
        } catch (error) {
            console.error("Error adding to cart:", error);
            this.showMessage(
                "Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng",
                "error"
            );
            return false;
        }
    }

    // Update cart counter in header/navigation
    async updateCartCounter() {
        try {
            const response = await fetch(this.routes.summary, {
                headers: { Accept: "application/json" },
                credentials: "same-origin",
            });
            if (!response.ok) {
                throw new Error("HTTP " + response.status);
            }
            const data = await response.json();

            if (data.success) {
                const counter = document.querySelector(".cart-counter");
                if (counter) {
                    counter.textContent = data.data.total_items;
                    counter.style.display =
                        data.data.total_items > 0 ? "inline" : "none";
                }
            }
        } catch (error) {
            console.error("Error updating cart counter:", error);
        }
    }

    // Show message to user

    showMessage(message, type = "info") {
        // 1. Xóa timer cũ nếu đang chạy (Sửa lỗi: dùng this)
        if (this.activeAlertTimer) {
            clearTimeout(this.activeAlertTimer);
        }

        // 2. Xóa thông báo cũ để tránh bị chồng chéo
        let oldAlert = document.querySelector(".global-alert");
        if (oldAlert) {
            oldAlert.remove();
        }

        // 3. Chọn màu sắc dựa trên type
        const alertClass =
            {
                success: "alert-success",
                error: "alert-danger",
                warning: "alert-warning",
                info: "alert-info",
            }[type] || "alert-info";

        // 4. Tạo phần tử HTML
        const alertDiv = document.createElement("div");
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed global-alert shadow`; // Thêm shadow cho đẹp

        // Style để hiện góc trên bên phải
        Object.assign(alertDiv.style, {
            top: "20px",
            right: "20px",
            zIndex: "9999",
            minWidth: "300px",
            maxWidth: "400px",
        });

        alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <strong class="me-2">${
                type === "success"
                    ? '<i class="fa fa-check-circle"></i>'
                    : '<i class="fa fa-info-circle"></i>'
            }</strong>
            <span>${message}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

        document.body.appendChild(alertDiv);

        // 5. Đặt hẹn giờ tự tắt (Sửa lỗi: dùng this)
        this.activeAlertTimer = setTimeout(() => {
            if (alertDiv && alertDiv.parentNode) {
                // Thêm hiệu ứng fade out trước khi xóa (tùy chọn)
                alertDiv.classList.remove("show");
                setTimeout(() => {
                    if (alertDiv.parentNode)
                        alertDiv.parentNode.removeChild(alertDiv);
                }, 150); // Đợi animation của bootstrap
            }
            this.activeAlertTimer = null;
        }, 3000); // 3 giây là vừa đủ, 5 giây hơi lâu
    }

    // Buy now: add product to cart and immediately redirect to checkout
    async buyNow(productId, quantity = 1, button = null) {
        if (typeof quantity === "string" && quantity.startsWith("#")) {
            const el = document.querySelector(quantity);
            if (el) {
                const parsed = parseInt(el.value, 10);
                quantity = Number.isNaN(parsed) ? 1 : Math.max(1, parsed);
            }
        }

        let originalHtml = "";
        if (button) {
            originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang xử lý...';
        }

        const checkoutUrl = this.routes.checkout || "/show-checkout";

        try {
            const added = await this.addToCart(productId, quantity);
            if (added) {
                this.showMessage("Đang chuyển đến trang thanh toán...", "success");
                window.location.href = checkoutUrl;
                return true;
            } else {
                // Nếu sản phẩm đã có trong giỏ hàng (vượt quá tồn kho), vẫn chuyển tới trang thanh toán
                if (this.lastAddResponse && this.lastAddResponse.message && this.lastAddResponse.message.includes("vượt quá tồn kho")) {
                    this.showMessage("Sản phẩm đã có trong giỏ hàng. Đang chuyển đến thanh toán...", "info");
                    setTimeout(() => {
                        window.location.href = checkoutUrl;
                    }, 500);
                    return true;
                }

                // Luôn đảm bảo chuyển hướng đến trang thanh toán
                setTimeout(() => {
                    window.location.href = checkoutUrl;
                }, 800);

                return false;
            }
        } catch (err) {
            console.error("Error in buyNow:", err);
            // Fallback: chuyển hướng ngay đến trang checkout
            window.location.href = checkoutUrl;
            return false;
        }
    }
}

// Initialize cart manager
const cartManager = new CartManager();

// Global direct handlers for inline onclick or external scripts
window.buyNowDirect = function (productId, button) {
    cartManager.buyNow(productId, 1, button);
};

window.addToCartDirect = function (productId, button) {
    cartManager.addToCart(productId, 1);
};

// Delegated click handler on document - active immediately
document.addEventListener("click", function (e) {
    const buyBtn = e.target.closest(".buy-now-btn");
    if (buyBtn) {
        e.preventDefault();

        const productId = buyBtn.dataset.productId || buyBtn.getAttribute("data-product-id");
        let quantity = buyBtn.dataset.quantitySelector || buyBtn.dataset.quantity || 1;

        if (!productId) {
            console.warn("Product ID not found on buy-now-btn, navigating to checkout directly");
            window.location.href = cartManager.routes.checkout || "/show-checkout";
            return;
        }

        cartManager.buyNow(productId, quantity, buyBtn);
        return;
    }

    const addBtn = e.target.closest(".add-to-cart-btn");
    if (addBtn) {
        e.preventDefault();

        const productId = addBtn.dataset.productId || addBtn.getAttribute("data-product-id");
        let quantity = addBtn.dataset.quantitySelector || addBtn.dataset.quantity || 1;

        if (!productId) {
            cartManager.showMessage("Không tìm thấy thông tin sản phẩm", "error");
            return;
        }

        cartManager.addToCart(productId, quantity);
        return;
    }
});

// Update cart counter on load
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
        cartManager.updateCartCounter();
    });
} else {
    cartManager.updateCartCounter();
}

// Export for use in other scripts
window.cartManager = cartManager;


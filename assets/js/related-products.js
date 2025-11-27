/**
 * KachoTech Related Products - Add to Cart Handler
 * Handles AJAX add to cart for related products on single product page
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        // Find all add to cart buttons in related products grid
        const cartButtons = document.querySelectorAll('.kt-related-grid .kt-btn-cart');

        cartButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const productId = this.getAttribute('data-product-id');
                const quantity = 1;
                const buttonText = this.innerHTML;

                // Disable button during request
                this.disabled = true;
                this.innerHTML = '<i class="ri-loader-4-line" style="animation: spin 1s linear infinite;"></i> Adding...';

                // Create FormData for WooCommerce add to cart
                const formData = new FormData();
                formData.append('action', 'woocommerce_add_to_cart');
                formData.append('product_id', productId);
                formData.append('quantity', quantity);

                // Send AJAX request
                fetch(ktAjax.ajax_url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Re-enable button
                    this.disabled = false;
                    this.innerHTML = '<i class="ri-shopping-cart-line"></i> Add';

                    // Trigger WooCommerce event for cart update
                    if (window.jQuery) {
                        jQuery(document.body).trigger('added_to_cart');
                    }

                    // Show success message
                    showNotification('Product added to cart!', 'success');

                    // Update mini cart if present
                    if (data && data.cart_contents_count !== undefined) {
                        updateMiniCart();
                    }
                })
                .catch(error => {
                    console.error('Error adding to cart:', error);
                    this.disabled = false;
                    this.innerHTML = buttonText;
                    showNotification('Error adding to cart. Please try again.', 'error');
                });
            });
        });

        /**
         * Show notification toast
         */
        function showNotification(message, type = 'success') {
            // Check if notification already exists
            let notification = document.querySelector('.kt-notification');
            if (!notification) {
                notification = document.createElement('div');
                notification.className = 'kt-notification';
                document.body.appendChild(notification);

                // Add styles dynamically
                const style = document.createElement('style');
                style.innerHTML = `
                    .kt-notification {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        padding: 16px 20px;
                        border-radius: 8px;
                        font-size: 14px;
                        font-weight: 500;
                        z-index: 9999;
                        animation: slideIn 0.3s ease;
                    }

                    .kt-notification.success {
                        background: #10b981;
                        color: #ffffff;
                    }

                    .kt-notification.error {
                        background: #ef4444;
                        color: #ffffff;
                    }

                    @keyframes slideIn {
                        from {
                            transform: translateX(100%);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }

                    @keyframes spin {
                        to {
                            transform: rotate(360deg);
                        }
                    }
                `;
                document.head.appendChild(style);
            }

            notification.textContent = message;
            notification.className = 'kt-notification ' + type;
            notification.style.display = 'block';

            // Auto-hide after 3 seconds
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        /**
         * Update mini cart (if cart plugin is active)
         */
        function updateMiniCart() {
            if (window.jQuery && jQuery.fn.wc_cart_fragments) {
                jQuery(document.body).trigger('wc_fragment_refresh');
            }
        }
    });
})();

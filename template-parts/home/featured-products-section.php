<?php
/**
 * Featured Products Template – KachoTech
 * Displays featured WooCommerce products matching Related Products design
 * 
 * @package KachoTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<style>
	/* Featured Products – Match Related Products Design */
	.kt-featured-wrapper {
		max-width: 1200px;
		margin: 60px auto 0;
		padding: 0 16px;
	}

	.kt-featured-header {
		margin-bottom: 24px;
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		align-items: center;
		gap: 20px;
	}

	.kt-featured-header h2 {
		font-size: 24px;
		font-weight: 700;
		color: #0f172a;
		margin: 0;
		letter-spacing: -0.5px;
	}

	.kt-featured-filters {
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
		align-items: center;
	}

	.kt-filter-btn {
		font-size: 12px;
		font-weight: 600;
		padding: 8px 16px;
		border-radius: 999px;
		border: 1px solid #e5e7eb;
		background: #ffffff;
		color: #6b7280;
		cursor: pointer;
		transition: all 0.2s ease;
		text-transform: uppercase;
		letter-spacing: 0.03em;
	}

	.kt-filter-btn:hover {
		border-color: #0f172a;
		background: #f3f4f6;
	}

	.kt-filter-btn.kt-filter-active {
		background: #0f172a;
		color: #ffffff;
		border-color: #0f172a;
	}

	.kt-featured-grid {
		display: grid;
		grid-template-columns: repeat(4, minmax(0, 1fr));
		gap: 20px;
	}

	.kt-featured-grid .kt-product-card {
		background: #ffffff;
		border-radius: 22px;
		border: 1px solid #e5e7eb;
		padding: 16px;
		display: flex;
		flex-direction: column;
		gap: 12px;
		box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
		transition: transform 0.3s ease, box-shadow 0.3s ease;
	}

	.kt-featured-grid .kt-product-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 20px 45px rgba(15, 23, 42, 0.14);
	}

	.kt-featured-grid .kt-product-card.out-of-stock {
		opacity: 0.8;
	}

	/* Thumbnail */
	.kt-featured-grid .kt-thumb {
		position: relative;
		padding: 12px;
		background: #f9fafb;
		border-radius: 18px;
		display: flex;
		justify-content: center;
		align-items: center;
		min-height: 170px;
		overflow: hidden;
	}

	.kt-featured-grid .kt-thumb a {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;
	}

	.kt-featured-grid .kt-thumb img {
		max-height: 150px;
		width: auto;
		object-fit: contain;
		transition: transform 0.3s ease;
	}

	.kt-featured-grid .kt-product-card:hover .kt-thumb img {
		transform: scale(1.05);
	}

	.kt-featured-grid .kt-product-image-placeholder {
		width: 100%;
		height: 100%;
		background: #e5e7eb;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #9ca3af;
		font-size: 0.8rem;
	}

	/* Badges */
	.kt-featured-grid .kt-badge,
	.kt-featured-grid .kt-stock-status {
		position: absolute;
		font-size: 11px;
		font-weight: 600;
		padding: 4px 10px;
		border-radius: 999px;
	}

	.kt-featured-grid .kt-badge {
		top: 10px;
		left: 10px;
		background: #fee2e2;
		color: #ff2446;
	}

	.kt-featured-grid .kt-badge.kt-badge-hot {
		background: #ff2446;
		color: #ffffff;
	}

	.kt-featured-grid .kt-badge-sale {
		background: #fef3c7;
		color: #b45309;
	}

	.kt-featured-grid .kt-badge-featured {
		background: #dbeafe;
		color: #1d4ed8;
	}

	.kt-featured-grid .kt-stock-status {
		top: 10px;
		right: 10px;
		color: #ef4444;
		background: #ffe4e6;
	}

	.kt-featured-grid .kt-stock-status.in-stock {
		background: #dcfce7;
		color: #10b981;
	}

	/* Rating */
	.kt-featured-grid .kt-rating-row {
		display: flex;
		align-items: center;
		gap: 6px;
		font-size: 13px;
		color: #6b7280;
	}

	.kt-featured-grid .kt-stars i {
		font-size: 15px;
		color: #fbbf24;
	}

	.kt-featured-grid .kt-stars i.fa-regular {
		color: #d1d5db;
	}

	.kt-featured-grid .kt-rating-count {
		font-size: 12px;
	}

	/* Category */
	.kt-featured-grid .kt-category {
		font-size: 11px;
		letter-spacing: 0.15em;
		text-transform: uppercase;
		color: #6b7280;
	}

	/* Title */
	.kt-featured-grid .kt-title {
		font-size: 15px;
		font-weight: 600;
		margin: 0;
		line-height: 1.3;
	}

	.kt-featured-grid .kt-title a {
		color: inherit;
		text-decoration: none;
		transition: color 0.2s ease;
	}

	.kt-featured-grid .kt-title a:hover {
		color: #ff2446;
	}

	/* Price */
	.kt-featured-grid .kt-price-row {
		display: flex;
		gap: 10px;
		align-items: baseline;
	}

	.kt-featured-grid .kt-price-current {
		font-size: 18px;
		font-weight: 700;
		color: #ff2446;
	}

	.kt-featured-grid .kt-price-old {
		font-size: 13px;
		color: #9ca3af;
		text-decoration: line-through;
	}

	/* Availability */
	.kt-featured-grid .kt-availability {
		font-size: 13px;
		color: #10b981;
	}

	.kt-featured-grid .kt-product-card.out-of-stock .kt-availability {
		color: #ef4444;
	}

	/* Footer Actions */
	.kt-featured-grid .kt-footer-actions {
		display: flex;
		gap: 10px;
		margin-top: 8px;
	}

	.kt-featured-grid .kt-btn-cart {
		flex: 1;
		padding: 10px 14px;
		border-radius: 999px;
		background: #111827;
		color: #fff;
		border: none;
		font-size: 14px;
		font-weight: 500;
		cursor: pointer;
		transition: background 0.2s ease, transform 0.1s ease;
		text-decoration: none;
		display: flex;
		align-items: center;
		justify-content: center;
		text-transform: uppercase;
		letter-spacing: 0.03em;
	}

	.kt-featured-grid .kt-btn-cart:hover {
		background: #000;
		transform: translateY(-1px);
	}

	.kt-featured-grid .kt-btn-cart[disabled],
	.kt-featured-grid .kt-product-card.out-of-stock .kt-btn-cart {
		background: #d1d5db;
		cursor: not-allowed;
	}

	.kt-featured-grid .kt-btn-details {
		width: 42px;
		height: 42px;
		background: #ffffff;
		border: 1px solid #e5e7eb;
		border-radius: 999px;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		transition: background 0.2s ease, border-color 0.2s ease;
		flex-shrink: 0;
		color: #0f172a;
		text-decoration: none;
	}

	.kt-featured-grid .kt-btn-details:hover {
		background: #f3f4f6;
		border-color: #ff2446;
		color: #ff2446;
	}

	.kt-featured-no-products {
		text-align: center;
		padding: 40px 20px;
		color: #6b7280;
		grid-column: 1 / -1;
	}

	/* Responsive Design */
	@media (max-width: 1024px) {
		.kt-featured-grid {
			grid-template-columns: repeat(3, minmax(0, 1fr));
		}
	}

	@media (max-width: 768px) {
		.kt-featured-grid {
			grid-template-columns: repeat(2, minmax(0, 1fr));
			gap: 16px;
		}

		.kt-featured-header {
			flex-direction: column;
			align-items: flex-start;
		}

		.kt-featured-header h2 {
			font-size: 20px;
		}
	}

	@media (max-width: 480px) {
		.kt-featured-grid {
			grid-template-columns: 1fr;
		}

		.kt-featured-header h2 {
			font-size: 18px;
		}

		.kt-featured-filters {
			width: 100%;
		}

		.kt-filter-btn {
			flex: 1;
		}
	}
</style>

<div class="kt-featured-wrapper">
	<div class="kt-featured-header">
		<h2><?php esc_html_e( 'Featured Products', 'astra-child' ); ?></h2>
		<div class="kt-featured-filters" id="kt-product-filters">
			<button class="kt-filter-btn kt-filter-active" data-category="all">
				<?php esc_html_e( 'All Products', 'astra-child' ); ?>
			</button>
			<button class="kt-filter-btn" data-category="heaters">
				<?php esc_html_e( 'Heaters', 'astra-child' ); ?>
			</button>
			<button class="kt-filter-btn" data-category="electronics">
				<?php esc_html_e( 'Electronics', 'astra-child' ); ?>
			</button>
			<button class="kt-filter-btn" data-category="cosmetics">
				<?php esc_html_e( 'Cosmetics', 'astra-child' ); ?>
			</button>
		</div>
	</div>

	<div id="kt-featured-products-grid" class="kt-featured-grid">
		<p class="kt-featured-no-products"><?php esc_html_e( 'Loading products...', 'astra-child' ); ?></p>
	</div>
</div>

<script>
(function() {
	const filterBtns = document.querySelectorAll('.kt-filter-btn');
	const productsGrid = document.getElementById('kt-featured-products-grid');

	function loadProducts(category) {
		const formData = new FormData();
		formData.append('action', 'kt_load_featured_products');
		formData.append('category', category);

		fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				productsGrid.innerHTML = data.data.html;
			} else {
				productsGrid.innerHTML = '<p class="kt-featured-no-products"><?php esc_html_e( 'No products found', 'astra-child' ); ?></p>';
			}
		})
		.catch(err => {
			console.error('Error loading products:', err);
			productsGrid.innerHTML = '<p class="kt-featured-no-products"><?php esc_html_e( 'Error loading products', 'astra-child' ); ?></p>';
		});
	}

	filterBtns.forEach(btn => {
		btn.addEventListener('click', function() {
			const category = this.getAttribute('data-category');
			
			// Update button states
			filterBtns.forEach(b => {
				b.classList.remove('kt-filter-active');
			});
			
			this.classList.add('kt-filter-active');
			
			// Load products
			loadProducts(category);
		});
	});

	// Load all products on page load
	loadProducts('all');
})();
</script>

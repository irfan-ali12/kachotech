<?php
/**
 * Promotional Banners Template
 * Displays promotional offers and deals
 * 
 * @package KachoTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get sale products for bottom row
$sale_args = array(
	'post_type'      => 'product',
	'posts_per_page' => 4,
	'meta_key'       => '_sale_price',
	'meta_compare'   => 'EXISTS',
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$sale_products = new WP_Query( $sale_args );
?>

<section class="kt-promos-section" style="max-width: 1200px; margin: 40px auto; padding: 0 16px; width: 100%; box-sizing: border-box;">
	<!-- Top Promo Row -->
	<div class="grid gap-4 md:grid-cols-[2fr_1.3fr_1.2fr]">
		<!-- Primary Promo -->
		<article class="flex items-center gap-6 rounded-3xl bg-gradient-to-br from-[#EC234A] to-[#F9C6B8] p-6 text-white shadow-soft hover:shadow-lg transition">
			<div class="space-y-3">
				<p class="text-[11px] uppercase tracking-wide text-white/80">
					<?php esc_html_e( 'Elegance, Extra Power', 'astra-child' ); ?>
				</p>
				<h3 class="text-xl font-extrabold tracking-tight"><?php esc_html_e( 'Premium Heater Series', 'astra-child' ); ?></h3>
				<p class="text-xs text-white/80 max-w-xs">
					<?php esc_html_e( '1000W ultra heater with dual-safety system for cozy winter nights.', 'astra-child' ); ?>
				</p>
				<div class="flex items-baseline gap-3 text-sm">
					<span class="text-lg font-extrabold">Rs 12,500</span>
					<span class="text-xs text-white/70 line-through">Rs 15,000</span>
				</div>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-semibold text-[#EC234A] shadow-soft hover:shadow-lg transition">
					<?php esc_html_e( 'Shop Now', 'astra-child' ); ?>
					<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
				</a>
			</div>
			<img
				class="hidden h-40 w-auto object-contain drop-shadow-2xl md:block"
				src="https://via.placeholder.com/300x250?text=Premium+Heater"
				alt="<?php esc_attr_e( 'Premium Heater', 'astra-child' ); ?>"
				loading="lazy"
			/>
		</article>

		<!-- Smart Home Deals -->
		<article class="flex items-center justify-between gap-4 rounded-3xl bg-gradient-to-br from-[#FFF4F4] to-[#F6FBFF] p-6 shadow-soft hover:shadow-lg transition">
			<div class="space-y-2">
				<p class="text-[11px] font-semibold text-[#6B6F76]">
					<?php esc_html_e( 'Smart Home Deals', 'astra-child' ); ?>
				</p>
				<h3 class="text-lg font-extrabold leading-tight">
					<?php esc_html_e( 'It\'s More', 'astra-child' ); ?><br />
					<?php esc_html_e( 'Than a Fridge', 'astra-child' ); ?>
				</h3>
				<p class="text-[11px] text-[#6B6F76]">
					<?php esc_html_e( 'Energy-saving refrigerators for the entire family.', 'astra-child' ); ?>
				</p>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="mt-2 inline-flex items-center gap-2 rounded-full bg-[#EC234A] px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-[#C9193A] transition">
					<?php esc_html_e( 'Up to 45% OFF', 'astra-child' ); ?>
					<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
				</a>
			</div>
			<img
				class="hidden h-32 w-auto object-contain md:block"
				src="https://via.placeholder.com/200x150?text=Kitchen+Electronics"
				alt="<?php esc_attr_e( 'Kitchen Electronics', 'astra-child' ); ?>"
				loading="lazy"
			/>
		</article>

		<!-- Wireless Audio -->
		<article class="flex items-center justify-between gap-4 rounded-3xl bg-gradient-to-br from-[#FFE4F0] to-[#F3F7FF] p-6 shadow-soft hover:shadow-lg transition">
			<div class="space-y-2">
				<p class="text-[11px] font-semibold text-[#6B6F76]">
					<?php esc_html_e( 'Wireless Audio', 'astra-child' ); ?>
				</p>
				<h3 class="text-lg font-extrabold leading-tight">
					<?php esc_html_e( 'Audio Buds', 'astra-child' ); ?>
				</h3>
				<p class="text-[11px] text-[#6B6F76]">
					<?php esc_html_e( 'Deep bass earbuds with noise isolation & fast pairing.', 'astra-child' ); ?>
				</p>
				<p class="text-sm font-bold text-[#1A1A1D]">Rs 5,499</p>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="inline-flex items-center gap-2 rounded-full border border-[#1A1A1D]/10 bg-white px-3 py-1.5 text-[11px] font-semibold text-[#1A1A1D] hover:bg-[#1A1A1D] hover:text-white transition">
					<?php esc_html_e( 'Shop Now', 'astra-child' ); ?>
					<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
				</a>
			</div>
			<img
				class="hidden h-28 w-auto object-contain md:block"
				src="https://via.placeholder.com/200x120?text=Audio+Buds"
				alt="<?php esc_attr_e( 'Audio Buds', 'astra-child' ); ?>"
				loading="lazy"
			/>
		</article>
	</div>

	<!-- Sale Banner -->
	<div class="mt-12 mb-12" style="max-width: 1200px; margin-top:20px;">
		<article class="rounded-2xl bg-cover bg-center bg-contain bg-no-repeat flex items-center px-12" style="background-image: url('http://kachotech.com/wp-content/uploads/2025/12/sale-banner-img.jpg'); background-color: #1A1A1D; min-height: 300px; border: 1px solid rgba(0, 0, 0, 0.1); padding-left: 20px; background-size: contain; background-repeat: no-repeat; background-position: right;">
			
			<!-- Left: Sale Text -->
			<div class="flex-1">
				<p class="text-xs font-semibold text-[#EC234A] mb-3 uppercase tracking-widest">
					<?php esc_html_e( 'Limited Time Offer', 'astra-child' ); ?>
				</p>
				<h2 class="text-2xl font-bold text-white mb-6 leading-tight">
					<?php esc_html_e( 'Premium Heating', 'astra-child' ); ?>
					<br />
					<?php esc_html_e( 'Solutions', 'astra-child' ); ?>
				</h2>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-semibold text-[#EC234A] shadow-soft hover:shadow-lg transition">
					<?php esc_html_e( 'Shop Now', 'astra-child' ); ?>
					<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
				</a>
			</div>
		</article>
	</div>

	<!-- Products on Sale Section -->
	<div class="kt-sale-products-wrapper" style="margin-top: 40px; max-width: 1200px; margin-left: auto; margin-right: auto;">
		<div class="kt-sale-products-header" style="margin-bottom: 24px;">
			<h2 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; letter-spacing: -0.5px;">
				<?php esc_html_e( 'Products on Sale', 'astra-child' ); ?>
			</h2>
		</div>

		<div id="kt-sale-products-grid" class="kt-featured-grid">
			<!-- Sale products will load here via AJAX -->
		</div>
	</div>

	<script>
	(function() {
		const saleProductsGrid = document.getElementById('kt-sale-products-grid');

		function loadSaleProducts() {
			const formData = new FormData();
			formData.append('action', 'kt_load_sale_products');

			fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					saleProductsGrid.innerHTML = data.data.html;
				} else {
					saleProductsGrid.innerHTML = '<p style="text-align: center; padding: 40px 20px; color: #6b7280; grid-column: 1 / -1;"><?php esc_html_e( 'No products on sale', 'astra-child' ); ?></p>';
				}
			})
			.catch(err => {
				console.error('Error loading sale products:', err);
				saleProductsGrid.innerHTML = '<p style="text-align: center; padding: 40px 20px; color: #6b7280; grid-column: 1 / -1;"><?php esc_html_e( 'Error loading products', 'astra-child' ); ?></p>';
			});
		}

		// Load sale products on page load
		loadSaleProducts();
	})();
	</script>

</section>

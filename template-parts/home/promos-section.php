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

<section class="mx-auto mt-10 max-w-6xl px-4">
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

	<!-- Bottom Sale + Mini Products -->
	<div class="mt-4 grid gap-4 md:grid-cols-[1.6fr_1fr_1fr_1fr_1fr]">
		<!-- Countdown Timer -->
		<article class="flex flex-col justify-between rounded-3xl bg-gradient-to-br from-[#FFE6F0] via-[#FFF4DF] to-[#EAF5FF] p-6 shadow-soft">
			<div>
				<h3 class="text-lg font-extrabold"><?php esc_html_e( 'Biggest Friday Sale', 'astra-child' ); ?></h3>
				<p class="mt-2 text-xs text-[#6B6F76] max-w-xs">
					<?php esc_html_e( 'Limited-time offers on heaters, audio and cosmetics. Stock moves fast – grab your winter bundle.', 'astra-child' ); ?>
				</p>
				<div class="mt-4 grid grid-cols-4 gap-2 text-center text-[11px]">
					<div class="rounded-2xl bg-white/80 p-2 shadow-soft">
						<div class="text-sm font-extrabold">02</div>
						<div class="text-[10px] text-[#6B6F76]"><?php esc_html_e( 'Days', 'astra-child' ); ?></div>
					</div>
					<div class="rounded-2xl bg-white/80 p-2 shadow-soft">
						<div class="text-sm font-extrabold">05</div>
						<div class="text-[10px] text-[#6B6F76]"><?php esc_html_e( 'Hours', 'astra-child' ); ?></div>
					</div>
					<div class="rounded-2xl bg-white/80 p-2 shadow-soft">
						<div class="text-sm font-extrabold">33</div>
						<div class="text-[10px] text-[#6B6F76]"><?php esc_html_e( 'Minutes', 'astra-child' ); ?></div>
					</div>
					<div class="rounded-2xl bg-white/80 p-2 shadow-soft">
						<div class="text-sm font-extrabold">45</div>
						<div class="text-[10px] text-[#6B6F76]"><?php esc_html_e( 'Seconds', 'astra-child' ); ?></div>
					</div>
				</div>
			</div>
			<div class="mt-4 flex items-center justify-between gap-3">
				<p class="text-[10px] text-[#6B6F76]">
					<?php esc_html_e( 'Special prices apply automatically at checkout on eligible items.', 'astra-child' ); ?>
				</p>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="inline-flex items-center gap-2 rounded-full bg-[#EC234A] px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-[#C9193A] transition">
					<?php esc_html_e( 'See All', 'astra-child' ); ?>
					<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
				</a>
			</div>
		</article>

		<!-- Mini Product Cards -->
		<?php
		if ( $sale_products->have_posts() ) {
			while ( $sale_products->have_posts() ) {
				$sale_products->the_post();
				$product = wc_get_product( get_the_ID() );
				?>
				<article class="rounded-3xl bg-white p-3 shadow-soft hover:shadow-lg transition">
					<div class="relative rounded-2xl bg-[#F6F7FA] p-3">
						<span class="absolute left-2 top-2 rounded-full bg-[#FFE7EC] px-2 py-0.5 text-[10px] font-bold text-[#EC234A]">
							<?php esc_html_e( 'SALE', 'astra-child' ); ?>
						</span>
						<span class="absolute right-2 top-2 text-[10px] font-semibold text-[#40C6A8]">
							<?php esc_html_e( 'In Stock', 'astra-child' ); ?>
						</span>
						<?php
						if ( has_post_thumbnail() ) {
							echo '<img src="' . esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ) . '" alt="' . esc_attr( get_the_title() ) . '" class="mx-auto h-24 w-auto object-contain" />';
						} else {
							echo '<img src="https://via.placeholder.com/150x150?text=' . urlencode( get_the_title() ) . '" alt="' . esc_attr( get_the_title() ) . '" class="mx-auto h-24 w-auto object-contain" />';
						}
						?>
					</div>
					<div class="mt-2 text-[11px]">
						<h4 class="line-clamp-2 font-semibold"><?php the_title(); ?></h4>
						<div class="mt-1 flex items-baseline gap-2">
							<span class="text-[13px] font-extrabold">
								<?php echo wp_kses_post( $product->get_price_html() ); ?>
							</span>
						</div>
						<p class="mt-1 text-[10px] font-semibold text-[#40C6A8]">
							<?php
							$stock = $product->get_stock_quantity();
							printf( esc_html_e( '%d in stock', 'astra-child' ), absint( $stock ? $stock : 0 ) );
							?>
						</p>
					</div>
				</article>
				<?php
			}
			wp_reset_postdata();
		}
		?>
	</div>
</section>

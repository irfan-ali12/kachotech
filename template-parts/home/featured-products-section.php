<?php
/**
 * Featured Products Template
 * Displays featured WooCommerce products
 * 
 * @package KachoTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get featured products
$args = array(
	'post_type'      => 'product',
	'posts_per_page' => 8,
	'meta_key'       => '_featured',
	'meta_value'     => 'yes',
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$featured_products = new WP_Query( $args );

// If no featured products, get latest products
if ( ! $featured_products->have_posts() ) {
	$args['meta_key']   = '';
	$args['meta_value'] = '';
	$featured_products  = new WP_Query( $args );
}
?>

<section class="mx-auto mt-6 max-w-6xl px-4 pb-4">
	<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
		<h2 class="text-base font-semibold"><?php esc_html_e( 'Featured Products', 'astra-child' ); ?></h2>
		<div class="flex flex-wrap gap-2 text-[11px]">
			<button class="rounded-full border border-[#1A1A1D] bg-[#1A1A1D] text-white px-3 py-1 font-semibold">
				<?php esc_html_e( 'All Products', 'astra-child' ); ?>
			</button>
			<button class="rounded-full border border-[#E4E6EC] bg-white text-[#6B6F76] px-3 py-1 font-semibold hover:border-[#1A1A1D]/50">
				<?php esc_html_e( 'Heaters', 'astra-child' ); ?>
			</button>
			<button class="rounded-full border border-[#E4E6EC] bg-white text-[#6B6F76] px-3 py-1 font-semibold hover:border-[#1A1A1D]/50">
				<?php esc_html_e( 'Electronics', 'astra-child' ); ?>
			</button>
			<button class="rounded-full border border-[#E4E6EC] bg-white text-[#6B6F76] px-3 py-1 font-semibold hover:border-[#1A1A1D]/50">
				<?php esc_html_e( 'Cosmetics', 'astra-child' ); ?>
			</button>
		</div>
	</div>

	<div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
		<?php
		if ( $featured_products->have_posts() ) {
			while ( $featured_products->have_posts() ) {
				$featured_products->the_post();
				$product    = wc_get_product( get_the_ID() );
				$badge      = $product->is_on_sale() ? 'SALE' : ( $product->is_featured() ? 'FEATURED' : 'NEW' );
				$price_html = $product->get_price_html();
				?>
				<article class="flex flex-col rounded-3xl border border-[#edf0f6] bg-white p-3 shadow-soft transition hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(15,18,32,0.15)]">
					<div class="relative rounded-2xl bg-[#F6F7FA] p-3">
						<span class="absolute left-2 top-2 rounded-full bg-[#FFE7EC] px-2 py-0.5 text-[10px] font-bold text-[#EC234A]">
							<?php echo esc_html( $badge ); ?>
						</span>
						<span class="absolute right-2 top-2 text-[10px] font-semibold text-[#40C6A8]">
							<?php esc_html_e( 'Stock', 'astra-child' ); ?>
						</span>
						<?php
						if ( has_post_thumbnail() ) {
							echo '<img src="' . esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ) . '" alt="' . esc_attr( get_the_title() ) . '" class="mx-auto h-28 w-auto object-contain" />';
						} else {
							echo '<img src="https://via.placeholder.com/200x200?text=' . urlencode( get_the_title() ) . '" alt="' . esc_attr( get_the_title() ) . '" class="mx-auto h-28 w-auto object-contain" />';
						}
						?>
					</div>
					<div class="mt-3 flex flex-1 flex-col gap-1 text-xs">
						<span class="text-[10px] font-semibold uppercase tracking-wide text-[#6B6F76]">
							<?php echo esc_html( $product->get_attribute( 'pa_brand' ) ? $product->get_attribute( 'pa_brand' ) : 'KachoTech' ); ?>
						</span>
						<h3 class="line-clamp-2 text-[13px] font-semibold text-[#1A1A1D]">
							<?php the_title(); ?>
						</h3>
						<div class="mt-1 flex items-baseline gap-2 text-[13px]">
							<span class="font-extrabold">
								<?php echo wp_kses_post( $price_html ); ?>
							</span>
						</div>
						<span class="text-[11px] font-semibold text-[#40C6A8]">
							<?php
							$stock = $product->get_stock_quantity();
							printf( esc_html_e( 'Available: %d pcs', 'astra-child' ), absint( $stock ? $stock : 0 ) );
							?>
						</span>
						<div class="mt-2 flex gap-2">
							<form method="post" action="<?php echo esc_url( wc_get_cart_url() ); ?>" class="flex-1">
								<button type="submit" name="add-to-cart" value="<?php echo esc_attr( get_the_ID() ); ?>" class="w-full inline-flex items-center justify-center rounded-full bg-[#1A1A1D] px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-[#EC234A] transition">
									<?php esc_html_e( 'Add to Cart', 'astra-child' ); ?>
								</button>
							</form>
							<a href="<?php the_permalink(); ?>" class="flex h-8 w-8 items-center justify-center rounded-full border border-[#E4E6EC] bg-white text-xs text-[#1A1A1D] transition hover:border-[#1A1A1D] hover:bg-[#1A1A1D] hover:text-white">
								<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
							</a>
						</div>
					</div>
				</article>
				<?php
			}
			wp_reset_postdata();
		} else {
			echo '<p class="text-center col-span-full text-[#6B6F76]">' . esc_html_e( 'No products found', 'astra-child' ) . '</p>';
		}
		?>
	</div>
</section>

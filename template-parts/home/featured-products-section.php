<?php
/**
 * Featured Products Template
 * Displays featured WooCommerce products with category filtering
 * 
 * @package KachoTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get category filter from URL
$current_category = isset( $_GET['product_cat'] ) ? sanitize_text_field( $_GET['product_cat'] ) : 'all';

// Get featured products
$args = array(
	'post_type'      => 'product',
	'posts_per_page' => 8,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

// Add category filter if not 'all'
if ( 'all' !== $current_category && ! empty( $current_category ) ) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $current_category,
		),
	);
}

$featured_products = new WP_Query( $args );

// If no products found, try featured only
if ( ! $featured_products->have_posts() && 'all' === $current_category ) {
	$args['meta_key']   = '_featured';
	$args['meta_value'] = 'yes';
	$featured_products  = new WP_Query( $args );
}
?>

<section class="mx-auto mt-6 max-w-6xl px-4 pb-4">
	<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
		<h2 class="text-base font-semibold"><?php esc_html_e( 'Featured Products', 'astra-child' ); ?></h2>
		<div class="flex flex-wrap gap-2 text-[11px]">
			<a href="<?php echo esc_url( remove_query_arg( 'product_cat' ) ); ?>" class="rounded-full border <?php echo 'all' === $current_category ? 'border-[#1A1A1D] bg-[#1A1A1D] text-white' : 'border-[#E4E6EC] bg-white text-[#6B6F76] hover:border-[#1A1A1D]/50'; ?> px-3 py-1 font-semibold transition">
				<?php esc_html_e( 'All Products', 'astra-child' ); ?>
			</a>
			<a href="<?php echo esc_url( add_query_arg( 'product_cat', 'heaters' ) ); ?>" class="rounded-full border <?php echo 'heaters' === $current_category ? 'border-[#1A1A1D] bg-[#1A1A1D] text-white' : 'border-[#E4E6EC] bg-white text-[#6B6F76] hover:border-[#1A1A1D]/50'; ?> px-3 py-1 font-semibold transition">
				<?php esc_html_e( 'Heaters', 'astra-child' ); ?>
			</a>
			<a href="<?php echo esc_url( add_query_arg( 'product_cat', 'electronics' ) ); ?>" class="rounded-full border <?php echo 'electronics' === $current_category ? 'border-[#1A1A1D] bg-[#1A1A1D] text-white' : 'border-[#E4E6EC] bg-white text-[#6B6F76] hover:border-[#1A1A1D]/50'; ?> px-3 py-1 font-semibold transition">
				<?php esc_html_e( 'Electronics', 'astra-child' ); ?>
			</a>
			<a href="<?php echo esc_url( add_query_arg( 'product_cat', 'cosmetics' ) ); ?>" class="rounded-full border <?php echo 'cosmetics' === $current_category ? 'border-[#1A1A1D] bg-[#1A1A1D] text-white' : 'border-[#E4E6EC] bg-white text-[#6B6F76] hover:border-[#1A1A1D]/50'; ?> px-3 py-1 font-semibold transition">
				<?php esc_html_e( 'Cosmetics', 'astra-child' ); ?>
			</a>
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
							<?php
							$stock = $product->get_stock_quantity();
							echo $stock > 0 ? esc_html_e( 'In Stock', 'astra-child' ) : esc_html_e( 'Out of Stock', 'astra-child' );
							?>
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
							<?php
							$categories = $product->get_category_ids();
							if ( ! empty( $categories ) ) {
								$cat = get_term( $categories[0], 'product_cat' );
								echo esc_html( $cat->name );
							} else {
								esc_html_e( 'KachoTech', 'astra-child' );
							}
							?>
						</span>
						<h3 class="line-clamp-2 text-[13px] font-semibold text-[#1A1A1D]">
							<a href="<?php the_permalink(); ?>" class="hover:text-[#EC234A] transition">
								<?php the_title(); ?>
							</a>
						</h3>
						<div class="mt-1 flex items-baseline gap-2 text-[13px]">
							<span class="font-extrabold">
								<?php echo wp_kses_post( $price_html ); ?>
							</span>
						</div>
						<span class="text-[11px] font-semibold text-[#40C6A8]">
							<?php
							$stock = $product->get_stock_quantity();
							printf( esc_html__( 'Available: %d pcs', 'astra-child' ), absint( $stock ? $stock : 0 ) );
							?>
						</span>
						<div class="mt-2 flex gap-2">
							<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="flex-1 inline-flex items-center justify-center rounded-full bg-[#1A1A1D] px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-[#EC234A] transition">
								<?php esc_html_e( 'Add to Cart', 'astra-child' ); ?>
							</a>
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

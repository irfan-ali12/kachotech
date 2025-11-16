<?php
/**
 * Category Strip Template
 * Displays product categories with images
 * 
 * @package KachoTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get product categories
$categories = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'number'     => 4,
	'orderby'    => 'term_order',
	'order'      => 'ASC',
) );
?>

<section class="mx-auto mt-8 max-w-6xl px-4">
	<div class="mb-3 flex items-center justify-between text-sm">
		<h2 class="text-base font-semibold"><?php esc_html_e( 'Shop Deals by Category', 'astra-child' ); ?></h2>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-[#EC234A] hover:underline">
			<?php esc_html_e( 'All Categories', 'astra-child' ); ?>
			<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
		</a>
	</div>

	<div class="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-4">
		<?php
		if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
				$image_url    = wp_get_attachment_url( $thumbnail_id );
				if ( ! $image_url ) {
					$image_url = 'https://via.placeholder.com/100x100?text=' . urlencode( $category->name );
				}
				?>
				<a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="flex flex-col items-center gap-2 rounded-2xl border border-[#E4E6EC] bg-white p-3 text-center text-xs shadow-soft transition hover:-translate-y-0.5 hover:shadow-soft hover:border-[#EC234A]">
					<div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F6F7FA]">
						<img
							src="<?php echo esc_url( $image_url ); ?>"
							alt="<?php echo esc_attr( $category->name ); ?>"
							class="h-10 w-10 object-contain"
							loading="lazy"
						/>
					</div>
					<span class="font-semibold text-[11px]"><?php echo esc_html( $category->name ); ?></span>
				</a>
				<?php
			}
		} else {
			// Fallback categories
			$fallback_categories = array(
				array( 'name' => 'All Products', 'icon' => 'Store' ),
				array( 'name' => 'Heaters', 'icon' => 'Heater' ),
				array( 'name' => 'Electronics', 'icon' => 'Tv' ),
				array( 'name' => 'Cosmetics', 'icon' => 'Beauty' ),
			);
			foreach ( $fallback_categories as $cat ) {
				?>
				<div class="flex flex-col items-center gap-2 rounded-2xl border border-[#E4E6EC] bg-white p-3 text-center text-xs shadow-soft">
					<div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F6F7FA]">
						<img
							src="https://via.placeholder.com/100x100?text=<?php echo urlencode( $cat['name'] ); ?>"
							alt="<?php echo esc_attr( $cat['name'] ); ?>"
							class="h-10 w-10 object-contain"
							loading="lazy"
						/>
					</div>
					<span class="font-semibold text-[11px]"><?php echo esc_html( $cat['name'] ); ?></span>
				</div>
				<?php
			}
		}
		?>
	</div>
</section>

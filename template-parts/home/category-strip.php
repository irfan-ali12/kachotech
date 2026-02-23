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

// Define the specific categories to display in order
$category_slugs = array( 'heaters', 'gas-kerosene', 'electric', 'home-appliances', 'kitchen', 'cosmetics-personal-care' );

// Get categories by slug
$categories = array();
foreach ( $category_slugs as $slug ) {
	$cat = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $cat && ! is_wp_error( $cat ) ) {
		$categories[] = $cat;
	}
}
?>

<section class="kt-category-strip">
	<div class="kt-category-strip-header">
		<h2 class="kt-category-strip-title"><?php esc_html_e( 'Shop Deals by Category', 'astra-child' ); ?></h2>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="kt-category-strip-link">
			<?php esc_html_e( 'All Categories', 'astra-child' ); ?>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
		</a>
	</div>

	<div class="kt-category-grid">
		<?php
		if ( ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
				$image_url    = wp_get_attachment_url( $thumbnail_id );
				
				// Use placeholder if no image
				if ( ! $image_url ) {
					$image_url = 'https://via.placeholder.com/300x300?text=' . urlencode( $category->name );
				}
				?>
				<a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="kt-category-card">
					<div class="kt-category-card-icon">
						<img
							src="<?php echo esc_url( $image_url ); ?>"
							alt="<?php echo esc_attr( $category->name ); ?>"
							loading="lazy"
							width="300"
							height="300"
						/>
					</div>
					<span class="kt-category-card-name"><?php echo esc_html( $category->name ); ?></span>
				</a>
				<?php
			}
		}
		?>
	</div>
</section>

<?php
/**
 * Home Page Template
 * Main template for the KachoTech homepage
 * 
 * @package KachoTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="min-h-screen bg-[#F6F7FA] text-[#1A1A1D]">
	<?php
	// Hero Section
	get_template_part( 'template-parts/home/hero-section' );

	// Category Strip
	get_template_part( 'template-parts/home/category-strip' );

	// Featured Products
	get_template_part( 'template-parts/home/featured-products-section' );

	// Promotional Banners
	get_template_part( 'template-parts/home/promos-section' );

	// Perks and Mini Promos
	get_template_part( 'template-parts/home/perks-section' );
	?>
</main>

<?php
// Footer
get_template_part( 'template-parts/home/footer-section' );

get_footer();

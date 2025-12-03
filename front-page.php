<?php
/**
 * Template Name: KachoTech Home
 * Description: Custom homepage template for the KachoTech website.
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

	// Featured Products Section
	get_template_part( 'template-parts/home/featured-products-section' );

	// Promotional Banners
	get_template_part( 'template-parts/home/promos-section' );


	// Perks Section
	get_template_part( 'template-parts/home/perks-section' );
	?>

</main>

<?php
// Footer is now loaded globally via footer.php
get_footer();


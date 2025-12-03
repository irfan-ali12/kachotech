<?php
/**
 * KachoTech Child - Footer Wrapper
 * Closes the HTML markup and enqueues footer hooks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

	</main><!-- Close main from header.php -->

	<?php
	/**
	 * Load custom KachoTech footer template-part
	 */
	get_template_part( 'template-parts/home/footer-section' );
	
	/**
	 * Astra footer hooks - keep compatibility
	 */
	do_action( 'astra_footer_after' );

	wp_footer();
	?>

</body>
</html>

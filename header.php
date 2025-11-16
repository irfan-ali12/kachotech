<?php
/**
 * KachoTech Child - Header Wrapper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
/**
 * Astra standard hooks (keep compatibility)
 */
do_action( 'astra_header_before' );

// Our custom header layout:
get_template_part( 'template-parts/header/header', 'main' );

do_action( 'astra_header_after' );

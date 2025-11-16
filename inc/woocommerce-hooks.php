<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX: live product search for header
 */
function kachotech_live_search() {
	check_ajax_referer( 'kt_live_search_nonce', 'nonce' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		wp_send_json_error();
	}

	$q   = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
	$cat = isset( $_GET['cat'] ) ? sanitize_text_field( wp_unslash( $_GET['cat'] ) ) : '';

	if ( strlen( $q ) < 2 ) {
		wp_send_json_success( array() );
	}

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		's'              => $q,
		'meta_query'     => WC()->query->get_meta_query(),
		'tax_query'      => array(),
	);

	if ( $cat ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $cat,
		);
	}

	$query = new WP_Query( $args );
	$items = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$product = wc_get_product( get_the_ID() );

			$items[] = array(
				'id'        => get_the_ID(),
				'name'      => get_the_title(),
				'permalink' => get_permalink(),
				'image'     => get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_thumbnail' ),
				'price'     => $product && '' !== $product->get_price()
					? wc_price( $product->get_price() )
					: '',
				'price_html' => $product ? $product->get_price_html() : '',
			);
		}
		wp_reset_postdata();
	}

	wp_send_json_success( $items );
}

add_action( 'wp_ajax_kt_live_search', 'kachotech_live_search' );
add_action( 'wp_ajax_nopriv_kt_live_search', 'kachotech_live_search' );


/**
 * AJAX: Add simple product to cart from hero section
 */
function kt_hero_add_to_cart() {
	check_ajax_referer( 'kt_hero_nonce', 'nonce' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		wp_send_json_error( array( 'message' => 'WooCommerce not available' ) );
	}

	$product_id = isset( $_POST['product_id'] ) ? intval( wp_unslash( $_POST['product_id'] ) ) : 0;
	$quantity   = isset( $_POST['quantity'] ) ? max( 1, intval( wp_unslash( $_POST['quantity'] ) ) ) : 1;

	if ( $product_id <= 0 ) {
		wp_send_json_error( array( 'message' => 'Invalid product' ) );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product || ! $product->is_purchasable() ) {
		wp_send_json_error( array( 'message' => 'Product not purchasable' ) );
	}

	$added = WC()->cart->add_to_cart( $product_id, $quantity );

	if ( $added ) {
		$count = WC()->cart->get_cart_contents_count();
		$total = WC()->cart->get_cart_total();
		wp_send_json_success( array( 'count' => $count, 'total' => $total ) );
	}

	wp_send_json_error( array( 'message' => 'Unable to add to cart' ) );
}

add_action( 'wp_ajax_kt_hero_add_to_cart', 'kt_hero_add_to_cart' );
add_action( 'wp_ajax_nopriv_kt_hero_add_to_cart', 'kt_hero_add_to_cart' );

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function kachotech_child_enqueue_scripts() {

	// Parent Astra CSS
	wp_enqueue_style(
		'astra-theme-css',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( 'astra' )->get( 'Version' )
	);

	// Child theme main CSS (put header styles here)
	wp_enqueue_style(
		'kachotech-child-css',
		get_stylesheet_uri(),
		array( 'astra-theme-css' ),
		wp_get_theme()->get( 'Version' )
	);

	// Remixicon (for header icons)
	wp_enqueue_style(
		'remixicon',
		'https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css',
		array(),
		'4.2.0'
	);

	// Header CSS
	wp_enqueue_style(
		'kt-header-custom-css',
		get_stylesheet_directory_uri() . '/assets/css/header-custom.css',
		array(),
		'1.0'
	);

	// Homepage and section styles (load on all front-end pages to ensure availability)
	if ( ! is_admin() ) {

		// Tailwind Play CDN (provides utility classes used by hero markup)
		wp_register_script(
			'tailwind-cdn',
			'https://cdn.tailwindcss.com',
			array(),
			null,
			false
		);

		// Inline tailwind.config to extend theme colors used in hero
		$tw_config = "tailwind.config = { theme: { extend: { colors: { kt: { primary: '#ff2446', dark: '#050816' } } } } };";
		wp_add_inline_script( 'tailwind-cdn', $tw_config );
		wp_enqueue_script( 'tailwind-cdn' );

		// Homepage common CSS (if present)
		wp_enqueue_style(
			'kachotech-homepage-css',
			get_stylesheet_directory_uri() . '/assets/css/homepage.css',
			array( 'kachotech-child-css' ),
			wp_get_theme()->get( 'Version' )
		);

		// Hero styles (ensure available on front-end)
		wp_enqueue_style(
			'kt-hero-css',
			get_stylesheet_directory_uri() . '/assets/css/hero.css',
			array( 'kachotech-homepage-css' ),
			'1.0'
		);

		// Hero JS (carousel + add-to-cart)
		wp_enqueue_script(
			'kt-hero-js',
			get_stylesheet_directory_uri() . '/assets/js/hero.js',
			array(),
			'1.0',
			true
		);

		wp_localize_script( 'kt-hero-js', 'KT_AJAX', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'hero_nonce' => wp_create_nonce( 'kt_hero_nonce' ),
		) );

		// Ensure WooCommerce scripts are loaded for AJAX functionality
		if ( function_exists( 'wp_enqueue_cart' ) ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'woocommerce' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'kachotech_child_enqueue_scripts', 20 );

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

	// Font Awesome (for star ratings and icons) - loaded globally for all pages
	wp_enqueue_style(
		'font-awesome-css',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
		array(),
		'6.5.1'
	);

	// Header CSS
	wp_enqueue_style(
		'kt-header-custom-css',
		get_stylesheet_directory_uri() . '/assets/css/header-custom.css',
		array(),
		'1.0'
	);

	// Load Tailwind CSS on homepage and product pages
	if ( ! is_admin() && ( is_home() || is_front_page() || is_product() ) ) {
		
		// Load locally compiled Tailwind CSS (no CDN dependency)
		// Built from assets/css/tailwind-input.css using npm run build
		wp_enqueue_style(
			'tailwind-local-css',
			get_stylesheet_directory_uri() . '/assets/css/tailwind.min.css',
			array(),
			'1.0'
		);
	}

	// Shop and cart page styles
	if ( ! is_admin() && ( is_cart() || is_checkout() || is_shop() || is_product_category() || is_product_tag() ) ) {
		wp_enqueue_style(
			'kt-shop-layout-css',
			get_stylesheet_directory_uri() . '/assets/css/shop-layout.css',
			array( 'kachotech-child-css' ),
			'1.0'
		);
		
		// Cart page specific styles
		if ( is_cart() ) {
			wp_enqueue_style(
				'kt-cart-custom-css',
				get_stylesheet_directory_uri() . '/assets/css/cart-custom.css',
				array( 'kachotech-child-css' ),
				'1.0'
			);
		}
	}

	// Homepage and section styles (load on homepage only)
	if ( ! is_admin() && ( is_home() || is_front_page() ) ) {

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

		// Shop JS for sale products add to cart functionality
		wp_enqueue_script(
			'kt-shop-js',
			get_stylesheet_directory_uri() . '/assets/js/shop.js',
			array( 'jquery', 'woocommerce' ),
			'1.0',
			true
		);

		wp_localize_script( 'kt-hero-js', 'KT_AJAX', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'hero_nonce' => wp_create_nonce( 'kt_hero_nonce' ),
		) );

		// Localize shop script for AJAX
		wp_localize_script( 'kt-shop-js', 'ktShopAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'kt_shop_nonce' ),
		) );
	}

	// Ensure WooCommerce scripts are loaded for AJAX functionality on all pages
	if ( ! is_admin() ) {
		if ( function_exists( 'wp_enqueue_cart' ) ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'woocommerce' );
		}
	}

	// Single product page scripts and styles
	if ( is_product() ) {
		// Load shop layout CSS so related products have proper styling
		wp_enqueue_style(
			'kt-shop-layout-css',
			get_stylesheet_directory_uri() . '/assets/css/shop-layout.css',
			array( 'kachotech-child-css' ),
			'1.0'
		);

		// Related products CSS to match shop page design
		wp_enqueue_style(
			'kt-related-products-css',
			get_stylesheet_directory_uri() . '/assets/css/related-products.css',
			array( 'kachotech-child-css' ),
			'1.0'
		);

		wp_enqueue_script(
			'kt-single-product-js',
			get_stylesheet_directory_uri() . '/assets/js/single-product.js',
			array(),
			'1.0',
			true
		);

		// Related products add to cart handler
		wp_enqueue_script(
			'kt-related-products-js',
			get_stylesheet_directory_uri() . '/assets/js/related-products.js',
			array( 'jquery', 'woocommerce' ),
			'1.0',
			true
		);

		// Localize AJAX data for related products
		wp_localize_script( 'kt-related-products-js', 'ktAjax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'woocommerce-add-to-cart' ),
		) );
	}

	// Global page loader for entire site
	wp_enqueue_script(
		'kt-global-loader-js',
		get_stylesheet_directory_uri() . '/assets/js/global-loader.js',
		array( 'jquery' ),
		'1.0',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'kachotech_child_enqueue_scripts', 20 );

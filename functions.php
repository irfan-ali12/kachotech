<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

// Add theme support for child theme
add_action( 'after_setup_theme', function() {
    // Make sure parent loads first
    add_theme_support( 'astra-child-theme' );
});

/**
 * Disable Astra's default header to use custom header
 */
add_action( 'after_setup_theme', function() {
	// Remove Astra's default header
	remove_action( 'astra_header', 'astra_header_markup' );
}, 15 );

require get_stylesheet_directory() . '/inc/setup.php';
require get_stylesheet_directory() . '/inc/enqueue.php';
require get_stylesheet_directory() . '/inc/search-ajax.php';
require get_stylesheet_directory() . '/inc/diagnostic.php';
require get_stylesheet_directory() . '/inc/shortcodes.php';
require get_stylesheet_directory() . '/inc/woocommerce-hooks.php';
require get_stylesheet_directory() . '/inc/header-hooks.php';
require get_stylesheet_directory() . '/inc/helpers.php';



/**
 * KachoTech header helpers – dashicons + ajax search
 */

// Front-end assets
add_action( 'wp_enqueue_scripts', function () {
    // Dashicons (WordPress icon set)
    wp_enqueue_style( 'dashicons' );

    // Header CSS
    wp_enqueue_style(
        'kt-header-custom-css',
        get_stylesheet_directory_uri() . '/assets/css/header-custom.css',
        array(),
        '1.0'
    );

    // AJAX product search script
    wp_enqueue_script(
        'kt-ajax-search',
        get_stylesheet_directory_uri() . '/assets/js/kt-ajax-search.js',
        array( 'jquery' ),
        '1.0',
        true
    );

    wp_localize_script(
        'kt-ajax-search',
        'ktAjaxSearch',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'kt_ajax_search' ),
            'minChars'=> 2,
        )
    );
});

// AJAX handler removed - now in inc/search-ajax.php

/**
 * Serve a modern order-tracking view when visitor uses ?order-tracking=1
 * Falls back to a dedicated page if user created one with slug 'order-tracking'.
 */
add_action( 'template_redirect', function() {
    if ( empty( $_GET['order-tracking'] ) ) {
        return;
    }

    // Allow plugins/themes to short-circuit
    if ( apply_filters( 'kachotech_disable_order_tracking_endpoint', false ) ) {
        return;
    }

    status_header( 200 );
    nocache_headers();

    get_header();

    echo '<main class="site-main">';
    // Load the modern order tracking template
    include get_stylesheet_directory() . '/template-parts/order-tracking-page.php';
    echo '</main>';

    get_footer();
    exit;
} );


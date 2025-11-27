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
require get_stylesheet_directory() . '/inc/shop-ajax.php';



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

/**
 * AJAX Handler: Load Featured Products with Category Filter
 */
add_action( 'wp_ajax_kt_load_featured_products', 'kt_load_featured_products' );
add_action( 'wp_ajax_nopriv_kt_load_featured_products', 'kt_load_featured_products' );

function kt_load_featured_products() {
	$category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : 'all';

	// Build query args
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => 8,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	// Add category filter if not 'all'
	if ( 'all' !== $category && ! empty( $category ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$products = new WP_Query( $args );

	if ( ! $products->have_posts() ) {
		wp_send_json_success( array( 'html' => '<p class="text-center col-span-full text-[#6B6F76]">' . esc_html__( 'No products found', 'astra-child' ) . '</p>' ) );
		wp_die();
	}

	// Build HTML
	$html = '';
	while ( $products->have_posts() ) {
		$products->the_post();
		$product    = wc_get_product( get_the_ID() );
		$badge      = $product->is_on_sale() ? 'SALE' : ( $product->is_featured() ? 'FEATURED' : 'NEW' );
		$price_html = $product->get_price_html();
		$stock      = $product->get_stock_quantity();

		$html .= '<article class="flex flex-col rounded-3xl border border-[#edf0f6] bg-white p-3 shadow-soft transition hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(15,18,32,0.15)]">';
		$html .= '<div class="relative rounded-2xl bg-[#F6F7FA] p-3">';
		$html .= '<span class="absolute left-2 top-2 rounded-full bg-[#FFE7EC] px-2 py-0.5 text-[10px] font-bold text-[#EC234A]">' . esc_html( $badge ) . '</span>';
		$html .= '<span class="absolute right-2 top-2 text-[10px] font-semibold text-[#40C6A8]">' . ( $stock > 0 ? esc_html__( 'In Stock', 'astra-child' ) : esc_html__( 'Out of Stock', 'astra-child' ) ) . '</span>';

		if ( has_post_thumbnail() ) {
			$html .= '<img src="' . esc_url( get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ) . '" alt="' . esc_attr( get_the_title() ) . '" class="mx-auto h-28 w-auto object-contain" />';
		} else {
			$html .= '<img src="https://www.daewooelectricals.com/blog/wp-content/uploads/2023/11/admin-ajax.jpg" alt="' . esc_attr( get_the_title() ) . '" class="mx-auto h-28 w-auto object-contain" />';
		}

		$html .= '</div>';
		$html .= '<div class="mt-3 flex flex-1 flex-col gap-1 text-xs">';

		// Category name
		$cat_name = 'KachoTech';
		$categories = $product->get_category_ids();
		if ( ! empty( $categories ) ) {
			$cat = get_term( $categories[0], 'product_cat' );
			if ( $cat ) {
				$cat_name = $cat->name;
			}
		}
		$html .= '<span class="text-[10px] font-semibold uppercase tracking-wide text-[#6B6F76]">' . esc_html( $cat_name ) . '</span>';

		// Title
		$html .= '<h3 class="line-clamp-2 text-[13px] font-semibold text-[#1A1A1D]">';
		$html .= '<a href="' . esc_url( get_permalink() ) . '" class="hover:text-[#EC234A] transition">' . esc_html( get_the_title() ) . '</a>';
		$html .= '</h3>';

		// Price
		$html .= '<div class="mt-1 flex items-baseline gap-2 text-[13px]">';
		$html .= '<span class="font-extrabold">' . wp_kses_post( $price_html ) . '</span>';
		$html .= '</div>';

		// Stock
		$html .= '<span class="text-[11px] font-semibold text-[#40C6A8]">';
		$html .= sprintf( esc_html__( 'Available: %d pcs', 'astra-child' ), absint( $stock ? $stock : 0 ) );
		$html .= '</span>';

		// Buttons
		$html .= '<div class="mt-2 flex gap-2">';
		$html .= '<a href="' . esc_url( $product->add_to_cart_url() ) . '" class="flex-1 inline-flex items-center justify-center rounded-full bg-[#1A1A1D] px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-[#EC234A] transition">';
		$html .= esc_html__( 'Add to Cart', 'astra-child' );
		$html .= '</a>';
		$html .= '<a href="' . esc_url( get_permalink() ) . '" class="flex h-8 w-8 items-center justify-center rounded-full border border-[#E4E6EC] bg-white text-xs text-[#1A1A1D] transition hover:border-[#1A1A1D] hover:bg-[#1A1A1D] hover:text-white">';
		$html .= '<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>';
		$html .= '</a>';
		$html .= '</div>';

		$html .= '</div>';
		$html .= '</article>';
	}

	wp_reset_postdata();

	wp_send_json_success( array( 'html' => $html ) );
	wp_die();
}

// Allow "per page" from query (?per_page=12 etc.)
add_filter( 'loop_shop_per_page', function ( $cols ) {
    if ( isset( $_GET['per_page'] ) && (int) $_GET['per_page'] > 0 ) {
        return (int) $_GET['per_page'];
    }
    return $cols;
}, 20 );

//----------------------------------------
// End of KachoTech header helpers
//----------------------------------------

// KachoTech contact form handler
add_action( 'wp_ajax_nopriv_kt_contact_form', 'kt_handle_contact_form' );
add_action( 'wp_ajax_kt_contact_form',        'kt_handle_contact_form' );

function kt_handle_contact_form() {
    // Basic security: allow only POST
    if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        wp_send_json_error( array( 'message' => 'Invalid request.' ), 400 );
    }

    $name    = isset( $_POST['name'] )    ? sanitize_text_field( $_POST['name'] )    : '';
    $email   = isset( $_POST['email'] )   ? sanitize_email( $_POST['email'] )        : '';
    $phone   = isset( $_POST['phone'] )   ? sanitize_text_field( $_POST['phone'] )   : '';
    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

    if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
        wp_send_json_error( array(
            'message' => 'Please complete all required fields.'
        ), 400 );
    }

    if ( ! is_email( $email ) ) {
        wp_send_json_error( array(
            'message' => 'Please enter a valid email address.'
        ), 400 );
    }

    $admin_email = get_option( 'admin_email' );
    $subject     = sprintf( 'New contact form message from %s', $name );

    $body  = "You have received a new message from the KachoTech contact form.\n\n";
    $body .= "Name: {$name}\n";
    $body .= "Email: {$email}\n";
    if ( ! empty( $phone ) ) {
        $body .= "Phone: {$phone}\n";
    }
    $body .= "\nMessage:\n{$message}\n";

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );

    $sent = wp_mail( $admin_email, $subject, $body, $headers );

    if ( ! $sent ) {
        wp_send_json_error( array(
            'message' => 'Unable to send email. Please try again later.'
        ), 500 );
    }

    wp_send_json_success( array(
        'message' => 'Thank you! Your message has been sent. Our team will contact you shortly.'
    ) );
}

//-------------------------------------------------------
// Enqueue Single product page scripts and styles
//-------------------------------------------------------
// KachoTech – Load Tailwind on WooCommerce product pages
function kt_enqueue_tailwind_for_product() {

	// Only on WooCommerce single product pages
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	wp_enqueue_style(
		'kt-tailwind',
		get_stylesheet_directory_uri() . '/assets/css/tailwind.min.css',
		array(),
		'1.0.0'
	);

	// Inter font (optional but matches your mock)
	wp_enqueue_style(
		'kt-inter-font',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'kt_enqueue_tailwind_for_product', 30 );

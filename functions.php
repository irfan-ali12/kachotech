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
		wp_send_json_success( array( 'html' => '<p class="kt-featured-no-products">' . esc_html__( 'No products found', 'astra-child' ) . '</p>' ) );
		wp_die();
	}

	// Build HTML matching related products design
	$html = '';
	while ( $products->have_posts() ) {
		$products->the_post();
		$product     = wc_get_product( get_the_ID() );
		$product_id  = $product->get_id();
		$title       = $product->get_name();
		$price       = $product->get_regular_price();
		$sale_price  = $product->get_sale_price();
		$rating      = $product->get_average_rating();
		$review_count = $product->get_review_count();
		$in_stock    = $product->is_in_stock();
		$on_sale     = $product->is_on_sale();
		$featured    = $product->is_featured();
		$stock       = $product->get_stock_quantity();

		// Get badge
		$badge_class = '';
		$badge_text  = '';
		if ( $on_sale ) {
			$badge_class = 'kt-badge-hot';
			$badge_text  = 'SALE';
		} elseif ( $featured ) {
			$badge_class = 'kt-badge-new';
			$badge_text  = 'FEATURED';
		}

		// Get category
		$product_cats = $product->get_category_ids();
		$cat_name = '';
		if ( ! empty( $product_cats ) ) {
			$cat = get_term( $product_cats[0], 'product_cat' );
			if ( $cat && ! is_wp_error( $cat ) ) {
				$cat_name = $cat->name;
			}
		}

		// Start product card
		$html .= '<div class="kt-product-card ' . ( $in_stock ? '' : 'out-of-stock' ) . '">';

		// THUMBNAIL + BADGES
		$html .= '<div class="kt-thumb">';
		if ( $badge_text ) {
			$html .= '<span class="kt-badge ' . esc_attr( $badge_class ) . '">' . esc_html( $badge_text ) . '</span>';
		}
		$html .= '<span class="kt-stock-status ' . ( $in_stock ? 'in-stock' : '' ) . '">';
		$html .= $in_stock ? esc_html__( 'In Stock', 'astra-child' ) : esc_html__( 'Out of Stock', 'astra-child' );
		$html .= '</span>';

		$html .= '<a href="' . esc_url( $product->get_permalink() ) . '">';
		$image_id = $product->get_image_id();
		if ( $image_id ) {
			$image_url = wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' );
			if ( $image_url ) {
				$html .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" class="kt-product-image">';
			} else {
				$html .= '<div class="kt-product-image-placeholder"></div>';
			}
		} else {
			$html .= '<img src="' . esc_url( wc_placeholder_img_src( 'woocommerce_thumbnail' ) ) . '" alt="' . esc_attr( $title ) . '" class="kt-product-image">';
		}
		$html .= '</a>';
		$html .= '</div>';

		// RATING ROW
		$html .= '<div class="kt-rating-row">';
		$html .= '<div class="kt-stars">';
		$filled = floor( $rating );
		$half   = ( $rating - $filled ) >= 0.5;
		for ( $i = 0; $i < 5; $i++ ) {
			if ( $i < $filled ) {
				$html .= '<i class="fa-solid fa-star"></i>';
			} elseif ( $i === $filled && $half ) {
				$html .= '<i class="fa-solid fa-star-half-stroke"></i>';
			} else {
				$html .= '<i class="fa-regular fa-star"></i>';
			}
		}
		$html .= '</div>';
		$html .= '<span class="kt-rating-count">';
		if ( $review_count > 0 ) {
			$html .= '(' . esc_html( $review_count ) . ')';
		} else {
			$html .= 'No reviews yet';
		}
		$html .= '</span>';
		$html .= '</div>';

		// CATEGORY
		$html .= '<div class="kt-category">';
		$html .= esc_html( $cat_name );
		$html .= '</div>';

		// TITLE
		$html .= '<h3 class="kt-title">';
		$html .= '<a href="' . esc_url( $product->get_permalink() ) . '">';
		$html .= esc_html( $title );
		$html .= '</a>';
		$html .= '</h3>';

		// PRICE
		$html .= '<div class="kt-price-row">';
		if ( $on_sale && $sale_price ) {
			$html .= '<span class="kt-price-current">Rs ' . esc_html( number_format( (float) $sale_price, 0, '.', ',' ) ) . '</span>';
			$html .= '<span class="kt-price-old">Rs ' . esc_html( number_format( (float) $price, 0, '.', ',' ) ) . '</span>';
		} else {
			$html .= '<span class="kt-price-current">Rs ' . esc_html( number_format( (float) $price, 0, '.', ',' ) ) . '</span>';
		}
		$html .= '</div>';

		// AVAILABILITY
		$html .= '<div class="kt-availability ' . ( $in_stock ? 'in-stock' : 'out-of-stock' ) . '">';
		if ( $in_stock && $stock ) {
			$html .= 'Available: ' . esc_html( (int) $stock ) . ' pcs';
		} elseif ( $in_stock ) {
			$html .= 'Available';
		} else {
			$html .= 'Out of Stock';
		}
		$html .= '</div>';

		// FOOTER BUTTONS
		$html .= '<div class="kt-footer-actions">';
		if ( $in_stock ) {
			$html .= '<a href="' . esc_url( $product->add_to_cart_url() ) . '" data-product_id="' . esc_attr( $product_id ) . '" class="kt-btn-cart add_to_cart_button ajax_add_to_cart">';
			$html .= esc_html__( 'Add to Cart', 'astra-child' );
			$html .= '</a>';
		} else {
			$html .= '<button class="kt-btn-cart" disabled>Add to Cart</button>';
		}
		$html .= '<a href="' . esc_url( $product->get_permalink() ) . '" class="kt-btn-details" title="View Details">';
		$html .= '<i class="fa-solid fa-arrow-right"></i>';
		$html .= '</a>';
		$html .= '</div>';

		$html .= '</div>';
	}

	wp_reset_postdata();

	wp_send_json_success( array( 'html' => $html ) );
	wp_die();
}

/**
 * AJAX Handler: Load Sale Products (On Sale)
 */
add_action( 'wp_ajax_kt_load_sale_products', 'kt_load_sale_products' );
add_action( 'wp_ajax_nopriv_kt_load_sale_products', 'kt_load_sale_products' );

function kt_load_sale_products() {
	// Build query args - only get products on sale
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => 8,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_key'       => '_sale_price',
		'meta_compare'   => 'EXISTS',
	);

	$products = new WP_Query( $args );

	if ( ! $products->have_posts() ) {
		wp_send_json_success( array( 'html' => '<p style="text-align: center; padding: 40px 20px; color: #6b7280; grid-column: 1 / -1;">' . esc_html__( 'No products on sale', 'astra-child' ) . '</p>' ) );
		wp_die();
	}

	// Build HTML matching featured products design
	$html = '';
	while ( $products->have_posts() ) {
		$products->the_post();
		$product     = wc_get_product( get_the_ID() );
		$product_id  = $product->get_id();
		$title       = $product->get_name();
		$price       = $product->get_regular_price();
		$sale_price  = $product->get_sale_price();
		$rating      = $product->get_average_rating();
		$review_count = $product->get_review_count();
		$in_stock    = $product->is_in_stock();
		$on_sale     = $product->is_on_sale();
		$featured    = $product->is_featured();
		$stock       = $product->get_stock_quantity();

		// Get badge - all sale products get SALE badge
		$badge_class = 'kt-badge-hot';
		$badge_text  = 'SALE';

		// Get category
		$product_cats = $product->get_category_ids();
		$cat_name = '';
		if ( ! empty( $product_cats ) ) {
			$cat = get_term( $product_cats[0], 'product_cat' );
			if ( $cat && ! is_wp_error( $cat ) ) {
				$cat_name = $cat->name;
			}
		}

		// Start product card
		$html .= '<div class="kt-product-card ' . ( $in_stock ? '' : 'out-of-stock' ) . '">';

		// THUMBNAIL + BADGES
		$html .= '<div class="kt-thumb">';
		if ( $badge_text ) {
			$html .= '<span class="kt-badge ' . esc_attr( $badge_class ) . '">' . esc_html( $badge_text ) . '</span>';
		}
		$html .= '<span class="kt-stock-status ' . ( $in_stock ? 'in-stock' : '' ) . '">';
		$html .= $in_stock ? esc_html__( 'In Stock', 'astra-child' ) : esc_html__( 'Out of Stock', 'astra-child' );
		$html .= '</span>';

		$html .= '<a href="' . esc_url( $product->get_permalink() ) . '">';
		$image_id = $product->get_image_id();
		if ( $image_id ) {
			$image_url = wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' );
			if ( $image_url ) {
				$html .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" class="kt-product-image">';
			} else {
				$html .= '<div class="kt-product-image-placeholder"></div>';
			}
		} else {
			$html .= '<img src="' . esc_url( wc_placeholder_img_src( 'woocommerce_thumbnail' ) ) . '" alt="' . esc_attr( $title ) . '" class="kt-product-image">';
		}
		$html .= '</a>';
		$html .= '</div>';

		// RATING ROW
		$html .= '<div class="kt-rating-row">';
		$html .= '<div class="kt-stars">';
		$filled = floor( $rating );
		$half   = ( $rating - $filled ) >= 0.5;
		for ( $i = 0; $i < 5; $i++ ) {
			if ( $i < $filled ) {
				$html .= '<i class="fa-solid fa-star"></i>';
			} elseif ( $i === $filled && $half ) {
				$html .= '<i class="fa-solid fa-star-half-stroke"></i>';
			} else {
				$html .= '<i class="fa-regular fa-star"></i>';
			}
		}
		$html .= '</div>';
		$html .= '<span class="kt-rating-count">';
		if ( $review_count > 0 ) {
			$html .= '(' . esc_html( $review_count ) . ')';
		} else {
			$html .= 'No reviews yet';
		}
		$html .= '</span>';
		$html .= '</div>';

		// CATEGORY
		$html .= '<div class="kt-category">';
		$html .= esc_html( $cat_name );
		$html .= '</div>';

		// TITLE
		$html .= '<h3 class="kt-title">';
		$html .= '<a href="' . esc_url( $product->get_permalink() ) . '">';
		$html .= esc_html( $title );
		$html .= '</a>';
		$html .= '</h3>';

		// PRICE
		$html .= '<div class="kt-price-row">';
		if ( $on_sale && $sale_price ) {
			$html .= '<span class="kt-price-current">Rs ' . esc_html( number_format( (float) $sale_price, 0, '.', ',' ) ) . '</span>';
			$html .= '<span class="kt-price-old">Rs ' . esc_html( number_format( (float) $price, 0, '.', ',' ) ) . '</span>';
		} else {
			$html .= '<span class="kt-price-current">Rs ' . esc_html( number_format( (float) $price, 0, '.', ',' ) ) . '</span>';
		}
		$html .= '</div>';

		// AVAILABILITY
		$html .= '<div class="kt-availability ' . ( $in_stock ? 'in-stock' : 'out-of-stock' ) . '">';
		if ( $in_stock && $stock ) {
			$html .= 'Available: ' . esc_html( (int) $stock ) . ' pcs';
		} elseif ( $in_stock ) {
			$html .= 'Available';
		} else {
			$html .= 'Out of Stock';
		}
		$html .= '</div>';

		// FOOTER BUTTONS
		$html .= '<div class="kt-footer-actions">';
		if ( $in_stock ) {
			$html .= '<a href="' . esc_url( $product->add_to_cart_url() ) . '" data-product_id="' . esc_attr( $product_id ) . '" class="kt-btn-cart add_to_cart_button ajax_add_to_cart">';
			$html .= esc_html__( 'Add to Cart', 'astra-child' );
			$html .= '</a>';
		} else {
			$html .= '<button class="kt-btn-cart" disabled>Add to Cart</button>';
		}
		$html .= '<a href="' . esc_url( $product->get_permalink() ) . '" class="kt-btn-details" title="View Details">';
		$html .= '<i class="fa-solid fa-arrow-right"></i>';
		$html .= '</a>';
		$html .= '</div>';

		$html .= '</div>';
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


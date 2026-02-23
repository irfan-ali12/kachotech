<?php
/**
 * KachoTech – Shop Archive (Astra Child Theme)
 * Custom shop layout with AJAX-ready filters & pagination
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1) Compute global min / max price for slider - OPTIMIZED WITH CACHING
 */
function kt_get_product_price_range() {
    // Check transient cache first
    $cache_key = 'kt_shop_price_range';
    $cached_range = get_transient( $cache_key );
    
    if ( $cached_range !== false ) {
        return $cached_range;
    }
    
    global $wpdb;
    
    // More efficient query to get price range from products that have prices > 0
    $price_range = $wpdb->get_row("
        SELECT 
            MIN(CAST(meta_value AS DECIMAL(10,2))) as min_price,
            MAX(CAST(meta_value AS DECIMAL(10,2))) as max_price
        FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE 
            pm.meta_key = '_price'
            AND p.post_type = 'product'
            AND p.post_status = 'publish'
            AND CAST(meta_value AS DECIMAL(10,2)) > 0
    ");
    
    if ( $price_range && $price_range->min_price !== null ) {
        $min_price = floor( (float) $price_range->min_price );
        $max_price = ceil( (float) $price_range->max_price );
        
        // Ensure max is greater than min
        if ($max_price <= $min_price) {
            $max_price = $min_price + 1000; // Add reasonable buffer
        }
        
        $result = array(
            'min' => $min_price,
            'max' => $max_price
        );
    } else {
        // Fallbacks with reasonable values
        $result = array(
            'min' => 0,
            'max' => 25000
        );
    }
    
    // Cache for 2 hours
    set_transient( $cache_key, $result, 2 * HOUR_IN_SECONDS );
    
    return $result;
}

$price_range = kt_get_product_price_range();
$min_price = $price_range['min'];
$max_price = $price_range['max'];

/**
 * 2) Get dynamic brands from product_brand taxonomy
 */
function kt_get_product_brands() {
    $brands = get_terms( array(
        'taxonomy'   => 'product_brand',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC'
    ) );
    
    if ( ! is_wp_error( $brands ) && ! empty( $brands ) ) {
        return $brands;
    }
    
    // Fallback to pa_brand if product_brand doesn't exist
    $brands = get_terms( array(
        'taxonomy'   => 'pa_brand',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC'
    ) );
    
    if ( ! is_wp_error( $brands ) && ! empty( $brands ) ) {
        return $brands;
    }
    
    return array();
}

$product_brands = kt_get_product_brands();

/**
 * 3) Modify main WooCommerce query to exclude products with no price or price = 0
 */
function kt_modify_shop_query($q) {
    if (!$q->is_main_query() || is_admin()) return;
    
    if (is_shop() || is_product_category() || is_product_tag()) {
        $meta_query = $q->get('meta_query');
        if (!is_array($meta_query)) {
            $meta_query = array();
        }
        
        // Only show products that have a price greater than 0
        $meta_query[] = array(
            'key' => '_price',
            'value' => 0,
            'compare' => '>',
            'type' => 'NUMERIC'
        );
        
        $q->set('meta_query', $meta_query);
    }
}
add_action('woocommerce_product_query', 'kt_modify_shop_query');

/**
 * 4) Enqueue layout CSS + JS + AJAX / price data
 */
wp_enqueue_style(
    'kt-shop-layout',
    get_stylesheet_directory_uri() . '/assets/css/shop-layout.css',
    array(),
    '1.0',
    'all'
);

wp_enqueue_script(
    'kt-shop-js',
    get_stylesheet_directory_uri() . '/assets/js/shop.js',
    array( 'jquery' ),
    '1.0',
    true
);

// AJAX settings for filters
wp_localize_script(
    'kt-shop-js',
    'ktShopAjax',
    array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'kt_filter_nonce' ),
        'min_price' => $min_price,
        'max_price' => $max_price,
        'posts_per_page' => 12
    )
);

// WooCommerce add-to-cart JS
if ( ! wp_script_is( 'wc-add-to-cart', 'enqueued' ) ) {
    wp_enqueue_script( 'wc-add-to-cart' );
}

// FontAwesome (for icons in search, etc.)
wp_enqueue_style(
    'kt-fontawesome',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    array(),
    '6.4.0'
);

// Current orderby (for sort dropdown)
$current_orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : 'date';

get_header();

// Get products with prices for initial display
$initial_args = array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'paged' => max(1, get_query_var('paged')),
    'meta_query' => array(
        array(
            'key' => '_price',
            'value' => 0,
            'compare' => '>',
            'type' => 'NUMERIC'
        )
    )
);

// Check if there's a search parameter in the URL (from header search)
if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
    $search_term = sanitize_text_field( wp_unslash( $_GET['s'] ) );
    $initial_args['s'] = $search_term;
}

$initial_query = new WP_Query($initial_args);

// Apply smart sorting to initial query (same as AJAX handler)
if ( ! isset( $_GET['s'] ) || empty( $_GET['s'] ) ) {
	// Collect all products with their metadata for custom sorting
	$product_list = array();
	foreach ( $initial_query->posts as $post ) {
		$product = wc_get_product( $post->ID );
		if ( ! $product ) {
			continue;
		}
		
		$product_id = $product->get_id();
		
		// Check if product is in heaters category
		$product_cats = $product->get_category_ids();
		$is_heater = false;
		
		if ( ! empty( $product_cats ) ) {
			foreach ( $product_cats as $cat_id ) {
				$cat = get_term( $cat_id, 'product_cat' );
				if ( $cat && ! is_wp_error( $cat ) && 'heaters' === $cat->slug ) {
					$is_heater = true;
					break;
				}
			}
		}
		
		// Get rating from database directly (more reliable)
		global $wpdb;
		$rating_query = $wpdb->get_var( $wpdb->prepare(
			"SELECT AVG(CAST(pm.meta_value AS FLOAT))
			FROM {$wpdb->postmeta} pm
			WHERE pm.post_id = %d AND pm.meta_key = '_wc_average_rating'",
			$product_id
		) );
		
		$rating = $rating_query ? (float) $rating_query : 0;
		$has_rating = $rating > 0;
		
		$product_list[] = array(
			'post'        => $post,
			'product_id'  => $product_id,
			'rating'      => $rating,
			'is_heater'   => $is_heater,
			'has_rating'  => $has_rating,
		);
	}
	
	// Sort products by rating and Heater priority
	usort( $product_list, function( $a, $b ) {
		$a_has_rating = $a['has_rating'];
		$b_has_rating = $b['has_rating'];
		
		// Rated products come first
		if ( $a_has_rating !== $b_has_rating ) {
			return $a_has_rating ? -1 : 1;
		}
		
		// Among rated products: sort by rating (highest first)
		if ( $a_has_rating && $b_has_rating ) {
			if ( $a['rating'] !== $b['rating'] ) {
				return $b['rating'] <=> $a['rating'];
			}
			// If ratings are equal, heaters come first
			if ( $a['is_heater'] !== $b['is_heater'] ) {
				return $a['is_heater'] ? -1 : 1;
			}
		}
		
		// Among unrated products: heaters come first
		if ( ! $a_has_rating && ! $b_has_rating ) {
			if ( $a['is_heater'] !== $b['is_heater'] ) {
				return $a['is_heater'] ? -1 : 1;
			}
		}
		
		return 0;
	} );
	
	// Replace query posts with sorted posts
	$initial_query->posts = array_column( $product_list, 'post' );
}

$total_products = $initial_query->found_posts;

// Replace the main query with our filtered query
$wp_query = $initial_query;
?>

<div class="kt-page">

    <!-- PAGE HEADING -->
    <div class="kt-header-bar">
        <div class="kt-heading-group">
            <h1><?php woocommerce_page_title(); ?></h1>
            <p>Heaters, cosmetics &amp; electronics curated for cozy winter days.</p>
        </div>
        <div class="kt-breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="kt-breadcrumb-link">Home</a>
            <span>/</span>
            <span class="kt-breadcrumb-current">Shop</span>
        </div>
    </div>

    <!-- MAIN LAYOUT: SIDEBAR + PRODUCTS -->
    <div class="kt-shop-layout">

        <!-- DESKTOP SIDEBAR FILTERS -->
        <aside class="kt-sidebar">

            <!-- Search Inside Sidebar -->
            <div class="kt-sidebar-group">
                <h3 class="kt-sidebar-title">Search</h3>
                <div class="kt-search-bar">
                    <input
                        type="text"
                        id="kt-product-search"
                        class="kt-search-input"
                        placeholder="Search products..."
                    >
                    <button type="button" class="kt-search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="kt-sidebar-group">
                <h3 class="kt-sidebar-title">Categories</h3>
                <div class="kt-categories-list">
                    <?php
                    // Get categories with counts only for products that have prices
$categories = get_terms(
    array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
    )
);

// Manually update counts to only include products with prices
foreach ($categories as $category) {
    $products_with_price = get_posts(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'numberposts' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $category->term_id,
            )
        ),
        'meta_query' => array(
            array(
                'key' => '_price',
                'value' => 0,
                'compare' => '>',
                'type' => 'NUMERIC'
            )
        ),
        'fields' => 'ids'
    ));
    
    $category->count = count($products_with_price);
}

                    if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
                        foreach ( $categories as $category ) {
                            $count = (int) $category->count;
                            ?>
                            <label class="kt-checkbox-row">
                                <span class="kt-checkbox-left">
                                    <input
                                        type="checkbox"
                                        value="<?php echo esc_attr( $category->slug ); ?>"
                                        class="kt-category-filter"
                                    >
                                    <?php echo esc_html( $category->name ); ?>
                                </span>
                                <span class="kt-count"><?php echo esc_html( $count ); ?></span>
                            </label>
                            <?php
                        }
                    } else {
                        echo '<p class="kt-no-items">No categories found</p>';
                    }
                    ?>
                </div>
            </div>

            <div class="kt-sidebar-group">
                <h3 class="kt-sidebar-title">Price (Rs)</h3>
                <div class="kt-price-range-wrapper">
                    <input
                        type="range"
                        min="<?php echo esc_attr( $min_price ); ?>"
                        max="<?php echo esc_attr( $max_price ); ?>"
                        value="<?php echo esc_attr( $max_price ); ?>"
                        step="1"
                        class="kt-range"
                        id="kt-price-range"
                    >
                    <div class="kt-price-tooltip" id="kt-price-tooltip">
                        <span id="kt-price-tooltip-text">
                            Rs <?php echo esc_html( number_format( $max_price ) ); ?>
                        </span>
                    </div>
                </div>
                <div class="kt-price-labels">
                    <span>Rs <?php echo esc_html( number_format( $min_price ) ); ?></span>
                    <span>Rs <?php echo esc_html( number_format( $max_price ) ); ?></span>
                </div>
            </div>

            <?php if ( ! empty( $product_brands ) ) : ?>
            <div class="kt-sidebar-group">
                <h3 class="kt-sidebar-title">Brands</h3>
                <div class="kt-pill-wrap">
                    <?php foreach ( $product_brands as $brand ) : ?>
                        <button class="kt-pill" data-brand="<?php echo esc_attr( $brand->slug ); ?>">
                            <?php echo esc_html( $brand->name ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="kt-sidebar-group">
                <h3 class="kt-sidebar-title">Rating</h3>
                <div class="kt-rating-options">
                    <label class="kt-radio-row">
                        <input type="radio" name="rating" value="4">
                        <span>4★ &amp; above</span>
                    </label>
                    <label class="kt-radio-row">
                        <input type="radio" name="rating" value="3">
                        <span>3★ &amp; above</span>
                    </label>
                    <label class="kt-radio-row">
                        <input type="radio" name="rating" value="2">
                        <span>2★ &amp; above</span>
                    </label>
                    <label class="kt-radio-row">
                        <input type="radio" name="rating" value="1">
                        <span>1★ &amp; above</span>
                    </label>
                </div>
            </div>

            <div class="kt-sidebar-group">
                <h3 class="kt-sidebar-title">Availability</h3>
                <label class="kt-checkbox-row">
                    <span class="kt-checkbox-left">
                        <input type="checkbox" value="in-stock" class="kt-availability-filter">
                        In stock
                    </span>
                </label>
                <label class="kt-checkbox-row">
                    <span class="kt-checkbox-left">
                        <input type="checkbox" value="out-of-stock" class="kt-availability-filter">
                        Out of stock
                    </span>
                </label>
            </div>

            <div class="kt-sidebar-actions">
                <button class="kt-btn-primary" id="kt-apply-filters-desktop" type="button">
                    Apply Filters
                </button>
                <button class="kt-btn-link" id="kt-clear-filters-desktop" type="button">
                    Clear all
                </button>
            </div>

        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="kt-main-content">

            <!-- SORT + FILTER BAR -->
            <div class="kt-sort-bar">
                <div class="kt-filters-left">
                    <button id="kt-open-filters" class="kt-btn-filters">
                        <span>☰</span> Filters
                    </button>
                    <span class="kt-results-count">
                        <?php echo esc_html( $total_products ); ?> products found
                    </span>
                </div>

                <div class="kt-filters-right">
                    <form method="get" class="kt-sort-form">
                        <label for="kt-sort-select">Sort by:</label>
                        <select name="orderby" id="kt-sort-select">
                            <option value="date"        <?php selected( $current_orderby, 'date' ); ?>>Latest</option>
                            <option value="menu_order"  <?php selected( $current_orderby, 'menu_order' ); ?>>Featured</option>
                            <option value="price"       <?php selected( $current_orderby, 'price' ); ?>>Price: Low to High</option>
                            <option value="price-desc"  <?php selected( $current_orderby, 'price-desc' ); ?>>Price: High to Low</option>
                            <option value="rating"      <?php selected( $current_orderby, 'rating' ); ?>>Rating</option>
                            <option value="popularity"  <?php selected( $current_orderby, 'popularity' ); ?>>Popularity</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- PRODUCTS GRID -->
            <div class="kt-products" id="kt-products-container">
                <?php
                if ( have_posts() ) {
                    while ( have_posts() ) {
                        the_post();
                        global $product;

                        if ( ! $product ) {
                            continue;
                        }

                        $product_id   = $product->get_id();
                        $title        = $product->get_name();
                        $description  = wp_trim_words( $product->get_short_description() ?: $product->get_description(), 15 );
                        $price        = $product->get_regular_price();
                        $sale_price   = $product->get_sale_price();
                        $image        = get_the_post_thumbnail_url( $product_id, 'woocommerce_thumbnail' );
                        $on_sale      = $product->is_on_sale();
                        $featured     = $product->is_featured();
                        $in_stock     = $product->is_in_stock();
                        $rating       = (float) $product->get_average_rating();
                        $review_count = (int) $product->get_review_count();

                        // Skip products with no price or price = 0
                        if ( empty( $price ) || floatval( $price ) <= 0 ) {
                            continue;
                        }

                        $badge_class = '';
                        $badge_text  = '';

                        if ( $on_sale ) {
                            $badge_class = 'kt-badge-hot';
                            $badge_text  = 'SALE';
                        } elseif ( $featured ) {
                            $badge_class = 'kt-badge-new';
                            $badge_text  = 'FEATURED';
                        }
                        ?>
                        <div class="kt-product-card <?php echo $in_stock ? '' : 'out-of-stock'; ?>">
                            <!-- THUMBNAIL + BADGES INSIDE -->
                            <div class="kt-thumb">
                                <?php if ( $badge_text ) : ?>
                                    <span class="kt-badge <?php echo esc_attr( $badge_class ); ?>">
                                        <?php echo esc_html( $badge_text ); ?>
                                    </span>
                                <?php endif; ?>

                                <span class="kt-stock-status <?php echo $in_stock ? 'in-stock' : ''; ?>">
                                    <?php echo $in_stock ? 'In Stock' : 'Out of Stock'; ?>
                                </span>

                                <a href="<?php the_permalink(); ?>">
                                    <?php
                                    if ( $image ) {
                                        echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $title ) . '" class="kt-product-image">';
                                    } else {
                                        echo '<div class="kt-product-image-placeholder"></div>';
                                    }
                                    ?>
                                </a>
                            </div>

                            <!-- RATING ROW -->
                            <div class="kt-rating-row">
                                <div class="kt-stars">
                                    <?php
                                    $filled = floor( $rating );
                                    $half = ( $rating - $filled ) >= 0.5;
                                    for ( $i = 0; $i < 5; $i++ ) {
                                        if ( $i < $filled ) {
                                            echo '<i class="fa-solid fa-star"></i>';
                                        } elseif ( $i === $filled && $half ) {
                                            echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                        } else {
                                            echo '<i class="fa-regular fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                                <span class="kt-rating-count">
                                    <?php
                                    if ( $review_count > 0 ) {
                                        echo '(' . esc_html( $review_count ) . ')';
                                    } else {
                                        echo 'No reviews yet';
                                    }
                                    ?>
                                </span>
                            </div>

                            <!-- CATEGORY -->
                            <div class="kt-category">
                                <?php
                                $product_cats = $product->get_category_ids();
                                if ( ! empty( $product_cats ) ) {
                                    $cat = get_term( $product_cats[0], 'product_cat' );
                                    if ( $cat && ! is_wp_error( $cat ) ) {
                                        echo esc_html( $cat->name );
                                    }
                                }
                                ?>
                            </div>

                            <!-- TITLE -->
                            <h3 class="kt-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo esc_html( $title ); ?>
                                </a>
                            </h3>

                            <!-- PRICE -->
                            <div class="kt-price-row">
                                <?php if ( $on_sale && $sale_price ) : ?>
                                    <span class="kt-price-current">Rs <?php echo esc_html( number_format( (float) $sale_price, 0, '.', ',' ) ); ?></span>
                                    <span class="kt-price-old">Rs <?php echo esc_html( number_format( (float) $price, 0, '.', ',' ) ); ?></span>
                                <?php else : ?>
                                    <span class="kt-price-current">Rs <?php echo esc_html( number_format( (float) $price, 0, '.', ',' ) ); ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- AVAILABILITY -->
                            <div class="kt-availability <?php echo $in_stock ? 'in-stock' : 'out-of-stock'; ?>">
                                <?php
                                $stock = $product->get_stock_quantity();
                                if ( $in_stock && $stock ) {
                                    echo 'Available: ' . esc_html( (int) $stock ) . ' pcs';
                                } elseif ( $in_stock ) {
                                    echo 'Available';
                                } else {
                                    echo 'Out of Stock';
                                }
                                ?>
                            </div>

                            <!-- FOOTER BUTTONS -->
                            <div class="kt-footer-actions">
                                <?php if ( $in_stock ) : ?>
                                    <a
                                        href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
                                        data-product_id="<?php echo esc_attr( $product_id ); ?>"
                                        class="kt-btn-cart add_to_cart_button ajax_add_to_cart"
                                    >
                                        Add to Cart
                                    </a>
                                <?php else : ?>
                                    <button class="kt-btn-cart" disabled>Add to Cart</button>
                                <?php endif; ?>
                                <a href="<?php the_permalink(); ?>" class="kt-btn-details" title="View Details">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="kt-no-products">
                        <p><?php esc_html_e( 'No products found', 'woocommerce' ); ?></p>
                    </div>
                    <?php
                }
                ?>
            </div><!-- #kt-products-container -->

            <!-- PAGINATION -->
            <?php
            $pagination = paginate_links(
                array(
                    'format'  => '?paged=%#%',
                    'current' => max( 1, get_query_var( 'paged' ) ),
                    'total'   => (int) $wp_query->max_num_pages,
                    'echo'    => false,
                    'type'    => 'plain',
                )
            );

            if ( $pagination ) {
                // Add kt-page-link to page numbers
                $pagination = str_replace(
                    'class="page-numbers',
                    'class="page-numbers kt-page-link',
                    $pagination
                );

                // But keep current page without kt-page-link
                $pagination = str_replace(
                    'class="page-numbers kt-page-link current',
                    'class="page-numbers current',
                    $pagination
                );
                ?>
                <div class="kt-pagination">
                    <?php echo wp_kses_post( $pagination ); ?>
                </div>
                <?php
            }
            ?>

        </main><!-- .kt-main-content -->

    </div><!-- .kt-shop-layout -->

    <!-- MOBILE FILTER DRAWER -->
    <div id="kt-filter-drawer" class="kt-filter-drawer">
        <div class="kt-filter-overlay"></div>
        <div class="kt-filter-panel">
            <div class="kt-filter-header">
                <h3 class="kt-filter-title">Filters</h3>
                <button id="kt-close-filters" class="kt-filter-close" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="kt-filter-scroll">
                <!-- Mobile search -->
                <div class="kt-filter-section">
                    <h4 class="kt-filter-section-title">Search</h4>
                    <div class="kt-search-bar">
                        <input
                            type="text"
                            id="kt-product-search-mobile"
                            class="kt-search-input"
                            placeholder="Search products..."
                        >
                        <button type="button" class="kt-search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Categories for mobile -->
                <div class="kt-filter-section">
                    <h4 class="kt-filter-section-title">Categories</h4>
                    <div class="kt-categories-list">
                        <?php
                        if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
                            foreach ( $categories as $category ) {
                                ?>
                                <label class="kt-checkbox-row">
                                    <input
                                        type="checkbox"
                                        class="kt-category-filter-mobile"
                                        value="<?php echo esc_attr( $category->slug ); ?>"
                                    >
                                    <?php echo esc_html( $category->name ); ?>
                                </label>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Price for mobile -->
                <div class="kt-filter-section">
                    <h4 class="kt-filter-section-title">Price (Rs)</h4>
                    <div class="kt-price-range-wrapper">
                        <input
                            type="range"
                            min="<?php echo esc_attr( $min_price ); ?>"
                            max="<?php echo esc_attr( $max_price ); ?>"
                            value="<?php echo esc_attr( $max_price ); ?>"
                            step="1"
                            class="kt-range"
                            id="kt-price-range-mobile"
                        >
                        <div class="kt-price-tooltip" id="kt-price-tooltip-mobile">
                            <span id="kt-price-tooltip-text-mobile">
                                Rs <?php echo esc_html( number_format( $max_price ) ); ?>
                            </span>
                        </div>
                    </div>
                    <div class="kt-price-labels">
                        <span>Rs <?php echo esc_html( number_format( $min_price ) ); ?></span>
                        <span>Rs <?php echo esc_html( number_format( $max_price ) ); ?></span>
                    </div>
                </div>

                <!-- Brands for mobile -->
                <?php if ( ! empty( $product_brands ) ) : ?>
                <div class="kt-filter-section">
                    <h4 class="kt-filter-section-title">Brands</h4>
                    <div class="kt-pill-wrap">
                        <?php foreach ( $product_brands as $brand ) : ?>
                            <button class="kt-pill kt-pill-mobile" data-brand="<?php echo esc_attr( $brand->slug ); ?>">
                                <?php echo esc_html( $brand->name ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Rating for mobile -->
                <div class="kt-filter-section">
                    <h4 class="kt-filter-section-title">Rating</h4>
                    <div class="kt-rating-options">
                        <label class="kt-radio-row">
                            <input type="radio" name="rating-mobile" value="4">
                            <span>4★ &amp; above</span>
                        </label>
                        <label class="kt-radio-row">
                            <input type="radio" name="rating-mobile" value="3">
                            <span>3★ &amp; above</span>
                        </label>
                        <label class="kt-radio-row">
                            <input type="radio" name="rating-mobile" value="2">
                            <span>2★ &amp; above</span>
                        </label>
                        <label class="kt-radio-row">
                            <input type="radio" name="rating-mobile" value="1">
                            <span>1★ &amp; above</span>
                        </label>
                    </div>
                </div>

                <!-- Availability for mobile -->
                <div class="kt-filter-section">
                    <h4 class="kt-filter-section-title">Availability</h4>
                    <label class="kt-checkbox-row">
                        <input type="checkbox" class="kt-availability-filter-mobile" value="in-stock">
                        In stock
                    </label>
                    <label class="kt-checkbox-row">
                        <input type="checkbox" class="kt-availability-filter-mobile" value="out-of-stock">
                        Out of stock
                    </label>
                </div>
            </div>

            <div class="kt-filter-actions">
                <button class="kt-btn-primary" id="kt-apply-filters-mobile" type="button">
                    Apply Filters
                </button>
                <button class="kt-btn-secondary" id="kt-clear-filters-mobile" type="button">
                    Clear all
                </button>
            </div>
        </div>
    </div>

</div><!-- .kt-page -->

<?php
get_footer();
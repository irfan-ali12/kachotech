<?php
/**
 * KachoTech – Product Category Archive
 * Custom category layout with category-specific filters, colorful heading, and featured image
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1) Get category object and metadata
 */
$category = get_queried_object();
$category_id = $category->term_id;
$category_name = $category->name;
$category_description = $category->description;
$category_image_id = get_term_meta( $category_id, 'thumbnail_id', true );
$category_image = $category_image_id ? wp_get_attachment_url( $category_image_id ) : null;

/**
 * 2) Get category color (stored in term meta or use default color based on category)
 */
$category_colors = array(
    'heaters' => array( 'bg' => '#F39C12', 'text' => '#FFFFFF', 'secondary' => '#FF8E8E' ),
    'cosmetics' => array( 'bg' => '#FF69B4', 'text' => '#FFFFFF', 'secondary' => '#FF85C0' ),
    'electronics' => array( 'bg' => '#4ECDC4', 'text' => '#FFFFFF', 'secondary' => '#6BE3DB' ),
    'personal-care' => array( 'bg' => '#9B59B6', 'text' => '#FFFFFF', 'secondary' => '#B370CC' ),
    'home' => array( 'bg' => '#F39C12', 'text' => '#FFFFFF', 'secondary' => '#F5B041' ),
    'sports' => array( 'bg' => '#1ABC9C', 'text' => '#FFFFFF', 'secondary' => '#48D1C3' ),
    'books' => array( 'bg' => '#3498DB', 'text' => '#FFFFFF', 'secondary' => '#5DADE2' ),
    'default' => array( 'bg' => '#ff2446', 'text' => '#FFFFFF', 'secondary' => '#FF4466' )
);

$category_slug = $category->slug;
$color_scheme = isset( $category_colors[ $category_slug ] ) ? $category_colors[ $category_slug ] : $category_colors['default'];

/**
 * 3) Compute price range
 */
function kt_get_category_price_range( $category_id ) {
    global $wpdb;
    
    $price_range = $wpdb->get_row( $wpdb->prepare(
        "SELECT 
            MIN(CAST(pm.meta_value AS DECIMAL(10,2))) as min_price,
            MAX(CAST(pm.meta_value AS DECIMAL(10,2))) as max_price
        FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        WHERE 
            pm.meta_key = '_price'
            AND p.post_type = 'product'
            AND p.post_status = 'publish'
            AND tt.term_id = %d
            AND CAST(pm.meta_value AS DECIMAL(10,2)) > 0",
        $category_id
    ) );
    
    if ( $price_range && $price_range->min_price !== null ) {
        $min_price = floor( (float) $price_range->min_price );
        $max_price = ceil( (float) $price_range->max_price );
        
        if ( $max_price <= $min_price ) {
            $max_price = $min_price + 1000;
        }
        
        return array(
            'min' => $min_price,
            'max' => $max_price
        );
    }
    
    return array(
        'min' => 0,
        'max' => 25000
    );
}

$price_range = kt_get_category_price_range( $category_id );
$min_price = $price_range['min'];
$max_price = $price_range['max'];

/**
 * 4) Get category-specific attributes/filters
 */
function kt_get_category_attributes( $category_id ) {
    global $wpdb;
    
    // Get product IDs in this category
    $product_ids = $wpdb->get_col( $wpdb->prepare(
        "SELECT p.ID FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        WHERE tt.term_id = %d AND p.post_type = 'product' AND p.post_status = 'publish'",
        $category_id
    ) );
    
    if ( empty( $product_ids ) ) {
        return array();
    }
    
    // Get all attributes used by products in this category
    $placeholders = implode( ',', array_fill( 0, count( $product_ids ), '%d' ) );
    $attributes = $wpdb->get_results( $wpdb->prepare(
        "SELECT DISTINCT att.attribute_name, att.attribute_id
        FROM {$wpdb->prefix}postmeta pm
        JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies att ON 1=1
        WHERE pm.post_id IN ($placeholders)
        AND pm.meta_key LIKE 'attribute_%'
        LIMIT 5",
        ...$product_ids
    ) );
    
    return ! is_wp_error( $attributes ) ? $attributes : array();
}

$category_attributes = kt_get_category_attributes( $category_id );

/**
 * 5) Enqueue assets
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

wp_localize_script(
    'kt-shop-js',
    'ktShopAjax',
    array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'kt_filter_nonce' ),
        'min_price' => $min_price,
        'max_price' => $max_price,
        'posts_per_page' => 12,
        'category_id' => $category_id,
        'is_category_page' => true,
        'category_name' => $category_slug
    )
);

if ( ! wp_script_is( 'wc-add-to-cart', 'enqueued' ) ) {
    wp_enqueue_script( 'wc-add-to-cart' );
}

wp_enqueue_style(
    'kt-fontawesome',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    array(),
    '6.4.0'
);

$current_orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : 'date';

get_header();

// Get products with prices for initial display
$initial_args = array(
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => 12,
    'paged' => max( 1, get_query_var( 'paged' ) ),
    'tax_query' => array(
        array(
            'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $category_id
        )
    ),
    'meta_query' => array(
        array(
            'key' => '_price',
            'value' => 0,
            'compare' => '>',
            'type' => 'NUMERIC'
        )
    )
);

$initial_query = new WP_Query( $initial_args );
$total_products = $initial_query->found_posts;

$wp_query = $initial_query;

// Apply orderby if needed
if ( $current_orderby && $current_orderby !== 'date' ) {
    switch ( $current_orderby ) {
        case 'price':
            $wp_query->query_vars['orderby'] = 'meta_value_num';
            $wp_query->query_vars['meta_key'] = '_price';
            $wp_query->query_vars['order'] = 'ASC';
            break;
        case 'price-desc':
            $wp_query->query_vars['orderby'] = 'meta_value_num';
            $wp_query->query_vars['meta_key'] = '_price';
            $wp_query->query_vars['order'] = 'DESC';
            break;
        case 'rating':
            $wp_query->query_vars['orderby'] = 'meta_value_num';
            $wp_query->query_vars['meta_key'] = '_wc_average_rating';
            $wp_query->query_vars['order'] = 'DESC';
            break;
        case 'popularity':
            $wp_query->query_vars['orderby'] = 'meta_value_num';
            $wp_query->query_vars['meta_key'] = 'total_sales';
            $wp_query->query_vars['order'] = 'DESC';
            break;
        case 'menu_order':
            $wp_query->query_vars['orderby'] = 'menu_order';
            $wp_query->query_vars['order'] = 'ASC';
            break;
    }
    $wp_query = new WP_Query( $wp_query->query_vars );
}
?>

<style>
    /* Category-specific styling */
    :root {
        --cat-primary: <?php echo esc_attr( $color_scheme['bg'] ); ?>;
        --cat-text: <?php echo esc_attr( $color_scheme['text'] ); ?>;
        --cat-secondary: <?php echo esc_attr( $color_scheme['secondary'] ); ?>;
    }

    .kt-category-header {
        background: linear-gradient(135deg, var(--cat-primary) 0%, var(--cat-secondary) 100%);
        color: var(--cat-text);
        padding: 20px 20px;
        margin-bottom: 40px;
        border-radius: 12px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        align-items: center;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        position: relative;
        overflow: hidden;
    }

    .kt-category-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        filter: blur(40px);
        z-index: 0;
    }

    .kt-category-header .kt-cat-content {
        position: relative;
        z-index: 1;
    }

    .kt-category-header h1 {
        font-size: 42px;
        font-weight: 700;
        margin: 0 0 10px 0;
        letter-spacing: -0.5px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .kt-category-header .kt-cat-label {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
        margin-bottom: 12px;
        display: inline-block;
        padding: 4px 12px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
    }

    .kt-category-header .kt-cat-description {
        font-size: 15px;
        line-height: 1.6;
        opacity: 0.95;
        margin: 0;
    }

    .kt-category-header .kt-cat-image {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .kt-category-header .kt-cat-image img {
        max-width: 100%;
        height: auto;
        max-height: 300px;
        object-fit: contain;
        filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.15));
    }

    .kt-category-header .kt-product-count {
        font-size: 12px;
        opacity: 0.85;
        margin-top: 8px;
        display: inline-block;
        padding: 6px 10px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 6px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .kt-category-header {
            grid-template-columns: 1fr;
            padding: 30px 16px;
        }

        .kt-category-header h1 {
            font-size: 28px;
        }

        .kt-category-header .kt-cat-image {
            justify-content: center;
        }

        .kt-category-header .kt-cat-image img {
            max-height: 200px;
        }
    }
</style>

<div class="kt-page">

    <!-- CATEGORY HEADER with Colorful Background & Image -->
    <div class="kt-category-header">
        <div class="kt-cat-content">
            <div class="kt-cat-label">
                <i class="fas fa-tag"></i> Product Category
            </div>
            <h1><?php echo esc_html( $category_name ); ?></h1>
            <?php if ( $category_description ) : ?>
                <p class="kt-cat-description"><?php echo wp_kses_post( $category_description ); ?></p>
            <?php endif; ?>
            <div class="kt-product-count">
                <i class="fas fa-box"></i> <?php echo esc_html( $total_products ); ?> products available
            </div>
        </div>
        <?php if ( $category_image ) : ?>
            <div class="kt-cat-image">
                <img src="<?php echo esc_url( $category_image ); ?>" alt="<?php echo esc_attr( $category_name ); ?>" />
            </div>
        <?php endif; ?>
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
                        placeholder="Search in category..."
                    >
                    <button type="button" class="kt-search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Sub-categories -->
            <?php
            $sub_categories = get_terms( array(
                'taxonomy'   => 'product_cat',
                'parent'     => $category_id,
                'hide_empty' => true,
                'orderby'    => 'name',
                'order'      => 'ASC'
            ) );

            if ( ! is_wp_error( $sub_categories ) && ! empty( $sub_categories ) ) :
                ?>
                <div class="kt-sidebar-group">
                    <h3 class="kt-sidebar-title">Sub-Categories</h3>
                    <div class="kt-categories-list">
                        <?php foreach ( $sub_categories as $sub_cat ) : ?>
                            <label class="kt-checkbox-row">
                                <span class="kt-checkbox-left">
                                    <input
                                        type="checkbox"
                                        value="<?php echo esc_attr( $sub_cat->slug ); ?>"
                                        class="kt-category-filter"
                                    >
                                    <?php echo esc_html( $sub_cat->name ); ?>
                                </span>
                                <span class="kt-count"><?php echo esc_html( $sub_cat->count ); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Price Range Filter -->
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

            <!-- Rating Filter -->
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

            <!-- Availability Filter -->
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

            <!-- Filter Actions -->
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
                            <!-- THUMBNAIL + BADGES -->
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
                                        echo 'No reviews';
                                    }
                                    ?>
                                </span>
                            </div>

                            <!-- PRODUCT NAME & DESCRIPTION -->
                            <h3 class="kt-product-name">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo esc_html( $title ); ?>
                                </a>
                            </h3>
                            <p class="kt-product-description"><?php echo esc_html( $description ); ?></p>

                            <!-- PRICE -->
                            <div class="kt-price-row">
                                <?php if ( $on_sale ) : ?>
                                    <span class="kt-sale-price">Rs <?php echo esc_html( number_format( $sale_price, 2 ) ); ?></span>
                                    <span class="kt-original-price">Rs <?php echo esc_html( number_format( $price, 2 ) ); ?></span>
                                    <?php
                                    $discount = round( ( ( $price - $sale_price ) / $price ) * 100 );
                                    echo '<span class="kt-discount">-' . esc_html( $discount ) . '%</span>';
                                    ?>
                                <?php else : ?>
                                    <span class="kt-sale-price">Rs <?php echo esc_html( number_format( $price, 2 ) ); ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- ADD TO CART BUTTON -->
                            <form class="cart" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" enctype="multipart/form-data">
                                <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>" class="kt-add-to-cart-btn">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;">
                        <p><strong>No products found in this category.</strong></p>
                    </div>
                    <?php
                }
                ?>
            </div>

            <!-- PAGINATION -->
            <div class="kt-pagination">
                <?php
                echo wp_kses_post( paginate_links( array(
                    'total'   => $wp_query->max_num_pages,
                    'current' => max( 1, get_query_var( 'paged' ) ),
                ) ) );
                ?>
            </div>

        </main>

    </div>

</div>

<?php get_footer(); ?>

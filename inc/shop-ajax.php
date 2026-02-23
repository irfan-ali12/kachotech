<?php
/**
 * Shop AJAX Filter Handler
 * Handles AJAX requests for filtering products
 * Based on WooCommerce Product Filter plugin best practices
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get price range for the current context (shop or category)
 * Simplified and optimized with caching
 */
function kt_get_price_range_for_context( $category_id, $is_category_page, $cat_array ) {
    global $wpdb;
    
    // Create cache key based on context
    $cache_key = 'kt_price_range_' . md5( json_encode( array( $category_id, $is_category_page, $cat_array ) ) );
    $cached_range = get_transient( $cache_key );
    
    if ( $cached_range !== false ) {
        return $cached_range;
    }
    
    // Build query args for WP_Query (more reliable than raw SQL)
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => array(
            array(
                'key'     => '_price',
                'value'   => 0,
                'compare' => '>',
                'type'    => 'NUMERIC'
            )
        ),
    );

    // If category page, filter by that category
    if ( $is_category_page && $category_id > 0 ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'id',
                'terms'    => $category_id,
            )
        );
    }
    // If categories selected on shop page
    elseif ( ! empty( $cat_array ) && ! $is_category_page ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $cat_array,
            )
        );
    }

    $query = new WP_Query( $args );
    $product_ids = $query->posts;

    if ( empty( $product_ids ) ) {
        // Fallback with safe defaults
        $result = array( 'min' => 0, 'max' => 25000 );
        set_transient( $cache_key, $result, HOUR_IN_SECONDS );
        return $result;
    }

    // Get min and max price from the products
    $prices = array();
    foreach ( $product_ids as $product_id ) {
        $product = wc_get_product( $product_id );
        if ( $product ) {
            $price = (float) $product->get_price();
            if ( $price > 0 ) {
                $prices[] = $price;
            }
        }
    }

    if ( ! empty( $prices ) ) {
        $min_price = floor( min( $prices ) );
        $max_price = ceil( max( $prices ) );
        
        if ( $max_price <= $min_price ) {
            $max_price = $min_price + 1000;
        }
        
        $result = array( 'min' => $min_price, 'max' => $max_price );
    } else {
        $result = array( 'min' => 0, 'max' => 25000 );
    }

    // Cache for 1 hour
    set_transient( $cache_key, $result, HOUR_IN_SECONDS );
    
    return $result;
}

/**
 * AJAX endpoint for filtering products
 */
function kt_filter_products_ajax() {
    // Disable LiteSpeed Cache for this AJAX request
    if ( function_exists( 'do_action' ) ) {
        do_action( 'litespeed_control_set_cacheable', false );
    }
    
    // Disable browser cache
    header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
    header( 'Pragma: no-cache' );
    
    // Verify nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'kt_filter_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Security check failed' ) );
    }

    // Get filter parameters with proper sanitization
    $categories = isset( $_POST['categories'] ) ? sanitize_text_field( wp_unslash( $_POST['categories'] ) ) : '';
    $availability = isset( $_POST['availability'] ) ? sanitize_text_field( wp_unslash( $_POST['availability'] ) ) : '';
    $brands = isset( $_POST['brands'] ) ? sanitize_text_field( wp_unslash( $_POST['brands'] ) ) : '';
    $min_rating = isset( $_POST['min_rating'] ) ? intval( wp_unslash( $_POST['min_rating'] ) ) : 0;
    $max_price = isset( $_POST['max_price'] ) ? floatval( wp_unslash( $_POST['max_price'] ) ) : 0;
    $search = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
    $paged = isset( $_POST['paged'] ) ? intval( wp_unslash( $_POST['paged'] ) ) : 1;
    $orderby = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : 'date';
    
    // Get category_id if this is a category page (for filtering only category products)
    $category_id = isset( $_POST['category_id'] ) ? intval( wp_unslash( $_POST['category_id'] ) ) : 0;
    $is_category_page = isset( $_POST['is_category_page'] ) ? wp_validate_boolean( wp_unslash( $_POST['is_category_page'] ) ) : false;

    // Parse arrays
    $cat_array = ! empty( $categories ) ? array_filter( array_map( 'sanitize_text_field', explode( ',', $categories ) ) ) : array();
    $availability_array = ! empty( $availability ) ? array_filter( array_map( 'sanitize_text_field', explode( ',', $availability ) ) ) : array();
    $brands_array = ! empty( $brands ) ? array_filter( array_map( 'sanitize_text_field', explode( ',', $brands ) ) ) : array();

    // Ensure minimum pagination
    $paged = max( 1, $paged );
    $posts_per_page = 12;

    // Simplified: Use defaults if price params are invalid or not set
    if ( $max_price <= 0 ) {
        $max_price = 25000; // Safe default
    }

    // Build WooCommerce query args - EXCLUDE PRODUCTS WITH NO PRICE
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'meta_query'     => array(
            // Always exclude products with no price or price = 0
            array(
                'key' => '_price',
                'value' => 0,
                'compare' => '>',
                'type' => 'NUMERIC'
            )
        ),
        'tax_query'      => array(),
    );

    // Handle sort order
    switch ( $orderby ) {
        case 'price':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order']    = 'ASC';
            break;
        case 'price-desc':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order']    = 'DESC';
            break;
        case 'rating':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_wc_average_rating';
            $args['order']    = 'DESC';
            break;
        case 'popularity':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = 'total_sales';
            $args['order']    = 'DESC';
            break;
        case 'menu_order':
            $args['orderby']  = 'menu_order title';
            $args['order']    = 'ASC';
            break;
        case 'date':
        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
    }

    // Add search
    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    // If this is a category page, ALWAYS filter by the current category
    if ( $is_category_page && $category_id > 0 ) {
        $args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'id',
            'terms'    => $category_id,
            'operator' => 'IN',
        );
        
        // If user selected sub-categories, add them to the filter
        if ( ! empty( $cat_array ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $cat_array,
                'operator' => 'IN',
            );
            // Set relation to AND so products must be in main category AND selected sub-category
            if ( count( $args['tax_query'] ) > 1 ) {
                $args['tax_query']['relation'] = 'AND';
            }
        }
    } elseif ( ! empty( $cat_array ) ) {
        // On shop page, use selected categories
        $args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $cat_array,
            'operator' => 'IN',
        );
    }

    // Add tax_query for brands - using product_brand taxonomy
    if ( ! empty( $brands_array ) ) {
        // Try product_brand first, then fallback to pa_brand
        if ( taxonomy_exists( 'product_brand' ) ) {
            $brand_taxonomy = 'product_brand';
        } elseif ( taxonomy_exists( 'pa_brand' ) ) {
            $brand_taxonomy = 'pa_brand';
        } else {
            $brand_taxonomy = '';
        }
        
        if ( $brand_taxonomy ) {
            $args['tax_query'][] = array(
                'taxonomy' => $brand_taxonomy,
                'field'    => 'slug',
                'terms'    => $brands_array,
                'operator' => 'IN',
            );
        }
    }

    // Add meta_query for max price - only if a valid price is specified
    // When max_price is 0, it means no price filter was applied
    if ( $max_price > 0 ) {
        $args['meta_query'][] = array(
            'key'     => '_price',
            'value'   => array( 0.01, (float) $max_price ), // Include prices above 0 up to max
            'type'    => 'NUMERIC',
            'compare' => 'BETWEEN',
        );
    }

    // Handle availability/stock status filter
    if ( ! empty( $availability_array ) ) {
        $stock_queries = array();
        
        if ( in_array( 'in-stock', $availability_array ) ) {
            $stock_queries[] = array(
                'key'   => '_stock_status',
                'value' => 'instock',
            );
        }
        
        if ( in_array( 'out-of-stock', $availability_array ) ) {
            $stock_queries[] = array(
                'key'   => '_stock_status',
                'value' => 'outofstock',
            );
        }
        
        // Only add if we have valid stock conditions
        if ( ! empty( $stock_queries ) ) {
            if ( count( $stock_queries ) > 1 ) {
                $stock_queries['relation'] = 'OR';
            }
            $args['meta_query'][] = $stock_queries;
        }
    }

    // Remove empty tax_query if no tax conditions before setting relations
    if ( empty( $args['tax_query'] ) ) {
        unset( $args['tax_query'] );
    } else {
        // Only set tax_query relation if we have multiple queries
        if ( count( $args['tax_query'] ) > 1 ) {
            $args['tax_query']['relation'] = 'AND';
        }
    }

    // Set meta_query relation if we have multiple meta conditions
    if ( count( $args['meta_query'] ) > 1 ) {
        $args['meta_query']['relation'] = 'AND';
    }

    // For custom sorting (only when NO filters are applied)
    // Skip custom sorting if user has applied any filters
    $has_filters = ! empty( $brands_array ) || ! empty( $availability_array ) || $min_rating > 0 || 
                   ( $max_price > 0 && $max_price < 25000 ) || ! empty( $search );
    $use_custom_sorting = empty( $cat_array ) && ! $is_category_page && ! $has_filters;
    
    if ( $use_custom_sorting ) {
        // Get all matching products for custom sorting (only on initial unfiltered shop view)
        $args_all = $args;
        $args_all['posts_per_page'] = -1;  // Get all
        $args_all['paged'] = 1;
        $query = new WP_Query( $args_all );
    } else {
        // Normal pagination query
        $query = new WP_Query( $args );
    }

    // Apply smart sorting when NO category filter is applied (default shop view)
    // Priority: Rated products first (by rating DESC), then unrated, with Heaters prioritized in each group
    if ( $use_custom_sorting ) {
        // Optimize: Get all ratings and heater category info in one query instead of per-product
        global $wpdb;
        $post_ids = wp_list_pluck( $query->posts, 'ID' );
        
        // Get all ratings at once
        $rating_query = "SELECT post_id, AVG(CAST(pm.meta_value AS FLOAT)) as avg_rating
                         FROM {$wpdb->postmeta} pm
                         WHERE pm.post_id IN (" . implode(',', array_map('absint', $post_ids)) . ")
                         AND pm.meta_key = '_wc_average_rating'
                         GROUP BY pm.post_id";
        $ratings = wp_cache_get( 'kt_product_ratings_' . md5( implode( ',', $post_ids ) ) );
        if ( false === $ratings ) {
            $ratings = $wpdb->get_results( $rating_query );
            wp_cache_set( 'kt_product_ratings_' . md5( implode( ',', $post_ids ) ), $ratings, '', 3600 );
        }
        $ratings_map = wp_list_pluck( $ratings, 'avg_rating', 'post_id' );
        
        // Check heater category
        $heater_term = get_term_by( 'slug', 'heaters', 'product_cat' );
        $heater_term_id = $heater_term ? $heater_term->term_id : 0;
        
        // Collect all products with their metadata for custom sorting
        $product_list = array();
        foreach ( $query->posts as $post ) {
            $product_id = $post->ID;
            
            // Get rating from cache
            $rating = isset( $ratings_map[ $product_id ] ) ? (float) $ratings_map[ $product_id ] : 0;
            $has_rating = $rating > 0;
            
            // Check if product is in heaters category
            $is_heater = $heater_term_id && has_term( $heater_term_id, 'product_cat', $product_id );
            
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
        $query->posts = array_column( $product_list, 'post' );
    }

    // Apply rating filter in PHP (since WooCommerce stores ratings in postmeta)
    $filtered_posts = $query->posts;
    // For custom sorting, the actual post count is the filtered posts array
    if ( $use_custom_sorting ) {
        $total_filtered_products = count( $filtered_posts );
    } else {
        $total_filtered_products = $query->found_posts;
    }
    
    if ( $min_rating > 0 ) {
        $filtered_posts = array_filter( $filtered_posts, function( $post ) use ( $min_rating ) {
            $product = wc_get_product( $post->ID );
            if ( ! $product ) {
                return false;
            }
            $average_rating = $product->get_average_rating();
            return $average_rating >= $min_rating;
        });
        
        // Re-index array after filtering
        $filtered_posts = array_values( $filtered_posts );
        
        // Update total count after rating filter
        $total_filtered_products = count( $filtered_posts );
        
        // Update query posts to reflect rating filter
        $query->posts = $filtered_posts;
    }

    // Calculate pagination correctly
    $total_products = $total_filtered_products;
    $max_pages = ceil( $total_products / $posts_per_page );

    // Apply pagination to filtered/sorted posts for current page
    // This applies when we have custom sorting or rating filter
    if ( $use_custom_sorting || $min_rating > 0 ) {
        $offset = ( $paged - 1 ) * $posts_per_page;
        $current_page_posts = array_slice( $query->posts, $offset, $posts_per_page );
        $query->posts = $current_page_posts;
        $query->post_count = count( $current_page_posts );
    }

    // Generate HTML for products
    ob_start();
    
    if ( ! empty( $query->posts ) ) {
        $products_displayed = 0;
        foreach ( $query->posts as $post ) {
            setup_postdata( $post );
            $product = wc_get_product( $post->ID );
            if ( ! $product ) {
                continue;
            }

            // Get product data
            $product_id = $product->get_id();
            $title = $product->get_name();
            $description = wp_trim_words( $product->get_short_description() ?: $product->get_description(), 15 );
            $price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();
            $image = get_the_post_thumbnail_url( $product_id, 'woocommerce_thumbnail' );
            $on_sale = $product->is_on_sale();
            $featured = $product->is_featured();
            $in_stock = $product->is_in_stock();
            $rating = $product->get_average_rating();
            $review_count = $product->get_review_count();

            // Double check: Skip products with no price or price = 0 (safety check)
            if ( empty( $price ) || floatval( $price ) <= 0 ) {
                continue;
            }

            // Determine badge
            $badge_class = '';
            $badge_text = '';
            if ( $on_sale ) {
                $badge_class = 'kt-badge-hot';
                $badge_text = 'SALE';
            } elseif ( $featured ) {
                $badge_class = 'kt-badge-new';
                $badge_text = 'FEATURED';
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

                                <a href="<?php echo esc_url( $product->get_permalink() ); ?>">
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
                                <a href="<?php echo esc_url( $product->get_permalink() ); ?>">
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
                                <a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="kt-btn-details" title="View Details">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

            <?php
            $products_displayed++;
        }
        
        // If no products were displayed due to price filtering, show no products message
        if ( $products_displayed === 0 ) {
            ?>
            <div class="kt-no-products-found">
                <p><?php esc_html_e( 'No products found matching your criteria.', 'woocommerce' ); ?></p>
                <p><button class="kt-btn-link" id="kt-clear-filters-ajax">Clear all filters</button></p>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="kt-no-products-found">
            <p><?php esc_html_e( 'No products found matching your criteria.', 'woocommerce' ); ?></p>
            <p><button class="kt-btn-link" id="kt-clear-filters-ajax">Clear all filters</button></p>
        </div>
        <?php
    }

    wp_reset_postdata();
    $html = ob_get_clean();
    
    // Generate pagination HTML with data attributes for AJAX handling
    $pagination_html = '';
    if ( $max_pages > 1 ) {
        $pagination_html = '<div class="woocommerce-pagination">';
        
        // Previous link
        if ( $paged > 1 ) {
            $pagination_html .= '<a class="page-numbers kt-page-link prev-page" data-paged="' . ( $paged - 1 ) . '" href="#">&laquo;</a>';
        }
        
        // Page numbers with ellipsis support
        $start_page = max( 1, $paged - 2 );
        $end_page = min( $max_pages, $paged + 2 );
        
        if ( $start_page > 1 ) {
            $pagination_html .= '<a class="page-numbers kt-page-link" data-paged="1" href="#">1</a>';
            if ( $start_page > 2 ) {
                $pagination_html .= '<span class="page-numbers dots">&hellip;</span>';
            }
        }
        
        for ( $i = $start_page; $i <= $end_page; $i++ ) {
            if ( $i == $paged ) {
                $pagination_html .= '<span aria-current="page" class="page-numbers current">' . $i . '</span>';
            } else {
                $pagination_html .= '<a class="page-numbers kt-page-link" data-paged="' . $i . '" href="#">' . $i . '</a>';
            }
        }
        
        if ( $end_page < $max_pages ) {
            if ( $end_page < $max_pages - 1 ) {
                $pagination_html .= '<span class="page-numbers dots">&hellip;</span>';
            }
            $pagination_html .= '<a class="page-numbers kt-page-link" data-paged="' . $max_pages . '" href="#">' . $max_pages . '</a>';
        }
        
        // Next link
        if ( $paged < $max_pages ) {
            $pagination_html .= '<a class="page-numbers kt-page-link next-page" data-paged="' . ( $paged + 1 ) . '" href="#">&raquo;</a>';
        }
        
        $pagination_html .= '</div>';
    }
    
    // Prepare response data
    $response_data = array( 
        'html' => $html,
        'pagination' => $pagination_html,
        'total_products' => $total_products,
        'total_pages' => $max_pages,
        'current_page' => $paged,
        'posts_per_page' => $posts_per_page,
    );

    wp_send_json_success( $response_data );
}

add_action( 'wp_ajax_kt_filter_products', 'kt_filter_products_ajax' );
add_action( 'wp_ajax_nopriv_kt_filter_products', 'kt_filter_products_ajax' );
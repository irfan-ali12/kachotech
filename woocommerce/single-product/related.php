<?php
/**
 * Related Products – KachoTech Custom Template
 * Displays related products with same design as shop page
 */

defined( 'ABSPATH' ) || exit;

// Get the global product object passed by WooCommerce
global $product;

if ( empty( $product ) ) {
    return;
}

// Get products from same category
$product_cats = $product->get_category_ids();

if ( empty( $product_cats ) ) {
    // No category, try related products from WooCommerce
    $related_ids = wc_get_related_products( $product->get_id(), 4 );
    if ( empty( $related_ids ) ) {
        return;
    }
} else {
    // Query products from same category
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'orderby'        => 'rand',
        'post__not_in'   => array( $product->get_id() ),
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'id',
                'terms'    => $product_cats[0],
            ),
        ),
    );
    
    $query = new WP_Query( $args );
    $related_ids = wp_list_pluck( $query->posts, 'ID' );
    wp_reset_postdata();
    
    if ( empty( $related_ids ) ) {
        return;
    }
}

// Get related product objects and filter by price
$related_products = array();
foreach ( $related_ids as $id ) {
    $rel_product = wc_get_product( $id );
    
    // Only include products with valid prices (not 0 and not empty) and purchasable
    if ( $rel_product && $rel_product->get_price() && $rel_product->get_price() > 0 ) {
        $related_products[] = $rel_product;
    }
}

if ( empty( $related_products ) ) {
    return;
}
?>

<div class="kt-related-wrapper">
    <div class="kt-related-header">
        <h2><?php esc_html_e( 'Related Products', 'woocommerce' ); ?></h2>
    </div>

    <div class="kt-related-grid">
        <?php
        foreach ( $related_products as $rel_product ) {
            if ( ! $rel_product ) {
                continue;
            }

            // Get product data
            $product_id = $rel_product->get_id();
            $title = $rel_product->get_name();
            $price = $rel_product->get_regular_price();
            $sale_price = $rel_product->get_sale_price();
            $rating = $rel_product->get_average_rating();
            $review_count = $rel_product->get_review_count();
            $in_stock = $rel_product->is_in_stock();
            $on_sale = $rel_product->is_on_sale();
            $featured = $rel_product->is_featured();
            $stock = $rel_product->get_stock_quantity();

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
            $product_cats = $rel_product->get_category_ids();
            $cat_name = '';
            if ( ! empty( $product_cats ) ) {
                $cat = get_term( $product_cats[0], 'product_cat' );
                if ( $cat && ! is_wp_error( $cat ) ) {
                    $cat_name = $cat->name;
                }
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

                <a href="<?php echo esc_url( $rel_product->get_permalink() ); ?>">
                    <?php
                    // Get the image ID for proper handling
                    $image_id = $rel_product->get_image_id();
                    if ( $image_id ) {
                        $image_url = wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' );
                        if ( $image_url ) {
                            echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" class="kt-product-image">';
                        } else {
                            echo '<div class="kt-product-image-placeholder"></div>';
                        }
                    } else {
                        // Use WooCommerce placeholder
                        echo '<img src="' . esc_url( wc_placeholder_img_src( 'woocommerce_thumbnail' ) ) . '" alt="' . esc_attr( $title ) . '" class="kt-product-image">';
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
                $product_cats = $rel_product->get_category_ids();
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
                <a href="<?php echo esc_url( $rel_product->get_permalink() ); ?>">
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
                        href="<?php echo esc_url( $rel_product->add_to_cart_url() ); ?>"
                        data-product_id="<?php echo esc_attr( $product_id ); ?>"
                        class="kt-btn-cart add_to_cart_button ajax_add_to_cart"
                    >
                        Add to Cart
                    </a>
                <?php else : ?>
                    <button class="kt-btn-cart" disabled>Add to Cart</button>
                <?php endif; ?>
                <a href="<?php echo esc_url( $rel_product->get_permalink() ); ?>" class="kt-btn-details" title="View Details">
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <?php
        }
        ?>
    </div>
</div>

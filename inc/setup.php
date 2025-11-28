<?php
/**
 * KachoTech – Setup & Attributes Registration
 * 
 * Registers product attributes for different product categories:
 * - Heaters
 * - Electronic Products
 * - Cosmetic Products
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register WooCommerce product attributes for KachoTech
 */
function kt_register_product_attributes() {
    $attributes = array(
        // HEATERS - Specific attributes
        'heater_type' => array(
            'name'          => 'Heater Type',
            'slug'          => 'heater_type',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'power_wattage' => array(
            'name'          => 'Power (Wattage)',
            'slug'          => 'power_wattage',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'heating_area' => array(
            'name'          => 'Heating Area (sq ft)',
            'slug'          => 'heating_area',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'temperature_control' => array(
            'name'          => 'Temperature Control',
            'slug'          => 'temperature_control',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'safety_features' => array(
            'name'          => 'Safety Features',
            'slug'          => 'safety_features',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        
        // ELECTRONICS - Specific attributes
        'connectivity' => array(
            'name'          => 'Connectivity',
            'slug'          => 'connectivity',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'battery_life' => array(
            'name'          => 'Battery Life',
            'slug'          => 'battery_life',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'display_type' => array(
            'name'          => 'Display Type',
            'slug'          => 'display_type',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'warranty_period' => array(
            'name'          => 'Warranty Period',
            'slug'          => 'warranty_period',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'voltage_input' => array(
            'name'          => 'Voltage Input',
            'slug'          => 'voltage_input',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),

        // COSMETICS - Specific attributes
        'skin_type' => array(
            'name'          => 'Skin Type',
            'slug'          => 'skin_type',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'key_ingredients' => array(
            'name'          => 'Key Ingredients',
            'slug'          => 'key_ingredients',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'spf_rating' => array(
            'name'          => 'SPF Rating',
            'slug'          => 'spf_rating',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'product_format' => array(
            'name'          => 'Product Format',
            'slug'          => 'product_format',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
        'dermatologist_tested' => array(
            'name'          => 'Dermatologist Tested',
            'slug'          => 'dermatologist_tested',
            'type'          => 'select',
            'orderby'       => 'menu_order',
            'has_archives'  => true,
        ),
    );

    // Register each attribute
    foreach ( $attributes as $slug => $args ) {
        // Check if attribute already exists
        if ( ! wc_get_attribute( $slug ) ) {
            wc_create_attribute( $args );
        }
    }
}

add_action( 'init', 'kt_register_product_attributes', 5 );

/**
 * Helper function to get attribute suggestions based on product category
 * 
 * @param int $product_id Product ID
 * @return array Array of recommended attribute slugs
 */
function kt_get_recommended_attributes( $product_id ) {
    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return array();
    }

    $category_ids = $product->get_category_ids();
    $recommended = array();

    foreach ( $category_ids as $cat_id ) {
        $cat = get_term( $cat_id, 'product_cat' );
        if ( ! $cat || is_wp_error( $cat ) ) {
            continue;
        }

        $cat_slug = strtolower( $cat->slug );

        // Match category slug to recommend attributes
        if ( strpos( $cat_slug, 'heater' ) !== false ) {
            $recommended = array_merge( $recommended, array(
                'heater_type',
                'power_wattage',
                'heating_area',
                'temperature_control',
                'safety_features',
            ) );
        }

        if ( strpos( $cat_slug, 'electronic' ) !== false || strpos( $cat_slug, 'gadget' ) !== false || strpos( $cat_slug, 'device' ) !== false ) {
            $recommended = array_merge( $recommended, array(
                'connectivity',
                'battery_life',
                'display_type',
                'warranty_period',
                'voltage_input',
            ) );
        }

        if ( strpos( $cat_slug, 'cosmetic' ) !== false || strpos( $cat_slug, 'beauty' ) !== false || strpos( $cat_slug, 'skincare' ) !== false ) {
            $recommended = array_merge( $recommended, array(
                'skin_type',
                'key_ingredients',
                'spf_rating',
                'product_format',
                'dermatologist_tested',
            ) );
        }
    }

    // Remove duplicates and return
    return array_unique( $recommended );
}

/**
 * Add admin notice to suggest attributes based on product category
 */
add_action( 'edit_form_after_title', function() {
    global $post, $product;

    if ( get_post_type() !== 'product' ) {
        return;
    }

    $recommended = kt_get_recommended_attributes( $post->ID );

    if ( ! empty( $recommended ) ) {
        $attr_names = array();
        foreach ( $recommended as $slug ) {
            $attr = wc_get_attribute( $slug );
            if ( $attr ) {
                $attr_names[] = $attr->name;
            }
        }

        if ( ! empty( $attr_names ) ) {
            echo '<div class="notice notice-info is-dismissible"><p>';
            echo '<strong>Recommended Attributes for this product:</strong> ' . esc_html( implode( ', ', $attr_names ) );
            echo '</p></div>';
        }
    }
} );

<?php
/**
 * Custom Search AJAX Handler for WooCommerce Products
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Handle AJAX product search - accepts GET requests
 */
add_action( 'wp_ajax_nopriv_kt_product_search', 'kt_product_search_handler' );
add_action( 'wp_ajax_kt_product_search', 'kt_product_search_handler' );

function kt_product_search_handler() {
  // Verify nonce
  $nonce = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
  
  if ( ! wp_verify_nonce( $nonce, 'kt_ajax_search' ) ) {
    wp_send_json_error( array( 'message' => 'Security check failed' ), 403 );
  }
  
  $term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
  $product_cat = isset( $_GET['product_cat'] ) ? sanitize_text_field( wp_unslash( $_GET['product_cat'] ) ) : '';
  
  if ( strlen( $term ) < 2 ) {
    wp_send_json_success( array() );
  }
  
  $args = array(
    'post_type'      => 'product',
    'posts_per_page' => 8,
    's'              => $term,
    'post_status'    => 'publish',
  );
  
  if ( ! empty( $product_cat ) ) {
    $args['tax_query'] = array(
      array(
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => $product_cat,
      ),
    );
  }
  
  $query = new WP_Query( $args );
  $results = array();
  
  if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
      $query->the_post();
      
      $product = wc_get_product( get_the_ID() );
      
      if ( ! $product ) {
        continue;
      }
      
      $thumb = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
      
      $results[] = array(
        'title'      => $product->get_name(),
        'url'        => $product->get_permalink(),
        'price_html' => wp_kses_post( $product->get_price_html() ),
        'thumb'      => $thumb ? $thumb : '',
      );
    }
    wp_reset_postdata();
  }
  
  wp_send_json_success( $results );
}


<?php
/**
 * Template Name: Login
 * Template Post Type: page
 * Description: Custom login page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
include get_stylesheet_directory() . '/template-parts/auth-login.php';
get_footer();

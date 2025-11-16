<?php
/**
 * Template Name: Register
 * Template Post Type: page
 * Description: Custom registration page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
include get_stylesheet_directory() . '/template-parts/auth-register.php';
get_footer();

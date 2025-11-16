<?php
/**
 * Template Name: Order Tracking
 * Template Post Type: page
 * Description: Modern order tracking page template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main class="site-main">
    <?php
    while ( have_posts() ) {
        the_post();
        ?>
        <div class="kt-order-tracking-wrapper">
            <?php
            // Output page title if exists
            if ( get_the_title() ) {
                echo '<header class="page-header"><h1 class="page-title">' . esc_html( get_the_title() ) . '</h1></header>';
            }

            // Output page content before the tracking form
            if ( get_the_content() ) {
                echo '<div class="page-content">';
                the_content();
                echo '</div>';
            }

            // Load the order tracking template
            include get_stylesheet_directory() . '/template-parts/order-tracking-page.php';
            ?>
        </div>
        <?php
    }
    ?>
</main>

<?php
get_footer();

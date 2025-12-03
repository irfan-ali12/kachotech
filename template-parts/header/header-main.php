<?php
/**
 * KachoTech Custom Header v3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cart_count = 0;
if ( function_exists( 'WC' ) && WC()->cart ) {
    $cart_count = WC()->cart->get_cart_contents_count();
}

/**
 * Top-level product categories for dropdown
 */
$kt_cats = array();
if ( taxonomy_exists( 'product_cat' ) ) {
    $kt_cats = get_terms( array(
        'taxonomy'   => 'product_cat',
        'parent'     => 0,
        'hide_empty' => false,
        'orderby'    => 'menu_order',
        'order'      => 'ASC',
    ) );
}
?>

<header class="kt-header">

    <!-- Main header row -->
    <div class="kt-header-main">
        <div class="ast-container kt-header-main-inner">

            <!-- Logo -->
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="kt-logo-wrap">
                    <?php if ( has_custom_logo() ) : ?>
                        <span class="kt-logo-img-wrap">
                            <?php the_custom_logo(); ?>
                        </span>
                    <?php else : ?>
                        <span class="kt-logo-placeholder">KachoTech</span>
                    <?php endif; ?>
            </a>

            <!-- Search pill (desktop) -->
            <div class="kt-search-wrap">
                <form role="search" method="get" class="kt-search-form" action="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
                    <div class="kt-search-form-wrap">

                        <div class="kt-search-inner">

                            <!-- Category pill -->
                            <div class="kt-search-cat">
                                <button type="button" class="kt-search-cat-toggle" aria-haspopup="true" aria-expanded="false" aria-controls="kt-search-cat-menu">
                                    <span class="kt-search-cat-icon dashicons dashicons-screenoptions"></span>
                                    <span class="kt-search-cat-label">All Categories</span>
                                    <span class="kt-search-cat-caret">▾</span>
                                </button>

                                <input type="hidden" name="product_cat" value="">
                            </div>

                            <!-- Search field -->
                            <input
                                type="search"
                                class="kt-search-input"
                                name="s"
                                value="<?php echo esc_attr( get_search_query() ); ?>"
                                placeholder="Search heaters, TVs, cosmetics, kitchen &amp; more..."
                                autocomplete="off"
                            />
                            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                                <input type="hidden" name="post_type" value="product">
                            <?php endif; ?>

                            <!-- Button -->
                            <button type="submit" class="kt-search-button">
                                <span class="kt-search-button-icon dashicons dashicons-search"></span>
                                <span class="kt-search-button-text">Search</span>
                            </button>
                        </div>

                        <!-- Category menu (outside inner so it's not clipped) -->
                        <div id="kt-search-cat-menu" class="kt-search-cat-menu" hidden>
                            <ul>
                                <li data-slug=""><?php esc_html_e( 'All Categories', 'kacho' ); ?></li>
                                <?php if ( ! empty( $kt_cats ) && ! is_wp_error( $kt_cats ) ) : ?>
                                    <?php foreach ( $kt_cats as $cat ) : ?>
                                        <li data-slug="<?php echo esc_attr( $cat->slug ); ?>">
                                            <?php echo esc_html( $cat->name ); ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <li data-slug="heaters">Heaters</li>
                                    <li data-slug="electronics">Electronics</li>
                                    <li data-slug="cosmetics">Cosmetics</li>
                                    <li data-slug="kitchen">Kitchen</li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- AJAX suggestions -->
                        <div class="kt-search-suggestions" hidden></div>

                    </div>
                </form>
            </div>

            <!-- Right side icons (including mobile search icon) -->
            <div class="kt-header-actions">

                <!-- Search icon button (mobile only) -->
                <button class="kt-search-icon-mobile" aria-label="Open search" id="kt-search-toggle-mobile">
                    <span class="dashicons dashicons-search"></span>
                </button>

                <!-- Order tracker (use page permalink if available, fallback to query param) -->
                <?php
                $order_tracking_link = home_url( '/?order-tracking=1' );
                $ot_page = get_page_by_path( 'order-tracking' );
                if ( $ot_page ) {
                    $order_tracking_link = get_permalink( $ot_page );
                }
                ?>
                <a href="<?php echo esc_url( $order_tracking_link ); ?>" class="kt-header-icon" title="Order Tracking">
                    <span class="dashicons dashicons-location-alt"></span>
                </a>

                <!-- My account -->
                <a class="kt-header-icon" href="<?php echo esc_url( function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'dashboard' ) : ( function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'myaccount' ) ) : home_url( '/account/' ) ) ); ?>" title="My Account">
                    <span class="dashicons dashicons-admin-users"></span>
                </a>

                <!-- Cart with badge -->
                <a class="kt-header-icon kt-header-badge" href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ) ); ?>" title="Cart">
                    <span class="dashicons dashicons-cart"></span>
                    <span class="kt-header-badge-count"><?php echo esc_html( $cart_count > 0 ? $cart_count : 0 ); ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Search Modal (Mobile - Simple search without categories) -->
    <div id="kt-search-modal" class="kt-search-modal" hidden>
        <div class="kt-search-modal-overlay"></div>
        <div class="kt-search-modal-content">
            <button class="kt-search-modal-close" aria-label="Close search">&times;</button>
            <form role="search" method="get" class="kt-search-form-mobile" action="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
                <div class="kt-search-form-wrap-mobile">
                    <div class="kt-search-inner-mobile">
                        <!-- Simple search field for mobile (no category filter) -->
                        <input
                            type="search"
                            class="kt-search-input-mobile"
                            name="s"
                            value="<?php echo esc_attr( get_search_query() ); ?>"
                            placeholder="Search products..."
                            autocomplete="off"
                        />
                        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                            <input type="hidden" name="post_type" value="product">
                        <?php endif; ?>

                        <!-- Submit button for mobile -->
                        <button type="submit" class="kt-search-button-mobile">
                            <span class="dashicons dashicons-search"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</header>

<script>
/* Mobile search modal */
document.addEventListener('DOMContentLoaded', function () {
    const searchToggleMobile = document.getElementById('kt-search-toggle-mobile');
    const searchModal = document.getElementById('kt-search-modal');
    const searchModalClose = document.querySelector('.kt-search-modal-close');
    const searchModalOverlay = document.querySelector('.kt-search-modal-overlay');

    if (searchToggleMobile && searchModal) {
        searchToggleMobile.addEventListener('click', function () {
            searchModal.removeAttribute('hidden');
            document.body.style.overflow = 'hidden';
            document.querySelector('.kt-search-input-mobile').focus();
        });

        if (searchModalClose) {
            searchModalClose.addEventListener('click', function () {
                searchModal.setAttribute('hidden', 'hidden');
                document.body.style.overflow = '';
            });
        }

        if (searchModalOverlay) {
            searchModalOverlay.addEventListener('click', function () {
                searchModal.setAttribute('hidden', 'hidden');
                document.body.style.overflow = '';
            });
        }
    }

    // Mobile category dropdown
    // Mobile search focus
    const searchInput = document.querySelector('.kt-search-input-mobile');
    if (searchInput) {
        searchInput.focus();
    }
});

/* Category dropdown behaviour (desktop) */
document.addEventListener('DOMContentLoaded', function () {
    const wrap = document.querySelector('.kt-search-form-wrap');
    if (!wrap) return;

    const toggle = wrap.querySelector('.kt-search-cat-toggle');
    const menu = wrap.querySelector('#kt-search-cat-menu');
    const hidden = wrap.querySelector('input[name="product_cat"]');
    const label = wrap.querySelector('.kt-search-cat-label');

    if (!toggle || !menu || !hidden || !label) return;

    function closeMenu() {
        menu.setAttribute('hidden', 'hidden');
        toggle.setAttribute('aria-expanded', 'false');
    }

    function openMenu() {
        menu.removeAttribute('hidden');
        toggle.setAttribute('aria-expanded', 'true');
    }

    toggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (menu.hasAttribute('hidden')) {
            openMenu();
        } else {
            closeMenu();
        }
    });

    menu.addEventListener('click', function (e) {
        const item = e.target.closest('li');
        if (!item) return;

        const slug = item.getAttribute('data-slug') || '';
        const text = item.textContent.trim();

        hidden.value = slug;
        label.textContent = text;

        menu.querySelectorAll('li').forEach(function (li) {
            li.classList.toggle('is-active', li === item);
        });

        closeMenu();
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.kt-search-cat')) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeMenu();
        }
    });
});
</script>


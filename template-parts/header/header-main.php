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

    <!-- Top promo bar -->
    <div class="kt-top-bar">
        <div class="ast-container kt-top-bar-inner">
            <div class="kt-top-pill">
                <span class="kt-top-pill-icon">❄</span>
                <span class="kt-top-pill-text">
                    <strong>Winter Heating Sale</strong>
                    <span class="kt-top-pill-sub">Up to 35% OFF</span>
                </span>
            </div>

            <div class="kt-top-info">
                Free delivery on orders over Rs 4,999 &nbsp; | &nbsp;
                100% Original Electronics &amp; Cosmetics
            </div>
        </div>
    </div>

    <!-- Main header row -->
    <div class="kt-header-main">
        <div class="ast-container kt-header-main-inner">

            <!-- Menu toggle (hamburger) - left-most -->
            <button class="kt-open-sidebar flex h-9 w-9 flex-col justify-center rounded-full border border-slate-600/70 bg-black/50 pl-[9px] hover:border-white">
                <span class="mb-[3px] h-0.5 w-4 bg-white"></span>
                <span class="mb-[3px] h-0.5 w-4 bg-white"></span>
                <span class="h-0.5 w-4 bg-white"></span>
            </button>
  
            <!-- Logo -->
            <div class="kt-logo-wrap">
                    <?php if ( has_custom_logo() ) : ?>
                        <span class="kt-logo-img-wrap">
                            <?php the_custom_logo(); ?>
                        </span>
                    <?php else : ?>
                        <span class="kt-logo-placeholder">KachoTech</span>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Search pill -->
            <div class="kt-search-wrap">
                <form role="search" method="get" class="kt-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
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

            <!-- Right side icons -->
            <div class="kt-header-actions">

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

    <!-- Nav + trust line -->
    <div class="kt-nav-bar">
        <div class="ast-container kt-nav-inner">
            <nav class="kt-primary-nav" aria-label="Main navigation">
                <?php
                if ( has_nav_menu( 'primary' ) ) {
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'container'      => false,
                            'menu_class'     => 'kt-primary-menu',
                            'depth'          => 1,
                        )
                    );
                } else {
                    ?>
                    <ul class="kt-primary-menu">
                        <li class="current"><a href="#">All Products</a></li>
                        <li><a href="#">Heaters</a></li>
                        <li><a href="#">Electronics</a></li>
                        <li><a href="#">Cosmetics</a></li>
                        <li><a href="#">Deals</a></li>
                    </ul>
                    <?php
                }
                ?>
            </nav>

            <div class="kt-nav-trust">
                <span>🚚 Nationwide Delivery</span>
                <span>💳 COD &amp; Online Payments</span>
                <span>✅ 100% Original Stock</span>
            </div>
        </div>
    </div>

</header>

<script>
/* Sticky header hide/show on scroll */
(function() {
    const header = document.querySelector('.kt-header');
    if (!header) return;

    let lastScrollTop = 0;
    let isHidden = false;

    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollDelta = 5; // Minimum pixels to trigger hide/show

        // Scrolling down - hide header
        if (scrollTop > lastScrollTop + scrollDelta) {
            if (!isHidden) {
                header.classList.remove('show');
                header.classList.add('hide');
                isHidden = true;
            }
        }
        // Scrolling up - show header
        else if (scrollTop < lastScrollTop - scrollDelta) {
            if (isHidden) {
                header.classList.remove('hide');
                header.classList.add('show');
                isHidden = false;
            }
        }

        // Near top - always show
        if (scrollTop < 50) {
            header.classList.remove('hide');
            header.classList.add('show');
            isHidden = false;
        }

        lastScrollTop = scrollTop;
    }, false);
})();
</script>

<script>
/* Category dropdown behaviour (wait for DOM) */
document.addEventListener('DOMContentLoaded', function () {
    const wrap = document.querySelector('.kt-search-form-wrap');
    if (!wrap) {
        console.warn('KT: .kt-search-form-wrap not found');
        return;
    }

    const toggle = wrap.querySelector('.kt-search-cat-toggle');
    const menu = wrap.querySelector('#kt-search-cat-menu');
    const hidden = wrap.querySelector('input[name="product_cat"]');
    const label = wrap.querySelector('.kt-search-cat-label');

    if (!toggle) console.warn('KT: .kt-search-cat-toggle not found');
    if (!menu) console.warn('KT: #kt-search-cat-menu not found');
    if (!hidden) console.warn('KT: input[name="product_cat"] not found');
    if (!label) console.warn('KT: .kt-search-cat-label not found');

    if (!toggle || !menu || !hidden || !label) return;

    function closeMenu() {
        if (menu.hasAttribute('hidden')) return; // Already hidden
        menu.setAttribute('hidden', 'hidden');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.classList.remove('open');
    }

    function openMenu() {
        if (!menu.hasAttribute('hidden')) return; // Already open
        menu.removeAttribute('hidden');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.classList.add('open');
    }

    toggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('KT: Toggle clicked, menu hidden?', menu.hasAttribute('hidden'));
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

    <script>
    // Robust menu toggle behaviour: toggles mobile nav overlay and button X state,
    // sets aria-expanded and ensures nav is visible even if CSS loading order differs.
    (function () {
        function initMenuToggle() {
            var btn = document.getElementById('kt-menu-toggle');
            var nav = document.querySelector('.kt-nav-bar');
            if (!btn || !nav) return;

            function openNav() {
                document.body.classList.add('kt-menu-open');
                btn.classList.add('open');
                btn.setAttribute('aria-expanded', 'true');
                nav.style.display = 'block';
                nav.classList.add('kt-open');
                nav.setAttribute('data-kt-open', '1');
            }

            function closeNav() {
                document.body.classList.remove('kt-menu-open');
                btn.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
                nav.style.display = 'none';
                nav.classList.remove('kt-open');
                nav.removeAttribute('data-kt-open');
            }

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if ( document.body.classList.contains('kt-menu-open') ) {
                    closeNav();
                } else {
                    openNav();
                }
            });

            // Close nav when clicking outside on mobile overlay
            nav.addEventListener('click', function (ev) {
                if ( ev.target === nav ) {
                    closeNav();
                }
            });

            // Ensure initial aria state
            btn.setAttribute('aria-expanded', document.body.classList.contains('kt-menu-open') ? 'true' : 'false');
        }

        if ( document.readyState === 'loading' ) {
            document.addEventListener('DOMContentLoaded', initMenuToggle);
        } else {
            initMenuToggle();
        }
    })();

    // SIDEBAR for hamburger menu
    (function() {
        const sidebarBackdrop = document.getElementById('kt-sidebar-backdrop');
        const sidebar = document.getElementById('kt-sidebar');
        const closeSidebarBtn = document.getElementById('kt-close-sidebar');
        const openSidebarBtns = Array.from(document.querySelectorAll('.kt-open-sidebar'));

        function openSidebar() {
            sidebarBackdrop.classList.remove('hidden');
            requestAnimationFrame(() => {
                sidebar.classList.remove('-translate-x-full');
            });
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            setTimeout(() => sidebarBackdrop.classList.add('hidden'), 250);
        }

        openSidebarBtns.forEach(btn => btn.addEventListener('click', openSidebar));
        closeSidebarBtn?.addEventListener('click', closeSidebar);
        sidebarBackdrop?.addEventListener('click', e => {
            if (e.target === sidebarBackdrop) closeSidebar();
        });
    })();
    </script>

<!-- SIDEBAR (appears on all pages) -->
<div id="kt-sidebar-backdrop" class="kt-sidebar-backdrop fixed inset-0 z-50 hidden items-stretch">
    <div id="kt-sidebar" class="h-full w-72 max-w-full bg-[#020617] px-6 py-6 transform -translate-x-full transition-transform duration-300 ease-out">
        <div class="mb-6 flex items-center justify-between">
            <span class="text-lg font-bold tracking-[.18em] uppercase">KACHOTECH</span>
            <button id="kt-close-sidebar" class="text-slate-300 hover:text-white">✕</button>
        </div>
        <nav class="space-y-4 text-sm font-semibold uppercase tracking-[.16em] text-slate-300">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block hover:text-white">Home</a>
            <a href="<?php echo esc_url( get_term_link( 'heaters', 'product_cat' ) ); ?>" class="block hover:text-white">Heaters</a>
            <a href="<?php echo esc_url( get_term_link( 'cosmetics', 'product_cat' ) ); ?>" class="block hover:text-white">Cosmetics</a>
            <a href="<?php echo esc_url( get_term_link( 'electronics', 'product_cat' ) ); ?>" class="block hover:text-white">Electronics</a>
            <a href="#" class="block hover:text-white">Winter Deals</a>
            <a href="#" class="block hover:text-white">Contact</a>
        </nav>
        <div class="mt-8 border-t border-slate-700/80 pt-4 text-xs text-slate-400">Trusted heating, electronics &amp; cosmetics across Pakistan.</div>
    </div>
</div>


<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
// Hero section: product carousel + rows with AJAX add-to-cart
// Categories to show (slug => label)
$categories = array(
    'heaters'     => 'HEATERS',
    'cosmetics'   => 'COSMETICS',
    'electronics' => 'ELECTRONICS',
);

// Helper to get products for a category (falls back to recent products)
function kt_get_products_for_cat( $cat_slug, $limit = 6 ) {
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if ( taxonomy_exists( 'product_cat' ) && $cat_slug ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $cat_slug,
            ),
        );
    }

    $q = new WP_Query( $args );
    $products = array();
    if ( $q->have_posts() ) {
        while ( $q->have_posts() ) {
            $q->the_post();
            $products[] = wc_get_product( get_the_ID() );
        }
        wp_reset_postdata();
    }

    return $products;
}

?>

<section class="kt-hero-bg min-h-screen overflow-hidden">

  <div class="mx-auto flex h-full max-w-6xl flex-col px-4">

    <!-- HERO CONTENT & PRODUCTS (no scroll, fills remaining height) -->
    <div class="flex min-h-0 flex-1 gap-6 pb-4">
      <!-- LEFT: tabs + social -->
      <aside class="hidden w-16 flex-col items-center justify-between py-4 lg:flex">
        <div class="flex flex-col items-center gap-10 text-xs font-semibold text-slate-500">
          <button class="kt-tab kt-tab-active" data-kt-tab="0">HEATERS</button>
          <button class="kt-tab" data-kt-tab="1">COSMETICS</button>
          <button class="kt-tab" data-kt-tab="2">ELECTRONICS</button>
        </div>
        <div class="flex flex-col items-center gap-3 text-slate-400"><a href="#" class="hover:text-white" aria-label="Instagram">IG</a><a href="#" class="hover:text-white" aria-label="Facebook">f</a></div>
      </aside>

      <!-- MAIN hero + bottom cards -->
      <div class="flex min-h-0 flex-1 flex-col gap-4 pb-2">

        <!-- SLIDES -->
        <div class="relative flex-1 min-h-[250px] md:min-h-[320px] lg:min-h-[360px]">
          <!-- HEATERS -->
          <div class="kt-slide kt-slide-active" data-kt-slide="0">
            <div class="grid h-full items-center gap-8 md:grid-cols-[1.1fr_minmax(0,1fr)]">
              <div class="kt-anim-text space-y-6">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight">BORING<br><span class="text-kt-primary">ROOMS?</span></h1>
                <p class="max-w-md text-sm md:text-base text-slate-300">Turn freezing nights into warm, cozy evenings with safe, fuel-efficient heaters curated for Pakistan’s harsh winters.</p>
                <div class="flex flex-wrap items-center gap-4 text-xs md:text-sm text-slate-300"><span>Starting from <span class="font-semibold text-white">Rs 2,499</span></span><span class="h-1 w-1 rounded-full bg-slate-500"></span><span>Extra <span class="font-semibold text-kt-primary">10% OFF</span> on online payments</span></div>
                <div class="flex flex-wrap items-center gap-4 pt-2"><a href="#" class="inline-flex items-center justify-center rounded-full bg-kt-primary px-6 py-3 text-xs md:text-sm font-semibold uppercase tracking-[.2em] shadow-lg shadow-kt-primary/50 hover:bg-[#ff3b5b]">SHOP HEATERS</a></div>
              </div>

              <div class="kt-anim-image relative flex items-center justify-center">
                <div class="kt-arc absolute -right-6 top-4 h-60 w-60 md:h-72 md:w-72 border-slate-600/60"></div>
                <div class="relative z-10 flex items-center justify-center rounded-[32px] bg-black/40 px-4 py-4 shadow-[0_40px_90px_rgba(0,0,0,.75)]">
                  <div class="kt-product-visual flex items-center justify-center"><img src="http://localhost/kachotech/wp-content/uploads/2025/11/1243678-removebg-preview.png" alt="Heater" class="h-full w-full object-contain"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- COSMETICS -->
          <div class="kt-slide" data-kt-slide="1">
            <div class="grid h-full items-center gap-8 md:grid-cols-[1.1fr_minmax(0,1fr)]">
              <div class="kt-anim-text space-y-6">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight">DRY<br><span class="text-kt-primary">SKIN?</span></h2>
                <p class="max-w-md text-sm md:text-base text-slate-300">Hydrating serums, moisturisers and makeup bundles that keep your skin glowing instead of cracking all winter.</p>
                <div class="flex flex-wrap items-center gap-4 text-xs md:text-sm text-slate-300"><span>Bundles from <span class="font-semibold text-white">Rs 1,799</span></span><span class="h-1 w-1 rounded-full bg-slate-500"></span><span>Free mini serum on orders over <span class="font-semibold">Rs 4,000</span></span></div>
                <div class="flex flex-wrap items-center gap-4 pt-2"><a href="<?php echo esc_url( get_term_link( 'cosmetics', 'product_cat' ) ); ?>" class="inline-flex items-center justify-center rounded-full bg-kt-primary px-6 py-3 text-xs md:text-sm font-semibold uppercase tracking-[.2em] shadow-lg shadow-kt-primary/50 hover:bg-[#ff3b5b]">SHOP COSMETICS</a></div>
              </div>
              <div class="kt-anim-image relative flex items-center justify-center">
                <div class="kt-arc absolute -right-6 top-4 h-60 w-60 md:h-72 md:w-72 border-rose-400/70"></div>
                <div class="relative z-10 flex items-center justify-center rounded-[32px] bg-black/40 px-4 py-4 shadow-[0_40px_90px_rgba(0,0,0,.75)]">
                  <div class="kt-product-visual flex items-center justify-center"><img src="http://localhost/kachotech/wp-content/uploads/2025/11/bioxcin-whitening-face-cream-bioxsine-pure-white-whitening-face-cream-43085223330049-removebg-preview.png" alt="Cosmetics" class="h-full w-full object-contain"></div>
                </div>
              </div>
            </div>
          </div>
          <!-- ELECTRONICS -->
          <div class="kt-slide" data-kt-slide="2">
            <div class="grid h-full items-center gap-8 md:grid-cols-[1.1fr_minmax(0,1fr)]">
              <div class="kt-anim-text space-y-6">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight">DULL<br><span class="text-kt-primary">EVENINGS?</span></h2>
                <p class="max-w-md text-sm md:text-base text-slate-300">Kettles, audio and smart gadgets for the perfect winter entertainment setup at home.</p>
                <div class="flex flex-wrap items-center gap-4 text-xs md:text-sm text-slate-300"><span>Combos from <span class="font-semibold text-white">Rs 3,299</span></span><span class="h-1 w-1 rounded-full bg-slate-500"></span><span>Bundle &amp; save up to <span class="font-semibold">25%</span></span></div>
                <div class="flex flex-wrap items-center gap-4 pt-2"><a href="<?php echo esc_url( get_term_link( 'electronics', 'product_cat' ) ); ?>" class="inline-flex items-center justify-center rounded-full bg-kt-primary px-6 py-3 text-xs md:text-sm font-semibold uppercase tracking-[.2em] shadow-lg shadow-kt-primary/50 hover:bg-[#ff3b5b]">BROWSE ELECTRONICS</a></div>
              </div>
              <div class="kt-anim-image relative flex items-center justify-center">
                <div class="kt-arc absolute -right-6 top-4 h-60 w-60 md:h-72 md:w-72 border-sky-400/70"></div>
                <div class="relative z-10 flex items-center justify-center rounded-[32px] bg-black/40 px-4 py-4 shadow-[0_40px_90px_rgba(0,0,0,.75)]">
                  <div class="kt-product-visual flex items-center justify-center"><img src="http://localhost/kachotech/wp-content/uploads/2025/11/71drPES0yyL._UF1000_1000_QL80_-removebg-preview.png" alt="Electronics combo" class="h-full w-full object-contain"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- bottom products + NEXT -->
        <div class="mt-3 flex items-center justify-between gap-4">
          <div id="kt-product-rows" class="flex-1 overflow-hidden">
            <?php
            // Display only heaters row
            $products = kt_get_products_for_cat( 'heaters', 3 );
            ?>
            <div class="kt-products-row kt-products-row-active gap-3 md:gap-4">
                <?php
                if ( ! empty( $products ) ) {
                    foreach ( $products as $product ) {
                        $product_id = $product->get_id();
                        $product_title = $product->get_name();
                        $product_price = wc_price( $product->get_price() );
                        $product_image = $product->get_image( array( 80, 64 ) );
                        ?>
                        <article class="kt-product-card flex-1 rounded-2xl bg-gradient-to-r from-rose-500 via-amber-400 to-rose-500 p-0.5 text-xs">
                            <div class="flex h-full w-full items-center gap-3 rounded-[15px] bg-slate-950/95 p-3">
                                <div class="h-16 w-20 overflow-hidden rounded-xl bg-slate-800">
                                    <?php echo wp_kses_post( $product_image ); ?>
                                </div>
                                <div class="flex flex-1 flex-col gap-1">
                                    <h3 class="text-[11px] font-semibold uppercase tracking-[.16em] text-slate-100 line-clamp-2"><?php echo esc_html( $product_title ); ?></h3>
                                    <p class="text-[11px] text-slate-400"><?php echo wp_kses_post( $product_price ); ?></p>
                                    <div class="flex items-center justify-between text-[11px]">
                                        <button class="kt-add-to-cart font-semibold text-kt-primary hover:text-[#ff3b5b]" data-product-id="<?php echo esc_attr( $product_id ); ?>">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <?php
                    }
                }
                ?>
            </div>
          </div>

          <button id="kt-next" class="ml-3 inline-flex h-9 items-center justify-center rounded-xl bg-kt-primary px-3 text-xs font-semibold tracking-[.16em] text-white shadow-lg shadow-kt-primary/40 hover:bg-[#ff3b5b]">NEXT<svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5l7 7-7 7"/></svg></button>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
/* Hero JS: slides, sidebar and sticky header behaviour (from static prototype) */
(function () {
  const slides  = Array.from(document.querySelectorAll('[data-kt-slide]'));
  const tabs    = Array.from(document.querySelectorAll('[data-kt-tab]'));
  const rows    = Array.from(document.querySelectorAll('.kt-products-row'));
  const nextBtn = document.getElementById('kt-next');

  const stickyHeader = document.getElementById('kt-sticky-header');

  let current   = 0;
  let timer     = null;
  const AUTO_MS = 9000;

  function setActive(index){
    if(index === current) return;
    if(index < 0) index = slides.length - 1;
    if(index >= slides.length) index = 0;

    slides[current].classList.remove('kt-slide-active');
    slides[index].classList.add('kt-slide-active');

    tabs[current]?.classList.remove('kt-tab-active');
    tabs[index]?.classList.add('kt-tab-active');

    rows[current]?.classList.remove('kt-products-row-active');
    rows[index]?.classList.add('kt-products-row-active');

    current = index;
  }

  function goNext(){ setActive(current + 1); }

  function restartAuto(){ if(timer) clearInterval(timer); timer = setInterval(goNext, AUTO_MS); }

  tabs.forEach((tab, idx) => { tab.addEventListener('click', () => { setActive(idx); restartAuto(); }); });
  nextBtn?.addEventListener('click', () => { goNext(); restartAuto(); });

  let lastY = window.scrollY;
  window.addEventListener('scroll', () => {
    const y = window.scrollY;
    if (y > 140 && y < lastY) {
      stickyHeader.classList.remove('-translate-y-full');
      stickyHeader.classList.add('translate-y-0');
    } else if (y < 100 || y > lastY) {
      stickyHeader.classList.add('-translate-y-full');
      stickyHeader.classList.remove('translate-y-0');
    }
    lastY = y;
  });

  rows[0]?.classList.add('kt-products-row-active');
  restartAuto();

  // AJAX Add to Cart Handler
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('kt-add-to-cart')) {
      const btn = e.target;
      const productId = btn.getAttribute('data-product-id');
      
      if (!productId) return;

      btn.disabled = true;
      const originalText = btn.textContent;
      btn.textContent = 'Adding...';

      // Use jQuery if available, otherwise use fetch
      if (typeof jQuery !== 'undefined' && typeof wc_add_to_cart_params !== 'undefined') {
        jQuery.post(
          wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
          {
            product_id: productId,
            quantity: 1,
            security: wc_add_to_cart_params.security
          },
          function(response) {
            btn.textContent = '✓ Added';
            jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, btn]);
            
            setTimeout(() => {
              btn.textContent = originalText;
              btn.disabled = false;
            }, 1500);
          }
        );
      } else {
        // Fallback: Direct add to cart via standard WooCommerce method
        const form = new FormData();
        form.append('action', 'woocommerce_add_to_cart');
        form.append('product_id', productId);
        form.append('quantity', 1);
        form.append('nonce', document.querySelector('input[name="woocommerce-add-to-cart-nonce"]')?.value || '');

        fetch('/wp-admin/admin-ajax.php', {
          method: 'POST',
          body: form,
          credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
          btn.textContent = '✓ Added';
          setTimeout(() => {
            btn.textContent = originalText;
            btn.disabled = false;
          }, 1500);
          // Reload to refresh cart
          setTimeout(() => location.reload(), 1600);
        })
        .catch(err => {
          console.error('Add to cart error:', err);
          btn.textContent = 'Error';
          setTimeout(() => {
            btn.textContent = originalText;
            btn.disabled = false;
          }, 1500);
        });
      }
    }
  });
})();
</script>

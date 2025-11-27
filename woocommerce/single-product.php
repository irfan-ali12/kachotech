<?php
/**
 * KachoTech – Custom Single Product Template (no Tailwind)
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$product = wc_get_product( get_the_ID() );

if ( ! $product ) {
    echo '<p>Product not found.</p>';
    get_footer( 'shop' );
    return;
}

$rating       = $product->get_average_rating();
$review_count = $product->get_review_count();

// ----------------- GALLERY DATA + FALLBACKS -----------------

$all_image_ids = array();

// 1) Main (thumbnail) image if set
$main_id = $product->get_image_id();
if ( $main_id ) {
    $all_image_ids[] = $main_id;
}

// 2) Gallery images (unique, no duplicates)
$gallery_ids = $product->get_gallery_image_ids();
if ( ! empty( $gallery_ids ) ) {
    foreach ( $gallery_ids as $gid ) {
        if ( $gid && $gid !== $main_id ) {
            $all_image_ids[] = $gid;
        }
    }
}

// 3) Fallbacks - if no images, we'll use placeholder URL directly
// Note: wc_get_placeholder_image_id() may not exist in all WooCommerce versions
// So we handle it gracefully below

// Build URLs array for JS + markup
$image_urls = array();
foreach ( $all_image_ids as $img_id ) {
    $src = wp_get_attachment_image_url( $img_id, 'large' );
    if ( $src ) {
        $image_urls[] = $src;
    }
}

// Make sure we have *at least* a placeholder URL even if IDs didn't resolve
if ( empty( $image_urls ) ) {
    $image_urls[] = wc_placeholder_img_src( 'large' );
}

$first_src = $image_urls[0];
?>

<style>
  /* KachoTech – Single Product Page (no Tailwind) */

  /* General layout */
  .kt-product-page {
    font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    background: #f8fafc; /* slate-50 */
    padding: 32px 0 48px;
  }

  /* Breadcrumb */
  .kt-product-page .kt-breadcrumb {
    max-width: 1200px;
    margin: 0 auto 12px;
    font-size: 12px;
    color: #6b7280; /* slate-500 */
  }

  .kt-product-page .kt-breadcrumb a {
    color: #6b7280;
    text-decoration: none;
  }

  .kt-product-page .kt-breadcrumb a:hover {
    color: #ff2446; /* kt primary */
  }

  /* Main grid */
  .kt-product-page .kt-product-grid {
    max-width: 1200px;
    margin: 0 auto 32px;
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.1fr);
    column-gap: 40px;
    row-gap: 32px;
  }

  /* LEFT – GALLERY */
  .kt-product-page .kt-gallery {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  /* Main image card */
  .kt-product-page .kt-main-image {
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
    height: 420px;              /* fixed height */
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px;
  }

  .kt-product-page .kt-main-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    transition: opacity 0.25s ease, transform 0.25s ease;
  }

  /* fade helpers */
  .kt-product-page .kt-main-image img.kt-fade-out {
    opacity: 0;
    transform: scale(0.98);
  }
  .kt-product-page .kt-main-image img.kt-fade-in {
    opacity: 1;
    transform: scale(1);
  }




  /* Thumbnails row */
  .kt-product-page .kt-thumbnails {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  /* Thumbnail nav arrows */

  .kt-product-page .kt-thumb-nav {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: #ffffff;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    cursor: pointer;
    font-size: 16px; /* nicer chevron size */
  }

  .kt-product-page .kt-thumb-nav span {
    display: block;
    line-height: 1;
  }

  .kt-product-page .kt-thumb-nav:hover {
    color: #ff2446;
  }

  .kt-product-page .kt-thumb-nav.disabled {
    opacity: 0.35;
    cursor: default;
    pointer-events: none;
  }


  

  /* Thumb strip */

  .kt-product-page .kt-thumb-track {
    flex: 1;
    display: flex;
    gap: 12px;
    overflow: hidden;
  }

  .kt-product-page .kt-thumb {
    flex: 0 0 96px;           /* fixed width slot */
    height: 96px;
    background: #ffffff;
    border-radius: 14px;
    padding: 8px;
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
  }

  .kt-product-page .kt-thumb img {
    width: 80px;
    height: 80px;
    object-fit: contain;
  }

  .kt-product-page .kt-thumb:hover {
    border-color: #ff2446;
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
  }

  .kt-product-page .kt-thumb.active {
    border-width: 2px;
    border-color: #ff2446;
  }

  /* hide thumbs outside window */
  .kt-product-page .kt-thumb.kt-thumb-hidden {
    display: none;
  }

  /* RIGHT – DETAILS */
  .kt-product-page .kt-details {
    display: flex;
    flex-direction: column;
  }

  /* Title */
  .kt-product-page .kt-title {
    font-size: 26px;
    line-height: 1.25;
    font-weight: 600;
    margin: 0 0 8px;
    color: #0f172a; /* slate-900 */
  }

  /* Rating line */
  .kt-product-page .kt-rating-line {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 6px;
  }

  /* Woo star rating inside kt-stars */
  .kt-product-page .kt-stars .star-rating {
    display: inline-block;
    font-size: 14px;
    color: #fbbf24; /* amber-400 */
    margin-right: 4px;
  }

  .kt-product-page .kt-stars .star-rating span::before {
    /* keep Woo’s star characters but ensure color */
    color: #fbbf24;
  }

  .kt-product-page .kt-reviews {
    font-size: 12px;
  }

  .kt-product-page .kt-divider {
    color: #d1d5db; /* slate-300 */
    margin: 0 4px;
  }

  .kt-product-page .kt-sku {
    font-size: 12px;
  }

  /* Stock badge */
  .kt-product-page .kt-stock {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 999px;
    border: 1px solid;
    font-weight: 500;
  }

  .kt-product-page .kt-stock.in {
    color: #059669;
    background: #ecfdf5;
    border-color: #6ee7b7;
  }

  .kt-product-page .kt-stock.out {
    color: #b91c1c;
    background: #fef2f2;
    border-color: #fecaca;
  }

  /* Price row */
  .kt-product-page .kt-price-row {
    display: flex;
    align-items: baseline;
    gap: 12px;
    margin: 10px 0 4px;
  }

  .kt-product-page .kt-price {
    font-size: 30px;
    line-height: 1.1;
    font-weight: 600;
    color: #ff2446;
  }

  /* Woo puts sale/regular inside .price; we keep that red via above and adjust strike-through inside */
  .kt-product-page .kt-price .woocommerce-Price-amount {
    font-size: inherit;
  }

  /* Category line */
  .kt-product-page .kt-categories {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.18em;
    color: #6b7280;
    margin: 4px 0 10px;
  }

  .kt-product-page .kt-categories a {
    color: inherit;
    text-decoration: none;
  }

  /* Short description */
  .kt-product-page .kt-short-desc {
    font-size: 14px;
    color: #4b5563;
    max-width: 520px;
    margin-bottom: 14px;
  }

  .kt-product-page .kt-short-desc p {
    margin: 0 0 4px;
  }

  /* Buy row */
  .kt-product-page .kt-buy-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
  }

  /* Qty box */
  .kt-product-page .kt-qty-box {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    border: 1px solid #d1d5db;
    overflow: hidden;
    background: #ffffff;
    height: 44px;
  }

  .kt-product-page .kt-qty-box button {
    width: 32px;
    height: 44px;
    border: none;
    background: transparent;
    color: #6b7280;
    font-size: 18px;
    cursor: pointer;
  }

  .kt-product-page .kt-qty-box button:hover {
    background: #f3f4f6;
  }

  .kt-product-page .kt-qty-box input {
    width: 50px;
    height: 44px;
    text-align: center;
    border: 0;
    border-left: 1px solid #e5e7eb;
    border-right: 1px solid #e5e7eb;
    font-size: 13px;
  }

  /* Forms */
  .kt-product-page .kt-cart-form {
    display: inline-block;
  }

  /* Add to cart button (green pill) */
  .kt-product-page .kt-add-cart {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 44px;
    padding: 0 34px;
    border-radius: 999px;
    border: none;
    cursor: pointer;
    background: #22c55e;
    color: #ffffff;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 10px 20px rgba(34, 197, 94, 0.35);
    transition: background 0.18s ease, box-shadow 0.18s ease, transform 0.1s ease;
  }

  .kt-product-page .kt-add-cart:hover {
    background: #16a34a;
    box-shadow: 0 12px 24px rgba(22, 163, 74, 0.35);
    transform: translateY(-1px);
  }

  /* Buy now button (black pill) */
  .kt-product-page .kt-buy-now {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 44px;
    padding: 0 34px;
    border-radius: 999px;
    background: #020617;
    color: #ffffff;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.18s ease, transform 0.1s ease;
  }

  .kt-product-page .kt-buy-now:hover {
    background: #000000;
    transform: translateY(-1px);
  }

  /* Stock count text */
  .kt-product-page .kt-stock-count {
    font-size: 11px;
    color: #059669;
    margin-bottom: 16px;
  }

  /* Specifications section */
  .kt-product-page .kt-specs-section {
    border-top: 2px solid #e5e7eb;
    padding-top: 14px;
    margin-top: 6px;
    margin-bottom: 12px;
  }

  .kt-product-page .kt-specs-section h3 {
    font-size: 14px;
    font-weight: 600;
    margin: 0 0 8px;
  }

  .kt-product-page .kt-specs-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    column-gap: 40px;
    row-gap: 4px;
    font-size: 13px;
  }

  .kt-product-page .kt-spec-row {
    display: flex;
    justify-content: space-between;
  }

  .kt-product-page .kt-spec-name {
    font-weight: 500;
    color: #111827;
  }

  .kt-product-page .kt-spec-value {
    color: #6b7280;
  }

  /* Payment methods */
  .kt-product-page .kt-payment-methods {
    border-top: 2px solid #e5e7eb;
    padding-top: 16px;
    margin-top: 8px;
  }

  .kt-product-page .kt-payment-methods h4 {
    font-size: 11px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: #6b7280;
    margin: 0 0 6px;
  }

  .kt-product-page .kt-payment-methods img {
    max-width: 260px;
    width: 100%;
    height: auto;
  }

  /* TABS */
  .kt-product-page .kt-tabs {
    max-width: 1200px;
    margin: 0 auto 40px;
    font-size: 14px;
    color: #374151;
  }

  /* Tab buttons row */
  .kt-product-page .kt-tab-buttons {
    display: flex;
    gap: 24px;
    border-bottom: 1px solid #e5e7eb;
  }

  .kt-product-page .kt-tab-buttons button {
    padding: 12px 0;
    margin: 0;
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 13px;
    color: #6b7280;
    border-bottom: 2px solid transparent;
    transition: color 0.18s ease, border-color 0.18s ease;
  }

  .kt-product-page .kt-tab-buttons button.active {
    color: #ff2446;
    border-color: #ff2446;
    font-weight: 500;
    border-radius:0px;
  }

  /* Tab content */
  .kt-product-page .kt-tab-content {
    display: none;
    padding-top: 16px;
    line-height: 1.7;
  }

  .kt-product-page .kt-tab-content.active {
    display: block;
  }

  .kt-product-page .kt-tab-content p {
    margin: 0 0 10px;
  }

  /* Lists in description */
  .kt-product-page .kt-tab-content ul {
    margin: 0 0 10px 18px;
    padding: 0;
  }

  .kt-product-page .kt-tab-content li {
    margin-bottom: 4px;
  }

  /* Related products - now using custom template with shop card design */
  .kt-product-page .kt-related {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 16px;
  }

  .kt-product-page .kt-related .kt-related-wrapper {
    margin-top: 48px;
  }

  /* Hide default WooCommerce related products markup if any */
  .kt-product-page .kt-related ul.products,
  .kt-product-page .kt-related > .products {
    display: none !important;
  }

  /* RESPONSIVE */
  @media (max-width: 992px) {
    .kt-product-page {
      padding-top: 24px;
    }

    .kt-product-page .kt-product-grid {
      grid-template-columns: minmax(0, 1fr);
    }

    .kt-product-page .kt-product-grid {
      row-gap: 24px;
    }

    .kt-product-page .kt-main-image {
      min-height: 320px;
    }

    .kt-product-page .kt-buy-row {
      align-items: stretch;
    }

    .kt-product-page .kt-add-cart,
    .kt-product-page .kt-buy-now {
      flex: 1;
      text-align: center;
    }
  }

  @media (max-width: 640px) {
    .kt-product-page .kt-breadcrumb {
      padding: 0 16px;
    }
    .kt-product-page .kt-product-grid,
    .kt-product-page .kt-tabs,
    .kt-product-page .kt-related {
      padding: 0 16px;
    }

    .kt-product-page .kt-title {
      font-size: 22px;
    }

    .kt-product-page .kt-specs-grid {
      grid-template-columns: 1fr;
    }

    .kt-product-page .kt-thumb-track {
      gap: 8px;
    }

    .kt-product-page .kt-thumb img {
      height: 70px;
    }
  }


/* Ensure 4 items fill the row nicely */
.kt-product-page .kt-thumb-track {
  display: flex;
  gap: 12px;
}

.kt-product-page .kt-thumb {
  flex: 1 1 0;
}

/* Remove browser spin buttons from qty input */
.kt-product-page .kt-qty-box input[type=number]::-webkit-inner-spin-button,
.kt-product-page .kt-qty-box input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.kt-product-page .kt-qty-box input[type=number] {
  -moz-appearance: textfield;
}


</style>

<div class="kt-product-page">

    <!-- Breadcrumb -->
    <div class="kt-breadcrumb">
        <?php woocommerce_breadcrumb(); ?>
    </div>

    <!-- MAIN GRID -->
    <div class="kt-product-grid">

        <!-- LEFT: IMAGE GALLERY -->
        <div class="kt-gallery">

            <div class="kt-main-image">
                <img id="kt-main-img" src="<?php echo esc_url( $first_src ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
            </div>

            <div class="kt-thumbnails">
                <?php
                $thumb_count = count( $image_urls );
                $show_nav    = $thumb_count > 4;
                ?>

                <!-- Previous arrow -->
                <?php if ( $show_nav ) : ?>
                    <button class="kt-thumb-nav kt-thumb-prev disabled" id="kt-thumb-prev">
                        <span>&lsaquo;</span>
                    </button>
                <?php endif; ?>

                <div class="kt-thumb-track" id="kt-thumb-track">
                    <?php
                    $index = 0;
                    foreach ( $image_urls as $src ) :
                        ?>
                        <div class="kt-thumb <?php echo $index === 0 ? 'active' : ''; ?>"
                             data-index="<?php echo esc_attr( $index ); ?>">
                            <img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?> thumbnail <?php echo esc_attr( $index + 1 ); ?>" />
                        </div>
                        <?php
                        $index++;
                    endforeach;
                    ?>
                </div>

                <!-- Next arrow -->
                <?php if ( $show_nav ) : ?>
                    <button class="kt-thumb-nav kt-thumb-next" id="kt-thumb-next">
                        <span>&rsaquo;</span>
                    </button>
                <?php endif; ?>
            </div>

        </div>

        <!-- RIGHT: PRODUCT DETAILS -->
        <div class="kt-details">

            <h1 class="kt-title"><?php the_title(); ?></h1>

            <div class="kt-rating-line">
                <div class="kt-stars">
                    <?php echo wc_get_rating_html( $rating ); ?>
                </div>
                <?php if ( $review_count ) : ?>
                    <span class="kt-reviews">(<?php echo esc_html( $review_count ); ?> reviews)</span>
                <?php endif; ?>
                <span class="kt-divider">|</span>
                <span class="kt-sku">SKU: <?php echo esc_html( $product->get_sku() ); ?></span>
                <span class="kt-divider">|</span>
                <span class="kt-stock <?php echo $product->is_in_stock() ? 'in' : 'out'; ?>">
                    <?php echo $product->is_in_stock() ? 'In Stock' : 'Out of Stock'; ?>
                </span>
            </div>

            <div class="kt-price-row">
                <span class="kt-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
            </div>

            <div class="kt-categories">
                <?php echo wp_kses_post( wc_get_product_category_list( $product->get_id(), ' • ' ) ); ?>
            </div>

            <div class="kt-short-desc">
                <?php echo wpautop( $product->get_short_description() ); ?>
            </div>

            <div class="kt-buy-row">

                <!-- QTY -->
                <div class="kt-qty-box">
                    <button class="minus">−</button>
                    <input id="kt-qty" type="number" min="1" value="1" />
                    <button class="plus">+</button>
                </div>

                <!-- ADD TO CART -->
                <form method="post" class="kt-cart-form">
                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">
                    <input id="kt-qty-hidden" type="hidden" name="quantity" value="1">
                    <button type="submit" class="kt-add-cart">Add to Cart</button>
                </form>

                <!-- BUY NOW -->
                <a href="<?php echo esc_url( wc_get_checkout_url() . '?add-to-cart=' . $product->get_id() ); ?>"
                   class="kt-buy-now">
                    Buy Now
                </a>

            </div>

            <div class="kt-stock-count">
                Available: <?php echo esc_html( $product->get_stock_quantity() ); ?> pcs
            </div>

            <!-- SPECIFICATIONS -->
            <div class="kt-specs-section">
                <h3>Specifications</h3>
                <div class="kt-specs-grid">
                    <?php
                    foreach ( $product->get_attributes() as $attribute ) :
                        if ( $attribute->get_visible() ) :
                            $label = wc_attribute_label( $attribute->get_name() );
                            $terms = wc_get_product_terms(
                                $product->get_id(),
                                $attribute->get_name(),
                                array( 'fields' => 'names' )
                            );
                            ?>
                            <div class="kt-spec-row">
                                <span class="kt-spec-name"><?php echo esc_html( $label ); ?></span>
                                <span class="kt-spec-value"><?php echo esc_html( implode( ', ', $terms ) ); ?></span>
                            </div>
                        <?php endif; endforeach; ?>
                </div>
            </div>

            <!-- PAYMENT METHODS -->
            <div class="kt-payment-methods">
                <h4>PAYMENT METHODS</h4>
                <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/payment-methods.png' ); ?>" alt="Accepted payment methods" />
            </div>

        </div>

    </div>

    <!-- TABS -->
    <div class="kt-tabs">
        <div class="kt-tab-buttons">
            <button data-tab="desc" class="active">Product Description</button>
            <button data-tab="spec">Specification</button>
            <button data-tab="rev">Reviews (<?php echo esc_html( $review_count ); ?>)</button>
        </div>

        <div class="kt-tab-content active" id="kt-tab-desc">
            <?php echo wpautop( $product->get_description() ); ?>
        </div>

        <div class="kt-tab-content" id="kt-tab-spec">
            <div class="kt-specs-grid">
                <?php foreach ( $product->get_attributes() as $attribute ) :
                    if ( $attribute->get_visible() ) :
                        $label = wc_attribute_label( $attribute->get_name() );
                        $terms = wc_get_product_terms(
                            $product->get_id(),
                            $attribute->get_name(),
                            array( 'fields' => 'names' )
                        );
                        ?>
                        <div class="kt-spec-row">
                            <span class="kt-spec-name"><?php echo esc_html( $label ); ?></span>
                            <span class="kt-spec-value"><?php echo esc_html( implode( ', ', $terms ) ); ?></span>
                        </div>
                    <?php endif; endforeach; ?>
            </div>
        </div>

        <div class="kt-tab-content" id="kt-tab-rev">
            <?php comments_template(); ?>
        </div>
    </div>

    <!-- RELATED -->
    <div class="kt-related">
        <?php woocommerce_output_related_products(); ?>
    </div>

</div>

<script>
// ---------- QTY ----------
(function() {
  const plusBtn = document.querySelector('.kt-qty-box .plus');
  const minusBtn = document.querySelector('.kt-qty-box .minus');
  const qtyInput = document.getElementById('kt-qty');
  const qtyHidden = document.getElementById('kt-qty-hidden');

  function syncHidden() {
    if (!qtyInput || !qtyHidden) return;
    let v = parseInt(qtyInput.value || '1', 10);
    if (isNaN(v) || v < 1) v = 1;
    qtyInput.value = v;
    qtyHidden.value = v;
  }

  if (plusBtn) {
    plusBtn.addEventListener('click', function() {
      let v = parseInt(qtyInput.value || '1', 10);
      if (isNaN(v)) v = 1;
      qtyInput.value = v + 1;
      syncHidden();
    });
  }

  if (minusBtn) {
    minusBtn.addEventListener('click', function() {
      let v = parseInt(qtyInput.value || '1', 10);
      if (isNaN(v) || v <= 1) v = 1;
      else v = v - 1;
      qtyInput.value = v;
      syncHidden();
    });
  }

  if (qtyInput) {
    qtyInput.addEventListener('change', syncHidden);
  }
})();

// ---------- GALLERY CAROUSEL ----------
(function() {
  const ktImages = <?php echo wp_json_encode( $image_urls ); ?>;
  const mainImg = document.getElementById('kt-main-img');
  const thumbs = Array.prototype.slice.call(document.querySelectorAll('.kt-thumb'));
  const prevBtn = document.getElementById('kt-thumb-prev');
  const nextBtn = document.getElementById('kt-thumb-next');

  if (!ktImages.length || !mainImg || !thumbs.length) return;

  let currentIndex = 0;          // active image index
  let windowStart = 0;           // first visible thumb index
  const visibleCount = 4;        // number of thumbs on desktop
  const total = thumbs.length;

  function fadeTo(src) {
    mainImg.classList.remove('kt-fade-in', 'kt-fade-out');
    // start fade-out
    mainImg.classList.add('kt-fade-out');
    setTimeout(function() {
      mainImg.src = src;
      mainImg.classList.remove('kt-fade-out');
      mainImg.classList.add('kt-fade-in');
      setTimeout(function() {
        mainImg.classList.remove('kt-fade-in');
      }, 250);
    }, 150);
  }

  function updateMain(index) {
    currentIndex = index;
    fadeTo(ktImages[index]);
    thumbs.forEach(function(t, i) {
      if (i === index) {
        t.classList.add('active');
      } else {
        t.classList.remove('active');
      }
    });
  }

  function ensureVisible() {
    // keep active index inside [windowStart, windowStart+visibleCount-1]
    if (currentIndex < windowStart) {
      windowStart = currentIndex;
    } else if (currentIndex >= windowStart + visibleCount) {
      windowStart = currentIndex - visibleCount + 1;
    }

    // normalize windowStart into [0, total-visibleCount] if total > visibleCount
    if (total <= visibleCount) {
      windowStart = 0;
    } else {
      if (windowStart < 0) windowStart = 0;
      if (windowStart > total - visibleCount) windowStart = total - visibleCount;
    }
  }

  function updateThumbWindow() {
    thumbs.forEach(function(t, i) {
      if (i >= windowStart && i < windowStart + visibleCount) {
        t.classList.remove('kt-thumb-hidden');
      } else {
        t.classList.add('kt-thumb-hidden');
      }
    });

    if (prevBtn) {
      if (total <= visibleCount) {
        prevBtn.classList.add('disabled');
      } else {
        prevBtn.classList.remove('disabled');
      }
    }
    if (nextBtn) {
      if (total <= visibleCount) {
        nextBtn.classList.add('disabled');
      } else {
        nextBtn.classList.remove('disabled');
      }
    }
  }

  // Thumb click
  thumbs.forEach(function(t) {
    t.addEventListener('click', function() {
      const idx = parseInt(t.getAttribute('data-index'), 10);
      if (!isNaN(idx)) {
        updateMain(idx);
        ensureVisible();
        updateThumbWindow();
      }
    });
  });

  // Prev/next buttons – FULL LOOP
  if (prevBtn) {
    prevBtn.addEventListener('click', function() {
      // loop backwards
      currentIndex = (currentIndex - 1 + total) % total;
      ensureVisible();
      updateMain(currentIndex);
      updateThumbWindow();
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      // loop forwards
      currentIndex = (currentIndex + 1) % total;
      ensureVisible();
      updateMain(currentIndex);
      updateThumbWindow();
    });
  }

  // Initial state
  updateMain(0);
  ensureVisible();
  updateThumbWindow();
})();

// ---------- TABS ----------
(function() {
  const btns = Array.prototype.slice.call(document.querySelectorAll('.kt-tab-buttons button'));
  const contents = {
    desc: document.getElementById('kt-tab-desc'),
    spec: document.getElementById('kt-tab-spec'),
    rev: document.getElementById('kt-tab-rev')
  };

  btns.forEach(function(btn) {
    btn.addEventListener('click', function() {
      const tab = btn.getAttribute('data-tab');
      if (!tab || !contents[tab]) return;

      // buttons
      btns.forEach(function(b) { b.classList.remove('active'); });
      btn.classList.add('active');

      // panels
      Object.keys(contents).forEach(function(key) {
        if (contents[key]) {
          contents[key].classList.toggle('active', key === tab);
        }
      });
    });
  });
})();
</script>

<?php
get_footer( 'shop' );

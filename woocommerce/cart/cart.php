<?php
/**
 * KachoTech – Modern Cart Layout
 */

defined( 'ABSPATH' ) || exit;
?>

<style>
/* Hide Astra/Woo default page title */
body.woocommerce-cart .entry-title {
	display: none;
}

/* ---------- Cart page layout ---------- */
.kt-cart-page {
	max-width: 1160px;
	margin: 0 auto;
	padding: 32px 16px 48px;
	font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
	color: #0f172a;
	box-sizing: border-box;
}

/* Header: title + breadcrumb, left aligned */
.kt-cart-header {
	margin-bottom: 20px;
}
.kt-cart-header h1 {
	font-size: 28px;
	font-weight: 600;
	letter-spacing: -0.02em;
	margin: 0 0 6px;
}
.kt-cart-header .kt-cart-breadcrumb {
	font-size: 13px;
	color: #9ca3af;
}
.kt-cart-header .kt-cart-breadcrumb a {
	color: #9ca3af;
	text-decoration: none;
}
.kt-cart-header .kt-cart-breadcrumb a:hover {
	color: #ff2446;
}

/* Notices should sit under header and above layout */
.woocommerce-notices-wrapper {
	margin-bottom: 16px;
}

/* 2-column grid: items (left) + summary (right) */
.kt-cart-layout {
	display: grid;
	grid-template-columns: minmax(0, 1.7fr) minmax(260px, 0.9fr);
	gap: 32px;
}

/* ---------- Table styling (left side) ---------- */

.woocommerce-cart-form .shop_table {
	width: 100%;
	border-collapse: collapse;
	background: #ffffff;
	border-radius: 16px;
	box-shadow: 0 8px 24px rgba(15,23,42,0.06);
	overflow: hidden;
	margin: 0;
}

.woocommerce-cart-form .shop_table thead {
	background: #f9fafb;
}
.woocommerce-cart-form .shop_table thead th {
	font-size: 12px;
	font-weight: 500;
	text-transform: uppercase;
	letter-spacing: 0.12em;
	padding: 12px 16px;
	border: none;
	color: #6b7280;
}
.woocommerce-cart-form .shop_table thead th.product-name { text-align: left; }
.woocommerce-cart-form .shop_table thead th.product-price { text-align: center; }
.woocommerce-cart-form .shop_table thead th.product-quantity { text-align: center; }
.woocommerce-cart-form .shop_table thead th.product-subtotal { text-align: right; }

.woocommerce-cart-form .shop_table tbody tr {
	border-top: 1px solid #e5e7eb;
	transition: background-color .15s ease;
}
.woocommerce-cart-form .shop_table tbody tr:hover {
	background-color: #f9fafb;
}
.woocommerce-cart-form .shop_table td {
	padding: 14px 16px;
	border: none;
	vertical-align: middle;
	font-size: 14px;
}

/* Remove column */
.woocommerce-cart-form .product-remove {
	width: 32px;
	text-align: center;
}
.woocommerce-cart-form .product-remove .remove {
	color: #9ca3af;
	text-decoration: none;
	font-size: 20px;
	line-height: 1;
	transition: color .15s;
}
.woocommerce-cart-form .product-remove .remove:hover {
	color: #ef4444;
}

/* Thumbnail */
.woocommerce-cart-form .product-thumbnail {
	width: 80px;
	text-align: center;
}
.woocommerce-cart-form .product-thumbnail img {
	max-width: 64px;
	height: auto;
	border-radius: 10px;
}

/* Product name */
.woocommerce-cart-form .product-name {
	text-align: left;
	font-weight: 500;
}
.woocommerce-cart-form .product-name a {
	color: #111827;
	text-decoration: none;
}
.woocommerce-cart-form .product-name a:hover {
	color: #ff2446;
}

/* Price & subtotal */
.woocommerce-cart-form .product-price {
	text-align: center;
	color: #111827;
	font-weight: 500;
}
.woocommerce-cart-form .product-subtotal {
	text-align: right;
	color: #111827;
	font-weight: 600;
}

/* ---------- Quantity with +/- buttons ---------- */

.woocommerce-cart-form .product-quantity {
	text-align: center;
}

/* Outer pill */
.kt-qty-control {
	display: inline-flex;
	align-items: center;
	border-radius: 999px;
	border: 1px solid #d1d5db;
	height: 30px;
	background: #ffffff;
	overflow: hidden;
}

/* Plus / minus buttons */
.kt-qty-control button.kt-qty-btn {
	width: 26px;
	height: 100%;
	border: none;
	background: #f3f4f6;
	cursor: pointer;
	font-size: 16px;
	color: #6b7280;
	line-height: 1;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 0;
}
.kt-qty-control button.kt-qty-btn:hover {
	background: #e5e7eb;
}

/* Inner Woo quantity wrapper */
.kt-qty-control .quantity {
	border: none;
	height: 100%;
	display: flex !important;
	align-items: center;
	padding: 0 2px;
}

/* Number input */
.kt-qty-control .quantity input.qty {
	width: 46px !important;
	height: 100% !important;
	border: none !important;
	text-align: center;
	font-size: 14px;
	background: transparent;
	padding: 0 !important;
}

/* Remove browser arrows */
.kt-qty-control .quantity input[type=number]::-webkit-inner-spin-button,
.kt-qty-control .quantity input[type=number]::-webkit-outer-spin-button{
	-webkit-appearance:none;
	margin:0;
}
.kt-qty-control .quantity input[type=number]{-moz-appearance:textfield;}
.kt-qty-control .quantity input.qty:focus {
	outline: none !important;
	box-shadow: none !important;
}

/* ---------- Actions row (only Update cart) ---------- */

.woocommerce-cart-form .actions {
	margin-top: 10px;
	display: flex;
	justify-content: flex-end;
}
.woocommerce-cart-form .actions .button {
	padding: 8px 18px;
	border-radius: 999px;
	background: #ff2446;
	color: #ffffff;
	border: none;
	font-size: 13px;
	font-weight: 500;
	cursor: pointer;
	text-transform: none;
}
.woocommerce-cart-form .actions .button:hover {
	background: #e0203b;
}

/* ---------- Summary column (right) ---------- */

.kt-cart-summary {
	align-self: flex-start;
}

/* Coupon above totals */
.kt-cart-summary .kt-coupon-box {
	background: #ffffff;
	border-radius: 16px;
	border: 1px solid #e5e7eb;
	padding: 14px 16px;
	box-shadow: 0 6px 18px rgba(15,23,42,0.05);
	margin-bottom: 14px;
}
.kt-cart-summary .kt-coupon-box label {
	display: block;
	font-size: 13px;
	font-weight: 500;
	margin-bottom: 6px;
}
.kt-cart-summary .kt-coupon-row {
	display: flex;
	gap: 8px;
}
.kt-cart-summary .kt-coupon-row input[type="text"] {
	flex: 1;
	padding: 8px 12px;
	border-radius: 8px;
	border: 1px solid #d1d5db;
	font-size: 14px;
}
.kt-cart-summary .kt-coupon-row input[type="text"]:focus {
	outline: none;
	border-color: #ff2446;
	box-shadow: 0 0 0 2px rgba(255,36,70,.15);
}
.kt-cart-summary .kt-coupon-row .button {
	padding: 8px 16px;
	border-radius: 999px;
	background: #0f172a;
	color: #ffffff;
	border: none;
	font-size: 13px;
	font-weight: 500;
	cursor: pointer;
}
.kt-cart-summary .kt-coupon-row .button:hover {
	background: #ff2446;
}

/* Totals card */
.kt-cart-summary .cart_totals {
	background: #ffffff;
	border-radius: 16px;
	border: 1px solid #e5e7eb;
	padding: 18px 20px 14px;
	box-shadow: 0 8px 24px rgba(15,23,42,0.05);
	margin: 0 !important;
}
.kt-cart-summary .cart_totals h2 {
	font-size: 16px;
	font-weight: 600;
	margin: 0 0 12px !important;
	padding-bottom: 8px !important;
	border-bottom: 1px solid #e5e7eb;
}

.kt-cart-summary .cart_totals table {
	width: 100%;
	margin-bottom: 8px;
}
.kt-cart-summary .cart_totals th,
.kt-cart-summary .cart_totals td {
	border: none;
	padding: 6px 0;
	font-size: 14px;
}
.kt-cart-summary .cart_totals th {
	text-align: left;
	color: #6b7280;
	font-weight: 500;
}
.kt-cart-summary .cart_totals td {
	text-align: right;
	color: #111827;
	font-weight: 500;
}
.kt-cart-summary .cart_totals tr.order-total th,
.kt-cart-summary .cart_totals tr.order-total td {
	padding-top: 10px;
	border-top: 1px solid #e5e7eb;
}
.kt-cart-summary .cart_totals tr.order-total td {
	color: #ff2446;
}

/* Proceed to checkout button – dark pill */
.kt-cart-summary .wc-proceed-to-checkout {
	margin-top: 8px !important;
}
.kt-cart-summary .wc-proceed-to-checkout .checkout-button {
	display: block;
	width: 100%;
	padding: 10px 18px !important;
	border-radius: 999px;
	background: #111827 !important;
	color: #ffffff !important;
	font-size: 14px;
	font-weight: 500;
	text-align: center;
	border: none !important;
	text-decoration: none !important;
}
.kt-cart-summary .wc-proceed-to-checkout .checkout-button:hover {
	background: #000000 !important;
}

/* Cross-sells under layout */
.kt-cart-cross {
	margin-top: 32px;
}
.kt-cart-cross .cross-sells {
	background: #ffffff;
	border-radius: 16px;
	border: 1px solid #e5e7eb;
	padding: 18px 20px;
	box-shadow: 0 8px 24px rgba(15,23,42,0.04);
}
.kt-cart-cross .cross-sells h2 {
	font-size: 16px;
	font-weight: 600;
	margin-bottom: 12px;
}

/* ---------- Responsive ---------- */
@media (max-width: 880px) {
	.kt-cart-layout {
		grid-template-columns: 1fr;
		gap: 24px;
	}
}

@media (max-width: 768px) {
	.kt-cart-page {
		padding: 24px 12px 40px;
	}

	.woocommerce-cart-form .shop_table thead {
		display: none;
	}

	.woocommerce-cart-form .shop_table tbody tr {
		display: block;
		margin: 0 0 12px;
		border-radius: 12px;
		border: 1px solid #e5e7eb;
		overflow: hidden;
	}

	.woocommerce-cart-form .shop_table td {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 10px 12px;
		border-bottom: 1px solid #f3f4f6;
		text-align: right;
	}
	.woocommerce-cart-form .shop_table td:before {
		content: attr(data-title);
		font-weight: 500;
		color: #4b5563;
		margin-right: 8px;
	}

	.woocommerce-cart-form .product-remove,
	.woocommerce-cart-form .product-thumbnail {
		justify-content: flex-end;
	}
	.woocommerce-cart-form .product-thumbnail img {
		max-width: 56px;
	}

	.kt-cart-summary .kt-coupon-row {
		flex-direction: column;
	}
	.kt-cart-summary .kt-coupon-row .button {
		width: 100%;
		text-align: center;
	}
}
</style>

<div class="kt-cart-page">

	<div class="kt-cart-header">
		<h1>Shopping cart</h1>
		<p class="kt-cart-breadcrumb">
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Shop</a> &nbsp;&gt;&nbsp; Shopping cart
		</p>
	</div>

	<?php
	// Notices under custom header
	wc_print_notices();
	?>

	<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
		<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>

		<div class="kt-cart-layout">

			<div class="kt-cart-main">
				<?php do_action( 'woocommerce_before_cart_table' ); ?>

				<table class="shop_table shop_table_responsive cart_table" cellspacing="0">
					<thead>
						<tr>
							<th class="product-remove">&nbsp;</th>
							<th class="product-thumbnail">&nbsp;</th>
							<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
							<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
							<th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
							<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php do_action( 'woocommerce_before_cart_contents' ); ?>

						<?php
						foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
							$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

							if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
								$product_permalink = apply_filters(
									'woocommerce_cart_item_permalink',
									$_product->is_visible() ? $_product->get_permalink( $cart_item ) : '',
									$cart_item,
									$cart_item_key
								);
								?>
								<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

									<td class="product-remove" data-title="<?php esc_attr_e( 'Remove', 'woocommerce' ); ?>">
										<?php
										echo apply_filters(
											'woocommerce_cart_item_remove_link',
											sprintf(
												'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
												esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
												esc_attr__( 'Remove this item', 'woocommerce' ),
												esc_attr( $product_id ),
												esc_attr( $_product->get_sku() )
											),
											$cart_item_key
										);
										?>
									</td>

									<td class="product-thumbnail" data-title="<?php esc_attr_e( 'Product image', 'woocommerce' ); ?>">
										<?php
										$thumbnail = apply_filters(
											'woocommerce_cart_item_thumbnail',
											$_product->get_image(),
											$cart_item,
											$cart_item_key
										);

										if ( ! $product_permalink ) {
											echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput
										} else {
											printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput
										}
										?>
									</td>

									<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
										<?php
										if ( ! $product_permalink ) {
											echo wp_kses_post(
												apply_filters(
													'woocommerce_cart_item_name',
													$_product->get_name(),
													$cart_item,
													$cart_item_key
												) . '&nbsp;'
											);
										} else {
											echo wp_kses_post(
												apply_filters(
													'woocommerce_cart_item_name',
													sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ),
													$cart_item,
													$cart_item_key
												)
											);
										}

										do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
										?>
									</td>

									<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
										<?php
										echo apply_filters(
											'woocommerce_cart_item_price',
											WC()->cart->get_product_price( $_product ),
											$cart_item,
											$cart_item_key
										); // phpcs:ignore WordPress.Security.EscapeOutput
										?>
									</td>

									<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
										<div class="kt-qty-control">
											<button type="button" class="kt-qty-btn" onclick="ktCartQtyChange(this,-1)">−</button>
											<?php
											if ( $_product->is_sold_individually() ) {
												$product_quantity = sprintf(
													'1 <input type="hidden" name="cart[%s][qty]" value="1" />',
													esc_attr( $cart_item_key )
												);
											} else {
												$product_quantity = woocommerce_quantity_input(
													array(
														'input_name'   => "cart[{$cart_item_key}][qty]",
														'input_value'  => $cart_item['quantity'],
														'max_value'    => $_product->get_max_purchase_quantity(),
														'min_value'    => '0', // 0 allowed to remove item on update
														'product_name' => $_product->get_name(),
													),
													$_product,
													false
												);
											}
											echo apply_filters(
												'woocommerce_cart_item_quantity',
												$product_quantity,
												$cart_item_key,
												$cart_item
											); // phpcs:ignore WordPress.Security.EscapeOutput
											?>
											<button type="button" class="kt-qty-btn" onclick="ktCartQtyChange(this,1)">+</button>
										</div>
									</td>

									<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
										<?php
										echo apply_filters(
											'woocommerce_cart_item_subtotal',
											WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ),
											$cart_item,
											$cart_item_key
										); // phpcs:ignore WordPress.Security.EscapeOutput
										?>
									</td>
								</tr>
								<?php
							}
						}
						?>

						<?php do_action( 'woocommerce_cart_contents' ); ?>
					</tbody>
				</table>

				<?php do_action( 'woocommerce_after_cart_table' ); ?>

				<div class="actions">
					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>">
						<?php esc_html_e( 'Update cart', 'woocommerce' ); ?>
					</button>
				</div>

				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			</div>

			<!-- Right: Summary & coupon (inside the same form) -->
			<aside class="kt-cart-summary">

				<?php if ( wc_coupons_enabled() ) : ?>
					<div class="kt-coupon-box">
						<label for="coupon_code_sidebar"><?php esc_html_e( 'Have a coupon?', 'woocommerce' ); ?></label>
						<div class="kt-coupon-row">
							<input type="text" name="coupon_code" id="coupon_code_sidebar" value=""
								placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>">
							<button type="submit" class="button" name="apply_coupon"
								value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>">
								<?php esc_html_e( 'Apply', 'woocommerce' ); ?>
							</button>
						</div>
						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				<?php endif; ?>

				<?php woocommerce_cart_totals(); ?>
			</aside>

		</div><!-- /.kt-cart-layout -->

	</form>

	<div class="kt-cart-cross">
		<?php woocommerce_cross_sell_display(); ?>
	</div>

</div><!-- /.kt-cart-page -->

<script>
function ktCartQtyChange(btn, delta) {
	const wrap = btn.closest('.kt-qty-control');
	if (!wrap) return;
	const input = wrap.querySelector('input.qty');
	if (!input) return;

	let val = parseInt(input.value || '0', 10);
	if (isNaN(val)) val = 0;
	val += delta;
	if (val < 0) val = 0; // 0 allowed, Woo will remove item on update

	input.value = val;
	
	// Trigger change event so WooCommerce detects cart update
	input.dispatchEvent(new Event('change', { bubbles: true }));
}
</script>

<?php do_action( 'woocommerce_after_cart' ); ?>
<?php // footer continues from theme template ?>

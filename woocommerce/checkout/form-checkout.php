<?php
/**
 * KachoTech – Modern Checkout Layout
 */

defined( 'ABSPATH' ) || exit;

$checkout = WC()->checkout();

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<style>
.woocommerce-page.woocommerce-checkout form #customer_details.col2-set, .woocommerce.woocommerce-checkout form #order_review, .woocommerce.woocommerce-checkout form #order_review_heading, .woocommerce-page.woocommerce-checkout form #order_review{
    width: 100%;
}
form #order_review:not(.elementor-widget-woocommerce-checkout-page #order_review) {
    padding: 2em;
    border-width: 2px;
}
/* Hide theme's default title on checkout page */
body.woocommerce-checkout .entry-title {
	display: none;
}

/* ---------- Page shell ---------- */
.kt-checkout-page {
	max-width: 1160px;
	margin: 0 auto;
	padding: 32px 16px 48px;
	font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
	color: #0f172a;
	box-sizing: border-box;
}

/* Header (title + breadcrumb), left aligned */
.kt-co-header {
	margin-bottom: 18px;
}
.kt-co-header h1 {
	font-size: 28px;
	font-weight: 600;
	letter-spacing: -0.02em;
	margin: 0 0 6px;
}
.kt-co-header .kt-co-breadcrumb {
	font-size: 13px;
	color: #9ca3af;
}
.kt-co-header .kt-co-breadcrumb a {
	color: #9ca3af;
	text-decoration: none;
}
.kt-co-header .kt-co-breadcrumb a:hover {
	color: #ff2446;
}

/* Notices under header */
.woocommerce-notices-wrapper {
	margin-bottom: 16px;
}

/* 2-column layout: details + summary */
.kt-co-layout {
	display: grid;
	grid-template-columns: minmax(0, 1.6fr) minmax(260px, 0.9fr);
	gap: 32px;
}

/* ---------- Left column: customer details ---------- */

#customer_details {
	background: #ffffff;
	border-radius: 16px;
	border: 1px solid #e5e7eb;
	box-shadow: 0 8px 24px rgba(15,23,42,0.05);
	padding: 18px 20px 8px;
}

/* Billing / shipping section titles */
.woocommerce-billing-fields > h3,
.woocommerce-shipping-fields > h3,
.woocommerce-additional-fields > h3 {
	font-size: 16px;
	font-weight: 600;
	margin: 0 0 10px;
}

/* Form rows */
.woocommerce-billing-fields__field-wrapper .form-row,
.woocommerce-shipping-fields__field-wrapper .form-row,
.woocommerce-additional-fields .form-row {
	margin: 0 0 12px;
}

/* Labels */
.woocommerce form .form-row label {
	font-size: 13px;
	font-weight: 500;
	margin-bottom: 4px;
	color: #374151;
}

/* Inputs / selects / textareas */
.woocommerce form .form-row .input-text,
.woocommerce form .form-row select,
.woocommerce form .form-row textarea {
	width: 100%;
	font-size: 14px;
	padding: 8px 10px;
	border-radius: 8px;
	border: 1px solid #d1d5db;
	background: #ffffff;
	box-sizing: border-box;
}
.woocommerce form .form-row textarea {
	min-height: 90px;
	resize: vertical;
}
.woocommerce form .form-row .input-text:focus,
.woocommerce form .form-row select:focus,
.woocommerce form .form-row textarea:focus {
	outline: none;
	border-color: #ff2446;
	box-shadow: 0 0 0 2px rgba(255,36,70,.12);
}

/* Required asterisk */
.woocommerce form .form-row .required {
	color: #ef4444;
}

/* Checkbox / radio labels a bit tighter */
.woocommerce form .form-row .woocommerce-input-wrapper input[type="checkbox"],
.woocommerce form .form-row .woocommerce-input-wrapper input[type="radio"] {
	margin-right: 6px;
}

/* ---------- Right column: order summary ---------- */

.kt-co-summary {
	align-self: flex-start;
}

/* Summary card */
.kt-co-summary-card {
	background: #ffffff;
	border-radius: 16px;
	border: 1px solid #e5e7eb;
	box-shadow: 0 8px 24px rgba(15,23,42,0.05);
	padding: 16px 18px 14px;
}

.kt-co-summary-card h3 {
	font-size: 16px;
	font-weight: 600;
	margin: 0 0 10px;
	padding-bottom: 8px;
	border-bottom: 1px solid #e5e7eb;
}

/* Order review area (Woo outputs items & totals) */
.woocommerce-checkout-review-order-table {
	width: 100%;
	border-collapse: collapse;
	font-size: 14px;
	margin-bottom: 10px;
}
.woocommerce-checkout-review-order-table thead th {
	font-size: 12px;
	font-weight: 500;
	text-transform: uppercase;
	letter-spacing: 0.12em;
	color: #6b7280;
	padding-bottom: 8px;
}
.woocommerce-checkout-review-order-table th.product-name,
.woocommerce-checkout-review-order-table td.product-name {
	text-align: left;
}
.woocommerce-checkout-review-order-table th.product-total,
.woocommerce-checkout-review-order-table td.product-total {
	text-align: right;
}
.woocommerce-checkout-review-order-table tbody td,
.woocommerce-checkout-review-order-table tfoot td,
.woocommerce-checkout-review-order-table tfoot th {
	padding: 6px 0;
	border: none;
}
.woocommerce-checkout-review-order-table tfoot th {
	color: #6b7280;
	font-weight: 500;
}
.woocommerce-checkout-review-order-table tfoot td {
	text-align: right;
	font-weight: 500;
	color: #111827;
}
.woocommerce-checkout-review-order-table tfoot tr.order-total th,
.woocommerce-checkout-review-order-table tfoot tr.order-total td {
	padding-top: 10px;
	border-top: 1px solid #e5e7eb;
}
.woocommerce-checkout-review-order-table tfoot tr.order-total td {
	color: #ff2446;
}

/* Coupon above payment / place order (still inside #order_review) */
.kt-co-coupon-box {
	background: #f9fafb;
	border-radius: 12px;
	padding: 10px 12px;
	margin: 8px 0 12px;
}
.kt-co-coupon-box label {
	display: block;
	font-size: 13px;
	font-weight: 500;
	margin-bottom: 6px;
	color: #374151;
}
.kt-co-coupon-row {
	display: flex;
	gap: 8px;
}
.kt-co-coupon-row input[type="text"] {
	flex: 1;
	padding: 8px 11px;
	font-size: 14px;
	border-radius: 8px;
	border: 1px solid #d1d5db;
}
.kt-co-coupon-row input[type="text"]:focus {
	outline: none;
	border-color: #ff2446;
	box-shadow: 0 0 0 2px rgba(255,36,70,.15);
}
.kt-co-coupon-row .button {
	padding: 8px 16px;
	border-radius: 999px;
	background: #111827;
	color: #ffffff;
	font-size: 13px;
	font-weight: 500;
	border: none;
	cursor: pointer;
}
.kt-co-coupon-row .button:hover {
	background: #ff2446;
}

/* Payment box */
#payment {
	background: #ffffff;
	border-radius: 12px;
	border: 1px solid #e5e7eb;
	padding: 10px 12px 12px;
	margin-top: 10px;
}
#payment ul.payment_methods {
	padding: 0;
	margin: 0 0 10px;
	list-style: none;
}
#payment ul.payment_methods li {
	padding: 6px 0;
	border-bottom: 1px solid #f3f4f6;
}
#payment ul.payment_methods li:last-child {
	border-bottom: none;
}
#payment ul.payment_methods label {
	font-size: 13px;
	font-weight: 500;
}
#payment .payment_box {
	margin: 4px 0 0;
	padding: 6px 10px;
	background: #f9fafb;
	border-radius: 6px;
	font-size: 12px;
	color: #6b7280;
}

/* Place order button */
#payment .place-order {
	margin-top: 10px;
}
#payment .place-order .button {
	width: 100%;
	padding: 16px 18px;
	border-radius: 999px;
	background: #ff2446;
	color: #ffffff;
	font-size: 14px;
	font-weight: 500;
	border: none;
	cursor: pointer;
	text-transform: none;
}
#payment .place-order .button:hover {
	background: #e0203b;
}

/* Terms text small */
#payment .place-order .woocommerce-terms-and-conditions-wrapper {
	font-size: 12px;
	color: #6b7280;
	margin-bottom: 8px;
}

/* Order notes heading smaller */
.woocommerce-additional-fields p.form-row.notes {
	margin-top: 8px;
}

/* ---------- Responsive ---------- */
@media (max-width: 880px) {
	.kt-co-layout {
		grid-template-columns: 1fr;
		gap: 24px;
	}
}

@media (max-width: 768px) {
	.kt-checkout-page {
		padding: 24px 12px 40px;
	}
	.kt-co-coupon-row {
		flex-direction: column;
	}
	.kt-co-coupon-row .button {
		width: 100%;
		text-align: center;
	}
}
</style>

<div class="kt-checkout-page">

	<div class="kt-co-header">
		<h1><?php esc_html_e( 'Shopping cart', 'woocommerce' ); ?></h1>
		<p class="kt-co-breadcrumb">
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
				<?php esc_html_e( 'Shop', 'woocommerce' ); ?>
			</a>
			&nbsp;&gt;&nbsp;
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
				<?php esc_html_e( 'Shopping cart', 'woocommerce' ); ?>
			</a>
			&nbsp;&gt;&nbsp;
			<?php esc_html_e( 'Checkout', 'woocommerce' ); ?>
		</p>
	</div>

	<?php wc_print_notices(); ?>

	<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>

	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

		<div class="kt-co-layout">

			<!-- LEFT: Customer details -->
			<div class="kt-co-details">

				<div id="customer_details" class="col2-set">
					<div class="col-1">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
					</div>

					<div class="col-2">
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					</div>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<div class="woocommerce-additional-fields">
					<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

					<?php do_action( 'woocommerce_checkout_after_order_review_heading' ); ?>

					<?php do_action( 'woocommerce_checkout_after_order_review', $checkout ); ?>
				</div>

			</div>

			<!-- RIGHT: Summary + coupon + payment -->
			<div class="kt-co-summary">

				<div id="order_review" class="woocommerce-checkout-review-order kt-co-summary-card">
					<h3><?php esc_html_e( 'Summary', 'woocommerce' ); ?></h3>

					<?php if ( wc_coupons_enabled() ) : ?>
						<div class="kt-co-coupon-box">
							<label for="coupon_code_checkout"><?php esc_html_e( 'Have a coupon?', 'woocommerce' ); ?></label>
							<div class="kt-co-coupon-row">
								<input type="text" name="coupon_code" id="coupon_code_checkout" value=""
									placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>">
								<button type="submit" class="button" name="apply_coupon"
										value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>">
									<?php esc_html_e( 'Apply', 'woocommerce' ); ?>
								</button>
							</div>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php endif; ?>

					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

			</div>

		</div><!-- /.kt-co-layout -->

	</form>

	<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

</div>

<?php
/**
 * Checkout Page
 * Default WooCommerce checkout template with KachoTech styling
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', WC()->checkout );
?>

<style>
	/* KachoTech Checkout Page Styling */

	.woocommerce-checkout {
		max-width: 1200px;
		margin: 0 auto;
		padding: 40px 20px;
		font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
	}

	.woocommerce-checkout h1 {
		font-size: 32px;
		font-weight: 700;
		color: #0f172a;
		margin: 0 0 30px 0;
		letter-spacing: -0.5px;
	}

	.woocommerce-checkout h2 {
		font-size: 18px;
		font-weight: 600;
		color: #0f172a;
		margin: 0 0 20px 0;
		padding-bottom: 16px;
		border-bottom: 2px solid #ff2446;
	}

	.woocommerce-checkout h3 {
		font-size: 16px;
		font-weight: 600;
		color: #0f172a;
		margin: 0 0 16px 0;
	}

	/* Form layout */
	.woocommerce-checkout .col-1,
	.woocommerce-checkout .col-2 {
		display: grid;
	}

	.woocommerce form .form-row {
		margin-bottom: 16px;
	}

	.woocommerce form .form-row label {
		display: block;
		font-size: 14px;
		font-weight: 500;
		color: #0f172a;
		margin-bottom: 6px;
	}

	.woocommerce form .form-row label .required {
		color: #ff2446;
		margin-left: 4px;
	}

	.woocommerce form input[type="text"],
	.woocommerce form input[type="email"],
	.woocommerce form input[type="tel"],
	.woocommerce form input[type="password"],
	.woocommerce form input[type="date"],
	.woocommerce form input[type="number"],
	.woocommerce form select,
	.woocommerce form textarea {
		width: 100% !important;
		padding: 10px 14px;
		border: 1px solid #d1d5db;
		border-radius: 6px;
		font-size: 14px;
		font-family: inherit;
		background: #ffffff;
		color: #0f172a;
		transition: all 0.2s ease;
		box-sizing: border-box !important;
	}

	.woocommerce form input:focus,
	.woocommerce form select:focus,
	.woocommerce form textarea:focus {
		outline: none;
		border-color: #ff2446;
		box-shadow: 0 0 0 3px rgba(255, 36, 70, 0.1);
	}

	.woocommerce form input[type="checkbox"],
	.woocommerce form input[type="radio"] {
		margin-right: 8px;
		cursor: pointer;
	}

	/* Checkout form grid */
	.woocommerce-checkout form {
		display: grid;
		grid-template-columns: 1.5fr 1fr;
		gap: 30px;
		align-items: start;
	}

	.woocommerce-checkout #customer_details {
		grid-column: 1;
	}

	.woocommerce-checkout #order_review_heading {
		grid-column: 2;
		margin-top: 0;
	}

	.woocommerce-checkout #order_review {
		grid-column: 2;
		grid-row: 2;
		background: #ffffff;
		padding: 24px;
		border-radius: 12px;
		border: 1px solid #e5e7eb;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
		position: sticky;
		top: 20px;
	}

	/* Address sections */
	.woocommerce-checkout .woocommerce-billing-fields,
	.woocommerce-checkout .woocommerce-shipping-fields {
		background: #ffffff;
		padding: 24px;
		border-radius: 12px;
		border: 1px solid #e5e7eb;
		margin-bottom: 20px;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
	}

	.woocommerce-checkout .woocommerce-additional-fields {
		grid-column: 1;
		background: #ffffff;
		padding: 24px;
		border-radius: 12px;
		border: 1px solid #e5e7eb;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
	}

	/* Order review/totals table */
	.woocommerce-checkout .shop_table {
		width: 100%;
		margin-bottom: 20px;
		border-collapse: collapse;
	}

	.woocommerce-checkout .shop_table thead {
		background: #f9fafb;
		border-bottom: 2px solid #e5e7eb;
	}

	.woocommerce-checkout .shop_table th {
		padding: 12px;
		text-align: left;
		font-weight: 600;
		font-size: 14px;
		color: #0f172a;
	}

	.woocommerce-checkout .shop_table td {
		padding: 12px;
		border-bottom: 1px solid #e5e7eb;
		color: #0f172a;
	}

	.woocommerce-checkout .shop_table th:last-child,
	.woocommerce-checkout .shop_table td:last-child {
		text-align: right;
	}

	.woocommerce-checkout .shop_table tr.cart-subtotal td,
	.woocommerce-checkout .shop_table tr.shipping,
	.woocommerce-checkout .shop_table tr.tax {
		color: #6b7280;
		font-size: 13px;
	}

	.woocommerce-checkout .shop_table tr.order-total th,
	.woocommerce-checkout .shop_table tr.order-total td {
		font-size: 16px;
		font-weight: 600;
		background: #f9fafb;
		border-top: 2px solid #e5e7eb;
		padding: 16px 12px;
	}

	.woocommerce-checkout .shop_table tr.order-total td {
		color: #ff2446;
	}

	/* Payment methods */
	.woocommerce-checkout .woocommerce-checkout-payment {
		background: #ffffff;
		padding: 24px;
		border-radius: 12px;
		border: 1px solid #e5e7eb;
		margin-top: 20px;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
	}

	.woocommerce-checkout #payment {
		margin-bottom: 0 !important;
	}

	.woocommerce-checkout ul.payment_methods {
		list-style: none;
		margin: 0 0 20px 0;
		padding: 0;
	}

	.woocommerce-checkout ul.payment_methods li {
		padding: 12px;
		background: #f9fafb;
		border: 1px solid #e5e7eb;
		border-radius: 6px;
		margin-bottom: 12px;
		display: flex;
		align-items: center;
	}

	.woocommerce-checkout ul.payment_methods li input {
		margin-right: 12px !important;
	}

	.woocommerce-checkout ul.payment_methods li label {
		margin-bottom: 0;
		cursor: pointer;
		flex: 1;
	}

	/* Place order button */
	.woocommerce-checkout .place-order {
		margin-top: 20px;
	}

	.woocommerce-checkout .button.alt,
	.woocommerce-checkout .button.button-primary {
		display: block;
		width: 100%;
		padding: 14px 20px !important;
		background: #22c55e !important;
		color: #ffffff !important;
		border: none !important;
		border-radius: 6px;
		font-size: 14px;
		font-weight: 600;
		cursor: pointer;
		transition: background 0.2s ease;
		margin: 0 !important;
	}

	.woocommerce-checkout .button.alt:hover,
	.woocommerce-checkout .button.button-primary:hover {
		background: #16a34a !important;
	}

	/* Notices and validation */
	.woocommerce-error,
	.woocommerce-notice,
	.woocommerce-info {
		border-radius: 6px;
		padding: 12px 16px;
		margin-bottom: 20px;
		border-left: 4px solid #dc2626;
	}

	.woocommerce-info {
		border-left-color: #3b82f6;
	}

	.woocommerce-notice {
		border-left-color: #16a34a;
	}

	/* Responsive */
	@media (max-width: 768px) {
		.woocommerce-checkout {
			padding: 20px 12px;
		}

		.woocommerce-checkout form {
			grid-template-columns: 1fr;
		}

		.woocommerce-checkout #customer_details {
			grid-column: 1;
		}

		.woocommerce-checkout #order_review_heading {
			grid-column: 1;
		}

		.woocommerce-checkout #order_review {
			grid-column: 1;
			grid-row: auto;
			position: static;
		}

		.woocommerce-checkout .woocommerce-additional-fields {
			grid-column: 1;
		}
	}
</style>

<?php
if ( ! WC()->cart->is_empty() ) :
	?>
	<div class="woocommerce-checkout">
		<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div class="col-1">

					<?php do_action( 'woocommerce_checkout_billing' ); ?>

					<?php do_action( 'woocommerce_checkout_shipping' ); ?>

				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>

			<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

			<h2 id="order_review_heading"><?php esc_html_e( 'Order Summary', 'woocommerce' ); ?></h2>

			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>

			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

		</form>
	</div>
	<?php
else :
	?>
	<div class="woocommerce">
		<p class="woocommerce-notice woocommerce-notice--info woocommerce-info"><?php esc_html_e( 'Sorry, your session has expired.', 'woocommerce' ); ?></p>
		<?php do_action( 'woocommerce_cart_is_empty' ); ?>
	</div>
	<?php
endif;

do_action( 'woocommerce_after_checkout_form', WC()->checkout );
?>
	<div class="woocommerce" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
		<h1 style="font-size: 32px; font-weight: 700; color: #0f172a; margin-bottom: 30px;">Checkout</h1>

		<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

			<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
				<!-- Billing & Shipping Form -->
				<div>
					<?php
					do_action( 'woocommerce_checkout_before_customer_details' );

					if ( sizeof( WC()->checkout->get_checkout_fields() ) > 0 ) :
						?>

						<div id="customer_details">
							<h2 style="font-size: 18px; font-weight: 600; color: #0f172a; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Billing Details</h2>
							<div class="woocommerce-billing-fields">
								<?php do_action( 'woocommerce_before_checkout_billing_form', WC()->checkout ); ?>
								<?php
								foreach ( WC()->checkout->get_checkout_fields( 'billing' ) as $key => $field ) {
									woocommerce_form_field( $key, $field, WC()->checkout->get_value( $key ) );
								}
								?>
								<?php do_action( 'woocommerce_after_checkout_billing_form', WC()->checkout ); ?>
							</div>
						</div>

						<?php
						if ( WC()->cart->needs_shipping() ) :
							do_action( 'woocommerce_checkout_before_shipping_form', WC()->checkout );
							?>
							<div id="shipping_address">
								<h2 style="font-size: 18px; font-weight: 600; color: #0f172a; margin: 24px 0 16px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Shipping Details</h2>
								<div class="woocommerce-shipping-fields">
									<?php
									foreach ( WC()->checkout->get_checkout_fields( 'shipping' ) as $key => $field ) {
										woocommerce_form_field( $key, $field, WC()->checkout->get_value( $key ) );
									}
									?>
								</div>
							</div>
							<?php
							do_action( 'woocommerce_checkout_after_shipping_form', WC()->checkout );
						endif;
						?>

					<?php endif;

					do_action( 'woocommerce_checkout_after_customer_details' );
					?>
				</div>

				<!-- Order Review & Payment -->
				<div>
					<h2 style="font-size: 18px; font-weight: 600; color: #0f172a; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">Order Review</h2>

					<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

					<div id="order_review" class="woocommerce-checkout-review-order" style="background: #f9fafb; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
					</div>

					<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

					<?php do_action( 'woocommerce_checkout_before_payment' ); ?>

					<div id="payment" class="woocommerce-checkout-payment">
						<?php
						if ( WC()->cart->get_cart_contents_count() > 0 ) {
							do_action( 'woocommerce_checkout_payment' );
						}
						?>
					</div>

					<?php do_action( 'woocommerce_checkout_after_payment' ); ?>
				</div>
			</div>

		</form>

	</div>

<?php
endif;

do_action( 'woocommerce_after_checkout_form', WC()->checkout );
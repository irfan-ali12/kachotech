<?php
/**
 * Order Tracking Page Template
 * Allows customers to track their orders
 * 
 * @package KachoTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get current user
$current_user = wp_get_current_user();
$user_id      = get_current_user_id();

// Get user orders
if ( $user_id ) {
	$customer_orders = wc_get_orders( array(
		'customer_id' => $user_id,
		'limit'       => -1,
		'status'      => array( 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-shipped' ),
	) );
} else {
	$customer_orders = array();
}
?>

<div class="mx-auto px-4 py-8" style="max-width: 1200px; width: 100%; box-sizing: border-box;">
	<div class="grid gap-8 md:grid-cols-[1fr_3fr]">
		<!-- Sidebar -->
		<aside class="space-y-4">
			<div class="rounded-3xl bg-white p-6 shadow-soft">
				<h3 class="text-lg font-semibold mb-4"><?php esc_html_e( 'Account Menu', 'astra-child' ); ?></h3>
				<nav class="space-y-2">
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="block rounded-lg px-4 py-2 text-[#1A1A1D] hover:bg-[#F6F7FA] transition">
						<?php esc_html_e( 'Dashboard', 'astra-child' ); ?>
					</a>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) . 'orders/' ); ?>" class="block rounded-lg px-4 py-2 bg-[#EC234A] text-white">
						<?php esc_html_e( 'Track Orders', 'astra-child' ); ?>
					</a>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) . 'edit-address/' ); ?>" class="block rounded-lg px-4 py-2 text-[#1A1A1D] hover:bg-[#F6F7FA] transition">
						<?php esc_html_e( 'Addresses', 'astra-child' ); ?>
					</a>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) . 'edit-account/' ); ?>" class="block rounded-lg px-4 py-2 text-[#1A1A1D] hover:bg-[#F6F7FA] transition">
						<?php esc_html_e( 'Account Details', 'astra-child' ); ?>
					</a>
					<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="block rounded-lg px-4 py-2 text-[#1A1A1D] hover:bg-[#F6F7FA] transition">
						<?php esc_html_e( 'Logout', 'astra-child' ); ?>
					</a>
				</nav>
			</div>
		</aside>

		<!-- Main Content -->
		<main class="space-y-6">
			<div class="rounded-3xl bg-white p-6 shadow-soft">
				<h2 class="text-2xl font-bold mb-6"><?php esc_html_e( 'Track Your Orders', 'astra-child' ); ?></h2>

				<?php if ( ! empty( $customer_orders ) ) { ?>
					<div class="space-y-4">
						<?php foreach ( $customer_orders as $order ) { ?>
							<div class="border border-[#E4E6EC] rounded-2xl p-4 hover:shadow-soft transition">
								<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
									<div>
										<h3 class="font-semibold text-lg">
											<?php echo sprintf( __( 'Order #%s', 'astra-child' ), $order->get_order_number() ); ?>
										</h3>
										<p class="text-sm text-[#6B6F76]">
											<?php echo sprintf( __( 'Placed on %s', 'astra-child' ), $order->get_date_created()->format( 'M d, Y' ) ); ?>
										</p>
										<p class="text-sm font-semibold mt-2">
											<?php echo sprintf( __( 'Total: Rs %s', 'astra-child' ), $order->get_total() ); ?>
										</p>
									</div>
									<div class="flex flex-col items-start md:items-end gap-2">
										<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?php echo 'wc-completed' === $order->get_status() ? 'bg-[#E8F5E9] text-[#2E7D32]' : 'bg-[#FFF3E0] text-[#E65100]'; ?>">
											<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
										</span>
										<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="inline-flex items-center gap-2 text-[#EC234A] hover:underline text-sm font-semibold">
											<?php esc_html_e( 'View Details', 'astra-child' ); ?>
											<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
										</a>
									</div>
								</div>

								<!-- Order Items -->
								<div class="mt-4 pt-4 border-t border-[#E4E6EC]">
									<?php
									foreach ( $order->get_items() as $item_id => $item ) {
										$product = $item->get_product();
										?>
										<div class="flex justify-between py-2">
											<span class="text-sm">
												<?php echo esc_html( $item->get_name() ); ?>
												<span class="text-[#6B6F76]"> x<?php echo esc_html( $item->get_quantity() ); ?></span>
											</span>
											<span class="text-sm font-semibold">
												<?php echo wp_kses_post( $item->get_total() ); ?>
											</span>
										</div>
										<?php
									}
									?>
								</div>
							</div>
						<?php } ?>
					</div>
				<?php } else { ?>
					<div class="rounded-2xl bg-[#F6F7FA] p-8 text-center">
						<p class="text-[#6B6F76] mb-4"><?php esc_html_e( 'You haven\'t placed any orders yet.', 'astra-child' ); ?></p>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="inline-flex items-center gap-2 rounded-full bg-[#EC234A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#C9193A] transition">
							<?php esc_html_e( 'Start Shopping', 'astra-child' ); ?>
							<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
						</a>
					</div>
				<?php } ?>
			</div>
		</main>
	</div>
</div>

<?php
/**
 * Footer Template
 * Displays site footer with company info, links, and newsletter
 * 
 * @package KachoTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<footer class="mt-10 bg-[#1F2937]">
	<!-- Newsletter Subscription Strip -->
	<div class="bg-gradient-to-r from-[#EC234A] to-[#FF5577]">
		<div class="mx-auto px-4 py-6" style="max-width: 1200px; width: 100%; box-sizing: border-box;">
			<div class="flex flex-wrap items-center justify-between gap-6">
				<div>
					<h3 class="text-lg font-bold text-white mb-1">
						<?php esc_html_e( 'Stay Updated with Exclusive Deals', 'astra-child' ); ?>
					</h3>
					<p class="text-sm text-white/90">
						<?php esc_html_e( 'Subscribe to our newsletter for exclusive offers, winter deals, and product updates delivered straight to your inbox.', 'astra-child' ); ?>
					</p>
				</div>
				<form method="post" class="flex gap-2 flex-shrink-0" style="min-width: 300px;">
					<input
						type="email"
						name="email"
						class="flex-1 rounded-lg border border-white/30 bg-white/10 px-4 py-3 text-sm text-white placeholder-white/60 outline-none focus:border-white focus:bg-white/20 transition"
						placeholder="<?php esc_attr_e( 'Enter your email address', 'astra-child' ); ?>"
						required
					/>
					<button type="submit" class="rounded-lg bg-white text-[#EC234A] px-6 py-3 text-sm font-bold hover:bg-gray-100 transition whitespace-nowrap">
						<?php esc_html_e( 'Subscribe', 'astra-child' ); ?>
					</button>
				</form>
			</div>
		</div>
	</div>

	<!-- Main Footer -->
	<div class="mx-auto px-4 py-7" style="max-width: 1200px; width: 100%; box-sizing: border-box;">
		<div class="grid gap-6 text-[11px] md:grid-cols-[2.1fr_1.2fr_1.2fr_1.3fr_1.7fr]">
			<!-- About Section -->
			<div>
				<div class="text-lg font-extrabold tracking-[0.22em]">
					<?php esc_html_e( 'KACHOTECH', 'astra-child' ); ?>
				</div>
				<p class="mt-2 max-w-xs text-[#6B6F76]">
					<?php esc_html_e( 'KachoTech brings winter heaters, small home appliances and cosmetics together in one trusted online store.', 'astra-child' ); ?>
				</p>
				<div class="mt-4 space-y-1">
					<p>📍 <?php esc_html_e( 'B601-605, Ahmed Center Naya Mohallah Liaquat Road Rawalpindi', 'astra-child' ); ?></p>
					<p>📞 <?php esc_html_e( '0329-5111000', 'astra-child' ); ?></p>
					<p>✉️ support@kachotech.com</p>
				</div>
				<div class="flex gap-1">
						<a href="#" class="flex h-5 w-5 items-center justify-center rounded-full bg-[#D4E8E0] text-[10px] hover:bg-[#EC234A] hover:text-white transition">f</a>
						<a href="#" class="flex h-5 w-5 items-center justify-center rounded-full bg-[#D4E8E0] text-[10px] hover:bg-[#EC234A] hover:text-white transition">ig</a>
						<a href="#" class="flex h-5 w-5 items-center justify-center rounded-full bg-[#D4E8E0] text-[10px] hover:bg-[#EC234A] hover:text-white transition">yt</a>
				</div>
			</div>

			<!-- Get To Know Us -->
			<div>
				<h4 class="mb-2 text-xs font-semibold"><?php esc_html_e( 'Get To Know Us', 'astra-child' ); ?></h4>
				<ul class="space-y-1">
					<li><a href="#" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'About KachoTech', 'astra-child' ); ?></a></li>
					<li><a href="#" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'Careers', 'astra-child' ); ?></a></li>
					<li><a href="#" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'Customer Reviews', 'astra-child' ); ?></a></li>
					<li><a href="#" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'Store Locations', 'astra-child' ); ?></a></li>
				</ul>
			</div>

			<!-- Legal -->
			<div>
				<h4 class="mb-2 text-xs font-semibold"><?php esc_html_e( 'Legal', 'astra-child' ); ?></h4>
				<ul class="space-y-1">
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'Privacy Policy', 'astra-child' ); ?></a></li>
					<li><a href="#" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'Terms of Use', 'astra-child' ); ?></a></li>
					<li><a href="#" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'Cookies & Tracking', 'astra-child' ); ?></a></li>
					<li><a href="#" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'Compliance', 'astra-child' ); ?></a></li>
				</ul>
			</div>

			<!-- Orders & Returns -->
			<div>
				<h4 class="mb-2 text-xs font-semibold"><?php esc_html_e( 'Orders & Returns', 'astra-child' ); ?></h4>
				<ul class="space-y-1">
					<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'Your Orders', 'astra-child' ); ?></a></li>
					<li><a href="#" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'Return & Replacement', 'astra-child' ); ?></a></li>
					<li><a href="#" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'Shipping & Delivery', 'astra-child' ); ?></a></li>
					<li><a href="#" class="hover:text-[#EC234A] transition"><?php esc_html_e( 'Refund Policy', 'astra-child' ); ?></a></li>
				</ul>
			</div>

			<!-- Newsletter -->
			<div>
				<h4 class="mb-2 text-xs font-semibold"><?php esc_html_e( 'Let\'s Keep In Touch', 'astra-child' ); ?></h4>
				<p class="mb-2 text-[#6B6F76]">
					<?php esc_html_e( 'Get recommendations, tips and exclusive winter deals.', 'astra-child' ); ?>
				</p>
				<form method="post" class="mb-3 flex gap-2">
					<input
						type="email"
						name="email"
						class="flex-1 rounded-full border border-[#C7D6CF] bg-white px-3 py-1.5 text-[11px] outline-none focus:border-[#EC234A]"
						placeholder="<?php esc_attr_e( 'Enter email address', 'astra-child' ); ?>"
						required
					/>
					<button type="submit" class="rounded-full bg-[#EC234A] px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-[#C9193A] transition">
						<?php esc_html_e( 'Subscribe', 'astra-child' ); ?>
					</button>
				</form>
				<div class="mb-2 flex items-center gap-2">
					<span><?php esc_html_e( 'Social', 'astra-child' ); ?></span>
					
				</div>
			</div>
		</div>

		<div class="mt-5 flex flex-wrap items-center justify-between border-t border-[#D4EAE0] pt-3 text-[10px] text-[#7B8A85]">
			<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'KachoTech. All rights reserved.', 'astra-child' ); ?></span>
			<span><?php esc_html_e( 'Designed & developed by KachoTech Team.', 'astra-child' ); ?></span>
		</div>
	</div>
</footer>

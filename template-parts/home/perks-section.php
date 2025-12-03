<?php
/**
 * Perks and Trust Section Template
 * Displays shipping, returns, and support information
 * 
 * @package KachoTech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="kt-perks-section" style="max-width: 1200px; margin: 40px auto; padding: 0 16px; width: 100%; box-sizing: border-box;">
	<!-- Perks Row -->
	<div class="rounded-3xl bg-white px-7 py-5 shadow-soft grid gap-6 md:grid-cols-3">
		<?php
		$perks = array(
			array(
				'title' => __( 'Free Shipping', 'astra-child' ),
				'text'  => __( 'Fast nationwide delivery on eligible heaters, electronics and cosmetics.', 'astra-child' ),
				'icon'  => 'truck',
				'link'  => '#',
			),
			array(
				'title' => __( 'Money Back Guarantee', 'astra-child' ),
				'text'  => __( 'Easy returns within 7 days if your order doesn\'t meet expectations.', 'astra-child' ),
				'icon'  => 'check',
				'link'  => '#',
			),
			array(
				'title' => __( '24/7 Customer Support', 'astra-child' ),
				'text'  => __( 'Talk to us anytime on WhatsApp, phone or email for quick help.', 'astra-child' ),
				'icon'  => 'headphones',
				'link'  => '#',
			),
		);

		foreach ( $perks as $i => $perk ) {
			?>
			<article class="relative flex items-start gap-3 md:pr-5">
				<?php if ( $i < 2 ) { ?>
					<span class="pointer-events-none absolute right-0 top-3 hidden h-10 w-px bg-[#E4E6EC] md:block"></span>
				<?php } ?>
				<div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#EC234A]/10 text-[#EC234A] flex-shrink-0">
					<?php
					if ( 'truck' === $perk['icon'] ) {
						?>
						<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<rect x="1" y="3" width="15" height="13" rx="2"></rect>
							<path d="M16 8h3l3 4v4h-6"></path>
							<circle cx="6" cy="19" r="2"></circle>
							<circle cx="18" cy="19" r="2"></circle>
						</svg>
						<?php
					} elseif ( 'check' === $perk['icon'] ) {
						?>
						<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<rect x="3" y="3" width="18" height="18" rx="2"></rect>
							<path d="M16 8l-5 5-3-3"></path>
						</svg>
						<?php
					} else {
						?>
						<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
							<path d="M22 16.92V19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-2.08"></path>
							<path d="M18 8a6 6 0 0 0-12 0v4"></path>
							<circle cx="12" cy="19" r="1"></circle>
						</svg>
						<?php
					}
					?>
				</div>
				<div class="space-y-1">
					<h3 class="text-sm font-semibold"><?php echo esc_html( $perk['title'] ); ?></h3>
					<p class="text-[11px] text-[#6B6F76]"><?php echo esc_html( $perk['text'] ); ?></p>
					<button class="mt-1 inline-flex items-center gap-1 text-[11px] font-semibold text-<?php echo ( 1 === $i ? '[#EC234A]' : '[#1A1A1D]' ); ?> hover:underline">
						<?php
						if ( 0 === $i ) {
							esc_html_e( 'Learn More', 'astra-child' );
						} elseif ( 1 === $i ) {
							esc_html_e( 'Details', 'astra-child' );
						} else {
							esc_html_e( 'Get Support', 'astra-child' );
						}
						?>
						<svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
					</button>
				</div>
			</article>
			<?php
		}
		?>
	</div>

	<!-- Mini Promo Banners -->
	<div class="mt-5 grid gap-4 md:grid-cols-3">
		<!-- Kitchen Deals -->
		<article class="flex items-center justify-between gap-4 rounded-3xl bg-gradient-to-br from-[#E0F2FF] to-[#F3FBFF] p-5 shadow-soft hover:shadow-lg transition">
			<div>
				<p class="text-xs font-semibold text-[#1A1A1D]"><?php esc_html_e( 'Perfect Kitchen', 'astra-child' ); ?></p>
				<h3 class="text-sm font-extrabold"><?php esc_html_e( 'Has Arrived', 'astra-child' ); ?></h3>
				<p class="mt-2 text-sm font-extrabold text-[#EC234A]">30% OFF</p>
			</div>
			<img
				class="h-24 w-auto object-contain"
				src="http://kachotech.com/wp-content/uploads/2025/12/category-home-appliences.png"
				alt="<?php esc_attr_e( 'Kitchen Set', 'astra-child' ); ?>"
				loading="lazy"
			/>
		</article>

		<!-- Category Deals -->
		<article class="flex items-center justify-between gap-4 rounded-3xl bg-gradient-to-br from-[#FFE4F0] to-[#FFEAE0] p-5 shadow-soft hover:shadow-lg transition">
			<div>
				<p class="text-xs font-semibold text-[#1A1A1D]"><?php esc_html_e( 'Best Value', 'astra-child' ); ?></p>
				<h3 class="text-sm font-extrabold"><?php esc_html_e( 'Winter Special', 'astra-child' ); ?></h3>
				<p class="mt-1 text-[11px] text-[#6B6F76]"><?php esc_html_e( 'Heaters & Electronics on Sale.', 'astra-child' ); ?></p>
				<p class="mt-2 text-[13px] font-extrabold text-[#EC234A]">
					<?php esc_html_e( 'Save up to 25% Off', 'astra-child' ); ?>
				</p>
			</div>
			<div class="h-24 w-24 rounded-full bg-white/60 flex-shrink-0">
				<img
				class="h-24 w-auto object-contain"
				src="http://kachotech.com/wp-content/uploads/2025/12/category-heater.png"
				alt="<?php esc_attr_e( 'Kitchen Set', 'astra-child' ); ?>"
				loading="lazy"
			/>
			</div>
		</article>

		<!-- Electronics Deals -->
		<article class="flex items-center justify-between gap-4 rounded-3xl bg-gradient-to-br from-[#E0F2FF] to-[#F3FBFF] p-5 shadow-soft hover:shadow-lg transition">
			<div>
				<p class="text-xs font-semibold text-[#1A1A1D]"><?php esc_html_e( 'Electronics', 'astra-child' ); ?></p>
				<h3 class="text-sm font-extrabold"><?php esc_html_e( 'Just Launched', 'astra-child' ); ?></h3>
				<p class="mt-2 text-sm font-extrabold text-[#EC234A]">35% OFF</p>
			</div>
			<img
				class="h-24 w-auto object-contain"
				src="http://kachotech.com/wp-content/uploads/2025/12/category-electronics.png"
				alt="<?php esc_attr_e( 'Electronics', 'astra-child' ); ?>"
				loading="lazy"
			/>
		</article>
	</div>
</section>

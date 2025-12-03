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

<footer class="mt-10">
	<!-- Social Media Bar -->
	<div style="background-color: #EC234A; padding: 16px 0;">
		<div class="mx-auto px-4" style="max-width: 1200px; width: 100%; box-sizing: border-box;">
			<div class="flex items-center justify-between flex-wrap gap-4">
				<p style="color: white; font-size: 15px; margin: 0;">
					<?php esc_html_e( 'Get connected with us on social networks:', 'astra-child' ); ?>
				</p>
				<div class="flex gap-4">
					<a href="#" style="color: white; font-size: 18px; text-decoration: none; transition: all 0.3s ease;" class="hover:opacity-80" title="Facebook">
						<i class="fab fa-facebook-f"></i>
					</a>
					<a href="#" style="color: white; font-size: 18px; text-decoration: none; transition: all 0.3s ease;" class="hover:opacity-80" title="Twitter">
						<i class="fab fa-twitter"></i>
					</a>
					<a href="#" style="color: white; font-size: 18px; text-decoration: none; transition: all 0.3s ease;" class="hover:opacity-80" title="Google">
						<i class="fab fa-google"></i>
					</a>
					<a href="#" style="color: white; font-size: 18px; text-decoration: none; transition: all 0.3s ease;" class="hover:opacity-80" title="Instagram">
						<i class="fab fa-instagram"></i>
					</a>
					<a href="#" style="color: white; font-size: 18px; text-decoration: none; transition: all 0.3s ease;" class="hover:opacity-80" title="LinkedIn">
						<i class="fab fa-linkedin-in"></i>
					</a>
					<a href="#" style="color: white; font-size: 18px; text-decoration: none; transition: all 0.3s ease;" class="hover:opacity-80" title="GitHub">
						<i class="fab fa-github"></i>
					</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Main Footer Content -->
	<div style="background-color: #0F172A; padding: 48px 0;">
		<div class="mx-auto px-4" style="max-width: 1200px; width: 100%; box-sizing: border-box;">
			<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 40px; margin-bottom: 40px;">
				<!-- Company Info -->
				<div>
					<div style="display: flex; flex-direction: column; gap: 16px;">
						<img src="http://kachotech.com/wp-content/uploads/2025/12/Kacho-tech-white-logo-scaled.png" alt="KachoTech Logo" style="width: auto;">
						<p style="color: #C8CACE; font-size: 14px; line-height: 1.6; margin: 0;">
							<?php esc_html_e( 'KachoTech brings premium winter heaters, small home appliances and cosmetics together in one trusted online store. Your comfort is our mission.', 'astra-child' ); ?>
						</p>
					</div>
				</div>

				<!-- Products -->
				<div>
					<h4 style="color: white; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 20px 0;">
						<?php esc_html_e( 'PRODUCTS', 'astra-child' ); ?>
					</h4>
					<ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
						<li><a href="#" style="color: #C8CACE; text-decoration: none; font-size: 14px; transition: color 0.3s ease;" class="hover:text-white"><?php esc_html_e( 'Winter Heaters', 'astra-child' ); ?></a></li>
						<li><a href="#" style="color: #C8CACE; text-decoration: none; font-size: 14px; transition: color 0.3s ease;" class="hover:text-white"><?php esc_html_e( 'Home Appliances', 'astra-child' ); ?></a></li>
						<li><a href="#" style="color: #C8CACE; text-decoration: none; font-size: 14px; transition: color 0.3s ease;" class="hover:text-white"><?php esc_html_e( 'Beauty & Cosmetics', 'astra-child' ); ?></a></li>
						<li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="color: #C8CACE; text-decoration: none; font-size: 14px; transition: color 0.3s ease;" class="hover:text-white"><?php esc_html_e( 'View All Products', 'astra-child' ); ?></a></li>
					</ul>
				</div>

				<!-- Useful Links -->
				<div>
					<h4 style="color: white; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 20px 0;">
						<?php esc_html_e( 'USEFUL LINKS', 'astra-child' ); ?>
					</h4>
					<ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
						<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" style="color: #C8CACE; text-decoration: none; font-size: 14px; transition: color 0.3s ease;" class="hover:text-white"><?php esc_html_e( 'Your Account', 'astra-child' ); ?></a></li>
						<li><a href="#" style="color: #C8CACE; text-decoration: none; font-size: 14px; transition: color 0.3s ease;" class="hover:text-white"><?php esc_html_e( 'Become an Affiliate', 'astra-child' ); ?></a></li>
						<li><a href="#" style="color: #C8CACE; text-decoration: none; font-size: 14px; transition: color 0.3s ease;" class="hover:text-white"><?php esc_html_e( 'Shipping Rates', 'astra-child' ); ?></a></li>
						<li><a href="#" style="color: #C8CACE; text-decoration: none; font-size: 14px; transition: color 0.3s ease;" class="hover:text-white"><?php esc_html_e( 'Help Center', 'astra-child' ); ?></a></li>
					</ul>
				</div>

				<!-- Contact Info -->
				<div>
					<h4 style="color: white; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 20px 0;">
						<?php esc_html_e( 'CONTACT', 'astra-child' ); ?>
					</h4>
					<ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
						<li style="color: #C8CACE; font-size: 14px; display: flex; align-items: flex-start; gap: 10px;">
							<i class="fas fa-map-marker-alt" style="color: #EC234A; flex-shrink: 0; margin-top: 2px;"></i>
							<span><?php esc_html_e( 'B601-605, Ahmed Center Naya Mohallah Liaquat Road Rawalpindi', 'astra-child' ); ?></span>
						</li>
						<li style="color: #C8CACE; font-size: 14px; display: flex; align-items: center; gap: 10px;">
							<i class="fas fa-envelope" style="color: #EC234A; flex-shrink: 0;"></i>
							<a href="mailto:support@kachotech.com" style="color: #C8CACE; text-decoration: none; transition: color 0.3s ease;" class="hover:text-white">support@kachotech.com</a>
						</li>
						<li style="color: #C8CACE; font-size: 14px; display: flex; align-items: center; gap: 10px;">
							<i class="fas fa-phone" style="color: #EC234A; flex-shrink: 0;"></i>
							<a href="tel:+923295111000" style="color: #C8CACE; text-decoration: none; transition: color 0.3s ease;" class="hover:text-white"><?php esc_html_e( '+92 329-5111000', 'astra-child' ); ?></a>
						</li>
					</ul>
				</div>
			</div>

			<!-- Footer Divider -->
			<div style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 24px; margin-top: 24px;">
				<p style="color: #7A7F86; font-size: 13px; text-align: center; margin: 0;">
					© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'KachoTech. All rights reserved. Designed & developed by KachoTech Team.', 'astra-child' ); ?>
				</p>
			</div>
		</div>
	</div>
</footer>


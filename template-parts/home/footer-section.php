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

<style>
	/* Mobile footer responsive styles */
	@media (max-width: 768px) {
		.kt-social-bar {
			flex-direction: column !important;
			gap: 12px !important;
			align-items: flex-start !important;
		}
		
		.kt-social-bar p {
			width: 100% !important;
		}
		
		.kt-footer-grid-responsive {
			display: grid !important;
			grid-template-columns: 1fr !important;
			gap: 24px !important;
		}
		
		.kt-footer-grid-responsive > div {
			display: flex !important;
			flex-direction: column !important;
		}
		
		footer .mx-auto {
			padding-left: 12px !important;
			padding-right: 12px !important;
		}
	}
	
	@media (min-width: 769px) and (max-width: 1024px) {
		.kt-footer-grid-responsive {
			display: grid !important;
			grid-template-columns: repeat(2, 1fr) !important;
			gap: 30px !important;
		}
	}
	
	/* Remove overflow issues */
	footer {
		overflow-x: hidden !important;
	}
	
	footer .mx-auto {
		overflow-x: hidden !important;
	}
</style>

<footer class="mt-10">
	<!-- Social Media Bar -->
	<div style="background-color: #EC234A; padding: 16px 0; overflow-x: hidden;">
		<div style="max-width: 1200px; width: 100%; margin: 0 auto; padding: 0 12px; box-sizing: border-box;">
			<div class="kt-social-bar" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 4px;">
				<p style="color: white; font-size: 15px; margin: 0;">
					<?php esc_html_e( 'Get connected with us on social networks:', 'astra-child' ); ?>
				</p>
				<div class="kt-social-icons" style="display: flex; gap: 4px;">
					<a href="https://www.facebook.com/kachotech" style="color: white; font-size: 18px; text-decoration: none; transition: all 0.3s ease; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;" class="hover:opacity-80" title="Facebook">
						<i class="fab fa-facebook-f"></i>
					</a>
					<a href="https://www.google.com/maps/place/KachoTech/@33.6382705,73.0711845,17z/data=!3m1!4b1!4m6!3m5!1s0x38df95660fb946c1:0xbe12ba723d82b060!8m2!3d33.6382661!4d73.0737594!16s%2Fg%2F11ys3ss2qg?entry=ttu&g_ep=EgoyMDI1MTIwOS4wIKXMDSoASAFQAw%3D%3D" style="color: white; font-size: 18px; text-decoration: none; transition: all 0.3s ease; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;" class="hover:opacity-80" title="Google">
						<i class="fab fa-google"></i>
					</a>
					<a href="https://www.instagram.com/kachotech/" style="color: white; font-size: 18px; text-decoration: none; transition: all 0.3s ease; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;" class="hover:opacity-80" title="Instagram">
						<i class="fab fa-instagram"></i>
					</a>
					<a href="https://www.youtube.com/@kachotech" target="_blank" rel="noopener noreferrer" style="color: white; font-size: 18px; text-decoration: none; transition: all 0.3s ease; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;" class="hover:opacity-80" title="YouTube">
						<i class="fab fa-youtube"></i>
					</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Main Footer Content -->
	<div style="background-color: #0F172A; padding: 48px 0; overflow-x: hidden;">
		<div style="max-width: 1200px; width: 100%; margin: 0 auto; padding: 0 12px; box-sizing: border-box;">
			<div class="kt-footer-grid-responsive" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 40px; margin-bottom: 40px;">
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
						<li><a href="<?php echo site_url('/contact-us'); ?>" style="color: #C8CACE; text-decoration: none; font-size: 14px; transition: color 0.3s ease;" class="hover:text-white"><?php esc_html_e( 'Help Center', 'astra-child' ); ?></a></li>
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
							<span><?php esc_html_e( 'Ahmed Center shop no 12 (LG) property # B 601_605 Nia Mohalla Street no #1', 'astra-child' ); ?></span>
						</li>
						<li style="color: #C8CACE; font-size: 14px; display: flex; align-items: center; gap: 10px;">
							<i class="fas fa-envelope" style="color: #EC234A; flex-shrink: 0;"></i>
							<a href="mailto:support@kachotech.com" style="color: #C8CACE; text-decoration: none; transition: color 0.3s ease;" class="hover:text-white">support@kachotech.com</a>
						</li>
						<li style="color: #C8CACE; font-size: 14px; display: flex; align-items: center; gap: 10px;">
							<i class="fas fa-phone" style="color: #EC234A; flex-shrink: 0;"></i>
							<a href="tel:+923295111000" style="color: #C8CACE; text-decoration: none; transition: color 0.3s ease;" class="hover:text-white"><?php esc_html_e( '+92308-0007082', 'astra-child' ); ?></a>
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


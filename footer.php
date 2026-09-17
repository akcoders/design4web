<?php
/**
 * Site footer.
 *
 * @package Design4Web
 */
$socials = array(
	'facebook_url'  => array( 'Facebook', 'bi-facebook' ),
	'twitter_url'   => array( 'X / Twitter', 'bi-twitter-x' ),
	'instagram_url' => array( 'Instagram', 'bi-instagram' ),
	'linkedin_url'  => array( 'LinkedIn', 'bi-linkedin' ),
);
?>
</main>

<footer class="site-footer">
	<div class="container-fluid d4w-shell">
		<div class="row g-5 align-items-start">
			<div class="col-lg-5">
				<a class="d4w-logo d4w-footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img src="<?php echo esc_url( D4W_URI . '/assets/images/logo.png' ); ?>" width="326" height="77" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				</a>
				<p class="footer-intro"><?php esc_html_e( 'Websites, brands and digital systems created with curiosity, precision and a very human point of view.', 'design4web' ); ?></p>
			</div>
			<div class="col-6 col-lg-2 offset-lg-1">
				<p class="footer-label"><?php esc_html_e( 'Explore', 'design4web' ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'fallback_cb'    => 'd4w_primary_menu_fallback',
						'depth'          => 1,
					)
				);
				?>
			</div>
			<div class="col-6 col-lg-4">
				<p class="footer-label"><?php esc_html_e( 'Say hello', 'design4web' ); ?></p>
				<a class="footer-email" href="mailto:<?php echo esc_attr( d4w_get_option( 'contact_email' ) ); ?>"><?php echo esc_html( d4w_get_option( 'contact_email' ) ); ?></a>
				<a class="footer-phone" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', d4w_get_option( 'phone' ) ) ); ?>"><?php echo esc_html( d4w_get_option( 'phone' ) ); ?></a>
				<div class="footer-socials">
					<?php foreach ( $socials as $key => $social ) : ?>
						<?php if ( d4w_get_option( $key ) ) : ?>
							<a href="<?php echo esc_url( d4w_get_option( $key ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social[0] ); ?>"><i class="bi <?php echo esc_attr( $social[1] ); ?>"></i></a>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="footer-bottom">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'design4web' ); ?></p>
			<p><?php esc_html_e( 'Designed & developed in India', 'design4web' ); ?> <span aria-hidden="true">✦</span></p>
		</div>
	</div>
</footer>

<?php if ( d4w_get_option( 'whatsapp' ) ) : ?>
	<a class="d4w-whatsapp magnetic" href="https://wa.me/<?php echo esc_attr( preg_replace( '/\D+/', '', d4w_get_option( 'whatsapp' ) ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'design4web' ); ?>">
		<i class="bi bi-whatsapp"></i><span><?php esc_html_e( 'Let’s chat', 'design4web' ); ?></span>
	</a>
<?php endif; ?>

<button class="d4w-back-top" type="button" aria-label="<?php esc_attr_e( 'Back to top', 'design4web' ); ?>"><i class="bi bi-arrow-up"></i></button>

<?php wp_footer(); ?>
</body>
</html>

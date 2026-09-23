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
$footer_services = get_posts(
	array(
		'post_type'      => 'd4w_service',
		'post_status'    => 'publish',
		'posts_per_page' => 5,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'no_found_rows'  => true,
	)
);
?>
</main>

<footer class="site-footer">
	<div class="d4w-footer-orb" aria-hidden="true"></div>
	<div class="container-fluid d4w-shell">
		<div class="d4w-footer-cta">
			<div>
				<p class="d4w-section-label d4w-section-label--light"><span><?php esc_html_e( 'Next', 'design4web' ); ?></span><?php esc_html_e( 'Your move', 'design4web' ); ?></p>
				<h2><?php echo esc_html( d4w_get_option( 'footer_cta_title' ) ); ?></h2>
			</div>
			<a class="d4w-footer-cta__button magnetic" href="<?php echo esc_url( d4w_page_url( 'contact', home_url( '/#contact' ) ) ); ?>"><span><?php esc_html_e( 'Start a project', 'design4web' ); ?></span><i class="bi bi-arrow-up-right"></i></a>
		</div>

		<div class="d4w-footer-marquee" aria-hidden="true"><div><span>DESIGN <i>✦</i> BUILD <i>✦</i> GROW <i>✦</i></span><span>DESIGN <i>✦</i> BUILD <i>✦</i> GROW <i>✦</i></span></div></div>

		<div class="d4w-footer-grid">
			<div class="d4w-footer-brand">
				<a class="d4w-logo d4w-footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( has_custom_logo() ) : ?>
						<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'custom-logo', 'alt' => get_bloginfo( 'name' ) ) ); ?>
					<?php else : ?>
						<img src="<?php echo esc_url( D4W_URI . '/assets/images/logo.png' ); ?>" width="326" height="77" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<?php endif; ?>
				</a>
				<p class="footer-intro"><?php echo esc_html( d4w_get_option( 'footer_intro' ) ); ?></p>
				<span class="d4w-footer-status"><i></i><?php esc_html_e( 'Available for selected projects', 'design4web' ); ?></span>
			</div>
			<div class="d4w-footer-nav">
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
			<?php if ( $footer_services ) : ?>
				<div class="d4w-footer-services">
					<p class="footer-label"><?php esc_html_e( 'Capabilities', 'design4web' ); ?></p>
					<ul><?php foreach ( $footer_services as $footer_service ) : ?><li><a href="<?php echo esc_url( get_permalink( $footer_service ) ); ?>"><?php echo esc_html( get_the_title( $footer_service ) ); ?><i class="bi bi-arrow-up-right"></i></a></li><?php endforeach; ?></ul>
				</div>
			<?php endif; ?>
			<div class="d4w-footer-contact">
				<p class="footer-label"><?php esc_html_e( 'Say hello', 'design4web' ); ?></p>
				<a class="footer-email" href="mailto:<?php echo esc_attr( d4w_get_option( 'contact_email' ) ); ?>"><?php echo esc_html( d4w_get_option( 'contact_email' ) ); ?></a>
				<a class="footer-phone" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', d4w_get_option( 'phone' ) ) ); ?>"><?php echo esc_html( d4w_get_option( 'phone' ) ); ?></a>
				<div class="footer-socials">
					<?php foreach ( $socials as $key => $social ) : ?>
						<?php if ( d4w_get_option( $key ) ) : ?>
							<a href="<?php echo esc_url( d4w_get_option( $key ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social[0] ); ?>"><i class="bi <?php echo esc_attr( $social[1] ); ?>"></i><span><?php echo esc_html( $social[0] ); ?></span></a>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<div class="d4w-footer-word" aria-hidden="true">DESIGN4WEB</div>
		<div class="footer-bottom">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'design4web' ); ?></p>
			<p><?php esc_html_e( 'Designed & developed in India', 'design4web' ); ?> <span aria-hidden="true">✦</span></p>
		</div>
	</div>
</footer>

<?php if ( d4w_get_option( 'whatsapp' ) ) : ?>
	<a class="d4w-whatsapp magnetic" href="<?php echo esc_url( d4w_whatsapp_url() ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'design4web' ); ?>">
		<i class="bi bi-whatsapp"></i><span><?php esc_html_e( 'Let’s chat', 'design4web' ); ?></span>
	</a>
<?php endif; ?>

<button class="d4w-back-top" type="button" aria-label="<?php esc_attr_e( 'Back to top', 'design4web' ); ?>"><i class="bi bi-arrow-up"></i></button>

<?php wp_footer(); ?>
</body>
</html>

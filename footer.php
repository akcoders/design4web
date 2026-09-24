<?php
/**
 * Site footer.
 *
 * @package Design4Web
 */
$socials = array(
	'facebook_url'  => array( 'Facebook', 'bi-facebook' ),
	'instagram_url' => array( 'Instagram', 'bi-instagram' ),
	'youtube_url'   => array( 'YouTube', 'bi-youtube' ),
	'linkedin_url'  => array( 'LinkedIn', 'bi-linkedin' ),
);
$footer_services = get_posts(
	array(
		'post_type'      => 'd4w_service',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'no_found_rows'  => true,
	)
);
$footer_products = get_posts(
	array(
		'post_type'      => 'd4w_product',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		'no_found_rows'  => true,
	)
);
$explore_links = array(
	__( 'Home', 'design4web' )         => home_url( '/' ),
	__( 'Clients', 'design4web' )      => get_post_type_archive_link( 'd4w_project' ),
	__( 'Case Studies', 'design4web' ) => home_url( '/#case-studies' ),
	__( 'Testimonials', 'design4web' ) => home_url( '/#reviews' ),
	__( 'Contact Us', 'design4web' )   => d4w_page_url( 'contact', home_url( '/#contact' ) ),
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

		<div class="d4w-footer-grid d4w-footer-grid--v2">
			<div class="d4w-footer-brand d4w-footer-brand--wide">
				<a class="d4w-logo d4w-footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( has_custom_logo() ) : ?>
						<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'custom-logo', 'alt' => get_bloginfo( 'name' ) ) ); ?>
					<?php else : ?>
						<img src="<?php echo esc_url( D4W_URI . '/assets/images/logo.png' ); ?>" width="326" height="77" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<?php endif; ?>
				</a>
				<p class="footer-intro"><?php echo esc_html( d4w_get_option( 'footer_intro' ) ); ?></p>
				<div class="d4w-footer-contact-links">
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', d4w_get_option( 'phone' ) ) ); ?>"><i class="bi bi-telephone"></i><span><?php echo esc_html( d4w_get_option( 'phone' ) ); ?></span></a>
					<a href="mailto:<?php echo esc_attr( d4w_get_option( 'contact_email' ) ); ?>"><i class="bi bi-envelope"></i><span><?php echo esc_html( d4w_get_option( 'contact_email' ) ); ?></span></a>
				</div>
				<p class="d4w-footer-follow-label"><?php esc_html_e( 'Follow us', 'design4web' ); ?></p>
				<div class="footer-socials">
					<?php foreach ( $socials as $key => $social ) : ?>
						<?php if ( d4w_get_option( $key ) ) : ?>
							<a href="<?php echo esc_url( d4w_get_option( $key ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social[0] ); ?>"><i class="bi <?php echo esc_attr( $social[1] ); ?>"></i><span><?php echo esc_html( $social[0] ); ?></span></a>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="d4w-footer-column d4w-footer-nav">
				<p class="footer-label"><?php esc_html_e( 'Explore', 'design4web' ); ?></p>
				<ul class="d4w-footer-list"><?php foreach ( $explore_links as $label => $url ) : ?><li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?><i class="bi bi-arrow-up-right"></i></a></li><?php endforeach; ?></ul>
			</div>
			<div class="d4w-footer-column d4w-footer-column--services">
				<p class="footer-label"><?php esc_html_e( 'Services', 'design4web' ); ?></p>
				<a class="d4w-footer-all" href="<?php echo esc_url( get_post_type_archive_link( 'd4w_service' ) ); ?>"><?php esc_html_e( 'All Services', 'design4web' ); ?><i class="bi bi-arrow-right"></i></a>
				<?php if ( $footer_services ) : ?><ul class="d4w-footer-list d4w-footer-list--secondary"><?php foreach ( $footer_services as $footer_service ) : ?><li><a href="<?php echo esc_url( get_permalink( $footer_service ) ); ?>"><?php echo esc_html( get_the_title( $footer_service ) ); ?></a></li><?php endforeach; ?></ul><?php endif; ?>
			</div>
			<div class="d4w-footer-column d4w-footer-column--products">
				<p class="footer-label"><?php esc_html_e( 'Products', 'design4web' ); ?></p>
				<a class="d4w-footer-all" href="<?php echo esc_url( get_post_type_archive_link( 'd4w_product' ) ); ?>"><?php esc_html_e( 'All Products', 'design4web' ); ?><i class="bi bi-arrow-right"></i></a>
				<?php if ( $footer_products ) : ?><ul class="d4w-footer-list d4w-footer-list--secondary"><?php foreach ( $footer_products as $footer_product ) : ?><li><a href="<?php echo esc_url( get_permalink( $footer_product ) ); ?>"><?php echo esc_html( get_the_title( $footer_product ) ); ?></a></li><?php endforeach; ?></ul><?php endif; ?>
			</div>
		</div>
	</div>
	<div class="footer-bottom"><div class="container-fluid d4w-shell footer-bottom__inner"><p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'design4web' ); ?></p><p><?php esc_html_e( 'Designed & developed in India', 'design4web' ); ?> <span aria-hidden="true">✦</span></p></div></div>
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

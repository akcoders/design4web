<?php
/**
 * Reusable front-end helpers and template components.
 *
 * @package Design4Web
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the URL for a site page when it exists.
 *
 * @param string $slug     Page slug.
 * @param string $fallback Fallback URL.
 * @return string
 */
function d4w_page_url( $slug, $fallback = '' ) {
	$page = get_page_by_path( sanitize_title( $slug ) );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}
	return $fallback ? $fallback : home_url( '/' . trim( $slug, '/' ) . '/' );
}

/**
 * Get the plain-text excerpt with a reliable fallback.
 *
 * @param int $post_id Post ID.
 * @param int $words   Maximum words.
 * @return string
 */
function d4w_card_excerpt( $post_id = 0, $words = 24 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$text    = get_the_excerpt( $post_id );
	if ( ! $text ) {
		$text = get_post_field( 'post_content', $post_id );
	}
	return wp_trim_words( wp_strip_all_tags( strip_shortcodes( $text ) ), $words );
}

/**
 * Convert a newline-separated setting to clean list items.
 *
 * @param string $value Raw textarea value.
 * @return array
 */
function d4w_lines( $value ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $value );
	$lines = array_map( 'trim', $lines );
	return array_values( array_filter( $lines ) );
}

/**
 * Render the shared AJAX enquiry form.
 *
 * @param string $context Unique form context.
 */
function d4w_contact_form( $context = 'page' ) {
	$context  = sanitize_html_class( $context );
	$form_id  = 'd4w-contact-form-' . $context;
	$services = get_posts(
		array(
			'post_type'      => 'd4w_service',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	?>
	<form id="<?php echo esc_attr( $form_id ); ?>" class="d4w-contact-form d4w-ajax-contact reveal-up" novalidate>
		<input type="hidden" name="action" value="d4w_contact">
		<input type="hidden" name="source" value="<?php echo esc_attr( $context ); ?>">
		<div class="d4w-honeypot" aria-hidden="true">
			<label for="<?php echo esc_attr( $form_id ); ?>-website"><?php esc_html_e( 'Website', 'design4web' ); ?></label>
			<input id="<?php echo esc_attr( $form_id ); ?>-website" type="text" name="website" tabindex="-1" autocomplete="off">
		</div>
		<div class="row g-3">
			<div class="col-md-6"><label for="<?php echo esc_attr( $form_id ); ?>-name"><?php esc_html_e( 'Your name', 'design4web' ); ?> *</label><input id="<?php echo esc_attr( $form_id ); ?>-name" type="text" name="name" required autocomplete="name"></div>
			<div class="col-md-6"><label for="<?php echo esc_attr( $form_id ); ?>-email"><?php esc_html_e( 'Email address', 'design4web' ); ?> *</label><input id="<?php echo esc_attr( $form_id ); ?>-email" type="email" name="email" required autocomplete="email"></div>
			<div class="col-md-6"><label for="<?php echo esc_attr( $form_id ); ?>-phone"><?php esc_html_e( 'Phone number', 'design4web' ); ?></label><input id="<?php echo esc_attr( $form_id ); ?>-phone" type="tel" name="phone" autocomplete="tel"></div>
			<div class="col-md-6">
				<label for="<?php echo esc_attr( $form_id ); ?>-service"><?php esc_html_e( 'Interested in', 'design4web' ); ?></label>
				<select id="<?php echo esc_attr( $form_id ); ?>-service" name="service">
					<option value=""><?php esc_html_e( 'Select a service', 'design4web' ); ?></option>
					<?php foreach ( $services as $service ) : ?>
						<option value="<?php echo esc_attr( $service->post_title ); ?>"><?php echo esc_html( $service->post_title ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-md-6">
				<label for="<?php echo esc_attr( $form_id ); ?>-budget"><?php esc_html_e( 'Approx. budget', 'design4web' ); ?></label>
				<select id="<?php echo esc_attr( $form_id ); ?>-budget" name="budget">
					<option value=""><?php esc_html_e( 'Let’s discuss', 'design4web' ); ?></option>
					<option value="₹25k–₹50k">₹25k–₹50k</option>
					<option value="₹50k–₹1L">₹50k–₹1L</option>
					<option value="₹1L–₹3L">₹1L–₹3L</option>
					<option value="₹3L+">₹3L+</option>
				</select>
			</div>
			<div class="col-md-6"><label for="<?php echo esc_attr( $form_id ); ?>-timeline"><?php esc_html_e( 'Preferred timeline', 'design4web' ); ?></label><input id="<?php echo esc_attr( $form_id ); ?>-timeline" type="text" name="timeline" placeholder="<?php esc_attr_e( 'e.g. 6–8 weeks', 'design4web' ); ?>"></div>
			<div class="col-12"><label for="<?php echo esc_attr( $form_id ); ?>-message"><?php esc_html_e( 'Tell us about your project', 'design4web' ); ?> *</label><textarea id="<?php echo esc_attr( $form_id ); ?>-message" name="message" rows="4" required></textarea></div>
			<div class="col-12 d-flex flex-wrap align-items-center gap-3"><button class="d4w-btn d4w-btn--light magnetic" type="submit"><span><?php esc_html_e( 'Send enquiry', 'design4web' ); ?></span><i class="bi bi-arrow-up-right"></i></button><div class="d4w-form-status" role="status" aria-live="polite"></div></div>
		</div>
	</form>
	<?php
}

/**
 * Render the shared conversion panel.
 *
 * @param string $context Unique form context.
 */
function d4w_contact_panel( $context = 'panel' ) {
	?>
	<section class="d4w-contact section-space d4w-motion-section" id="contact">
		<div class="container-fluid d4w-shell">
			<div class="d4w-contact-card d4w-hover-surface">
				<div class="d4w-contact-shape d4w-parallax" data-speed="0.025"></div>
				<div class="row g-5 position-relative align-items-center">
					<div class="col-lg-6">
						<p class="d4w-section-label d4w-section-label--light reveal-up"><span>07</span><?php esc_html_e( 'Start a conversation', 'design4web' ); ?></p>
						<h2 class="d4w-display text-white reveal-text"><?php echo esc_html( d4w_get_option( 'cta_title' ) ); ?></h2>
						<p class="d4w-lead text-white-50 reveal-up"><?php echo esc_html( d4w_get_option( 'cta_text' ) ); ?></p>
						<div class="d4w-contact-direct reveal-up"><a href="mailto:<?php echo esc_attr( d4w_get_option( 'contact_email' ) ); ?>"><?php echo esc_html( d4w_get_option( 'contact_email' ) ); ?></a><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', d4w_get_option( 'phone' ) ) ); ?>"><?php echo esc_html( d4w_get_option( 'phone' ) ); ?></a></div>
					</div>
					<div class="col-lg-6"><?php d4w_contact_form( $context ); ?></div>
				</div>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Return a featured or bundled fallback image URL.
 *
 * @param int    $post_id  Post ID.
 * @param string $fallback Bundled filename.
 * @param string $size     Image size.
 * @return string
 */
function d4w_feature_image_url( $post_id, $fallback, $size = 'large' ) {
	if ( has_post_thumbnail( $post_id ) ) {
		$image = get_the_post_thumbnail_url( $post_id, $size );
		if ( $image ) {
			return $image;
		}
	}
	$bundled = get_post_meta( $post_id, '_d4w_bundled_image', true );
	if ( $bundled && preg_match( '#^(?:legacy/)?[a-z0-9_-]+\.jpg$#', $bundled ) && file_exists( D4W_DIR . '/assets/images/' . $bundled ) ) {
		return D4W_URI . '/assets/images/' . $bundled;
	}
	return D4W_URI . '/assets/images/' . ltrim( $fallback, '/' );
}

<?php
/**
 * Google Maps review settings.
 *
 * Reviews are fetched in the visitor's browser with the Places library. The
 * theme stores only the API configuration and never imports Google review
 * content into WordPress.
 *
 * @package Design4Web
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize a Google Maps browser key.
 *
 * @param string $value Submitted value.
 * @return string
 */
function d4w_sanitize_google_maps_key( $value ) {
	return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $value );
}

/**
 * Sanitize a Places API place ID.
 *
 * @param string $value Submitted value.
 * @return string
 */
function d4w_sanitize_google_place_id( $value ) {
	return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $value );
}

/**
 * Sanitize a two-letter Google region code.
 *
 * @param string $value Submitted value.
 * @return string
 */
function d4w_sanitize_google_region( $value ) {
	$value = strtoupper( preg_replace( '/[^A-Za-z]/', '', (string) $value ) );
	return 2 === strlen( $value ) ? $value : 'IN';
}

/**
 * Register review integration options.
 */
function d4w_register_google_review_settings() {
	register_setting(
		'd4w_google_reviews',
		'd4w_google_reviews_enabled',
		array(
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	register_setting(
		'd4w_google_reviews',
		'd4w_google_maps_api_key',
		array(
			'type'              => 'string',
			'default'           => '',
			'sanitize_callback' => 'd4w_sanitize_google_maps_key',
		)
	);
	register_setting(
		'd4w_google_reviews',
		'd4w_google_place_id',
		array(
			'type'              => 'string',
			'default'           => '',
			'sanitize_callback' => 'd4w_sanitize_google_place_id',
		)
	);
	register_setting(
		'd4w_google_reviews',
		'd4w_google_reviews_language',
		array(
			'type'              => 'string',
			'default'           => 'en',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	register_setting(
		'd4w_google_reviews',
		'd4w_google_reviews_region',
		array(
			'type'              => 'string',
			'default'           => 'IN',
			'sanitize_callback' => 'd4w_sanitize_google_region',
		)
	);
}
add_action( 'admin_init', 'd4w_register_google_review_settings' );

/**
 * Return the configured browser key, supporting a wp-config.php override.
 *
 * @return string
 */
function d4w_google_maps_api_key() {
	if ( defined( 'D4W_GOOGLE_MAPS_API_KEY' ) && D4W_GOOGLE_MAPS_API_KEY ) {
		return d4w_sanitize_google_maps_key( D4W_GOOGLE_MAPS_API_KEY );
	}
	return d4w_sanitize_google_maps_key( get_option( 'd4w_google_maps_api_key', '' ) );
}

/**
 * Whether the live Google review integration has everything it needs.
 *
 * @return bool
 */
function d4w_google_reviews_is_configured() {
	return (bool) get_option( 'd4w_google_reviews_enabled', false )
		&& '' !== d4w_google_maps_api_key()
		&& '' !== d4w_sanitize_google_place_id( get_option( 'd4w_google_place_id', '' ) );
}

/**
 * Public configuration passed to the front-end loader.
 *
 * The API key is intentionally a browser key and must be restricted to the
 * site's HTTP referrers in Google Cloud Console.
 *
 * @return array
 */
function d4w_google_reviews_public_config() {
	return array(
		'enabled'  => d4w_google_reviews_is_configured(),
		'apiKey'   => d4w_google_maps_api_key(),
		'placeId'  => d4w_sanitize_google_place_id( get_option( 'd4w_google_place_id', '' ) ),
		'language' => sanitize_key( get_option( 'd4w_google_reviews_language', 'en' ) ) ?: 'en',
		'region'   => d4w_sanitize_google_region( get_option( 'd4w_google_reviews_region', 'IN' ) ),
	);
}

/**
 * Add Google review settings below the Testimonials collection.
 */
function d4w_google_reviews_admin_menu() {
	add_submenu_page(
		'edit.php?post_type=d4w_testimonial',
		__( 'Google Reviews', 'design4web' ),
		__( 'Google Reviews', 'design4web' ),
		'manage_options',
		'd4w-google-reviews',
		'd4w_google_reviews_settings_page'
	);
}
add_action( 'admin_menu', 'd4w_google_reviews_admin_menu' );

/**
 * Render the Google review integration settings page.
 */
function d4w_google_reviews_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$configured = d4w_google_reviews_is_configured();
	$constant   = defined( 'D4W_GOOGLE_MAPS_API_KEY' ) && D4W_GOOGLE_MAPS_API_KEY;
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Google Reviews', 'design4web' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Show live, original Google Maps reviews inside the homepage review cards. Google returns up to five reviews ordered by relevance.', 'design4web' ); ?></p>

		<div class="notice <?php echo $configured ? 'notice-success' : 'notice-warning'; ?> inline" style="max-width:900px;margin:20px 0">
			<p><strong><?php echo $configured ? esc_html__( 'Ready:', 'design4web' ) : esc_html__( 'Setup required:', 'design4web' ); ?></strong>
			<?php echo $configured ? esc_html__( 'The homepage will request current Google reviews directly in each visitor’s browser.', 'design4web' ) : esc_html__( 'Add the browser API key and Place ID below, then enable live reviews.', 'design4web' ); ?>
			<?php if ( $configured ) : ?> <a href="<?php echo esc_url( home_url( '/#reviews' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open homepage reviews', 'design4web' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'design4web' ); ?></span></a><?php endif; ?></p>
		</div>

		<form method="post" action="options.php">
			<?php settings_fields( 'd4w_google_reviews' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Enable live Google reviews', 'design4web' ); ?></th>
					<td><input type="hidden" name="d4w_google_reviews_enabled" value="0"><label><input type="checkbox" name="d4w_google_reviews_enabled" value="1" <?php checked( get_option( 'd4w_google_reviews_enabled', false ) ); ?>> <?php esc_html_e( 'Replace locally managed testimonial cards when Google responds successfully', 'design4web' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><label for="d4w_google_maps_api_key"><?php esc_html_e( 'Maps JavaScript API key', 'design4web' ); ?></label></th>
					<td>
						<input class="regular-text" type="password" id="d4w_google_maps_api_key" name="d4w_google_maps_api_key" value="<?php echo esc_attr( get_option( 'd4w_google_maps_api_key', '' ) ); ?>" autocomplete="new-password" <?php disabled( $constant ); ?>>
						<?php if ( $constant ) : ?><p class="description"><?php esc_html_e( 'The API key is supplied by D4W_GOOGLE_MAPS_API_KEY in wp-config.php.', 'design4web' ); ?></p><?php else : ?><p class="description"><?php esc_html_e( 'Enable Maps JavaScript API and Places API (New), enable billing, and restrict this browser key to your live and staging website HTTP referrers.', 'design4web' ); ?></p><?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="d4w_google_place_id"><?php esc_html_e( 'Business Place ID', 'design4web' ); ?></label></th>
					<td><input class="regular-text code" type="text" id="d4w_google_place_id" name="d4w_google_place_id" value="<?php echo esc_attr( get_option( 'd4w_google_place_id', '' ) ); ?>" placeholder="ChIJ..."><p class="description"><a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How to find a Place ID', 'design4web' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens in a new tab)', 'design4web' ); ?></span></a></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="d4w_google_reviews_language"><?php esc_html_e( 'Review language', 'design4web' ); ?></label></th>
					<td><input class="small-text" type="text" id="d4w_google_reviews_language" name="d4w_google_reviews_language" value="<?php echo esc_attr( get_option( 'd4w_google_reviews_language', 'en' ) ); ?>" maxlength="10"><p class="description"><?php esc_html_e( 'Google language code, for example en or hi. Original review text is shown whenever available.', 'design4web' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="d4w_google_reviews_region"><?php esc_html_e( 'Region', 'design4web' ); ?></label></th>
					<td><input class="small-text" type="text" id="d4w_google_reviews_region" name="d4w_google_reviews_region" value="<?php echo esc_attr( get_option( 'd4w_google_reviews_region', 'IN' ) ); ?>" maxlength="2"><p class="description"><?php esc_html_e( 'Two-letter region code; use IN for India.', 'design4web' ); ?></p></td>
				</tr>
			</table>
			<?php submit_button( __( 'Save Google review settings', 'design4web' ) ); ?>
		</form>

		<div class="card" style="max-width:900px;margin-top:24px;padding:20px 24px">
			<h2><?php esc_html_e( 'Required Google Cloud setup', 'design4web' ); ?></h2>
			<ol>
				<li><?php esc_html_e( 'Create or select a Google Cloud project and attach a billing account.', 'design4web' ); ?></li>
				<li><?php esc_html_e( 'Enable Maps JavaScript API and Places API (New).', 'design4web' ); ?></li>
				<li><?php esc_html_e( 'Create a browser API key. Restrict it to your website HTTP referrers and only the two APIs above.', 'design4web' ); ?></li>
				<li><?php esc_html_e( 'Save the browser key and your Google Business Profile Place ID here, then enable live reviews.', 'design4web' ); ?></li>
			</ol>
			<p><?php esc_html_e( 'If Google is unavailable or the configuration is invalid, the locally managed Testimonials remain visible as a safe fallback.', 'design4web' ); ?></p>
		</div>
	</div>
	<?php
}

<?php
/**
 * Product detail page.
 *
 * @package Design4Web
 */
get_header();
while ( have_posts() ) :
	the_post();
	$product_id = get_the_ID();
	$icon       = get_post_meta( $product_id, '_d4w_product_icon', true ) ?: 'bi-box';
	$kicker     = get_post_meta( $product_id, '_d4w_product_kicker', true ) ?: __( 'Connected customer experience', 'design4web' );
	$accent     = sanitize_hex_color( get_post_meta( $product_id, '_d4w_product_accent', true ) ) ?: '#7c4dff';
	$features   = d4w_lines( get_post_meta( $product_id, '_d4w_product_features', true ) );
	$step_lines = d4w_lines( get_post_meta( $product_id, '_d4w_product_steps', true ) );
	$note       = get_post_meta( $product_id, '_d4w_product_note', true );
	$steps      = array();
	foreach ( $step_lines as $step_line ) {
		$parts   = array_map( 'trim', explode( '|', $step_line, 2 ) );
		$steps[] = array( $parts[0], isset( $parts[1] ) ? $parts[1] : '' );
	}
	?>
	<article <?php post_class( 'd4w-product-detail' ); ?> style="--product-accent:<?php echo esc_attr( $accent ); ?>">
		<header class="d4w-product-detail__hero d4w-motion-section">
			<div class="container-fluid d4w-shell position-relative"><a class="d4w-back-link hero-reveal" href="<?php echo esc_url( get_post_type_archive_link( 'd4w_product' ) ); ?>"><i class="bi bi-arrow-left"></i><?php esc_html_e( 'All products', 'design4web' ); ?></a><div class="row align-items-center g-5"><div class="col-xl-7"><p class="d4w-section-label d4w-section-label--light hero-reveal"><span><i class="bi <?php echo esc_attr( $icon ); ?>"></i></span><?php echo esc_html( $kicker ); ?></p><h1 class="d4w-page-title"><?php the_title(); ?></h1><p class="d4w-product-detail__lead hero-reveal"><?php echo esc_html( d4w_card_excerpt( $product_id, 38 ) ); ?></p><div class="d-flex flex-wrap gap-3 hero-reveal"><a class="d4w-btn d4w-btn--light magnetic" href="#product-enquiry"><span><?php esc_html_e( 'Plan this product', 'design4web' ); ?></span><i class="bi bi-arrow-down-right"></i></a><a class="d4w-btn d4w-btn--ghost magnetic" href="<?php echo esc_url( d4w_page_url( 'pricing' ) ); ?>"><span><?php esc_html_e( 'View pricing', 'design4web' ); ?></span><i class="bi bi-arrow-right"></i></a></div></div><div class="col-xl-5"><div class="d4w-product-device hero-reveal" aria-hidden="true"><div class="d4w-product-device__bar"><span></span><span></span><span></span></div><div class="d4w-product-device__brand"><i class="bi <?php echo esc_attr( $icon ); ?>"></i><div><strong><?php the_title(); ?></strong><small><?php esc_html_e( 'Design4web connected experience', 'design4web' ); ?></small></div></div><div class="d4w-product-device__message"><span></span><span></span><span></span></div><div class="d4w-product-device__message d4w-product-device__message--reply"><span></span><span></span></div><div class="d4w-product-device__action"><i class="bi bi-stars"></i><?php esc_html_e( 'Smart journey active', 'design4web' ); ?></div></div></div></div></div>
		</header>

		<?php if ( $features ) : ?><div class="d4w-product-ticker" aria-label="<?php esc_attr_e( 'Product capabilities', 'design4web' ); ?>"><div class="d4w-product-ticker__track"><?php for ( $loop = 0; $loop < 2; $loop++ ) : ?><div><?php foreach ( $features as $feature ) : ?><span><?php echo esc_html( $feature ); ?><i>✦</i></span><?php endforeach; ?></div><?php endfor; ?></div></div><?php endif; ?>

		<section class="d4w-product-story section-space-sm"><div class="container-fluid d4w-shell"><div class="row g-5"><div class="col-lg-4"><p class="d4w-section-label reveal-up"><span>01</span><?php esc_html_e( 'The opportunity', 'design4web' ); ?></p><div class="d4w-product-story__icon d4w-parallax" data-speed="0.03"><i class="bi <?php echo esc_attr( $icon ); ?>"></i></div></div><div class="col-lg-8"><div class="entry-content d4w-prose reveal-up"><?php the_content(); ?></div><?php if ( $note ) : ?><div class="d4w-product-note reveal-up"><i class="bi bi-info-circle"></i><p><?php echo esc_html( $note ); ?></p></div><?php endif; ?></div></div></div></section>

		<?php if ( $features ) : ?><section class="d4w-product-benefits section-space-sm"><div class="container-fluid d4w-shell"><div class="row align-items-end g-4 mb-5"><div class="col-lg-4"><p class="d4w-section-label d4w-section-label--light reveal-up"><span>02</span><?php esc_html_e( 'What is included', 'design4web' ); ?></p></div><div class="col-lg-8"><h2 class="d4w-display text-white reveal-text"><?php esc_html_e( 'Built as a useful system—not an isolated feature.', 'design4web' ); ?></h2></div></div><div class="row g-3"><?php foreach ( $features as $index => $feature ) : ?><div class="col-md-6 col-xl-4"><article class="d4w-product-benefit d4w-hover-card reveal-up"><span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><i class="bi bi-check2-circle"></i><h3><?php echo esc_html( $feature ); ?></h3></article></div><?php endforeach; ?></div></div></section><?php endif; ?>

		<?php if ( $steps ) : ?><section class="d4w-product-workflow section-space-sm"><div class="container-fluid d4w-shell"><div class="row g-5"><div class="col-lg-4"><p class="d4w-section-label reveal-up"><span>03</span><?php esc_html_e( 'How it works', 'design4web' ); ?></p><h2 class="d4w-display reveal-text"><?php esc_html_e( 'A clear path from idea to active journey.', 'design4web' ); ?></h2></div><div class="col-lg-8"><div class="d4w-product-steps"><?php foreach ( $steps as $index => $step ) : ?><article class="d4w-product-step reveal-up"><span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><div><h3><?php echo esc_html( $step[0] ); ?></h3><p><?php echo esc_html( $step[1] ); ?></p></div><i class="bi bi-arrow-down-right"></i></article><?php endforeach; ?></div></div></div></div></section><?php endif; ?>

		<?php
		$related = new WP_Query( array( 'post_type' => 'd4w_product', 'post_status' => 'publish', 'posts_per_page' => 3, 'post__not_in' => array( $product_id ), 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
		if ( $related->have_posts() ) : ?>
			<section class="d4w-related section-space-sm"><div class="container-fluid d4w-shell"><div class="row align-items-end g-4 mb-5"><div class="col-lg-4"><p class="d4w-section-label reveal-up"><span>04</span><?php esc_html_e( 'Build the stack', 'design4web' ); ?></p></div><div class="col-lg-8"><h2 class="d4w-display reveal-text"><?php esc_html_e( 'Connect this with another customer touchpoint.', 'design4web' ); ?></h2></div></div><div class="row g-4"><?php while ( $related->have_posts() ) : $related->the_post(); $related_icon = get_post_meta( get_the_ID(), '_d4w_product_icon', true ) ?: 'bi-box'; ?><div class="col-md-6 col-xl-4"><a class="d4w-related-card d4w-hover-card reveal-up" href="<?php the_permalink(); ?>"><span><i class="bi <?php echo esc_attr( $related_icon ); ?>"></i></span><h3><?php the_title(); ?></h3><p><?php echo esc_html( d4w_card_excerpt( get_the_ID(), 18 ) ); ?></p><i class="bi bi-arrow-up-right"></i></a></div><?php endwhile; wp_reset_postdata(); ?></div></div></section>
		<?php endif; ?>
	</article>
<?php endwhile; ?>
<div id="product-enquiry"><?php d4w_contact_panel( 'product' ); ?></div>
<?php get_footer(); ?>

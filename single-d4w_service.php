<?php
/**
 * Single service detail.
 *
 * @package Design4Web
 */
get_header();
while ( have_posts() ) :
	the_post();
	$service_id   = get_the_ID();
	$icon         = get_post_meta( $service_id, '_d4w_icon', true ) ?: 'bi-asterisk';
	$label        = get_post_meta( $service_id, '_d4w_short_label', true ) ?: __( 'Digital service', 'design4web' );
	$duration     = get_post_meta( $service_id, '_d4w_duration', true );
	$price        = get_post_meta( $service_id, '_d4w_starting_price', true );
	$deliverables = d4w_lines( get_post_meta( $service_id, '_d4w_deliverables', true ) );
	$image        = d4w_feature_image_url( $service_id, 'service-development.jpg', 'full' );
	?>
	<article <?php post_class( 'd4w-service-single' ); ?>>
		<header class="d4w-inner-hero d4w-service-single__hero">
			<div class="container-fluid d4w-shell position-relative">
				<a class="d4w-back-link hero-reveal" href="<?php echo esc_url( get_post_type_archive_link( 'd4w_service' ) ); ?>"><i class="bi bi-arrow-left"></i><?php esc_html_e( 'All services', 'design4web' ); ?></a>
				<div class="row align-items-end g-5">
					<div class="col-xl-9"><p class="d4w-section-label d4w-section-label--light hero-reveal"><span><i class="bi <?php echo esc_attr( $icon ); ?>"></i></span><?php echo esc_html( $label ); ?></p><h1 class="d4w-page-title"><?php the_title(); ?></h1></div>
					<div class="col-xl-3"><p class="d4w-inner-hero__lead hero-reveal"><?php echo esc_html( d4w_card_excerpt( $service_id, 32 ) ); ?></p></div>
				</div>
			</div>
		</header>

		<section class="d4w-service-detail section-space-sm">
			<div class="container-fluid d4w-shell">
				<div class="d4w-service-detail__visual d4w-image-curtain reveal-up"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="1500" height="900"></div>
				<div class="row g-5 d4w-service-detail__body">
					<aside class="col-lg-4">
						<div class="d4w-detail-panel reveal-up">
							<p class="d4w-detail-panel__label"><?php esc_html_e( 'Project essentials', 'design4web' ); ?></p>
							<?php if ( $duration ) : ?><div><span><?php esc_html_e( 'Typical timeline', 'design4web' ); ?></span><strong><?php echo esc_html( $duration ); ?></strong></div><?php endif; ?>
							<?php if ( $price ) : ?><div><span><?php esc_html_e( 'Investment', 'design4web' ); ?></span><strong><?php echo esc_html( $price ); ?></strong></div><?php endif; ?>
							<div><span><?php esc_html_e( 'Delivery', 'design4web' ); ?></span><strong><?php esc_html_e( 'Collaborative & transparent', 'design4web' ); ?></strong></div>
							<a class="d4w-btn d4w-btn--gradient magnetic" href="<?php echo esc_url( d4w_page_url( 'contact', home_url( '/#contact' ) ) ); ?>"><span><?php esc_html_e( 'Discuss this service', 'design4web' ); ?></span><i class="bi bi-arrow-up-right"></i></a>
						</div>
					</aside>
					<div class="col-lg-8">
						<div class="entry-content d4w-prose reveal-up"><?php the_content(); ?></div>
						<?php if ( $deliverables ) : ?>
							<div class="d4w-deliverables reveal-up"><p class="d4w-section-kicker"><?php esc_html_e( 'Typical deliverables', 'design4web' ); ?></p><div class="d4w-deliverables__grid"><?php foreach ( $deliverables as $index => $item ) : ?><div><span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><strong><?php echo esc_html( $item ); ?></strong><i class="bi bi-check2"></i></div><?php endforeach; ?></div></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>

		<?php
		$related = new WP_Query( array( 'post_type' => 'd4w_service', 'post_status' => 'publish', 'post__not_in' => array( $service_id ), 'posts_per_page' => 3, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ), 'no_found_rows' => true ) );
		if ( $related->have_posts() ) :
			?>
			<section class="d4w-related section-space-sm"><div class="container-fluid d4w-shell"><div class="row align-items-end mb-5 g-4"><div class="col-lg-4"><p class="d4w-section-label reveal-up"><span>Next</span><?php esc_html_e( 'More capabilities', 'design4web' ); ?></p></div><div class="col-lg-8"><h2 class="d4w-display reveal-text"><?php esc_html_e( 'A connected digital toolkit.', 'design4web' ); ?></h2></div></div><div class="row g-4">
				<?php while ( $related->have_posts() ) : $related->the_post(); $related_icon = get_post_meta( get_the_ID(), '_d4w_icon', true ) ?: 'bi-asterisk'; ?>
					<div class="col-md-6 col-xl-4"><a class="d4w-related-card d4w-hover-card reveal-up" href="<?php the_permalink(); ?>"><span><i class="bi <?php echo esc_attr( $related_icon ); ?>"></i></span><h3><?php the_title(); ?></h3><p><?php echo esc_html( d4w_card_excerpt( get_the_ID(), 18 ) ); ?></p><i class="bi bi-arrow-up-right"></i></a></div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div></div></section>
		<?php endif; ?>
	</article>
<?php endwhile; ?>
<?php d4w_contact_panel( 'service-detail' ); ?>
<?php get_footer(); ?>

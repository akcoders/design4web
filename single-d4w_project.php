<?php
/**
 * Single project case study.
 *
 * @package Design4Web
 */
get_header();
while ( have_posts() ) :
	the_post();
	$project_id       = get_the_ID();
	$terms            = get_the_terms( $project_id, 'd4w_project_type' );
	$category         = $terms && ! is_wp_error( $terms ) ? implode( ' · ', wp_list_pluck( $terms, 'name' ) ) : __( 'Digital Experience', 'design4web' );
	$client           = get_post_meta( $project_id, '_d4w_client', true );
	$year             = get_post_meta( $project_id, '_d4w_year', true ) ?: get_the_date( 'Y' );
	$project_url      = get_post_meta( $project_id, '_d4w_url', true );
	$industry         = get_post_meta( $project_id, '_d4w_industry', true );
	$services         = get_post_meta( $project_id, '_d4w_services', true );
	$challenge        = get_post_meta( $project_id, '_d4w_challenge', true );
	$outcome          = get_post_meta( $project_id, '_d4w_outcome', true );
	$metric_one       = get_post_meta( $project_id, '_d4w_metric_one', true );
	$metric_one_label = get_post_meta( $project_id, '_d4w_metric_one_label', true );
	$metric_two       = get_post_meta( $project_id, '_d4w_metric_two', true );
	$metric_two_label = get_post_meta( $project_id, '_d4w_metric_two_label', true );
	$image            = d4w_feature_image_url( $project_id, 'project-1.jpg', 'full' );
	?>
	<article <?php post_class( 'd4w-case-study' ); ?>>
		<header class="d4w-inner-hero d4w-case-study__hero">
			<div class="container-fluid d4w-shell position-relative">
				<a class="d4w-back-link hero-reveal" href="<?php echo esc_url( get_post_type_archive_link( 'd4w_project' ) ); ?>"><i class="bi bi-arrow-left"></i><?php esc_html_e( 'All work', 'design4web' ); ?></a>
				<p class="d4w-section-label d4w-section-label--light hero-reveal"><span><?php echo esc_html( $year ); ?></span><?php echo esc_html( $category ); ?></p>
				<h1 class="d4w-page-title"><?php the_title(); ?></h1>
				<div class="d4w-case-facts hero-reveal">
					<?php if ( $client ) : ?><div><span><?php esc_html_e( 'Client', 'design4web' ); ?></span><strong><?php echo esc_html( $client ); ?></strong></div><?php endif; ?>
					<?php if ( $industry ) : ?><div><span><?php esc_html_e( 'Industry', 'design4web' ); ?></span><strong><?php echo esc_html( $industry ); ?></strong></div><?php endif; ?>
					<?php if ( $services ) : ?><div><span><?php esc_html_e( 'Services', 'design4web' ); ?></span><strong><?php echo esc_html( $services ); ?></strong></div><?php endif; ?>
					<div><span><?php esc_html_e( 'Year', 'design4web' ); ?></span><strong><?php echo esc_html( $year ); ?></strong></div>
				</div>
			</div>
		</header>

		<div class="d4w-case-visual d4w-image-curtain"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="1800" height="1100"></div>

		<section class="d4w-case-story section-space-sm">
			<div class="container-fluid d4w-shell">
				<div class="row g-5">
					<div class="col-lg-4"><p class="d4w-section-label reveal-up"><span>01</span><?php esc_html_e( 'The brief', 'design4web' ); ?></p><h2 class="d4w-display reveal-text"><?php esc_html_e( 'From challenge to a focused digital answer.', 'design4web' ); ?></h2></div>
					<div class="col-lg-7 offset-lg-1">
						<?php if ( $challenge ) : ?><div class="d4w-case-block reveal-up"><span><?php esc_html_e( 'Challenge', 'design4web' ); ?></span><p><?php echo esc_html( $challenge ); ?></p></div><?php endif; ?>
						<div class="entry-content d4w-prose reveal-up"><?php the_content(); ?></div>
						<?php if ( $outcome ) : ?><div class="d4w-case-block reveal-up"><span><?php esc_html_e( 'Outcome', 'design4web' ); ?></span><p><?php echo esc_html( $outcome ); ?></p></div><?php endif; ?>
						<?php if ( $project_url ) : ?><a class="d4w-btn d4w-btn--gradient magnetic reveal-up" href="<?php echo esc_url( $project_url ); ?>" target="_blank" rel="noopener noreferrer"><span><?php esc_html_e( 'Visit live project', 'design4web' ); ?></span><i class="bi bi-arrow-up-right"></i></a><?php endif; ?>
					</div>
				</div>

				<?php if ( $metric_one || $metric_two ) : ?>
					<div class="d4w-case-results reveal-up">
						<?php if ( $metric_one ) : ?><div class="d4w-case-metric"><strong><?php echo esc_html( $metric_one ); ?></strong><span><?php echo esc_html( $metric_one_label ?: __( 'Primary result', 'design4web' ) ); ?></span></div><?php endif; ?>
						<?php if ( $metric_two ) : ?><div class="d4w-case-metric"><strong><?php echo esc_html( $metric_two ); ?></strong><span><?php echo esc_html( $metric_two_label ?: __( 'Secondary result', 'design4web' ) ); ?></span></div><?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
	</article>
	<?php
	$next = get_next_post( false, '', 'd4w_project_type' );
	if ( ! $next ) {
		$next_items = get_posts( array( 'post_type' => 'd4w_project', 'post_status' => 'publish', 'post__not_in' => array( $project_id ), 'posts_per_page' => 1, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
		$next       = $next_items ? $next_items[0] : null;
	}
	if ( $next ) :
		$next_image = d4w_feature_image_url( $next->ID, 'project-2.jpg', 'large' );
		?>
		<section class="d4w-next-project"><a href="<?php echo esc_url( get_permalink( $next ) ); ?>"><img src="<?php echo esc_url( $next_image ); ?>" alt="" loading="lazy"><span><?php esc_html_e( 'Next project', 'design4web' ); ?></span><strong><?php echo esc_html( get_the_title( $next ) ); ?></strong><i class="bi bi-arrow-right"></i></a></section>
	<?php endif; ?>
<?php endwhile; ?>
<?php get_footer(); ?>

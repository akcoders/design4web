<?php
/**
 * Filterable project archive.
 *
 * @package Design4Web
 */
get_header();
$project_terms = get_terms( array( 'taxonomy' => 'd4w_project_type', 'hide_empty' => true ) );
$is_term       = is_tax( 'd4w_project_type' );
$project_count = wp_count_posts( 'd4w_project' );
$published     = $project_count ? (int) $project_count->publish : 0;
$term_count    = is_wp_error( $project_terms ) ? 0 : count( $project_terms );
?>

<section class="d4w-inner-hero d4w-work-hero d4w-motion-section">
	<div class="d4w-work-hero__word" aria-hidden="true">WORK</div>
	<div class="container-fluid d4w-shell position-relative">
		<p class="d4w-section-label d4w-section-label--light hero-reveal"><span>Portfolio</span><?php esc_html_e( 'Selected work & case studies', 'design4web' ); ?></p>
		<div class="row align-items-end g-5"><div class="col-xl-8"><h1 class="d4w-page-title"><?php echo $is_term ? esc_html( single_term_title( '', false ) ) : esc_html__( 'Ideas made useful, visible and memorable.', 'design4web' ); ?></h1></div><div class="col-xl-4"><p class="d4w-inner-hero__lead hero-reveal"><?php esc_html_e( 'A growing archive of websites, commerce experiences, visual identities and campaigns shaped around real business needs.', 'design4web' ); ?></p><div class="d4w-work-stats hero-reveal"><div><strong><?php echo esc_html( $published ); ?>+</strong><small><?php esc_html_e( 'projects', 'design4web' ); ?></small></div><div><strong><?php echo esc_html( $term_count ); ?></strong><small><?php esc_html_e( 'capabilities', 'design4web' ); ?></small></div><div><strong><?php echo esc_html( d4w_get_option( 'experience_years' ) ); ?>+</strong><small><?php esc_html_e( 'years', 'design4web' ); ?></small></div></div></div></div>
	</div>
</section>

<section class="d4w-work-archive section-space-sm">
	<div class="container-fluid d4w-shell">
		<?php if ( $is_term ) : ?>
			<div class="d4w-project-filters reveal-up" aria-label="<?php esc_attr_e( 'Project archive navigation', 'design4web' ); ?>"><a href="<?php echo esc_url( get_post_type_archive_link( 'd4w_project' ) ); ?>"><?php esc_html_e( 'All work', 'design4web' ); ?></a><span class="is-active"><?php single_term_title(); ?></span></div>
		<?php elseif ( ! is_wp_error( $project_terms ) && $project_terms ) : ?>
			<div class="d4w-project-filters reveal-up" role="group" aria-label="<?php esc_attr_e( 'Filter projects', 'design4web' ); ?>"><button class="is-active" type="button" data-project-filter="*" aria-pressed="true"><?php esc_html_e( 'All work', 'design4web' ); ?><span><?php echo esc_html( $published ); ?></span></button><?php foreach ( $project_terms as $term ) : ?><button type="button" data-project-filter="<?php echo esc_attr( $term->slug ); ?>" aria-pressed="false"><?php echo esc_html( $term->name ); ?><span><?php echo esc_html( $term->count ); ?></span></button><?php endforeach; ?></div>
		<?php endif; ?>

		<div class="row g-4 d4w-filter-grid" aria-live="polite">
			<?php
			$project_index = 0;
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					++$project_index;
					$terms      = get_the_terms( get_the_ID(), 'd4w_project_type' );
					$term_names = $terms && ! is_wp_error( $terms ) ? wp_list_pluck( $terms, 'name' ) : array( __( 'Digital Experience', 'design4web' ) );
					$term_slugs = $terms && ! is_wp_error( $terms ) ? wp_list_pluck( $terms, 'slug' ) : array();
					$image      = d4w_feature_image_url( get_the_ID(), 'project-' . ( ( ( $project_index - 1 ) % 5 ) + 1 ) . '.jpg', 'd4w-project' );
					$year       = get_post_meta( get_the_ID(), '_d4w_year', true ) ?: get_the_date( 'Y' );
					$services   = get_post_meta( get_the_ID(), '_d4w_services', true );
					$featured   = 1 === $project_index;
					?>
					<div class="<?php echo $featured ? 'col-12' : 'col-md-6 col-xl-4'; ?> d4w-filter-item" data-project-types="<?php echo esc_attr( implode( ' ', $term_slugs ) ); ?>">
						<article class="d4w-work-card reveal-up <?php echo $featured ? 'd4w-work-card--featured' : ''; ?>">
							<a class="d4w-work-card__media d4w-image-curtain" href="<?php the_permalink(); ?>" data-cursor-label="VIEW"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="1200" height="900" loading="lazy"><span class="d4w-work-card__index"><?php echo esc_html( str_pad( (string) $project_index, 2, '0', STR_PAD_LEFT ) ); ?></span><span class="d4w-work-card__open"><i class="bi bi-arrow-up-right"></i></span></a>
							<div class="d4w-work-card__copy"><div><p><?php echo esc_html( implode( ' · ', $term_names ) ); ?></p><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php if ( $featured ) : ?><div><?php echo esc_html( d4w_card_excerpt( get_the_ID(), 30 ) ); ?></div><?php endif; ?></div><aside><strong><?php echo esc_html( $year ); ?></strong><?php if ( $services ) : ?><small><?php echo esc_html( $services ); ?></small><?php endif; ?></aside></div>
						</article>
					</div>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="col-12 d4w-empty-state"><h2><?php esc_html_e( 'New work is coming soon.', 'design4web' ); ?></h2><p><?php esc_html_e( 'Start a conversation about what we can create together.', 'design4web' ); ?></p></div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php d4w_contact_panel( 'work' ); ?>
<?php get_footer(); ?>

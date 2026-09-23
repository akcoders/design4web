<?php
/**
 * Filterable clients and project archive.
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

<section class="d4w-clients-hero d4w-motion-section">
	<div class="d4w-clients-hero__glow" aria-hidden="true"></div>
	<div class="d4w-clients-hero__word" aria-hidden="true">CLIENTS</div>
	<div class="container-fluid d4w-shell position-relative">
		<p class="d4w-section-label d4w-section-label--light hero-reveal"><span><?php esc_html_e( 'Clients', 'design4web' ); ?></span><?php esc_html_e( 'Selected projects & case studies', 'design4web' ); ?></p>
		<h1 class="d4w-page-title d4w-clients-hero__title"><?php echo $is_term ? esc_html( single_term_title( '', false ) ) : esc_html__( 'Work that earns attention.', 'design4web' ); ?></h1>
		<div class="d4w-clients-hero__foot hero-reveal">
			<p><?php esc_html_e( 'Websites, identities and campaigns shaped around real audiences, useful outcomes and long-term client relationships.', 'design4web' ); ?></p>
			<div class="d4w-clients-hero__stats" aria-label="<?php esc_attr_e( 'Portfolio summary', 'design4web' ); ?>">
				<div><strong><?php echo esc_html( $published ); ?>+</strong><span><?php esc_html_e( 'Projects', 'design4web' ); ?></span></div>
				<div><strong><?php echo esc_html( $term_count ); ?></strong><span><?php esc_html_e( 'Capabilities', 'design4web' ); ?></span></div>
				<div><strong><?php echo esc_html( d4w_get_option( 'experience_years' ) ); ?>+</strong><span><?php esc_html_e( 'Years', 'design4web' ); ?></span></div>
			</div>
		</div>
	</div>
</section>

<section class="d4w-work-archive d4w-clients-archive section-space-sm">
	<div class="container-fluid d4w-shell">
		<div class="d4w-clients-toolbar reveal-up">
			<p><i aria-hidden="true"></i><?php esc_html_e( 'Explore our client archive', 'design4web' ); ?></p>
			<?php if ( $is_term ) : ?>
				<div class="d4w-project-filters" aria-label="<?php esc_attr_e( 'Project archive navigation', 'design4web' ); ?>"><a href="<?php echo esc_url( get_post_type_archive_link( 'd4w_project' ) ); ?>"><?php esc_html_e( 'All clients', 'design4web' ); ?></a><span class="is-active"><?php single_term_title(); ?></span></div>
			<?php elseif ( ! is_wp_error( $project_terms ) && $project_terms ) : ?>
				<div class="d4w-project-filters" role="group" aria-label="<?php esc_attr_e( 'Filter client projects', 'design4web' ); ?>"><button class="is-active" type="button" data-project-filter="*" aria-pressed="true"><?php esc_html_e( 'All clients', 'design4web' ); ?><span><?php echo esc_html( $published ); ?></span></button><?php foreach ( $project_terms as $term ) : ?><button type="button" data-project-filter="<?php echo esc_attr( $term->slug ); ?>" aria-pressed="false"><?php echo esc_html( $term->name ); ?><span><?php echo esc_html( $term->count ); ?></span></button><?php endforeach; ?></div>
			<?php endif; ?>
		</div>

		<div class="d4w-work-gallery d4w-filter-grid" aria-live="polite">
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
					?>
					<div class="d4w-filter-item d4w-work-tile" data-project-types="<?php echo esc_attr( implode( ' ', $term_slugs ) ); ?>">
						<article class="d4w-work-card reveal-up">
							<a class="d4w-work-card__media d4w-image-curtain" href="<?php the_permalink(); ?>" data-cursor-label="VIEW">
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="1200" height="760" loading="lazy">
								<span class="d4w-work-card__index"><?php echo esc_html( str_pad( (string) $project_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
								<span class="d4w-work-card__open"><i class="bi bi-arrow-up-right"></i><small><?php esc_html_e( 'View case', 'design4web' ); ?></small></span>
							</a>
							<div class="d4w-work-card__copy">
								<div class="d4w-work-card__main">
									<p><span><?php echo esc_html( $year ); ?></span><?php echo esc_html( implode( ' · ', $term_names ) ); ?></p>
									<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
									<div><?php echo esc_html( d4w_card_excerpt( get_the_ID(), 18 ) ); ?></div>
								</div>
								<?php if ( $services ) : ?><aside><small><?php echo esc_html( $services ); ?></small></aside><?php endif; ?>
							</div>
						</article>
					</div>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="d4w-empty-state"><h2><?php esc_html_e( 'New client work is coming soon.', 'design4web' ); ?></h2><p><?php esc_html_e( 'Start a conversation about what we can create together.', 'design4web' ); ?></p></div>
			<?php endif; ?>
		</div>
		<?php if ( $published > 9 && ! $is_term ) : ?>
			<div class="d4w-work-more reveal-up"><button type="button" data-work-load-more><span><?php esc_html_e( 'Load more clients', 'design4web' ); ?></span><small aria-live="polite"></small><i class="bi bi-plus-lg"></i></button></div>
		<?php endif; ?>
	</div>
</section>

<?php d4w_contact_panel( 'work' ); ?>
<?php get_footer(); ?>

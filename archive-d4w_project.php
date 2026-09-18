<?php
/**
 * Filterable project archive.
 *
 * @package Design4Web
 */
get_header();
$project_terms = get_terms( array( 'taxonomy' => 'd4w_project_type', 'hide_empty' => true ) );
$is_term       = is_tax( 'd4w_project_type' );
?>

<section class="d4w-inner-hero d4w-inner-hero--work">
	<div class="container-fluid d4w-shell position-relative">
		<p class="d4w-section-label d4w-section-label--light hero-reveal"><span>02</span><?php esc_html_e( 'Selected work', 'design4web' ); ?></p>
		<div class="row align-items-end g-4"><div class="col-xl-9"><h1 class="d4w-page-title"><?php echo $is_term ? esc_html( single_term_title( '', false ) ) : esc_html__( 'Work that makes ideas tangible.', 'design4web' ); ?></h1></div><div class="col-xl-3"><p class="d4w-inner-hero__lead hero-reveal"><?php esc_html_e( 'Web platforms, commerce experiences, identities and campaigns shaped around a clear commercial purpose.', 'design4web' ); ?></p></div></div>
	</div>
</section>

<section class="d4w-portfolio-archive section-space-sm">
	<div class="container-fluid d4w-shell">
		<?php if ( $is_term ) : ?>
			<div class="d4w-project-filters reveal-up" aria-label="<?php esc_attr_e( 'Project archive navigation', 'design4web' ); ?>">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'd4w_project' ) ); ?>"><?php esc_html_e( 'All work', 'design4web' ); ?></a>
				<span class="is-active"><?php single_term_title(); ?></span>
			</div>
		<?php elseif ( ! is_wp_error( $project_terms ) && $project_terms ) : ?>
			<div class="d4w-project-filters reveal-up" role="group" aria-label="<?php esc_attr_e( 'Filter projects', 'design4web' ); ?>">
				<button class="is-active" type="button" data-project-filter="*" aria-pressed="true"><?php esc_html_e( 'All work', 'design4web' ); ?></button>
				<?php foreach ( $project_terms as $term ) : ?><button type="button" data-project-filter="<?php echo esc_attr( $term->slug ); ?>" aria-pressed="false"><?php echo esc_html( $term->name ); ?><span><?php echo esc_html( $term->count ); ?></span></button><?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="row g-4 g-xl-5 d4w-filter-grid" aria-live="polite">
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
					$client     = get_post_meta( get_the_ID(), '_d4w_client', true );
					?>
					<div class="col-md-6 <?php echo 1 === $project_index % 3 ? 'col-xl-7' : 'col-xl-5'; ?> d4w-filter-item" data-project-types="<?php echo esc_attr( implode( ' ', $term_slugs ) ); ?>">
						<article class="d4w-portfolio-card reveal-up <?php echo 0 === $project_index % 2 ? 'd4w-portfolio-card--offset' : ''; ?>">
							<a class="d4w-portfolio-card__media d4w-image-curtain" href="<?php the_permalink(); ?>" data-cursor-label="VIEW"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="900" height="1080" loading="lazy"><span><i class="bi bi-arrow-up-right"></i></span></a>
							<div class="d4w-portfolio-card__meta"><div><p><?php echo esc_html( implode( ' · ', $term_names ) ); ?></p><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php if ( $client ) : ?><small><?php echo esc_html( $client ); ?></small><?php endif; ?></div><strong><?php echo esc_html( $year ); ?></strong></div>
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

<?php
/**
 * Beyond-inspired client portfolio archive.
 *
 * @package Design4Web
 */
get_header();
$is_term = is_tax( 'd4w_project_type' );
?>

<section class="d4w-clients-hero d4w-motion-section">
	<div class="d4w-clients-hero__glow" aria-hidden="true"></div>
	<div class="container-fluid d4w-client-shell position-relative">
		<p class="d4w-clients-hero__eyebrow hero-reveal"><?php esc_html_e( 'Clients', 'design4web' ); ?></p>
		<h1 class="d4w-clients-hero__title hero-reveal">
			<?php echo $is_term ? esc_html( single_term_title( '', false ) ) : esc_html__( 'Want Results Like This?', 'design4web' ); ?>
		</h1>
	</div>
</section>

<section class="d4w-clients-archive" aria-label="<?php esc_attr_e( 'Client portfolio', 'design4web' ); ?>">
	<div class="container-fluid d4w-client-shell">
		<div class="d4w-work-gallery">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					$project_index = (int) $wp_query->current_post + 1;
					$image         = d4w_feature_image_url( get_the_ID(), 'project-' . ( ( ( $project_index - 1 ) % 5 ) + 1 ) . '.jpg', 'd4w-project' );
					$summary       = d4w_card_excerpt( get_the_ID(), 12 );
					?>
					<article class="d4w-work-card d4w-client-card">
						<a class="d4w-client-card__link" href="<?php the_permalink(); ?>" data-cursor-label="VIEW" aria-label="<?php echo esc_attr( sprintf( __( 'View %s case study', 'design4web' ), get_the_title() ) ); ?>">
							<figure class="d4w-client-card__media">
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="1200" height="675" loading="lazy">
							</figure>
							<div class="d4w-client-card__copy">
								<h2><?php the_title(); ?></h2>
								<?php if ( $summary ) : ?><p><?php echo esc_html( $summary ); ?></p><?php endif; ?>
							</div>
						</a>
					</article>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="d4w-empty-state">
					<h2><?php esc_html_e( 'New client work is coming soon.', 'design4web' ); ?></h2>
					<p><?php esc_html_e( 'Start a conversation about what we can create together.', 'design4web' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php d4w_contact_panel( 'work' ); ?>
<?php get_footer(); ?>

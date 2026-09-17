<?php
/**
 * Main index template.
 *
 * @package Design4Web
 */
get_header();
?>
<section class="d4w-inner-hero">
	<div class="container-fluid d4w-shell">
		<p class="d4w-section-label d4w-section-label--light"><span>Journal</span><?php bloginfo( 'name' ); ?></p>
		<h1><?php echo is_home() && get_option( 'page_for_posts' ) ? esc_html( get_the_title( get_option( 'page_for_posts' ) ) ) : esc_html__( 'Ideas, updates & useful thinking.', 'design4web' ); ?></h1>
	</div>
</section>
<section class="d4w-archive section-space-sm">
	<div class="container-fluid d4w-shell">
		<div class="row g-4">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="col-md-6 col-lg-4"><?php get_template_part( 'template-parts/content', get_post_type() ); ?></div>
				<?php endwhile; ?>
				<div class="col-12 d4w-pagination"><?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '←', 'next_text' => '→' ) ); ?></div>
			<?php else : ?>
				<div class="col-12"><h2><?php esc_html_e( 'Nothing found yet.', 'design4web' ); ?></h2></div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php get_footer(); ?>

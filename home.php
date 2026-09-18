<?php
/**
 * Journal archive.
 *
 * @package Design4Web
 */
get_header();
?>
<section class="d4w-inner-hero d4w-inner-hero--journal"><div class="container-fluid d4w-shell position-relative"><p class="d4w-section-label d4w-section-label--light hero-reveal"><span>Journal</span><?php esc_html_e( 'Ideas & practical guidance', 'design4web' ); ?></p><div class="row align-items-end g-4"><div class="col-xl-9"><h1 class="d4w-page-title"><?php esc_html_e( 'Clear thinking for better digital decisions.', 'design4web' ); ?></h1></div><div class="col-xl-3"><p class="d4w-inner-hero__lead hero-reveal"><?php esc_html_e( 'Notes on design, development, growth, content and keeping a business useful online.', 'design4web' ); ?></p></div></div></div></section>

<section class="d4w-journal-archive section-space-sm"><div class="container-fluid d4w-shell"><div class="row g-4 g-xl-5">
	<?php
	$post_index = 0;
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			++$post_index;
			$categories = get_the_category();
			$category   = $categories ? $categories[0]->name : __( 'Ideas', 'design4web' );
			$featured   = 1 === $post_index && ! is_paged();
			?>
			<div class="<?php echo $featured ? 'col-12' : 'col-md-6 col-xl-4'; ?>">
				<article <?php post_class( 'd4w-journal-card d4w-hover-card reveal-up ' . ( $featured ? 'd4w-journal-card--featured' : '' ) ); ?>>
					<a class="d4w-journal-card__media d4w-image-curtain" href="<?php the_permalink(); ?>" data-cursor-label="READ"><?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large', array( 'loading' => $featured ? 'eager' : 'lazy' ) ); else : ?><span><?php esc_html_e( 'Design4web Journal', 'design4web' ); ?></span><?php endif; ?><i class="bi bi-arrow-up-right"></i></a>
					<div class="d4w-journal-card__copy"><p class="d4w-post-meta"><?php echo esc_html( $category ); ?> <span>•</span> <?php echo esc_html( get_the_date( 'd M Y' ) ); ?></p><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html( d4w_card_excerpt( get_the_ID(), $featured ? 34 : 20 ) ); ?></p><a class="d4w-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read article', 'design4web' ); ?><i class="bi bi-arrow-right"></i></a></div>
				</article>
			</div>
		<?php endwhile; ?>
		<div class="col-12 d4w-pagination"><?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '←', 'next_text' => '→' ) ); ?></div>
	<?php else : ?><div class="col-12 d4w-empty-state"><h2><?php esc_html_e( 'The journal is being prepared.', 'design4web' ); ?></h2></div><?php endif; ?>
</div></div></section>
<?php d4w_contact_panel( 'journal' ); ?>
<?php get_footer(); ?>

<?php
/**
 * Single content template.
 *
 * @package Design4Web
 */
get_header();
while ( have_posts() ) : the_post();
?>
<article <?php post_class( 'd4w-single' ); ?>>
	<header class="d4w-inner-hero"><div class="container-fluid d4w-shell"><p class="d4w-section-label d4w-section-label--light"><span><?php echo esc_html( get_the_date( 'Y' ) ); ?></span><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></p><h1><?php the_title(); ?></h1><p class="d4w-single-meta"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?> · <?php echo esc_html( get_the_author() ); ?></p></div></header>
	<div class="container d4w-content-wrap">
		<?php if ( has_post_thumbnail() ) : ?><div class="d4w-featured-image"><?php the_post_thumbnail( 'full' ); ?></div><?php endif; ?>
		<div class="entry-content"><?php the_content(); wp_link_pages(); ?></div>
		<?php the_post_navigation( array( 'prev_text' => '<span>← ' . esc_html__( 'Previous', 'design4web' ) . '</span><strong>%title</strong>', 'next_text' => '<span>' . esc_html__( 'Next', 'design4web' ) . ' →</span><strong>%title</strong>' ) ); ?>
		<?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?>
	</div>
</article>
<?php endwhile; get_footer(); ?>

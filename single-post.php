<?php
/**
 * Journal article.
 *
 * @package Design4Web
 */
get_header();
while ( have_posts() ) :
	the_post();
	$categories = get_the_category();
	$category   = $categories ? $categories[0]->name : __( 'Journal', 'design4web' );
	?>
	<article <?php post_class( 'd4w-single d4w-article' ); ?>>
		<header class="d4w-inner-hero d4w-article__hero"><div class="container-fluid d4w-shell position-relative"><a class="d4w-back-link hero-reveal" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/journal/' ) ); ?>"><i class="bi bi-arrow-left"></i><?php esc_html_e( 'Journal', 'design4web' ); ?></a><p class="d4w-section-label d4w-section-label--light hero-reveal"><span><?php echo esc_html( $category ); ?></span><?php echo esc_html( get_the_date( 'd M Y' ) ); ?></p><h1 class="d4w-page-title"><?php the_title(); ?></h1><p class="d4w-single-meta hero-reveal"><?php echo esc_html( sprintf( __( '%1$s min read · By %2$s', 'design4web' ), max( 1, (int) ceil( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) ), get_the_author() ) ); ?></p></div></header>
		<div class="container d4w-content-wrap">
			<?php if ( has_post_thumbnail() ) : ?><div class="d4w-featured-image d4w-image-curtain reveal-up"><?php the_post_thumbnail( 'full' ); ?></div><?php endif; ?>
			<div class="entry-content d4w-prose reveal-up"><?php the_content(); wp_link_pages(); ?></div>
			<?php the_post_navigation( array( 'prev_text' => '<span>← ' . esc_html__( 'Previous', 'design4web' ) . '</span><strong>%title</strong>', 'next_text' => '<span>' . esc_html__( 'Next', 'design4web' ) . ' →</span><strong>%title</strong>' ) ); ?>
			<?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?>
		</div>
	</article>
<?php endwhile; ?>
<?php get_footer(); ?>

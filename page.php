<?php
/**
 * Page template.
 *
 * @package Design4Web
 */
get_header();
while ( have_posts() ) : the_post();
?>
<article <?php post_class( 'd4w-single' ); ?>><header class="d4w-inner-hero"><div class="container-fluid d4w-shell"><p class="d4w-section-label d4w-section-label--light"><span><?php esc_html_e( 'Page', 'design4web' ); ?></span><?php bloginfo( 'name' ); ?></p><h1><?php the_title(); ?></h1></div></header><div class="container d4w-content-wrap"><div class="entry-content"><?php the_content(); wp_link_pages(); ?></div></div></article>
<?php endwhile; get_footer(); ?>

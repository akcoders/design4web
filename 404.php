<?php
/**
 * 404 template.
 *
 * @package Design4Web
 */
get_header();
?>
<section class="d4w-not-found"><div class="container text-center"><p>404</p><h1><?php esc_html_e( 'This page wandered off.', 'design4web' ); ?></h1><p><?php esc_html_e( 'The link may be outdated, but there is plenty more to explore.', 'design4web' ); ?></p><a class="d4w-btn d4w-btn--gradient magnetic" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span><?php esc_html_e( 'Back to home', 'design4web' ); ?></span><i class="bi bi-arrow-right"></i></a></div></section>
<?php get_footer(); ?>

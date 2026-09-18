<?php
/**
 * Contact page.
 *
 * Template Name: Design4web Contact
 * Template Post Type: page
 *
 * @package Design4Web
 */
get_header();
while ( have_posts() ) :
	the_post();
	$email    = d4w_get_option( 'contact_email' );
	$phone    = d4w_get_option( 'phone' );
	$whatsapp = preg_replace( '/\D+/', '', d4w_get_option( 'whatsapp' ) );
	?>
	<article <?php post_class( 'd4w-contact-page' ); ?>>
		<header class="d4w-inner-hero d4w-contact-hero">
			<div class="container-fluid d4w-shell position-relative"><p class="d4w-section-label d4w-section-label--light hero-reveal"><span>Hello</span><?php esc_html_e( 'Start a conversation', 'design4web' ); ?></p><div class="row align-items-end g-4"><div class="col-xl-9"><h1 class="d4w-page-title"><?php esc_html_e( 'Tell us what you want to make possible.', 'design4web' ); ?></h1></div><div class="col-xl-3"><p class="d4w-inner-hero__lead hero-reveal"><?php esc_html_e( 'Share the goal, the challenge and your ideal timing. We will come back with a practical next step.', 'design4web' ); ?></p></div></div></div>
		</header>

		<section class="d4w-contact-options section-space-sm">
			<div class="container-fluid d4w-shell">
				<div class="row g-3">
					<div class="col-md-6 col-xl-3"><a class="d4w-contact-info-card d4w-hover-card reveal-up" href="mailto:<?php echo esc_attr( $email ); ?>"><i class="bi bi-envelope"></i><span><?php esc_html_e( 'Email', 'design4web' ); ?></span><strong><?php echo esc_html( $email ); ?></strong><small><?php esc_html_e( 'Best for project briefs', 'design4web' ); ?></small></a></div>
					<div class="col-md-6 col-xl-3"><a class="d4w-contact-info-card d4w-hover-card reveal-up" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><i class="bi bi-telephone"></i><span><?php esc_html_e( 'Call', 'design4web' ); ?></span><strong><?php echo esc_html( $phone ); ?></strong><small><?php esc_html_e( 'Monday–Saturday', 'design4web' ); ?></small></a></div>
					<div class="col-md-6 col-xl-3"><a class="d4w-contact-info-card d4w-hover-card reveal-up" href="https://wa.me/<?php echo esc_attr( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp"></i><span><?php esc_html_e( 'WhatsApp', 'design4web' ); ?></span><strong><?php esc_html_e( 'Message the team', 'design4web' ); ?></strong><small><?php esc_html_e( 'Quick questions welcome', 'design4web' ); ?></small></a></div>
					<div class="col-md-6 col-xl-3"><div class="d4w-contact-info-card d4w-hover-card reveal-up"><i class="bi bi-geo-alt"></i><span><?php esc_html_e( 'Studio', 'design4web' ); ?></span><strong><?php echo esc_html( d4w_get_option( 'address' ) ); ?></strong><small><?php esc_html_e( 'Mumbai, Maharashtra', 'design4web' ); ?></small></div></div>
				</div>
			</div>
		</section>

		<section class="d4w-contact-workspace section-space-sm">
			<div class="container-fluid d4w-shell"><div class="row g-5 align-items-start"><div class="col-lg-5"><p class="d4w-section-label reveal-up"><span>01</span><?php esc_html_e( 'Project enquiry', 'design4web' ); ?></p><h2 class="d4w-display reveal-text"><?php echo esc_html( d4w_get_option( 'cta_title' ) ); ?></h2><p class="d4w-lead reveal-up"><?php echo esc_html( d4w_get_option( 'cta_text' ) ); ?></p><?php if ( trim( get_the_content() ) ) : ?><div class="entry-content d4w-prose reveal-up"><?php the_content(); ?></div><?php endif; ?><div class="d4w-contact-address reveal-up"><span><?php esc_html_e( 'Office address', 'design4web' ); ?></span><p><?php echo nl2br( esc_html( d4w_get_option( 'address' ) ) ); ?></p></div></div><div class="col-lg-7"><?php d4w_contact_form( 'contact-page' ); ?></div></div></div>
		</section>

		<?php
		$faqs = new WP_Query( array( 'post_type' => 'd4w_faq', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
		if ( $faqs->have_posts() ) : ?>
			<section class="d4w-faq-section section-space-sm"><div class="container-fluid d4w-shell"><div class="row g-5"><div class="col-lg-4"><p class="d4w-section-label d4w-section-label--light reveal-up"><span>02</span><?php esc_html_e( 'Common questions', 'design4web' ); ?></p><h2 class="d4w-display text-white reveal-text"><?php esc_html_e( 'Useful answers before we begin.', 'design4web' ); ?></h2></div><div class="col-lg-8 d4w-faq-list">
				<?php $faq_index = 0; while ( $faqs->have_posts() ) : $faqs->the_post(); ++$faq_index; $faq_id = 'faq-' . get_the_ID(); $faq_open = 1 === $faq_index; $faq_category = get_post_meta( get_the_ID(), '_d4w_faq_category', true ); ?><article class="d4w-faq-item reveal-up <?php echo $faq_open ? 'is-open' : ''; ?>"><button type="button" aria-expanded="<?php echo $faq_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $faq_id ); ?>"><span><?php echo esc_html( str_pad( (string) $faq_index, 2, '0', STR_PAD_LEFT ) ); ?></span><strong><?php if ( $faq_category ) : ?><small><?php echo esc_html( $faq_category ); ?></small><?php endif; ?><?php the_title(); ?></strong><i class="bi bi-plus-lg"></i></button><div id="<?php echo esc_attr( $faq_id ); ?>" class="d4w-faq-item__answer" aria-hidden="<?php echo $faq_open ? 'false' : 'true'; ?>" <?php echo $faq_open ? '' : 'inert'; ?>><div><?php the_content(); ?></div></div></article><?php endwhile; wp_reset_postdata(); ?>
			</div></div></div></section>
		<?php endif; ?>
	</article>
<?php endwhile; ?>
<?php get_footer(); ?>

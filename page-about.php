<?php
/**
 * About page.
 *
 * Template Name: Design4web About
 * Template Post Type: page
 *
 * @package Design4Web
 */
get_header();
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'd4w-about-page' ); ?>>
		<header class="d4w-inner-hero d4w-about-hero">
			<div class="container-fluid d4w-shell position-relative">
				<p class="d4w-section-label d4w-section-label--light hero-reveal"><span>About</span><?php esc_html_e( 'Design4web, Mumbai', 'design4web' ); ?></p>
				<div class="row align-items-end g-4"><div class="col-xl-9"><h1 class="d4w-page-title"><?php esc_html_e( 'Small team energy. Full-service digital thinking.', 'design4web' ); ?></h1></div><div class="col-xl-3"><div class="d4w-about-hero__stamp hero-reveal"><strong><span class="counter" data-count="<?php echo esc_attr( d4w_get_option( 'experience_years' ) ); ?>">0</span>+</strong><small><?php esc_html_e( 'years creating for the web', 'design4web' ); ?></small></div></div></div>
			</div>
		</header>

		<section class="d4w-about-story section-space-sm">
			<div class="container-fluid d4w-shell">
				<div class="row g-5 align-items-stretch">
					<div class="col-lg-6">
						<figure class="d4w-about-portrait d4w-image-curtain reveal-up"><img src="<?php echo esc_url( d4w_option_image_url( 'about_story_image', 'service-web-design.jpg', 'full' ) ); ?>" alt="<?php esc_attr_e( 'Design4web creative team collaborating on a digital project', 'design4web' ); ?>" width="720" height="900"><figcaption><?php esc_html_e( 'Strategy × Craft × Care', 'design4web' ); ?></figcaption></figure>
					</div>
					<div class="col-lg-5 offset-lg-1"><p class="d4w-section-label reveal-up"><span>01</span><?php esc_html_e( 'Our story', 'design4web' ); ?></p><h2 class="d4w-display reveal-text"><?php echo esc_html( d4w_get_option( 'about_title' ) ); ?></h2><div class="entry-content d4w-prose reveal-up"><?php the_content(); ?></div><a class="d4w-text-link reveal-up" href="<?php echo esc_url( get_post_type_archive_link( 'd4w_service' ) ); ?>"><?php esc_html_e( 'Explore our services', 'design4web' ); ?><i class="bi bi-arrow-right"></i></a></div>
				</div>
				<div class="d4w-stats reveal-up"><div><strong><span class="counter" data-count="<?php echo esc_attr( d4w_get_option( 'projects_count' ) ); ?>">0</span>+</strong><small><?php esc_html_e( 'Projects delivered', 'design4web' ); ?></small></div><div><strong><span class="counter" data-count="<?php echo esc_attr( d4w_get_option( 'client_satisfaction' ) ); ?>">0</span>%</strong><small><?php esc_html_e( 'Client satisfaction', 'design4web' ); ?></small></div><div><strong><?php echo esc_html( d4w_get_option( 'support_label' ) ); ?></strong><small><?php esc_html_e( 'Responsive support', 'design4web' ); ?></small></div><div><strong>360°</strong><small><?php esc_html_e( 'Digital partnership', 'design4web' ); ?></small></div></div>
			</div>
		</section>

		<section class="d4w-values section-space-sm">
			<div class="container-fluid d4w-shell">
				<div class="row align-items-end mb-5 g-4"><div class="col-lg-4"><p class="d4w-section-label d4w-section-label--light reveal-up"><span>02</span><?php esc_html_e( 'What matters', 'design4web' ); ?></p></div><div class="col-lg-8"><h2 class="d4w-display text-white reveal-text"><?php esc_html_e( 'Principles that keep creative work useful.', 'design4web' ); ?></h2></div></div>
				<div class="row g-3">
					<?php
					$values = array(
						array( '01', 'Clarity first', 'We make complicated decisions understandable and keep every page focused on what its audience needs.' ),
						array( '02', 'Original by design', 'References guide the quality bar; your strategy, visual system and implementation remain distinctly yours.' ),
						array( '03', 'Built responsibly', 'Responsive behaviour, accessibility, performance and maintainability are part of the work from day one.' ),
						array( '04', 'Present after launch', 'Hosting, maintenance and thoughtful support keep the experience dependable as your business changes.' ),
					);
					foreach ( $values as $value ) : ?>
						<div class="col-md-6 col-xl-3"><div class="d4w-value-card d4w-hover-card reveal-up"><span><?php echo esc_html( $value[0] ); ?></span><h3><?php echo esc_html( $value[1] ); ?></h3><p><?php echo esc_html( $value[2] ); ?></p><i class="bi bi-arrow-down-right"></i></div></div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<?php
		$process = new WP_Query( array( 'post_type' => 'd4w_process', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
		if ( $process->have_posts() ) : ?>
			<section class="d4w-about-process section-space-sm"><div class="container-fluid d4w-shell"><div class="row g-5"><div class="col-lg-5"><p class="d4w-section-label reveal-up"><span>03</span><?php esc_html_e( 'Our approach', 'design4web' ); ?></p><h2 class="d4w-display reveal-text"><?php echo esc_html( d4w_get_option( 'process_title' ) ); ?></h2><p class="d4w-lead reveal-up"><?php echo esc_html( d4w_get_option( 'process_text' ) ); ?></p></div><div class="col-lg-7 d4w-timeline">
				<?php while ( $process->have_posts() ) : $process->the_post(); ?><div class="d4w-timeline__item reveal-up"><span><?php echo esc_html( get_post_meta( get_the_ID(), '_d4w_process_number', true ) ); ?></span><div><h3><?php the_title(); ?></h3><p><?php echo esc_html( d4w_card_excerpt( get_the_ID(), 28 ) ); ?></p></div></div><?php endwhile; wp_reset_postdata(); ?>
			</div></div></div></section>
		<?php endif; ?>

		<?php
		$team = new WP_Query( array( 'post_type' => 'd4w_team', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
		if ( $team->have_posts() ) : ?>
			<section class="d4w-team section-space-sm"><div class="container-fluid d4w-shell"><div class="row align-items-end mb-5 g-4"><div class="col-lg-4"><p class="d4w-section-label reveal-up"><span>04</span><?php esc_html_e( 'The people', 'design4web' ); ?></p></div><div class="col-lg-8"><h2 class="d4w-display reveal-text"><?php esc_html_e( 'A compact team with broad capability.', 'design4web' ); ?></h2></div></div><div class="row g-4">
				<?php while ( $team->have_posts() ) : $team->the_post(); $team_email = get_post_meta( get_the_ID(), '_d4w_team_email', true ); $team_linkedin = get_post_meta( get_the_ID(), '_d4w_team_linkedin', true ); ?><div class="col-md-6 col-lg-4"><article class="d4w-team-card d4w-hover-card reveal-up"><div class="d4w-team-card__media d4w-image-curtain"><?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); else : ?><img src="<?php echo esc_url( D4W_URI . '/assets/images/testimonial.jpg' ); ?>" alt="" loading="lazy"><?php endif; ?></div><p><?php echo esc_html( get_post_meta( get_the_ID(), '_d4w_team_role', true ) ); ?></p><h3><?php the_title(); ?></h3><div class="d4w-team-card__bio"><?php echo wp_kses_post( wpautop( get_the_content() ) ); ?></div><?php if ( $team_email || $team_linkedin ) : ?><div class="d4w-team-card__links"><?php if ( $team_email ) : ?><a href="mailto:<?php echo esc_attr( $team_email ); ?>" aria-label="<?php esc_attr_e( 'Email team member', 'design4web' ); ?>"><i class="bi bi-envelope"></i></a><?php endif; ?><?php if ( $team_linkedin ) : ?><a href="<?php echo esc_url( $team_linkedin ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'LinkedIn profile', 'design4web' ); ?>"><i class="bi bi-linkedin"></i></a><?php endif; ?></div><?php endif; ?></article></div><?php endwhile; wp_reset_postdata(); ?>
			</div></div></section>
		<?php endif; ?>
	</article>
<?php endwhile; ?>
<?php d4w_contact_panel( 'about' ); ?>
<?php get_footer(); ?>

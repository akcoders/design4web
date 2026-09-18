<?php
/**
 * Homepage template.
 *
 * @package Design4Web
 */
get_header();
$image_dir = D4W_URI . '/assets/images/';
?>

<section id="home" class="d4w-hero">
	<div class="d4w-hero__glow d4w-parallax" data-speed="-0.06"></div>
	<div class="container-fluid d4w-shell position-relative">
		<div class="row align-items-end">
			<div class="col-xl-10">
				<p class="d4w-eyebrow hero-reveal"><span></span><?php echo esc_html( d4w_get_option( 'hero_eyebrow' ) ); ?></p>
				<h1 class="d4w-hero__title">
					<span class="line-wrap"><span class="line-inner"><?php echo esc_html( d4w_get_option( 'hero_title' ) ); ?></span></span>
					<span class="line-wrap"><span class="line-inner gradient-text"><?php echo esc_html( d4w_get_option( 'hero_highlight' ) ); ?></span></span>
				</h1>
			</div>
			<div class="col-xl-2 d-none d-xl-flex justify-content-end">
				<div class="d4w-orbit hero-reveal">
					<svg viewBox="0 0 100 100" aria-hidden="true"><defs><path id="circlePath" d="M 50, 50 m -33, 0 a 33,33 0 1,1 66,0 a 33,33 0 1,1 -66,0"/></defs><text><textPath href="#circlePath">DESIGN • DEVELOP • GROW • </textPath></text></svg>
					<i class="bi bi-arrow-down"></i>
				</div>
			</div>
		</div>

		<div class="row align-items-center g-4 d4w-hero__meta hero-reveal">
			<div class="col-lg-6">
				<p><?php echo esc_html( d4w_get_option( 'hero_text' ) ); ?></p>
			</div>
			<div class="col-lg-6 d-flex flex-wrap gap-3 justify-content-lg-end">
				<a class="d4w-btn d4w-btn--gradient magnetic" href="<?php echo esc_url( d4w_get_option( 'hero_button_url' ) ); ?>"><span><?php echo esc_html( d4w_get_option( 'hero_button_text' ) ); ?></span><i class="bi bi-arrow-up-right"></i></a>
				<a class="d4w-btn d4w-btn--ghost magnetic" href="<?php echo esc_url( d4w_get_option( 'hero_secondary_url' ) ); ?>"><span><?php echo esc_html( d4w_get_option( 'hero_secondary_text' ) ); ?></span><i class="bi bi-arrow-down-right"></i></a>
			</div>
		</div>

		<div class="d4w-hero-gallery hero-reveal">
			<?php
			$hero_fallbacks = array( 'service-web-design.jpg', 'service-development.jpg', 'service-marketing.jpg', 'service-hosting.jpg' );
			$hero_services  = get_posts( array( 'post_type' => 'd4w_service', 'post_status' => 'publish', 'posts_per_page' => 4, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
			for ( $i = 1; $i <= 4; $i++ ) :
				$hero_service = isset( $hero_services[ $i - 1 ] ) ? $hero_services[ $i - 1 ] : null;
				$hero_image   = $hero_service ? d4w_feature_image_url( $hero_service->ID, $hero_fallbacks[ $i - 1 ], 'd4w-service' ) : $image_dir . $hero_fallbacks[ $i - 1 ];
				$hero_alt     = $hero_service ? $hero_service->post_title : '';
				?>
				<div class="d4w-hero-card d4w-parallax" data-speed="<?php echo esc_attr( ( $i % 2 ? 0.025 : -0.018 ) ); ?>">
					<img src="<?php echo esc_url( $hero_image ); ?>" alt="<?php echo esc_attr( $hero_alt ); ?>" width="524" height="546" <?php echo 1 === $i ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
					<span><?php echo esc_html( str_pad( (string) $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
				</div>
			<?php endfor; ?>
			<div class="d4w-project-stat"><strong><span class="counter" data-count="<?php echo esc_attr( d4w_get_option( 'projects_count' ) ); ?>">0</span>+</strong><small><?php esc_html_e( 'projects shaped with care', 'design4web' ); ?></small></div>
		</div>
	</div>
</section>

<div class="d4w-marquee" aria-hidden="true">
	<div class="d4w-marquee__track">
		<?php for ( $i = 0; $i < 2; $i++ ) : ?>
			<div class="d4w-marquee__group">
				<?php foreach ( $hero_services as $hero_service ) : ?><span><?php echo esc_html( $hero_service->post_title ); ?> <i>✦</i></span><?php endforeach; ?>
				<span>Brand Identity <i>✦</i></span><span>Digital Growth <i>✦</i></span>
			</div>
		<?php endfor; ?>
	</div>
</div>

<section id="about" class="d4w-about section-space">
	<div class="container-fluid d4w-shell">
		<div class="row g-5 align-items-start">
			<div class="col-lg-4">
				<p class="d4w-section-label reveal-up"><span>01</span><?php echo esc_html( d4w_get_option( 'about_label' ) ); ?></p>
				<div class="d4w-about-badge reveal-up">
					<strong><span class="counter" data-count="<?php echo esc_attr( d4w_get_option( 'experience_years' ) ); ?>">0</span>+</strong>
					<small><?php esc_html_e( 'Years of digital craft', 'design4web' ); ?></small>
				</div>
			</div>
			<div class="col-lg-8">
				<h2 class="d4w-display reveal-text"><?php echo esc_html( d4w_get_option( 'about_title' ) ); ?></h2>
				<div class="row mt-5 align-items-end g-4">
					<div class="col-md-8"><p class="d4w-lead reveal-up"><?php echo esc_html( d4w_get_option( 'about_text' ) ); ?></p></div>
					<div class="col-md-4 text-md-end"><a class="d4w-text-link reveal-up" href="<?php echo esc_url( d4w_page_url( 'about' ) ); ?>"><?php esc_html_e( 'Meet Design4web', 'design4web' ); ?><i class="bi bi-arrow-right"></i></a></div>
				</div>
			</div>
		</div>

		<div class="d4w-stats reveal-up">
			<div><strong><span class="counter" data-count="<?php echo esc_attr( d4w_get_option( 'projects_count' ) ); ?>">0</span>+</strong><small><?php esc_html_e( 'Projects delivered', 'design4web' ); ?></small></div>
			<div><strong><span class="counter" data-count="<?php echo esc_attr( d4w_get_option( 'client_satisfaction' ) ); ?>">0</span>%</strong><small><?php esc_html_e( 'Client satisfaction', 'design4web' ); ?></small></div>
			<div><strong><?php echo esc_html( d4w_get_option( 'support_label' ) ); ?></strong><small><?php esc_html_e( 'Responsive support', 'design4web' ); ?></small></div>
			<div><strong>360°</strong><small><?php esc_html_e( 'Digital partnership', 'design4web' ); ?></small></div>
		</div>
	</div>
</section>

<?php if ( d4w_get_option( 'show_services', true ) ) : ?>
<section id="services" class="d4w-services section-space">
	<div class="container-fluid d4w-shell">
		<div class="row align-items-end mb-5 g-4">
			<div class="col-lg-4"><p class="d4w-section-label d4w-section-label--light reveal-up"><span>02</span><?php esc_html_e( 'What we do', 'design4web' ); ?></p></div>
			<div class="col-lg-8"><h2 class="d4w-display text-white reveal-text"><?php echo esc_html( d4w_get_option( 'services_title' ) ); ?></h2></div>
		</div>
		<div class="d4w-service-list">
			<?php
			$service_query = new WP_Query( array( 'post_type' => 'd4w_service', 'posts_per_page' => 8, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
			$service_index = 0;
			while ( $service_query->have_posts() ) :
				$service_query->the_post();
				++$service_index;
				$icon        = get_post_meta( get_the_ID(), '_d4w_icon', true ) ?: 'bi-asterisk';
				$fallback    = array( 'service-web-design.jpg', 'service-development.jpg', 'service-marketing.jpg', 'service-hosting.jpg' );
				$service_img = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'd4w-service' ) : $image_dir . $fallback[ ( $service_index - 1 ) % count( $fallback ) ];
				?>
				<article class="d4w-service-item reveal-up" data-image="<?php echo esc_url( $service_img ); ?>">
					<span class="d4w-service-number"><?php echo esc_html( str_pad( (string) $service_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
					<i class="bi <?php echo esc_attr( $icon ); ?> d4w-service-icon"></i>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<p><?php echo esc_html( get_the_excerpt() ); ?></p>
					<a class="d4w-circle-link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Read about %s', 'design4web' ), get_the_title() ) ); ?>"><i class="bi bi-arrow-up-right"></i></a>
				</article>
			<?php endwhile; wp_reset_postdata(); ?>
			<div class="d4w-service-preview" aria-hidden="true"><img src="<?php echo esc_url( $image_dir . 'service-web-design.jpg' ); ?>" alt=""></div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( d4w_get_option( 'show_projects', true ) ) : ?>
<section id="work" class="d4w-work section-space">
	<div class="container-fluid d4w-shell">
		<div class="row align-items-end mb-5 g-4">
			<div class="col-lg-4"><p class="d4w-section-label reveal-up"><span>03</span><?php esc_html_e( 'Selected work', 'design4web' ); ?></p></div>
			<div class="col-lg-6"><h2 class="d4w-display reveal-text"><?php echo esc_html( d4w_get_option( 'work_title' ) ); ?></h2></div>
			<div class="col-lg-2 text-lg-end"><a class="d4w-text-link reveal-up" href="<?php echo esc_url( get_post_type_archive_link( 'd4w_project' ) ); ?>"><?php esc_html_e( 'View all work', 'design4web' ); ?><i class="bi bi-arrow-right"></i></a></div>
		</div>

		<div class="row g-4 d4w-project-grid">
			<?php
			$project_query = new WP_Query( array( 'post_type' => 'd4w_project', 'posts_per_page' => 5, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ), 'no_found_rows' => true ) );
			$project_index = 0;
			while ( $project_query->have_posts() ) :
				$project_query->the_post();
				++$project_index;
				$image    = d4w_feature_image_url( get_the_ID(), 'project-' . min( $project_index, 5 ) . '.jpg', 'd4w-project' );
				$terms    = get_the_terms( get_the_ID(), 'd4w_project_type' );
				$category = $terms && ! is_wp_error( $terms ) ? $terms[0]->name : __( 'Digital Experience', 'design4web' );
				$layout   = 1 === $project_index || 4 === $project_index ? 'col-lg-7' : 'col-lg-5';
				?>
				<div class="<?php echo esc_attr( $layout ); ?>">
					<article class="d4w-project-card reveal-up <?php echo 2 === $project_index || 4 === $project_index ? 'd4w-project-card--tall' : ''; ?>">
						<a href="<?php the_permalink(); ?>" class="d4w-project-media">
							<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="900" height="1080" loading="lazy">
							<span class="d4w-project-view"><i class="bi bi-arrow-up-right"></i></span>
						</a>
						<div class="d4w-project-copy"><div><p><?php echo esc_html( $category ); ?></p><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3></div><span><?php echo esc_html( get_post_meta( get_the_ID(), '_d4w_year', true ) ?: wp_date( 'Y' ) ); ?></span></div>
					</article>
				</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="d4w-tech section-space-sm">
	<div class="container-fluid d4w-shell text-center">
		<p class="d4w-section-kicker reveal-up"><?php echo esc_html( d4w_get_option( 'tech_kicker' ) ); ?></p>
		<h2 class="d4w-display reveal-text"><?php echo esc_html( d4w_get_option( 'tech_title' ) ); ?></h2>
		<div class="d4w-tech-cloud reveal-up">
			<?php foreach ( d4w_lines( d4w_get_option( 'tech_list' ) ) as $tech ) : ?>
				<span><?php echo esc_html( $tech ); ?><i class="bi bi-check2"></i></span>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php if ( d4w_get_option( 'show_process', true ) ) : ?>
<section id="process" class="d4w-process section-space">
	<div class="container-fluid d4w-shell">
		<div class="row g-5">
			<div class="col-lg-5">
				<p class="d4w-section-label d4w-section-label--light reveal-up"><span>04</span><?php esc_html_e( 'Our process', 'design4web' ); ?></p>
				<h2 class="d4w-display text-white reveal-text"><?php echo esc_html( d4w_get_option( 'process_title' ) ); ?></h2>
				<p class="d4w-lead text-white-50 reveal-up"><?php echo esc_html( d4w_get_option( 'process_text' ) ); ?></p>
			</div>
			<div class="col-lg-7">
				<?php
				$process_query = new WP_Query( array( 'post_type' => 'd4w_process', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
				while ( $process_query->have_posts() ) :
					$process_query->the_post();
					$step_number = get_post_meta( get_the_ID(), '_d4w_process_number', true ) ?: str_pad( (string) ( $process_query->current_post + 1 ), 2, '0', STR_PAD_LEFT );
					$step_icon   = get_post_meta( get_the_ID(), '_d4w_process_icon', true ) ?: 'bi-arrow-down-right';
					?>
					<div class="d4w-process-step reveal-up"><span><?php echo esc_html( $step_number ); ?></span><h3><?php the_title(); ?></h3><p><?php echo esc_html( d4w_card_excerpt( get_the_ID(), 24 ) ); ?></p><i class="bi <?php echo esc_attr( $step_icon ); ?>"></i></div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( d4w_get_option( 'show_testimonials', true ) ) : ?>
<?php
$testimonial_cover_posts = get_posts( array( 'post_type' => 'd4w_testimonial', 'post_status' => 'publish', 'posts_per_page' => 1, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ), 'no_found_rows' => true ) );
$testimonial_cover       = $testimonial_cover_posts ? d4w_feature_image_url( $testimonial_cover_posts[0]->ID, 'testimonial.jpg', 'large' ) : $image_dir . 'testimonial.jpg';
?>
<section class="d4w-testimonials section-space">
	<div class="container-fluid d4w-shell">
		<div class="row align-items-center g-5">
			<div class="col-lg-4">
				<div class="d4w-testimonial-image reveal-up"><img src="<?php echo esc_url( $testimonial_cover ); ?>" alt="<?php esc_attr_e( 'Creative team collaboration', 'design4web' ); ?>" width="542" height="571" loading="lazy"><span><?php esc_html_e( 'Built on trust', 'design4web' ); ?></span></div>
			</div>
			<div class="col-lg-8">
				<div class="d-flex justify-content-between align-items-center mb-4">
					<p class="d4w-section-label reveal-up"><span>05</span><?php esc_html_e( 'Client stories', 'design4web' ); ?></p>
					<div class="d4w-slider-controls"><button class="d4w-testimonial-prev" aria-label="<?php esc_attr_e( 'Previous testimonial', 'design4web' ); ?>"><i class="bi bi-arrow-left"></i></button><button class="d4w-testimonial-toggle" aria-label="<?php esc_attr_e( 'Pause testimonial autoplay', 'design4web' ); ?>" aria-pressed="false"><i class="bi bi-pause-fill"></i></button><button class="d4w-testimonial-next" aria-label="<?php esc_attr_e( 'Next testimonial', 'design4web' ); ?>"><i class="bi bi-arrow-right"></i></button></div>
				</div>
				<div class="d4w-testimonial-track reveal-up">
					<?php
					$testimonial_query = new WP_Query( array( 'post_type' => 'd4w_testimonial', 'posts_per_page' => 10, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ), 'no_found_rows' => true ) );
					while ( $testimonial_query->have_posts() ) :
						$testimonial_query->the_post();
						$rating = min( 5, max( 1, (int) get_post_meta( get_the_ID(), '_d4w_rating', true ) ) );
						?>
						<article class="d4w-testimonial-slide">
							<div class="d4w-stars"><?php for ( $star = 0; $star < $rating; $star++ ) : ?><i class="bi bi-star-fill"></i><?php endfor; ?></div>
							<blockquote>“<?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?>”</blockquote>
							<div class="d4w-testimonial-author"><?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'thumbnail', array( 'loading' => 'lazy' ) ); else : ?><span class="d4w-testimonial-avatar" aria-hidden="true"><?php echo esc_html( strtoupper( substr( get_the_title(), 0, 1 ) ) ); ?></span><?php endif; ?><div><strong><?php the_title(); ?></strong><span><?php echo esc_html( get_post_meta( get_the_ID(), '_d4w_role', true ) ); ?></span></div></div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( d4w_get_option( 'show_blog', true ) ) : ?>
<section id="insights" class="d4w-insights section-space-sm">
	<div class="container-fluid d4w-shell">
		<div class="row align-items-end mb-5 g-4">
			<div class="col-lg-4"><p class="d4w-section-label reveal-up"><span>06</span><?php esc_html_e( 'Ideas & insights', 'design4web' ); ?></p></div>
			<div class="col-lg-6"><h2 class="d4w-display reveal-text"><?php echo esc_html( d4w_get_option( 'insights_title' ) ); ?></h2></div>
			<div class="col-lg-2 text-lg-end"><a class="d4w-text-link reveal-up" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'All articles', 'design4web' ); ?><i class="bi bi-arrow-right"></i></a></div>
		</div>
		<div class="row g-4">
			<?php
			$blog_query = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
			while ( $blog_query->have_posts() ) :
				$blog_query->the_post();
				?>
				<div class="col-md-6 col-lg-4">
					<article class="d4w-post-card reveal-up">
						<a class="d4w-post-card__media" href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); else : ?><span><?php esc_html_e( 'Design4web Journal', 'design4web' ); ?></span><?php endif; ?><i class="bi bi-arrow-up-right"></i></a>
						<p class="d4w-post-meta"><?php echo esc_html( get_the_date( 'd M, Y' ) ); ?> <span>•</span> <?php echo esc_html( get_the_author() ); ?></p>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					</article>
				</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php d4w_contact_panel( 'home' ); ?>

<?php get_footer(); ?>

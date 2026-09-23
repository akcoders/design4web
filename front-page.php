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
			$service_query = new WP_Query( array( 'post_type' => 'd4w_service', 'posts_per_page' => -1, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
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

<?php if ( d4w_get_option( 'show_products', true ) ) : ?>
<section id="products" class="d4w-home-products section-space-sm">
	<div class="container-fluid d4w-shell">
		<div class="row align-items-end mb-5 g-4"><div class="col-lg-4"><p class="d4w-section-label reveal-up"><span>Products</span><?php esc_html_e( 'WhatsApp growth stack', 'design4web' ); ?></p></div><div class="col-lg-6"><h2 class="d4w-display reveal-text"><?php echo esc_html( d4w_get_option( 'products_title' ) ); ?></h2></div><div class="col-lg-2 text-lg-end"><a class="d4w-text-link reveal-up" href="<?php echo esc_url( get_post_type_archive_link( 'd4w_product' ) ); ?>"><?php esc_html_e( 'All products', 'design4web' ); ?><i class="bi bi-arrow-right"></i></a></div></div>
		<div class="d4w-home-product-grid">
			<?php
			$product_query = new WP_Query( array( 'post_type' => 'd4w_product', 'post_status' => 'publish', 'posts_per_page' => 6, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
			while ( $product_query->have_posts() ) :
				$product_query->the_post();
				$product_icon   = get_post_meta( get_the_ID(), '_d4w_product_icon', true ) ?: 'bi-box';
				$product_accent = sanitize_hex_color( get_post_meta( get_the_ID(), '_d4w_product_accent', true ) ) ?: '#7c4dff';
				?>
				<a class="d4w-home-product d4w-hover-card reveal-up" href="<?php the_permalink(); ?>" style="--product-accent:<?php echo esc_attr( $product_accent ); ?>"><span class="d4w-home-product__icon"><i class="bi <?php echo esc_attr( $product_icon ); ?>"></i></span><div><small><?php echo esc_html( get_post_meta( get_the_ID(), '_d4w_product_kicker', true ) ); ?></small><h3><?php the_title(); ?></h3><p><?php echo esc_html( d4w_card_excerpt( get_the_ID(), 17 ) ); ?></p></div><i class="bi bi-arrow-up-right"></i></a>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( d4w_get_option( 'show_projects', true ) ) : ?>
	<section id="work" class="d4w-work d4w-clients-archive d4w-home-work section-space">
		<div class="container-fluid d4w-shell">
			<div class="row align-items-end mb-5 g-4">
				<div class="col-lg-4"><p class="d4w-section-label d4w-section-label--light reveal-up"><span>Work</span><?php esc_html_e( 'Our work', 'design4web' ); ?></p></div>
				<div class="col-lg-6"><h2 class="d4w-display text-white reveal-text"><?php echo esc_html( d4w_get_option( 'work_title' ) ); ?></h2></div>
				<div class="col-lg-2 text-lg-end"><a class="d4w-text-link d4w-text-link--light reveal-up" href="<?php echo esc_url( get_post_type_archive_link( 'd4w_project' ) ); ?>"><?php esc_html_e( 'View all work', 'design4web' ); ?><i class="bi bi-arrow-right"></i></a></div>
			</div>

			<div class="d4w-work-gallery d4w-home-work__grid">
				<?php
				$project_query = new WP_Query( array( 'post_type' => 'd4w_project', 'posts_per_page' => 6, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ), 'no_found_rows' => true ) );
				$project_index = 0;
				while ( $project_query->have_posts() ) :
					$project_query->the_post();
					++$project_index;
					$image   = d4w_feature_image_url( get_the_ID(), 'project-' . min( $project_index, 5 ) . '.jpg', 'full' );
					$summary = d4w_card_excerpt( get_the_ID(), 12 );
					?>
					<article class="d4w-work-card d4w-client-card reveal-up">
						<a class="d4w-client-card__link" href="<?php the_permalink(); ?>" data-cursor-label="VIEW" aria-label="<?php echo esc_attr( sprintf( __( 'View %s case study', 'design4web' ), get_the_title() ) ); ?>">
							<figure class="d4w-client-card__media"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="1200" height="675" loading="lazy"></figure>
							<div class="d4w-client-card__copy"><h3><?php the_title(); ?></h3><?php if ( $summary ) : ?><p><?php echo esc_html( $summary ); ?></p><?php endif; ?></div>
						</a>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
	</div>
</section>
<?php endif; ?>

<?php if ( d4w_get_option( 'show_case_studies', true ) ) : ?>
<?php
$case_query = new WP_Query(
	array(
		'post_type'      => 'd4w_project',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'meta_key'       => '_d4w_featured_case_study',
		'meta_value'     => '1',
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	)
);
if ( $case_query->have_posts() ) : ?>
<section class="d4w-home-cases section-space-sm">
	<div class="container-fluid d4w-shell"><div class="row align-items-end mb-5 g-4"><div class="col-lg-4"><p class="d4w-section-label d4w-section-label--light reveal-up"><span>Cases</span><?php esc_html_e( 'Case studies', 'design4web' ); ?></p></div><div class="col-lg-6"><h2 class="d4w-display text-white reveal-text"><?php echo esc_html( d4w_get_option( 'case_studies_title' ) ); ?></h2></div><div class="col-lg-2 text-lg-end"><a class="d4w-text-link d4w-text-link--light reveal-up" href="<?php echo esc_url( get_post_type_archive_link( 'd4w_project' ) ); ?>"><?php esc_html_e( 'Read all cases', 'design4web' ); ?><i class="bi bi-arrow-right"></i></a></div></div><div class="d4w-case-strip">
	<?php while ( $case_query->have_posts() ) : $case_query->the_post(); $case_image = d4w_feature_image_url( get_the_ID(), 'project-' . ( $case_query->current_post + 1 ) . '.jpg', 'full' ); $case_terms = get_the_terms( get_the_ID(), 'd4w_project_type' ); ?><article class="d4w-home-case reveal-up"><a class="d4w-home-case__media d4w-image-curtain" href="<?php the_permalink(); ?>"><img src="<?php echo esc_url( $case_image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy"><span><?php echo esc_html( str_pad( (string) ( $case_query->current_post + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span></a><div class="d4w-home-case__copy"><p><?php echo esc_html( $case_terms && ! is_wp_error( $case_terms ) ? $case_terms[0]->name : __( 'Digital case study', 'design4web' ) ); ?></p><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><div><?php echo esc_html( d4w_card_excerpt( get_the_ID(), 24 ) ); ?></div><a class="d4w-text-link d4w-text-link--light" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Open case study', 'design4web' ); ?><i class="bi bi-arrow-up-right"></i></a></div></article><?php endwhile; wp_reset_postdata(); ?>
	</div></div>
</section>
<?php endif; endif; ?>

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
<section id="reviews" class="d4w-testimonials section-space" aria-labelledby="d4w-reviews-title">
	<div class="container-fluid d4w-shell">
		<div class="row align-items-center g-5">
			<div class="col-lg-4">
				<div class="d4w-review-proof reveal-up">
					<div class="d4w-testimonial-image"><img src="<?php echo esc_url( $testimonial_cover ); ?>" alt="<?php esc_attr_e( 'Creative team collaboration', 'design4web' ); ?>" width="542" height="571" loading="lazy"><span><?php esc_html_e( 'Built on trust', 'design4web' ); ?></span></div>
					<div class="d4w-google-review-summary" data-google-review-summary hidden>
						<span class="d4w-google-g" aria-hidden="true">G</span>
						<div><strong data-google-rating>—</strong><span><span data-google-count>—</span> <?php esc_html_e( 'Google reviews', 'design4web' ); ?></span></div>
					</div>
				</div>
			</div>
			<div class="col-lg-8">
				<div class="d-flex justify-content-between align-items-center mb-4">
					<p class="d4w-section-label reveal-up"><span>Reviews</span><?php esc_html_e( 'Google & client feedback', 'design4web' ); ?></p>
					<div class="d4w-slider-controls" aria-label="<?php esc_attr_e( 'Review carousel controls', 'design4web' ); ?>"><button class="d4w-testimonial-prev" type="button" aria-label="<?php esc_attr_e( 'Previous review', 'design4web' ); ?>"><i class="bi bi-arrow-left"></i></button><span class="d4w-slider-count" aria-live="polite"><b>01</b> / <span>01</span></span><button class="d4w-testimonial-next" type="button" aria-label="<?php esc_attr_e( 'Next review', 'design4web' ); ?>"><i class="bi bi-arrow-right"></i></button></div>
				</div>
				<div class="d4w-review-heading reveal-up"><h2 id="d4w-reviews-title"><?php echo esc_html( d4w_get_option( 'reviews_title' ) ); ?></h2><?php if ( d4w_get_option( 'google_reviews_url' ) ) : ?><a data-google-profile-link href="<?php echo esc_url( d4w_get_option( 'google_reviews_url' ) ); ?>" target="_blank" rel="noopener noreferrer"><span class="d4w-google-g" aria-hidden="true">G</span><span><?php esc_html_e( 'Find us on Google', 'design4web' ); ?></span><i class="bi bi-arrow-up-right"></i></a><?php endif; ?></div>
				<div class="d4w-testimonial-track reveal-up" data-google-reviews-track aria-live="polite" aria-busy="false">
					<?php
					$testimonial_query = new WP_Query( array( 'post_type' => 'd4w_testimonial', 'posts_per_page' => 10, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ), 'no_found_rows' => true ) );
					while ( $testimonial_query->have_posts() ) :
						$testimonial_query->the_post();
						$rating        = min( 5, max( 1, (int) get_post_meta( get_the_ID(), '_d4w_rating', true ) ) );
						$review_source = get_post_meta( get_the_ID(), '_d4w_review_source', true ) ?: __( 'Client feedback', 'design4web' );
						$review_url    = get_post_meta( get_the_ID(), '_d4w_review_url', true );
						?>
						<article class="d4w-testimonial-slide d4w-review-card">
							<div class="d4w-review-card__top"><div class="d4w-stars" aria-label="<?php echo esc_attr( sprintf( __( '%d out of 5 stars', 'design4web' ), $rating ) ); ?>"><?php for ( $star = 0; $star < 5; $star++ ) : ?><i class="bi <?php echo $star < $rating ? 'bi-star-fill' : 'bi-star'; ?>"></i><?php endfor; ?></div><i class="bi bi-quote" aria-hidden="true"></i></div>
							<blockquote>“<?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?>”</blockquote>
							<div class="d4w-testimonial-author"><?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'thumbnail', array( 'loading' => 'lazy' ) ); else : ?><span class="d4w-testimonial-avatar" aria-hidden="true"><?php echo esc_html( strtoupper( substr( get_the_title(), 0, 1 ) ) ); ?></span><?php endif; ?><div><strong><?php the_title(); ?></strong><span><?php echo esc_html( get_post_meta( get_the_ID(), '_d4w_role', true ) ); ?></span><?php if ( $review_url ) : ?><a href="<?php echo esc_url( $review_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $review_source ); ?><i class="bi bi-arrow-up-right"></i></a><?php else : ?><small><?php echo esc_html( $review_source ); ?></small><?php endif; ?></div></div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
				<p class="d4w-google-review-notice" data-google-review-notice hidden><span translate="no">Google Maps</span> <?php esc_html_e( 'reviews are displayed in Google’s relevance order. Reviews are not verified by Google, but Google checks for and removes fake content when identified.', 'design4web' ); ?> <a href="https://support.google.com/contributionpolicy/answer/7400114" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Review policy', 'design4web' ); ?></a><span data-google-attributions></span></p>
				<p class="screen-reader-text" data-google-review-status aria-live="polite"></p>
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

<?php if ( d4w_get_option( 'show_social', true ) ) : ?>
<?php $social_query = new WP_Query( array( 'post_type' => 'd4w_social', 'post_status' => 'publish', 'posts_per_page' => 6, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ), 'no_found_rows' => true ) ); if ( $social_query->have_posts() ) : ?>
<section class="d4w-social-feed section-space-sm"><div class="container-fluid d4w-shell"><div class="row align-items-end mb-5 g-4"><div class="col-lg-4"><p class="d4w-section-label reveal-up"><span>Social</span><?php esc_html_e( 'Studio & Instagram feed', 'design4web' ); ?></p></div><div class="col-lg-6"><h2 class="d4w-display reveal-text"><?php echo esc_html( d4w_get_option( 'social_title' ) ); ?></h2></div><div class="col-lg-2 text-lg-end"><?php if ( d4w_get_option( 'instagram_url' ) ) : ?><a class="d4w-text-link reveal-up" href="<?php echo esc_url( d4w_get_option( 'instagram_url' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Follow Instagram', 'design4web' ); ?><i class="bi bi-instagram"></i></a><?php endif; ?></div></div><div class="d4w-social-grid">
	<?php while ( $social_query->have_posts() ) : $social_query->the_post(); $social_url = get_post_meta( get_the_ID(), '_d4w_social_url', true ) ?: d4w_get_option( 'instagram_url' ); $social_image = d4w_feature_image_url( get_the_ID(), 'project-' . ( ( $social_query->current_post % 4 ) + 1 ) . '.jpg', 'large' ); $social_tag = $social_url ? 'a' : 'article'; ?>
	<<?php echo esc_attr( $social_tag ); ?> class="d4w-social-card d4w-image-curtain reveal-up" <?php if ( $social_url ) : ?>href="<?php echo esc_url( $social_url ); ?>" target="_blank" rel="noopener noreferrer"<?php endif; ?>><img src="<?php echo esc_url( $social_image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy"><span class="d4w-social-card__overlay"><span><i class="bi bi-instagram"></i><?php echo esc_html( get_post_meta( get_the_ID(), '_d4w_social_handle', true ) ?: get_bloginfo( 'name' ) ); ?></span><strong><?php the_title(); ?></strong><small><?php echo esc_html( d4w_card_excerpt( get_the_ID(), 16 ) ); ?></small></span></<?php echo esc_attr( $social_tag ); ?>>
	<?php endwhile; wp_reset_postdata(); ?>
</div></div></section>
<?php endif; endif; ?>

<?php d4w_contact_panel( 'home' ); ?>

<?php get_footer(); ?>

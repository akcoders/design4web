<?php
/**
 * Services archive.
 *
 * @package Design4Web
 */
get_header();
$fallback_images = array( 'service-web-design.jpg', 'service-development.jpg', 'service-marketing.jpg', 'service-hosting.jpg' );
?>

<section class="d4w-inner-hero d4w-inner-hero--services">
	<div class="container-fluid d4w-shell position-relative">
		<p class="d4w-section-label d4w-section-label--light hero-reveal"><span>01</span><?php esc_html_e( 'Capabilities', 'design4web' ); ?></p>
		<div class="row align-items-end g-4">
			<div class="col-xl-9"><h1 class="d4w-page-title"><?php esc_html_e( 'Services built around real business momentum.', 'design4web' ); ?></h1></div>
			<div class="col-xl-3"><p class="d4w-inner-hero__lead hero-reveal"><?php esc_html_e( 'Strategy, design, development, growth and dependable support—connected as one focused partnership.', 'design4web' ); ?></p></div>
		</div>
	</div>
</section>

<section class="d4w-service-archive section-space-sm">
	<div class="container-fluid d4w-shell">
		<div class="d4w-service-expanders">
			<?php
			$service_index = 0;
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					++$service_index;
					$service_id  = get_the_ID();
					$panel_id    = 'service-panel-' . $service_id;
					$icon        = get_post_meta( $service_id, '_d4w_icon', true ) ?: 'bi-asterisk';
					$label       = get_post_meta( $service_id, '_d4w_short_label', true ) ?: __( 'Digital capability', 'design4web' );
					$deliverables= d4w_lines( get_post_meta( $service_id, '_d4w_deliverables', true ) );
					$image        = d4w_feature_image_url( $service_id, $fallback_images[ ( $service_index - 1 ) % count( $fallback_images ) ], 'd4w-service' );
					$is_open      = 1 === $service_index;
					?>
					<article class="d4w-service-expander reveal-up <?php echo $is_open ? 'is-active' : ''; ?>" data-cursor-label="EXPLORE">
						<button class="d4w-service-expander__toggle" type="button" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
							<span class="d4w-service-expander__number"><?php echo esc_html( str_pad( (string) $service_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
							<span class="d4w-service-expander__icon"><i class="bi <?php echo esc_attr( $icon ); ?>"></i></span>
							<span class="d4w-service-expander__heading"><small><?php echo esc_html( $label ); ?></small><strong><?php the_title(); ?></strong></span>
							<span class="d4w-service-expander__summary"><?php echo esc_html( d4w_card_excerpt( $service_id, 18 ) ); ?></span>
							<span class="d4w-service-expander__arrow"><i class="bi bi-arrow-down-right"></i></span>
						</button>
						<div class="d4w-service-expander__panel" id="<?php echo esc_attr( $panel_id ); ?>" aria-hidden="<?php echo $is_open ? 'false' : 'true'; ?>" <?php echo $is_open ? '' : 'inert'; ?>>
							<div class="d4w-service-expander__panel-inner">
								<a class="d4w-service-expander__media d4w-image-curtain" href="<?php the_permalink(); ?>"><img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="720" height="620" loading="lazy"></a>
								<div class="d4w-service-expander__copy"><p><?php echo esc_html( d4w_card_excerpt( $service_id, 42 ) ); ?></p><a class="d4w-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Explore service', 'design4web' ); ?><i class="bi bi-arrow-right"></i></a></div>
								<?php if ( $deliverables ) : ?><ul class="d4w-check-list"><?php foreach ( array_slice( $deliverables, 0, 5 ) as $item ) : ?><li><i class="bi bi-check2"></i><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul><?php endif; ?>
							</div>
						</div>
					</article>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="d4w-empty-state"><h2><?php esc_html_e( 'New services are being prepared.', 'design4web' ); ?></h2><p><?php esc_html_e( 'Tell us what you need and we will recommend the right approach.', 'design4web' ); ?></p></div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
$process = new WP_Query( array( 'post_type' => 'd4w_process', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
if ( $process->have_posts() ) :
	?>
	<section class="d4w-process d4w-process--compact section-space-sm">
		<div class="container-fluid d4w-shell">
			<div class="row g-5"><div class="col-lg-5"><p class="d4w-section-label d4w-section-label--light reveal-up"><span>02</span><?php esc_html_e( 'How we work', 'design4web' ); ?></p><h2 class="d4w-display text-white reveal-text"><?php echo esc_html( d4w_get_option( 'process_title' ) ); ?></h2></div><div class="col-lg-7">
				<?php while ( $process->have_posts() ) : $process->the_post(); $number = get_post_meta( get_the_ID(), '_d4w_process_number', true ); $process_icon = get_post_meta( get_the_ID(), '_d4w_process_icon', true ) ?: 'bi-arrow-down-right'; ?>
					<div class="d4w-process-step reveal-up"><span><?php echo esc_html( $number ?: str_pad( (string) ( $process->current_post + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><h3><?php the_title(); ?></h3><p><?php echo esc_html( d4w_card_excerpt( get_the_ID(), 22 ) ); ?></p><i class="bi <?php echo esc_attr( $process_icon ); ?>"></i></div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div></div>
		</div>
	</section>
<?php endif; ?>

<?php d4w_contact_panel( 'services' ); ?>
<?php get_footer(); ?>

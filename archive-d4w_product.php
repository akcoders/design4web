<?php
/**
 * Products archive.
 *
 * @package Design4Web
 */
get_header();
$product_count = wp_count_posts( 'd4w_product' );
$published     = $product_count ? (int) $product_count->publish : 0;
?>

<section class="d4w-product-hero d4w-motion-section">
	<div class="d4w-product-hero__orb d4w-parallax" data-speed="-0.035" aria-hidden="true"><span></span><span></span><span></span></div>
	<div class="container-fluid d4w-shell position-relative">
		<div class="row align-items-end g-5">
			<div class="col-xl-8">
				<p class="d4w-section-label d4w-section-label--light hero-reveal"><span>Products</span><?php esc_html_e( 'WhatsApp growth stack', 'design4web' ); ?></p>
				<h1 class="d4w-page-title"><?php esc_html_e( 'Turn every message into a better customer journey.', 'design4web' ); ?></h1>
			</div>
			<div class="col-xl-4">
				<p class="d4w-inner-hero__lead hero-reveal"><?php esc_html_e( 'From official API onboarding and marketing campaigns to chatbots, AI assistance, QR journeys and verification readiness—choose the capability your next stage needs.', 'design4web' ); ?></p>
				<div class="d4w-product-hero__stats hero-reveal"><div><strong><?php echo esc_html( str_pad( (string) $published, 2, '0', STR_PAD_LEFT ) ); ?></strong><small><?php esc_html_e( 'connected products', 'design4web' ); ?></small></div><div><strong>24/7</strong><small><?php esc_html_e( 'conversation potential', 'design4web' ); ?></small></div></div>
			</div>
		</div>
	</div>
</section>

<section class="d4w-product-archive section-space-sm">
	<div class="container-fluid d4w-shell">
		<div class="d4w-product-grid">
			<?php
			$product_index = 0;
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					++$product_index;
					$icon   = get_post_meta( get_the_ID(), '_d4w_product_icon', true ) ?: 'bi-box';
					$kicker = get_post_meta( get_the_ID(), '_d4w_product_kicker', true );
					$accent = sanitize_hex_color( get_post_meta( get_the_ID(), '_d4w_product_accent', true ) ) ?: '#7c4dff';
					$features = d4w_lines( get_post_meta( get_the_ID(), '_d4w_product_features', true ) );
					?>
					<article class="d4w-product-card d4w-hover-card reveal-up" style="--product-accent:<?php echo esc_attr( $accent ); ?>">
						<a class="d4w-product-card__link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Explore %s', 'design4web' ), get_the_title() ) ); ?>"></a>
						<div class="d4w-product-card__top"><span class="d4w-product-card__number"><?php echo esc_html( str_pad( (string) $product_index, 2, '0', STR_PAD_LEFT ) ); ?></span><span class="d4w-product-card__icon"><i class="bi <?php echo esc_attr( $icon ); ?>"></i></span><i class="bi bi-arrow-up-right d4w-product-card__arrow"></i></div>
						<div class="d4w-product-card__visual" aria-hidden="true"><span class="d4w-product-pulse"></span><i class="bi <?php echo esc_attr( $icon ); ?>"></i><span class="d4w-product-dot"></span><span class="d4w-product-dot"></span><span class="d4w-product-dot"></span></div>
						<div class="d4w-product-card__copy"><?php if ( $kicker ) : ?><p><?php echo esc_html( $kicker ); ?></p><?php endif; ?><h2><?php the_title(); ?></h2><div><?php echo esc_html( d4w_card_excerpt( get_the_ID(), 25 ) ); ?></div><?php if ( $features ) : ?><ul><?php foreach ( array_slice( $features, 0, 3 ) as $feature ) : ?><li><i class="bi bi-check2"></i><?php echo esc_html( $feature ); ?></li><?php endforeach; ?></ul><?php endif; ?></div>
					</article>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="d4w-empty-state"><h2><?php esc_html_e( 'Products are being prepared.', 'design4web' ); ?></h2></div>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="d4w-product-bridge section-space-sm">
	<div class="container-fluid d4w-shell"><div class="d4w-product-bridge__inner d4w-hover-surface"><div><p class="d4w-section-label d4w-section-label--light reveal-up"><span>Plan</span><?php esc_html_e( 'Choose your route', 'design4web' ); ?></p><h2 class="d4w-display text-white reveal-text"><?php esc_html_e( 'Not sure where to begin? Start with the customer journey.', 'design4web' ); ?></h2></div><div class="d4w-product-bridge__actions reveal-up"><a class="d4w-btn d4w-btn--light magnetic" href="<?php echo esc_url( d4w_page_url( 'pricing' ) ); ?>"><span><?php esc_html_e( 'Explore pricing', 'design4web' ); ?></span><i class="bi bi-arrow-right"></i></a><a class="d4w-text-link d4w-text-link--light" href="<?php echo esc_url( d4w_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Talk to a specialist', 'design4web' ); ?><i class="bi bi-arrow-up-right"></i></a></div></div></div>
</section>

<?php d4w_contact_panel( 'products' ); ?>
<?php get_footer(); ?>

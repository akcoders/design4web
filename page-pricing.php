<?php
/**
 * Creative pricing page.
 *
 * Template Name: Design4web Pricing
 * Template Post Type: page
 *
 * @package Design4Web
 */
get_header();
$plans = new WP_Query( array( 'post_type' => 'd4w_plan', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
?>

<section class="d4w-pricing-hero d4w-motion-section">
	<div class="d4w-pricing-hero__rings" aria-hidden="true"><span></span><span></span><span></span></div>
	<div class="container-fluid d4w-shell position-relative text-center"><p class="d4w-section-label d4w-section-label--light justify-content-center hero-reveal"><span>Pricing</span><?php esc_html_e( 'Clear scopes, flexible routes', 'design4web' ); ?></p><h1 class="d4w-page-title"><?php esc_html_e( 'Choose momentum. We will shape the right scope.', 'design4web' ); ?></h1><p class="d4w-pricing-hero__lead hero-reveal"><?php esc_html_e( 'Every organisation has a different audience, toolset and level of readiness. Start with the closest plan, then receive a transparent scope before work begins.', 'design4web' ); ?></p><div class="d4w-pricing-toggle hero-reveal" role="group" aria-label="<?php esc_attr_e( 'Pricing mode', 'design4web' ); ?>"><button type="button" class="is-active" data-pricing-mode="monthly" aria-pressed="true"><?php esc_html_e( 'Managed monthly', 'design4web' ); ?></button><button type="button" data-pricing-mode="yearly" aria-pressed="false"><?php esc_html_e( 'Project / annual', 'design4web' ); ?></button><span aria-hidden="true"></span></div></div>
</section>

<section class="d4w-pricing-plans section-space-sm">
	<div class="container-fluid d4w-shell"><div class="row g-4 align-items-stretch">
		<?php if ( $plans->have_posts() ) : while ( $plans->have_posts() ) : $plans->the_post();
			$plan_id      = get_the_ID();
			$monthly     = get_post_meta( $plan_id, '_d4w_plan_monthly', true ) ?: __( 'Custom scope', 'design4web' );
			$yearly      = get_post_meta( $plan_id, '_d4w_plan_yearly', true ) ?: $monthly;
			$monthly_note= get_post_meta( $plan_id, '_d4w_plan_monthly_note', true );
			$yearly_note = get_post_meta( $plan_id, '_d4w_plan_yearly_note', true ) ?: $monthly_note;
			$badge       = get_post_meta( $plan_id, '_d4w_plan_badge', true );
			$features    = d4w_lines( get_post_meta( $plan_id, '_d4w_plan_features', true ) );
			$cta         = get_post_meta( $plan_id, '_d4w_plan_cta', true ) ?: __( 'Request a scope', 'design4web' );
			$featured    = '1' === get_post_meta( $plan_id, '_d4w_plan_featured', true );
			?>
			<div class="col-lg-4"><article class="d4w-price-card d4w-hover-card reveal-up <?php echo $featured ? 'is-featured' : ''; ?>"><?php if ( $badge ) : ?><span class="d4w-price-card__badge"><i class="bi bi-stars"></i><?php echo esc_html( $badge ); ?></span><?php endif; ?><div class="d4w-price-card__head"><span><?php echo esc_html( str_pad( (string) ( $plans->current_post + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><h2><?php the_title(); ?></h2><div><?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?></div></div><div class="d4w-price-card__price"><strong data-monthly="<?php echo esc_attr( $monthly ); ?>" data-yearly="<?php echo esc_attr( $yearly ); ?>"><?php echo esc_html( $monthly ); ?></strong><small data-monthly="<?php echo esc_attr( $monthly_note ); ?>" data-yearly="<?php echo esc_attr( $yearly_note ); ?>"><?php echo esc_html( $monthly_note ); ?></small></div><?php if ( $features ) : ?><ul><?php foreach ( $features as $feature ) : ?><li><i class="bi bi-check2"></i><?php echo esc_html( $feature ); ?></li><?php endforeach; ?></ul><?php endif; ?><a class="d4w-btn <?php echo $featured ? 'd4w-btn--light' : 'd4w-btn--gradient'; ?> magnetic" href="<?php echo esc_url( add_query_arg( 'plan', sanitize_title( get_the_title() ), d4w_page_url( 'contact' ) ) ); ?>"><span><?php echo esc_html( $cta ); ?></span><i class="bi bi-arrow-up-right"></i></a></article></div>
		<?php endwhile; wp_reset_postdata(); else : ?><div class="col-12 d4w-empty-state"><h2><?php esc_html_e( 'Pricing plans are being prepared.', 'design4web' ); ?></h2></div><?php endif; ?>
	</div><p class="d4w-pricing-disclaimer reveal-up"><i class="bi bi-info-circle"></i><?php esc_html_e( 'Displayed scopes cover Design4web strategy, creative and implementation work. WhatsApp/Meta message charges, third-party subscriptions, ad spend, taxes and provider fees are quoted separately where applicable.', 'design4web' ); ?></p></div>
</section>

<section class="d4w-pricing-includes section-space-sm"><div class="container-fluid d4w-shell"><div class="row g-5"><div class="col-lg-5"><p class="d4w-section-label d4w-section-label--light reveal-up"><span>Included</span><?php esc_html_e( 'Every engagement', 'design4web' ); ?></p><h2 class="d4w-display text-white reveal-text"><?php esc_html_e( 'A senior, practical team around the work.', 'design4web' ); ?></h2></div><div class="col-lg-7"><div class="d4w-pricing-includes__grid"><?php $includes = array( 'Discovery and success criteria', 'Clear scope and responsibilities', 'Responsive design and content thinking', 'Testing across current devices', 'Admin-friendly handover', 'Post-launch next-step plan' ); foreach ( $includes as $index => $include ) : ?><article class="reveal-up"><span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><h3><?php echo esc_html( $include ); ?></h3><i class="bi bi-arrow-down-right"></i></article><?php endforeach; ?></div></div></div></div></section>

<?php d4w_contact_panel( 'pricing' ); ?>
<?php get_footer(); ?>

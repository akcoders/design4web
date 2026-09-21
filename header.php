<?php
/**
 * Site header.
 *
 * @package Design4Web
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script>document.documentElement.classList.add('d4w-js');window.setTimeout(function(){document.documentElement.classList.add('d4w-js-timeout');},4000);</script>
	<?php if ( ! has_site_icon() ) : ?><link rel="icon" type="image/png" href="<?php echo esc_url( D4W_URI . '/assets/images/logo.png' ); ?>"><?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'design4web' ); ?></a>

<?php if ( d4w_get_option( 'enable_motion', true ) && d4w_get_option( 'enable_preloader', true ) ) : ?>
	<div class="d4w-preloader" aria-hidden="true">
		<div class="d4w-preloader__mark">D<span>4</span>W</div>
		<div class="d4w-preloader__line"><span></span></div>
		<div class="d4w-preloader__count">00</div>
	</div>
<?php endif; ?>

<?php if ( d4w_get_option( 'enable_motion', true ) && d4w_get_option( 'enable_cursor', true ) ) : ?>
	<div class="d4w-cursor" aria-hidden="true"><span class="d4w-cursor-label"></span></div>
	<div class="d4w-cursor-dot" aria-hidden="true"></div>
<?php endif; ?>
<div class="d4w-scroll-progress" aria-hidden="true"></div>
<?php if ( d4w_get_option( 'enable_motion', true ) && d4w_get_option( 'enable_page_transitions', true ) ) : ?>
	<div class="d4w-page-transition" aria-hidden="true"><span>D4W</span></div>
	<?php endif; ?>
<?php if ( d4w_get_option( 'enable_motion', true ) && d4w_get_option( 'enable_motion_loops', true ) ) : ?>
	<div class="d4w-ambient-grain" aria-hidden="true"></div>
<?php endif; ?>

<header id="masthead" class="site-header">
	<div class="container-fluid d4w-header-container">
		<div class="d-flex align-items-center justify-content-between">
			<div class="site-branding">
				<a class="d4w-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<?php if ( has_custom_logo() ) : ?>
						<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'custom-logo', 'alt' => get_bloginfo( 'name' ) ) ); ?>
					<?php else : ?>
						<img src="<?php echo esc_url( D4W_URI . '/assets/images/logo.png' ); ?>" width="326" height="77" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<?php endif; ?>
				</a>
			</div>

			<nav class="d-none d-xl-flex align-items-center ms-auto" aria-label="<?php esc_attr_e( 'Primary navigation', 'design4web' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'navbar-nav flex-row align-items-center',
						'fallback_cb'    => 'd4w_primary_menu_fallback',
						'depth'          => 2,
					)
				);
				?>
			</nav>

			<a class="d4w-header-cta d-none d-xl-inline-flex magnetic" href="<?php echo esc_url( d4w_page_url( 'contact', home_url( '/#contact' ) ) ); ?>">
				<span><?php esc_html_e( 'Let’s talk', 'design4web' ); ?></span><i class="bi bi-arrow-up-right"></i>
			</a>

			<button class="d4w-menu-toggle d-xl-none" type="button" aria-expanded="false" aria-controls="d4w-mobile-menu" aria-label="<?php esc_attr_e( 'Open menu', 'design4web' ); ?>">
				<span></span><span></span>
			</button>
		</div>
	</div>
</header>

<div id="d4w-mobile-menu" class="d4w-mobile-menu" aria-hidden="true">
	<div class="d4w-mobile-menu__backdrop"></div>
	<div class="d4w-mobile-menu__inner">
		<div class="d4w-mobile-menu__label"><?php esc_html_e( 'Navigation', 'design4web' ); ?></div>
		<nav aria-label="<?php esc_attr_e( 'Mobile navigation', 'design4web' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'd4w-mobile-nav',
					'fallback_cb'    => 'd4w_primary_menu_fallback',
					'depth'          => 2,
				)
			);
			?>
		</nav>
		<div class="d4w-mobile-contact">
			<a href="mailto:<?php echo esc_attr( d4w_get_option( 'contact_email' ) ); ?>"><?php echo esc_html( d4w_get_option( 'contact_email' ) ); ?></a>
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', d4w_get_option( 'phone' ) ) ); ?>"><?php echo esc_html( d4w_get_option( 'phone' ) ); ?></a>
		</div>
	</div>
</div>

<main id="main-content" class="site-main" tabindex="-1">

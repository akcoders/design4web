<?php
/**
 * Customizer settings.
 *
 * @package Design4Web
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function d4w_defaults() {
	return array(
		'primary_color'       => '#7c4dff',
		'accent_color'        => '#ff3eb5',
		'dark_color'          => '#111522',
		'enable_preloader'     => true,
		'enable_cursor'        => true,
		'enable_motion'        => true,
		'enable_page_transitions' => true,
		'enable_parallax'      => true,
		'enable_motion_loops'  => true,
		'hero_eyebrow'         => 'INDEPENDENT DIGITAL CREATIVE AGENCY',
		'hero_title'           => 'We Design Digital Experiences That Make Brands',
		'hero_highlight'       => 'Impossible To Ignore.',
		'hero_text'            => 'Design4web blends strategy, expressive design and dependable technology to create websites that look remarkable and work hard for your business.',
		'hero_button_text'     => 'Start a project',
		'hero_button_url'      => '#contact',
		'hero_secondary_text'  => 'Explore our work',
		'hero_secondary_url'   => '#work',
		'about_label'          => 'About Design4web',
		'about_title'          => 'Ideas with purpose. Design with personality. Technology that performs.',
		'about_text'           => 'Design4web is a web development and hosting company creating strong digital identities for ambitious businesses. From the first sketch to launch and ongoing care, we keep the experience clear, collaborative and focused on results.',
		'services_title'       => 'Everything your brand needs to look sharp and move forward.',
		'products_title'       => 'WhatsApp products that connect campaigns, conversations and customer care.',
		'work_title'           => 'Creative work, built to solve real business problems.',
		'case_studies_title'    => 'A closer look at the thinking behind selected outcomes.',
		'reviews_title'         => 'Good partnerships leave a lasting impression.',
		'social_title'          => 'Fresh launches, visual experiments and studio moments.',
		'tech_kicker'          => 'Technology chosen for the job—not the hype',
		'tech_title'           => 'Flexible tools. Reliable results.',
		'tech_list'            => "WordPress\nWooCommerce\nPHP\nLaravel\nJavaScript\njQuery\nBootstrap\nReact\nMySQL\nGoogle Cloud\nAWS\nSEO",
		'experience_years'     => '14',
		'projects_count'       => '250',
		'client_satisfaction'  => '98',
		'support_label'        => '24/7',
		'process_title'        => 'From first conversation to a launch that feels effortless.',
		'process_text'         => 'A transparent, focused approach keeps the work moving and gives every decision a reason.',
		'insights_title'       => 'Useful thinking for ambitious digital brands.',
		'cta_title'            => 'Have a project in mind? Let’s create something people remember.',
		'cta_text'             => 'Tell us what you are building. We will bring the right mix of strategy, creativity and technology.',
		'footer_intro'         => 'Websites, brands and digital systems created with curiosity, precision and a very human point of view.',
		'phone'                => '+91 99679 96645',
		'whatsapp'             => '919967996645',
		'contact_email'        => 'info@design4web.in',
		'address'              => "7A/B, 1st Floor, Adugiya Compound\nNear Darshan Photo Studio, Mamletdarwadi Main Road\nMalad West, Mumbai 400064, Maharashtra, India",
		'facebook_url'         => 'https://www.facebook.com/designforwebdevelopment',
		'twitter_url'          => 'https://twitter.com/design4website',
		'instagram_url'        => '',
		'linkedin_url'         => '',
		'google_reviews_url'    => 'https://www.google.com/maps/search/?api=1&query=Design4web+Malad+West+Mumbai',
		'google_map_embed_url'  => '',
		'show_services'        => true,
		'show_products'        => true,
		'show_projects'        => true,
		'show_case_studies'    => true,
		'show_process'         => true,
		'show_testimonials'    => true,
		'show_social'          => true,
		'show_blog'            => true,
	);
}

function d4w_get_option( $key, $fallback = '' ) {
	$defaults = d4w_defaults();
	$default  = array_key_exists( $key, $defaults ) ? $defaults[ $key ] : $fallback;
	return get_theme_mod( 'd4w_' . $key, $default );
}

function d4w_sanitize_checkbox( $checked ) {
	return (bool) $checked;
}

function d4w_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'd4w_options',
		array(
			'title'       => __( 'Design4web Theme Options', 'design4web' ),
			'description' => __( 'Control homepage content, brand colors, contact details and motion options.', 'design4web' ),
			'priority'    => 30,
		)
	);

	$sections = array(
		'brand'   => array( 'Brand & Motion', 10 ),
		'hero'    => array( 'Hero Section', 20 ),
		'about'   => array( 'About & Statistics', 30 ),
		'home'    => array( 'Homepage Sections', 40 ),
		'contact' => array( 'Contact & Social', 50 ),
	);
	foreach ( $sections as $id => $section ) {
		$wp_customize->add_section(
			'd4w_' . $id,
			array(
				'title'    => __( $section[0], 'design4web' ),
				'panel'    => 'd4w_options',
				'priority' => $section[1],
			)
		);
	}

	$colors = array(
		'primary_color' => array( 'Primary violet', '#7c4dff' ),
		'accent_color'  => array( 'Accent pink', '#ff3eb5' ),
		'dark_color'    => array( 'Dark background', '#111522' ),
	);
	foreach ( $colors as $id => $color ) {
		$wp_customize->add_setting( 'd4w_' . $id, array( 'default' => $color[1], 'sanitize_callback' => 'sanitize_hex_color' ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'd4w_' . $id, array( 'label' => __( $color[0], 'design4web' ), 'section' => 'd4w_brand' ) ) );
	}

	$checkboxes = array(
		'enable_motion'     => array( 'Enable advanced motion effects', 'd4w_brand' ),
		'enable_preloader'  => array( 'Enable animated preloader', 'd4w_brand' ),
		'enable_page_transitions' => array( 'Enable page-to-page transitions', 'd4w_brand' ),
		'enable_parallax'   => array( 'Enable scroll parallax', 'd4w_brand' ),
		'enable_motion_loops' => array( 'Enable decorative looping motion', 'd4w_brand' ),
		'enable_cursor'     => array( 'Enable creative desktop cursor', 'd4w_brand' ),
		'show_services'     => array( 'Show services section', 'd4w_home' ),
		'show_products'     => array( 'Show products section', 'd4w_home' ),
		'show_projects'     => array( 'Show projects section', 'd4w_home' ),
		'show_case_studies' => array( 'Show case studies section', 'd4w_home' ),
		'show_process'      => array( 'Show process section', 'd4w_home' ),
		'show_testimonials' => array( 'Show testimonials section', 'd4w_home' ),
		'show_social'       => array( 'Show social feed section', 'd4w_home' ),
		'show_blog'         => array( 'Show blog section', 'd4w_home' ),
	);
	foreach ( $checkboxes as $id => $data ) {
		$wp_customize->add_setting( 'd4w_' . $id, array( 'default' => true, 'sanitize_callback' => 'd4w_sanitize_checkbox' ) );
		$wp_customize->add_control( 'd4w_' . $id, array( 'label' => __( $data[0], 'design4web' ), 'section' => $data[1], 'type' => 'checkbox' ) );
	}

	$text_fields = array(
		'hero_eyebrow'        => array( 'Eyebrow', 'd4w_hero', 'text' ),
		'hero_title'          => array( 'Main title', 'd4w_hero', 'text' ),
		'hero_highlight'      => array( 'Gradient title line', 'd4w_hero', 'text' ),
		'hero_text'           => array( 'Description', 'd4w_hero', 'textarea' ),
		'hero_button_text'    => array( 'Primary button label', 'd4w_hero', 'text' ),
		'hero_secondary_text' => array( 'Secondary button label', 'd4w_hero', 'text' ),
		'about_label'         => array( 'Section label', 'd4w_about', 'text' ),
		'about_title'         => array( 'About title', 'd4w_about', 'textarea' ),
		'about_text'          => array( 'About description', 'd4w_about', 'textarea' ),
		'services_title'      => array( 'Services section title', 'd4w_home', 'textarea' ),
		'products_title'      => array( 'Products section title', 'd4w_home', 'textarea' ),
		'work_title'          => array( 'Work section title', 'd4w_home', 'textarea' ),
		'case_studies_title'   => array( 'Case studies section title', 'd4w_home', 'textarea' ),
		'reviews_title'        => array( 'Reviews section title', 'd4w_home', 'textarea' ),
		'social_title'         => array( 'Social feed section title', 'd4w_home', 'textarea' ),
		'tech_kicker'         => array( 'Technology section kicker', 'd4w_home', 'text' ),
		'tech_title'          => array( 'Technology section title', 'd4w_home', 'text' ),
		'tech_list'           => array( 'Technologies (one per line)', 'd4w_home', 'textarea' ),
		'experience_years'    => array( 'Years of experience', 'd4w_about', 'number' ),
		'projects_count'      => array( 'Projects completed', 'd4w_about', 'number' ),
		'client_satisfaction' => array( 'Client satisfaction %', 'd4w_about', 'number' ),
		'support_label'       => array( 'Support label', 'd4w_about', 'text' ),
		'process_title'       => array( 'Process title', 'd4w_home', 'textarea' ),
		'process_text'        => array( 'Process description', 'd4w_home', 'textarea' ),
		'insights_title'      => array( 'Journal section title', 'd4w_home', 'textarea' ),
		'cta_title'           => array( 'CTA title', 'd4w_home', 'textarea' ),
		'cta_text'            => array( 'CTA description', 'd4w_home', 'textarea' ),
		'footer_intro'        => array( 'Footer introduction', 'd4w_contact', 'textarea' ),
		'phone'               => array( 'Phone number', 'd4w_contact', 'text' ),
		'whatsapp'            => array( 'WhatsApp number (digits only)', 'd4w_contact', 'text' ),
		'contact_email'       => array( 'Contact email', 'd4w_contact', 'email' ),
		'address'             => array( 'Location / address', 'd4w_contact', 'textarea' ),
	);
	$defaults = d4w_defaults();
	foreach ( $text_fields as $id => $data ) {
		$sanitize = 'sanitize_text_field';
		if ( 'textarea' === $data[2] ) {
			$sanitize = 'sanitize_textarea_field';
		} elseif ( 'email' === $data[2] ) {
			$sanitize = 'sanitize_email';
		} elseif ( 'number' === $data[2] ) {
			$sanitize = 'absint';
		}
		$wp_customize->add_setting( 'd4w_' . $id, array( 'default' => $defaults[ $id ], 'sanitize_callback' => $sanitize ) );
		$wp_customize->add_control( 'd4w_' . $id, array( 'label' => __( $data[0], 'design4web' ), 'section' => $data[1], 'type' => $data[2] ) );
	}

	$url_fields = array(
		'hero_button_url'    => array( 'Primary button URL', 'd4w_hero' ),
		'hero_secondary_url' => array( 'Secondary button URL', 'd4w_hero' ),
		'facebook_url'       => array( 'Facebook URL', 'd4w_contact' ),
		'twitter_url'        => array( 'X / Twitter URL', 'd4w_contact' ),
		'instagram_url'      => array( 'Instagram URL', 'd4w_contact' ),
		'linkedin_url'       => array( 'LinkedIn URL', 'd4w_contact' ),
		'google_reviews_url' => array( 'Google reviews / business profile URL', 'd4w_contact' ),
		'google_map_embed_url' => array( 'Google Maps embed URL (optional)', 'd4w_contact' ),
	);
	foreach ( $url_fields as $id => $data ) {
		$wp_customize->add_setting( 'd4w_' . $id, array( 'default' => $defaults[ $id ], 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( 'd4w_' . $id, array( 'label' => __( $data[0], 'design4web' ), 'section' => $data[1], 'type' => 'url' ) );
	}
}
add_action( 'customize_register', 'd4w_customize_register' );

function d4w_customizer_css() {
	$primary = sanitize_hex_color( d4w_get_option( 'primary_color', '#7c4dff' ) );
	$accent  = sanitize_hex_color( d4w_get_option( 'accent_color', '#ff3eb5' ) );
	$dark    = sanitize_hex_color( d4w_get_option( 'dark_color', '#111522' ) );
	?>
	<style id="d4w-theme-vars">:root{--d4w-primary:<?php echo esc_html( $primary ); ?>;--d4w-accent:<?php echo esc_html( $accent ); ?>;--d4w-dark:<?php echo esc_html( $dark ); ?>;}</style>
	<?php
}
add_action( 'wp_head', 'd4w_customizer_css', 20 );

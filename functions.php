<?php
/**
 * Theme bootstrap and site functionality.
 *
 * @package Design4Web
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'D4W_VERSION', '1.0.0' );
define( 'D4W_DIR', get_template_directory() );
define( 'D4W_URI', get_template_directory_uri() );

require_once D4W_DIR . '/inc/customizer.php';

function d4w_setup() {
	load_theme_textdomain( 'design4web', D4W_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 77,
			'width'       => 326,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_image_size( 'd4w-project', 900, 1080, true );
	add_image_size( 'd4w-service', 720, 620, true );
	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'design4web' ),
			'footer'  => __( 'Footer navigation', 'design4web' ),
		)
	);
}
add_action( 'after_setup_theme', 'd4w_setup' );

function d4w_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'd4w_content_width', 1320 );
}
add_action( 'after_setup_theme', 'd4w_content_width', 0 );

function d4w_assets() {
	wp_enqueue_style( 'd4w-fonts', 'https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,500;0,600;0,700;0,800;0,900;1,700&family=DM+Sans:wght@400;500;600;700&display=swap', array(), null );
	wp_enqueue_style( 'd4w-bootstrap', D4W_URI . '/assets/vendor/bootstrap.min.css', array(), '5.3.8' );
	wp_enqueue_style( 'd4w-icons', D4W_URI . '/assets/vendor/bootstrap-icons.css', array(), '1.13.1' );
	wp_enqueue_style( 'd4w-style', get_stylesheet_uri(), array( 'd4w-bootstrap' ), D4W_VERSION );
	wp_enqueue_style( 'd4w-main', D4W_URI . '/assets/css/main.css', array( 'd4w-bootstrap', 'd4w-icons' ), D4W_VERSION );

	if ( ! is_admin() ) {
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', D4W_URI . '/assets/vendor/jquery.min.js', array(), '4.0.0', true );
	}
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'd4w-bootstrap', D4W_URI . '/assets/vendor/bootstrap.bundle.min.js', array(), '5.3.8', true );
	wp_enqueue_script( 'd4w-main', D4W_URI . '/assets/js/main.js', array( 'jquery' ), D4W_VERSION, true );
	wp_localize_script(
		'd4w-main',
		'd4wTheme',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'd4w_contact' ),
			'successText' => __( 'Thank you! Your message has been sent.', 'design4web' ),
			'errorText'   => __( 'Something went wrong. Please try again.', 'design4web' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'd4w_assets' );

function d4w_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.googleapis.com', 'crossorigin' => 'anonymous' );
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'd4w_resource_hints', 10, 2 );

function d4w_document_meta() {
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
		return;
	}
	$description = is_front_page() ? d4w_get_option( 'hero_text' ) : '';
	if ( is_singular() && has_excerpt() ) {
		$description = get_the_excerpt();
	}
	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $description ) ) . '">' . "\n";
	}
	if ( is_front_page() ) {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'ProfessionalService',
			'name'     => get_bloginfo( 'name' ),
			'url'      => home_url( '/' ),
			'email'    => d4w_get_option( 'contact_email' ),
			'telephone'=> d4w_get_option( 'phone' ),
			'areaServed' => 'India',
			'sameAs'   => array_values( array_filter( array( d4w_get_option( 'facebook_url' ), d4w_get_option( 'twitter_url' ), d4w_get_option( 'instagram_url' ), d4w_get_option( 'linkedin_url' ) ) ) ),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'd4w_document_meta', 4 );

function d4w_register_content_types() {
	register_post_type(
		'd4w_service',
		array(
			'labels' => array(
				'name'          => __( 'Services', 'design4web' ),
				'singular_name' => __( 'Service', 'design4web' ),
				'add_new_item'  => __( 'Add New Service', 'design4web' ),
				'edit_item'     => __( 'Edit Service', 'design4web' ),
			),
			'public'       => true,
			'menu_icon'    => 'dashicons-superhero-alt',
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'services' ),
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'd4w_project',
		array(
			'labels' => array(
				'name'          => __( 'Projects', 'design4web' ),
				'singular_name' => __( 'Project', 'design4web' ),
				'add_new_item'  => __( 'Add New Project', 'design4web' ),
				'edit_item'     => __( 'Edit Project', 'design4web' ),
			),
			'public'       => true,
			'menu_icon'    => 'dashicons-portfolio',
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'work' ),
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

	register_taxonomy(
		'd4w_project_type',
		'd4w_project',
		array(
			'labels'       => array( 'name' => __( 'Project Types', 'design4web' ), 'singular_name' => __( 'Project Type', 'design4web' ) ),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'work-type' ),
		)
	);

	register_post_type(
		'd4w_testimonial',
		array(
			'labels' => array(
				'name'          => __( 'Testimonials', 'design4web' ),
				'singular_name' => __( 'Testimonial', 'design4web' ),
				'add_new_item'  => __( 'Add New Testimonial', 'design4web' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-format-quote',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'd4w_register_content_types' );

function d4w_add_meta_boxes() {
	add_meta_box( 'd4w_service_details', __( 'Service Details', 'design4web' ), 'd4w_service_meta_box', 'd4w_service', 'side' );
	add_meta_box( 'd4w_project_details', __( 'Project Details', 'design4web' ), 'd4w_project_meta_box', 'd4w_project', 'normal' );
	add_meta_box( 'd4w_testimonial_details', __( 'Client Details', 'design4web' ), 'd4w_testimonial_meta_box', 'd4w_testimonial', 'side' );
}
add_action( 'add_meta_boxes', 'd4w_add_meta_boxes' );

function d4w_meta_field( $post_id, $key, $label, $type = 'text', $description = '' ) {
	$value = get_post_meta( $post_id, $key, true );
	?>
	<p>
		<label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label><br>
		<input class="widefat" type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
		<?php if ( $description ) : ?><small><?php echo esc_html( $description ); ?></small><?php endif; ?>
	</p>
	<?php
}

function d4w_service_meta_box( $post ) {
	wp_nonce_field( 'd4w_save_meta', 'd4w_meta_nonce' );
	d4w_meta_field( $post->ID, '_d4w_icon', __( 'Bootstrap icon class', 'design4web' ), 'text', __( 'Example: bi-code-slash', 'design4web' ) );
	d4w_meta_field( $post->ID, '_d4w_short_label', __( 'Short label', 'design4web' ) );
}

function d4w_project_meta_box( $post ) {
	wp_nonce_field( 'd4w_save_meta', 'd4w_meta_nonce' );
	d4w_meta_field( $post->ID, '_d4w_client', __( 'Client', 'design4web' ) );
	d4w_meta_field( $post->ID, '_d4w_year', __( 'Year', 'design4web' ), 'number' );
	d4w_meta_field( $post->ID, '_d4w_url', __( 'Project URL', 'design4web' ), 'url' );
}

function d4w_testimonial_meta_box( $post ) {
	wp_nonce_field( 'd4w_save_meta', 'd4w_meta_nonce' );
	d4w_meta_field( $post->ID, '_d4w_role', __( 'Role / company', 'design4web' ) );
	d4w_meta_field( $post->ID, '_d4w_rating', __( 'Rating (1-5)', 'design4web' ), 'number' );
}

function d4w_save_meta( $post_id ) {
	if ( ! isset( $_POST['d4w_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['d4w_meta_nonce'] ) ), 'd4w_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$fields = array(
		'_d4w_icon'        => 'sanitize_html_class',
		'_d4w_short_label' => 'sanitize_text_field',
		'_d4w_client'      => 'sanitize_text_field',
		'_d4w_year'        => 'absint',
		'_d4w_url'         => 'esc_url_raw',
		'_d4w_role'        => 'sanitize_text_field',
		'_d4w_rating'      => 'absint',
	);
	foreach ( $fields as $key => $callback ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, call_user_func( $callback, wp_unslash( $_POST[ $key ] ) ) );
		}
	}
}
add_action( 'save_post', 'd4w_save_meta' );

function d4w_primary_menu_fallback( $args = null ) {
	$items = array(
		__( 'Home', 'design4web' )     => '#home',
		__( 'About', 'design4web' )    => '#about',
		__( 'Services', 'design4web' ) => '#services',
		__( 'Work', 'design4web' )     => '#work',
		__( 'Process', 'design4web' )  => '#process',
		__( 'Contact', 'design4web' )  => '#contact',
	);
	$menu_class = 'navbar-nav flex-row align-items-center';
	if ( is_object( $args ) && ! empty( $args->menu_class ) ) {
		$menu_class = $args->menu_class;
	} elseif ( is_array( $args ) && ! empty( $args['menu_class'] ) ) {
		$menu_class = $args['menu_class'];
	}
	echo '<ul class="' . esc_attr( $menu_class ) . '">';
	foreach ( $items as $label => $url ) {
		echo '<li class="menu-item"><a href="' . esc_url( home_url( '/' ) . $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

function d4w_excerpt_length() {
	return 22;
}
add_filter( 'excerpt_length', 'd4w_excerpt_length', 999 );

function d4w_contact_form_handler() {
	if ( ! check_ajax_referer( 'd4w_contact', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Security check failed. Refresh and try again.', 'design4web' ) ), 403 );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$service = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $name || ! is_email( $email ) || ! $message ) {
		wp_send_json_error( array( 'message' => __( 'Please complete all required fields.', 'design4web' ) ), 422 );
	}

	$recipient = sanitize_email( d4w_get_option( 'contact_email', get_option( 'admin_email' ) ) );
	$subject   = sprintf( __( 'New website enquiry from %s', 'design4web' ), $name );
	$body      = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nService: {$service}\n\nMessage:\n{$message}";
	$headers   = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	if ( wp_mail( $recipient, $subject, $body, $headers ) ) {
		wp_send_json_success( array( 'message' => __( 'Thank you! Your message has been sent.', 'design4web' ) ) );
	}
	wp_send_json_error( array( 'message' => __( 'Message could not be sent. Please email or WhatsApp us.', 'design4web' ) ), 500 );
}
add_action( 'wp_ajax_d4w_contact', 'd4w_contact_form_handler' );
add_action( 'wp_ajax_nopriv_d4w_contact', 'd4w_contact_form_handler' );

function d4w_seed_content() {
	if ( get_option( 'd4w_seeded' ) ) {
		return;
	}

	$services = array(
		array( 'Web Design', 'Strategic interfaces and memorable digital experiences built around your brand.', 'bi-bezier2' ),
		array( 'Web Development', 'Fast, scalable and secure websites engineered for growth and easy management.', 'bi-code-slash' ),
		array( 'E-Commerce', 'Conversion-focused stores with simple catalog, payment and order workflows.', 'bi-bag-check' ),
		array( 'Domain & Hosting', 'Reliable domains, email and hosting with responsive technical support.', 'bi-cloud-check' ),
		array( 'SEO & Marketing', 'Search and social campaigns designed to turn attention into measurable demand.', 'bi-graph-up-arrow' ),
		array( 'Website Care', 'Updates, backups, security and ongoing improvements without the technical stress.', 'bi-shield-check' ),
	);
	foreach ( $services as $index => $service ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'd4w_service',
				'post_status'  => 'publish',
				'post_title'   => $service[0],
				'post_excerpt' => $service[1],
				'menu_order'   => $index,
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_d4w_icon', $service[2] );
		}
	}

	$projects = array(
		array( 'WhatsAppIndia Platform', 'Product Design', 'A responsive product experience created to help a growing digital community connect.', '2026' ),
		array( 'Commerce Experience', 'E-Commerce', 'A confident storefront concept shaped around discovery, trust and effortless checkout.', '2026' ),
		array( 'Growth Campaign', 'Digital Marketing', 'An energetic digital campaign system built for reach, engagement and measurable action.', '2025' ),
		array( 'Modern Brand System', 'Brand Identity', 'A flexible visual identity designed to stay consistent across web, print and social.', '2025' ),
	);
	foreach ( $projects as $index => $project ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'd4w_project',
				'post_status'  => 'publish',
				'post_title'   => $project[0],
				'post_excerpt' => $project[2],
				'menu_order'   => $index,
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_d4w_year', $project[3] );
			wp_set_object_terms( $post_id, $project[1], 'd4w_project_type' );
		}
	}

	$testimonials = array(
		array( 'Anita Sharma', 'Founder, Retail Brand', 'Design4web understood our business quickly and turned it into a website that feels premium, clear and very easy to use.' ),
		array( 'Rohit Mehta', 'Director, Service Company', 'The team stayed responsive from the first idea to launch. Our new site is faster and enquiries have become much more consistent.' ),
		array( 'Sameer Khan', 'E-Commerce Owner', 'Reliable support, thoughtful design and practical advice. We finally have a digital partner we can depend on.' ),
	);
	foreach ( $testimonials as $index => $testimonial ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'd4w_testimonial',
				'post_status'  => 'publish',
				'post_title'   => $testimonial[0],
				'post_content' => $testimonial[2],
				'menu_order'   => $index,
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_d4w_role', $testimonial[1] );
			update_post_meta( $post_id, '_d4w_rating', 5 );
		}
	}

	update_option( 'd4w_seeded', 1 );
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'd4w_seed_content' );

/**
 * Add a small, editable journal starter set on a fresh installation.
 */
function d4w_seed_journal() {
	if ( get_option( 'd4w_journal_seeded' ) ) {
		return;
	}

	$published = wp_count_posts( 'post' );
	$count     = isset( $published->publish ) ? (int) $published->publish : 0;
	$articles  = array(
		array(
			'Why a Fast Website Is a Better Salesperson',
			'A polished website is only useful when it feels immediate. Speed improves trust, search visibility and the number of visitors who become enquiries.',
		),
		array(
			'Building a Brand That Stays Consistent Online',
			'Consistency is more than repeating a logo. A flexible system for typography, color, imagery and tone helps every digital touchpoint feel unmistakably yours.',
		),
		array(
			'What to Prepare Before Starting Your Website',
			'Clear goals, priority audiences and a small set of strong references make the design process faster. Here is the practical brief every successful project needs.',
		),
	);

	$needed = max( 0, 3 - $count );
	for ( $index = 0; $index < $needed; $index++ ) {
		wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_title'   => $articles[ $index ][0],
				'post_excerpt' => $articles[ $index ][1],
				'post_content' => '<p>' . esc_html( $articles[ $index ][1] ) . '</p><h2>Start with the right foundation</h2><p>Strong digital work begins with clear priorities, useful content and a shared understanding of what success should look like. Design decisions become easier when each one supports a real business goal.</p>',
			)
		);
	}
	update_option( 'd4w_journal_seeded', 1 );
}
add_action( 'after_switch_theme', 'd4w_seed_journal', 20 );

function d4w_body_classes( $classes ) {
	if ( d4w_get_option( 'enable_cursor', true ) ) {
		$classes[] = 'd4w-cursor-enabled';
	}
	return $classes;
}
add_filter( 'body_class', 'd4w_body_classes' );

function d4w_admin_menu() {
	add_theme_page(
		__( 'Design4web Theme Setup', 'design4web' ),
		__( 'Design4web Options', 'design4web' ),
		'edit_theme_options',
		'd4w-options',
		'd4w_options_page'
	);
}
add_action( 'admin_menu', 'd4w_admin_menu' );

function d4w_options_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Design4web Theme Options', 'design4web' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Manage your homepage, colors, contact information and dynamic content from the links below.', 'design4web' ); ?></p>
		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px;max-width:1100px;margin-top:24px">
			<?php
			$cards = array(
				array( 'dashicons-admin-customizer', 'Brand, homepage & contact', 'Edit colors, hero copy, statistics, contact details, social links and section visibility.', admin_url( 'customize.php?autofocus[panel]=d4w_options' ), 'Open live options' ),
				array( 'dashicons-superhero-alt', 'Services', 'Add, edit, reorder and illustrate the services shown on the homepage.', admin_url( 'edit.php?post_type=d4w_service' ), 'Manage services' ),
				array( 'dashicons-portfolio', 'Projects', 'Publish portfolio work, categories, client details, dates and project images.', admin_url( 'edit.php?post_type=d4w_project' ), 'Manage projects' ),
				array( 'dashicons-format-quote', 'Testimonials', 'Control client quotes, roles, ratings and profile images.', admin_url( 'edit.php?post_type=d4w_testimonial' ), 'Manage testimonials' ),
				array( 'dashicons-menu-alt3', 'Navigation', 'Create menus and assign them to the primary or footer locations.', admin_url( 'nav-menus.php' ), 'Manage menus' ),
				array( 'dashicons-admin-site-alt3', 'Site identity', 'Upload the final logo and site icon, and edit the website title.', admin_url( 'customize.php?autofocus[section]=title_tagline' ), 'Edit identity' ),
			);
			foreach ( $cards as $card ) :
				?>
				<div class="card" style="max-width:none;margin:0;padding:22px">
					<span class="dashicons <?php echo esc_attr( $card[0] ); ?>" style="font-size:28px;width:28px;height:28px;color:#7c4dff"></span>
					<h2 style="margin:14px 0 8px"><?php echo esc_html( $card[1] ); ?></h2>
					<p style="min-height:54px"><?php echo esc_html( $card[2] ); ?></p>
					<a class="button button-primary" href="<?php echo esc_url( $card[3] ); ?>"><?php echo esc_html( $card[4] ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="notice notice-info inline" style="max-width:1060px;margin-top:24px"><p><strong><?php esc_html_e( 'Contact form delivery:', 'design4web' ); ?></strong> <?php esc_html_e( 'The form securely uses WordPress mail. Configure an SMTP plugin on the live server for authenticated, reliable delivery.', 'design4web' ); ?></p></div>
	</div>
	<?php
}

<?php
/**
 * Idempotent site structure and content upgrade routines.
 *
 * @package Design4Web
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'D4W_SCHEMA_VERSION', '2.0.0' );

/**
 * Find seeded content without relying on WP_Query's title handling. Titles
 * containing ampersands can otherwise be inserted twice after HTML escaping.
 *
 * @param string $title     Post title.
 * @param string $post_type Post type.
 * @return WP_Post|null
 */
function d4w_find_seeded_post( $title, $post_type ) {
	$post = get_page_by_path( sanitize_title( $title ), OBJECT, $post_type );
	if ( $post instanceof WP_Post ) {
		return $post;
	}

	$posts = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'no_found_rows'  => true,
		)
	);
	foreach ( $posts as $candidate ) {
		if ( 0 === strcasecmp( wp_specialchars_decode( $candidate->post_title, ENT_QUOTES ), $title ) ) {
			return $candidate;
		}
	}

	return null;
}

/**
 * Create a page when missing and preserve editor changes on later upgrades.
 *
 * @param string $title   Page title.
 * @param string $slug    Page slug.
 * @param string $content Starter content.
 * @return int
 */
function d4w_ensure_page( $title, $slug, $content = '' ) {
	$page = get_page_by_path( $slug );
	if ( $page instanceof WP_Post ) {
		if ( '' === trim( $page->post_content ) && $content ) {
			wp_update_post( array( 'ID' => $page->ID, 'post_content' => $content ) );
		}
		return (int) $page->ID;
	}

	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		)
	);
	return is_wp_error( $page_id ) ? 0 : (int) $page_id;
}

/**
 * Seed or enrich service content sourced from Design4web's legacy site.
 */
function d4w_upgrade_services() {
	$services = array(
		'Web Design' => array(
			'excerpt'      => 'Distinctive, responsive websites that turn your business goals into a clear and memorable digital experience.',
			'content'      => '<p>Great web design combines visual personality with logical navigation and effortless usability. We begin with your audience and business goals, then build a responsive interface that makes every interaction feel intentional.</p><h2>Designed around your identity</h2><p>From typography and colour to page hierarchy and conversion paths, every element supports a consistent brand. The result is a website that feels unmistakably yours on desktop, tablet and mobile.</p><h2>Creative work with a practical purpose</h2><p>We design company websites, landing pages, campaign experiences, UI systems, brand identities and supporting digital graphics. Each direction is refined with you before development begins.</p>',
			'deliverables' => "UX and content structure\nResponsive interface design\nCustom visual direction\nConversion-focused page layouts\nDesign system and handoff",
			'icon'         => 'bi-bezier2',
			'label'        => 'Design & UX',
		),
		'Brand & Creative' => array(
			'excerpt'      => 'Distinctive identity and campaign systems that keep every customer-facing touchpoint recognisable and consistent.',
			'content'      => '<p>A strong brand gives every digital experience a clear point of view. We create visual systems that can move confidently between websites, social channels, presentations, campaigns and physical collateral.</p><h2>One idea, expressed consistently</h2><p>Capabilities include logo and identity design, UI direction, campaign graphics, landing pages, digital banners, brochures, packaging, exhibition material and supporting photography or motion direction. Each asset belongs to one practical, reusable system.</p><h2>Creative work made for real use</h2><p>Files, guidelines and adaptable templates are prepared around the people who will use them, helping your team stay consistent after delivery.</p>',
			'deliverables' => "Logo and visual identity\nBrand color and typography system\nCampaign and social creative\nDigital and print collateral\nPractical usage guidelines",
			'icon'         => 'bi-palette2',
			'label'        => 'Identity & campaigns',
		),
		'Web Development' => array(
			'excerpt'      => 'Secure, scalable and easy-to-manage websites engineered for speed, accessibility and long-term growth.',
			'content'      => '<p>Our development work turns approved designs into fast, reliable digital products. We choose the right technical foundation for the project instead of forcing every business into the same template.</p><h2>Clean technology, useful outcomes</h2><p>Our capabilities include custom WordPress development, PHP and database applications, API integrations, responsive front-end engineering and secure deployment. Editors receive a clear admin experience so routine updates do not require a developer.</p><h2>Built to stay dependable</h2><p>Performance, browser testing, accessibility, security and maintainability are included throughout the build—not added as an afterthought.</p>',
			'deliverables' => "Custom WordPress development\nResponsive front-end engineering\nCMS and API integrations\nPerformance optimisation\nTesting, launch and training",
			'icon'         => 'bi-code-slash',
			'label'        => 'Code & CMS',
		),
		'CMS & WordPress' => array(
			'excerpt'      => 'Flexible content management that lets your team update pages, images and information without technical friction.',
			'content'      => '<p>A well-planned content management system gives your team control without compromising the visual system. We build intuitive WordPress editing experiences for everything from small company sites to structured content platforms.</p><h2>Your website, easy to manage</h2><p>Update content, images, links, services, projects and articles from a secure browser-based dashboard. Reusable blocks and sensible permissions keep publishing consistent while reducing ongoing maintenance costs.</p>',
			'deliverables' => "WordPress architecture\nCustom content types\nReusable editor components\nRole and permission setup\nAdmin training",
			'icon'         => 'bi-wordpress',
			'label'        => 'Editable platforms',
		),
		'E-Commerce' => array(
			'excerpt'      => 'Conversion-focused online stores with effortless discovery, secure payments and manageable order workflows.',
			'content'      => '<p>An online store can serve customers around the clock and reach beyond the limits of a physical location. We design e-commerce journeys around product discovery, trust and a checkout experience that removes unnecessary friction.</p><h2>Connected commerce</h2><p>Product catalogues, inventory, payments, shipping, customer communication and analytics are configured as one practical system. Search-friendly foundations and responsive layouts help customers find and buy with confidence on any device.</p>',
			'deliverables' => "WooCommerce store design\nProduct and category architecture\nPayment and shipping setup\nOrder email workflows\nAnalytics and conversion tracking",
			'icon'         => 'bi-bag-check',
			'label'        => 'Online stores',
		),
		'Domain & Hosting' => array(
			'excerpt'      => 'Domain, email and hosting infrastructure managed with responsive support and dependable monitoring.',
			'content'      => '<p>Your domain is the foundation of your online identity. We help select, register, configure and transfer domains, then connect them to reliable hosting and business email.</p><h2>One accountable technical partner</h2><p>Hosting is matched to the actual needs of the website, with SSL, backups, uptime monitoring and support included where required. If you are moving an existing site, we plan the migration to minimise disruption.</p>',
			'deliverables' => "Domain registration and transfer\nWebsite and email hosting\nSSL and DNS configuration\nMigration assistance\nMonitoring and support",
			'icon'         => 'bi-cloud-check',
			'label'        => 'Infrastructure',
		),
		'SEO & Marketing' => array(
			'excerpt'      => 'Search strategy, technical optimisation and content foundations designed to create sustainable visibility.',
			'content'      => '<p>Search engine optimisation works best when it begins with a well-constructed website. We balance technical improvements, useful content and trustworthy off-site signals to make important pages easier to discover.</p><h2>A measurable growth foundation</h2><p>Our work can include keyword research, metadata, internal linking, content recommendations, directory and search submissions, analytics and ongoing improvement. The priority is qualified traffic—not vanity rankings.</p>',
			'deliverables' => "Technical SEO audit\nKeyword and competitor research\nOn-page optimisation\nContent and internal-link plan\nAnalytics and reporting",
			'icon'         => 'bi-graph-up-arrow',
			'label'        => 'Search & growth',
		),
		'Social Media Marketing' => array(
			'excerpt'      => 'Platform-aware social campaigns, content systems and community touchpoints built to start useful conversations.',
			'content'      => '<p>Social platforms give brands a direct way to begin conversations with potential customers. We shape the creative system, content themes and campaign plan so those conversations remain consistent and recognisable.</p><h2>Content made for participation</h2><p>Services can include channel strategy, campaign creatives, short-form content, publishing plans, social integrations and performance reporting. Every recommendation is matched to the audience and resources available.</p>',
			'deliverables' => "Channel and audience strategy\nCampaign creative system\nContent calendar\nSocial platform integration\nPerformance reporting",
			'icon'         => 'bi-megaphone',
			'label'        => 'Social campaigns',
		),
		'Website Care' => array(
			'excerpt'      => 'Ongoing updates, backups, security and improvements that keep your website useful after launch.',
			'content'      => '<p>A live website needs regular attention: software changes, content updates, backups, security checks and performance reviews. Our care service gives you a dependable technical partner without the overhead of an in-house web team.</p><h2>Keep the experience current</h2><p>We can handle routine content changes, image replacement, new pages, functionality updates, security hardening and structural improvements. Support is planned around your site and business priorities.</p>',
			'deliverables' => "Core and plugin updates\nBackups and security checks\nContent and image updates\nPerformance monitoring\nPriority technical support",
			'icon'         => 'bi-shield-check',
			'label'        => 'Support & maintenance',
		),
	);

	foreach ( $services as $index => $service ) {
		$service_order = array_search( $index, array_keys( $services ), true );
		$post = d4w_find_seeded_post( $index, 'd4w_service' );
		if ( ! $post ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'd4w_service',
					'post_status'  => 'publish',
					'post_title'   => $index,
					'post_excerpt' => $service['excerpt'],
					'post_content' => $service['content'],
					'menu_order'   => $service_order,
				)
			);
		} else {
			$post_id = $post->ID;
			$update  = array( 'ID' => $post_id, 'menu_order' => $service_order );
			if ( ! trim( $post->post_content ) ) {
				$update['post_content'] = $service['content'];
			}
			if ( ! trim( $post->post_excerpt ) ) {
				$update['post_excerpt'] = $service['excerpt'];
			}
			if ( count( $update ) > 1 ) {
				wp_update_post( $update );
			}
		}
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			if ( ! get_post_meta( $post_id, '_d4w_icon', true ) ) {
				update_post_meta( $post_id, '_d4w_icon', $service['icon'] );
			}
			if ( ! get_post_meta( $post_id, '_d4w_short_label', true ) ) {
				update_post_meta( $post_id, '_d4w_short_label', $service['label'] );
			}
			if ( ! get_post_meta( $post_id, '_d4w_deliverables', true ) ) {
				update_post_meta( $post_id, '_d4w_deliverables', $service['deliverables'] );
			}
		}
	}
}

/**
 * Migrate the verified, client-owned legacy portfolio into editable projects.
 * Bundled images are local fallbacks and are replaced automatically when an
 * editor assigns a featured image.
 */
function d4w_upgrade_projects() {
	$projects = array(
		array( 'Ayurveda', 'ayurveda', 'ayurveda.jpg', 'http://www.ayurvedatodayworld.com', 'Web Hosting, Logo Design, Web Page Design', 'Ayurvedic Industry · Ayurvedic Medicine', array( 'Web Design', 'Brand Identity', 'Hosting' ) ),
		array( 'WhatsAppIndia', 'whatsappindia', 'whatsapp.jpg', 'http://whatsappindia.com', 'Web Hosting, Logo Design, Web Page Design, WordPress Integration', 'Video · Video Blog', array( 'Web Design', 'WordPress', 'Brand Identity' ) ),
		array( 'Screentex', 'screentex', 'screentex.jpg', 'http://www.screentex.in', 'Web Hosting, Logo Design, Web Page Design', 'Publishing · Screen, Digital & Textile Printing', array( 'Web Design', 'Brand Identity', 'Hosting' ) ),
		array( 'H-Link', 'h-link', 'hlink.jpg', 'http://h-link.in', 'Web Hosting, Logo Design, Web Page Design, HTML', 'Furniture & Fittings · S.S. Door Handles', array( 'Web Design', 'Brand Identity', 'Hosting' ) ),
		array( 'Fit-N-Fair', 'fit-n-fair', 'fitnfair.jpg', 'http://www.fitnfair.com', 'Web Hosting, Logo Design, Web Page Design', 'Slimming · Beauty & Wellness', array( 'Web Design', 'Brand Identity', 'Hosting' ) ),
		array( 'PSS Contractors', 'pss-contractors', 'pss.jpg', '', 'Web Hosting, Logo Design, Web Page Design', 'Manpower Consultancy', array( 'Web Design', 'Brand Identity', 'Hosting' ) ),
		array( 'Satyam Jewellers', 'satyam-jewellers', 'satyam.jpg', '', 'Logo Design, Web Page Design, Front-end Development', 'Imitation Jewellery · Earrings · Bracelets · Pendants', array( 'Web Design', 'Brand Identity' ) ),
		array( 'Ashtabhrahma', 'ashtabhrahma', 'ashtha.jpg', 'http://ashtabhrahma.org', 'Web Hosting, Logo Design, Web Page Design, HTML', 'Brahmin Community Organisation', array( 'Web Design', 'Brand Identity', 'Hosting' ) ),
		array( 'Times Club', 'times-club', 'times_club.jpg', '', 'Logo Design, Web Page Design, Front-end Development', 'Club & Resort', array( 'Web Design', 'Brand Identity' ) ),
		array( 'Jodi Milao', 'jodi-milao', 'jodimilao.jpg', '', 'Logo Design, Web Page Design, HTML', 'Matrimonial · Marriage Bureau', array( 'Web Design', 'Brand Identity' ) ),
		array( 'Crescent Moon', 'crescent-moon', 'crescent.jpg', 'http://crescentmoon.in', 'Logo Design, Web Page Design, Front-end Development', 'Exhibition Organiser', array( 'Web Design', 'Brand Identity' ) ),
		array( 'Neha Shyam', 'neha-shyam', 'neha.jpg', 'http://nehashyam.com', 'Logo Design, Web Page Design, HTML', 'Personal Portfolio', array( 'Web Design', 'Brand Identity' ) ),
		array( 'Trader Shipping', 'trader-shipping', 'tsp.jpg', 'http://tradershippingindia.com/', 'Logo Design, Web Page Design, Front-end Development', 'Multimodal Transport · Freight & Logistics', array( 'Web Design', 'Brand Identity' ) ),
		array( 'Arc Tech', 'arc-tech', 'arctec.jpg', 'http://www.arctecinteriors.com/', 'Logo Animation, Web Page Design, Front-end Development', 'Interior Design · Contracting · Project Coordination', array( 'Web Design', 'Brand Identity' ) ),
		array( 'SKJ', 'skj', 'skj.jpg', 'http://www.skj.in', 'Logo Design, Web Page Design, HTML5', 'Camera Solutions · IP Solutions', array( 'Web Design', 'Brand Identity' ) ),
		array( 'J-Link', 'j-link', 'jlink.jpg', 'http://www.j-link.in', 'Web Hosting, Logo Design, Web Page Design, HTML', 'Furniture & Fittings · S.S. Door Handles', array( 'Web Design', 'Brand Identity', 'Hosting' ) ),
	);

	foreach ( $projects as $order => $project ) {
		$matches = get_posts(
			array(
				'post_type'      => 'd4w_project',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'meta_key'       => '_d4w_legacy_key',
				'meta_value'     => $project[1],
				'no_found_rows'  => true,
			)
		);
		$post = $matches ? $matches[0] : d4w_find_seeded_post( $project[0], 'd4w_project' );
		if ( ! $post && 'whatsappindia' === $project[1] ) {
			$post = get_page_by_path( 'whatsappindia-platform', OBJECT, 'd4w_project' );
		}

		$excerpt = sprintf( 'A legacy Design4web portfolio project for %1$s, combining %2$s.', $project[0], strtolower( $project[4] ) );
		$content = sprintf( '<p>This archive project documents Design4web’s work for <strong>%1$s</strong> across %2$s.</p><h2>A clear digital presence for its audience</h2><p>The engagement brought the brand, website presentation and technical delivery into one focused experience. The original visual has been preserved as part of the Design4web portfolio archive.</p>', esc_html( $project[0] ), esc_html( $project[4] ) );

		if ( ! $post ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'd4w_project',
					'post_status'  => 'publish',
					'post_title'   => $project[0],
					'post_name'    => $project[1],
					'post_excerpt' => $excerpt,
					'post_content' => $content,
					'menu_order'   => $order,
				)
			);
		} else {
			$post_id = $post->ID;
			$update  = array( 'ID' => $post_id, 'menu_order' => $order );
			if ( 'WhatsAppIndia Platform' === $post->post_title ) {
				$update['post_title'] = 'WhatsAppIndia';
				$update['post_name']  = 'whatsappindia';
			}
			if ( ! trim( $post->post_excerpt ) ) {
				$update['post_excerpt'] = $excerpt;
			}
			if ( ! trim( $post->post_content ) ) {
				$update['post_content'] = $content;
			}
			wp_update_post( $update );
		}

		if ( ! $post_id || is_wp_error( $post_id ) ) {
			continue;
		}
		$meta = array(
			'_d4w_legacy_key'    => $project[1],
			'_d4w_bundled_image' => 'legacy/' . $project[2],
			'_d4w_client'        => $project[0],
			'_d4w_year'          => 'Archive',
			'_d4w_services'      => $project[4],
			'_d4w_industry'      => $project[5],
			'_d4w_challenge'     => 'Create a distinctive, useful web presence that communicates the organisation’s offer clearly to its intended audience.',
			'_d4w_outcome'       => 'A branded digital experience that brought identity, information and contact paths together in one coherent destination.',
		);
		foreach ( $meta as $key => $value ) {
			if ( $value && ! get_post_meta( $post_id, $key, true ) ) {
				update_post_meta( $post_id, $key, $value );
			}
		}
		wp_set_object_terms( $post_id, $project[6], 'd4w_project_type', true );
	}

	$concepts = array(
		'Commerce Experience'  => array( 30, 'project-2.jpg', 'Retail & Commerce', 'Commerce strategy, UX, interface design', 'A concept store experience shaped around intuitive product discovery, trust and a low-friction path to checkout.' ),
		'Growth Campaign'      => array( 31, 'project-3.jpg', 'Digital Marketing', 'Campaign strategy, creative system, landing experience', 'A coordinated campaign concept designed to turn attention into measurable action across digital touchpoints.' ),
		'Modern Brand System'  => array( 32, 'project-4.jpg', 'Brand Identity', 'Brand strategy, identity design, digital guidelines', 'A flexible identity concept built to remain recognisable across web, social and business communication.' ),
	);
	foreach ( $concepts as $title => $concept ) {
		$post = d4w_find_seeded_post( $title, 'd4w_project' );
		if ( ! $post || trim( $post->post_content ) ) {
			continue;
		}
		wp_update_post( array( 'ID' => $post->ID, 'menu_order' => $concept[0], 'post_content' => '<p>' . esc_html( $concept[4] ) . '</p><h2>Designed as a complete system</h2><p>Strategy, visual direction and interaction choices were developed together so the concept could stay consistent as it grows.</p>' ) );
		update_post_meta( $post->ID, '_d4w_bundled_image', $concept[1] );
		update_post_meta( $post->ID, '_d4w_industry', $concept[2] );
		update_post_meta( $post->ID, '_d4w_services', $concept[3] );
		update_post_meta( $post->ID, '_d4w_challenge', 'Turn a broad business goal into a focused digital direction with a clear visual point of view.' );
		update_post_meta( $post->ID, '_d4w_outcome', $concept[4] );
	}
}

/**
 * Create the editable process and FAQ starter content.
 */
function d4w_upgrade_supporting_content() {
	if ( ! get_posts( array( 'post_type' => 'd4w_process', 'posts_per_page' => 1, 'post_status' => 'any' ) ) ) {
		$steps = array(
			array( 'Discover', 'We listen, research and define the opportunity before deciding what to make.', '01', 'bi-compass' ),
			array( 'Design', 'We turn strategy into an expressive system and refine the important interactions with you.', '02', 'bi-bezier2' ),
			array( 'Build', 'Clean development, useful integrations and careful testing bring the approved idea to life.', '03', 'bi-code-slash' ),
			array( 'Grow', 'After launch, we support, measure and improve so the work keeps earning attention.', '04', 'bi-graph-up-arrow' ),
		);
		foreach ( $steps as $order => $step ) {
			$post_id = wp_insert_post( array( 'post_type' => 'd4w_process', 'post_status' => 'publish', 'post_title' => $step[0], 'post_content' => $step[1], 'menu_order' => $order ) );
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_d4w_process_number', $step[2] );
				update_post_meta( $post_id, '_d4w_process_icon', $step[3] );
			}
		}
	}

	if ( ! get_posts( array( 'post_type' => 'd4w_faq', 'posts_per_page' => 1, 'post_status' => 'any' ) ) ) {
		$faqs = array(
			array( 'How long does a website project take?', 'A focused company website typically takes six to ten weeks. E-commerce and integration-heavy projects are planned after discovery.', 'Projects' ),
			array( 'Will the website work on phones and tablets?', 'Yes. Responsive behaviour is designed and tested for modern phones, tablets, laptops and large desktop screens.', 'Design' ),
			array( 'Can our team update the website?', 'Yes. WordPress content types and editor controls are configured around your content, and handover training is included.', 'WordPress' ),
			array( 'Can you redesign or migrate an existing website?', 'Yes. We can audit the existing content, preserve valuable URLs, migrate data and launch the new site with a controlled transition.', 'Projects' ),
			array( 'Do you provide hosting and maintenance?', 'Yes. Domain, email, hosting, backups, security monitoring and ongoing website care can be managed as one service.', 'Support' ),
			array( 'How do we get an accurate quote?', 'Share your goals, key pages, required features, preferred timeline and any reference websites. We will respond with the right next step.', 'Pricing' ),
		);
		foreach ( $faqs as $order => $faq ) {
			$post_id = wp_insert_post( array( 'post_type' => 'd4w_faq', 'post_status' => 'publish', 'post_title' => $faq[0], 'post_content' => $faq[1], 'menu_order' => $order ) );
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_d4w_faq_category', $faq[2] );
			}
		}
	}
}

/**
 * Create the multipage structure and navigation for new and upgraded installs.
 */
function d4w_upgrade_site_structure() {
	$about_content = '<p><strong>Design4web is a web development, design and hosting company creating distinctive digital identities for ambitious businesses.</strong></p><p>We enjoy working with teams that value high-quality web experiences and a consistent brand—from the look and feel of a website to the colours, typography and communication used across digital and print.</p><p>Our relationship can continue beyond launch. Domain, hosting, maintenance, development and responsive support are brought together so clients have one accountable partner as their digital presence grows.</p>';
	$home_id       = d4w_ensure_page( 'Home', 'home' );
	$about_id      = d4w_ensure_page( 'About', 'about', $about_content );
	$contact_id    = d4w_ensure_page( 'Contact', 'contact' );
	$blog_id       = d4w_ensure_page( 'Journal', 'journal' );
	$about_template = $about_id ? get_post_meta( $about_id, '_wp_page_template', true ) : '';
	$contact_template = $contact_id ? get_post_meta( $contact_id, '_wp_page_template', true ) : '';
	if ( $about_id && ( ! $about_template || 'default' === $about_template ) ) {
		update_post_meta( $about_id, '_wp_page_template', 'page-about.php' );
	}
	if ( $contact_id && ( ! $contact_template || 'default' === $contact_template ) ) {
		update_post_meta( $contact_id, '_wp_page_template', 'page-contact.php' );
	}

	if ( $home_id && ! (int) get_option( 'page_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}
	if ( $blog_id && ! (int) get_option( 'page_for_posts' ) ) {
		update_option( 'page_for_posts', $blog_id );
	}

	$locations       = get_theme_mod( 'nav_menu_locations', array() );
	$old_primary     = isset( $locations['primary'] ) ? (int) $locations['primary'] : 0;
	$replace_anchors = false;
	if ( ! empty( $locations['primary'] ) ) {
		$existing_items = wp_get_nav_menu_items( $locations['primary'] );
		$anchor_count   = 0;
		foreach ( $existing_items ?: array() as $existing_item ) {
			if ( wp_parse_url( $existing_item->url, PHP_URL_FRAGMENT ) ) {
				++$anchor_count;
			}
		}
		$replace_anchors = $anchor_count >= 3 && $anchor_count >= ceil( count( $existing_items ?: array() ) / 2 );
	}

	if ( empty( $locations['primary'] ) || $replace_anchors ) {
		$menu_name = $replace_anchors ? 'Design4web Multipage Menu' : 'Design4web Main Menu';
		$menu      = wp_get_nav_menu_object( $menu_name );
		$menu_id   = $menu ? (int) $menu->term_id : wp_create_nav_menu( $menu_name );
		if ( ! is_wp_error( $menu_id ) ) {
			if ( ! wp_get_nav_menu_items( $menu_id ) ) {
				$items = array(
					array( 'Home', $home_id ? get_permalink( $home_id ) : home_url( '/' ) ),
					array( 'About', $about_id ? get_permalink( $about_id ) : home_url( '/about/' ) ),
					array( 'Services', get_post_type_archive_link( 'd4w_service' ) ?: home_url( '/services/' ) ),
					array( 'Work', get_post_type_archive_link( 'd4w_project' ) ?: home_url( '/work/' ) ),
					array( 'Journal', $blog_id ? get_permalink( $blog_id ) : home_url( '/journal/' ) ),
					array( 'Contact', $contact_id ? get_permalink( $contact_id ) : home_url( '/contact/' ) ),
				);
				foreach ( $items as $item ) {
					wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => $item[0], 'menu-item-url' => $item[1], 'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ) );
				}
			}
			$locations['primary'] = $menu_id;
			if ( empty( $locations['footer'] ) || ( $replace_anchors && (int) $locations['footer'] === $old_primary ) ) {
				$locations['footer'] = $menu_id;
			}
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}
}

/**
 * Run safe one-time upgrades after all content types are registered.
 */
function d4w_run_schema_upgrade() {
	if ( D4W_SCHEMA_VERSION === get_option( 'd4w_schema_version' ) ) {
		return;
	}
	d4w_upgrade_services();
	d4w_upgrade_projects();
	d4w_upgrade_supporting_content();
	d4w_upgrade_site_structure();
	update_option( 'd4w_schema_version', D4W_SCHEMA_VERSION );
	flush_rewrite_rules( false );
}
add_action( 'init', 'd4w_run_schema_upgrade', 99 );

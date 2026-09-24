<?php
/**
 * Idempotent site structure and content upgrade routines.
 *
 * @package Design4Web
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'D4W_SCHEMA_VERSION', '3.7.0' );

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
		'Website Design' => array(
			'excerpt'      => 'Distinctive, responsive websites that turn your business goals into a clear and memorable digital experience.',
			'content'      => '<p>Great web design combines visual personality with logical navigation and effortless usability. We begin with your audience and business goals, then build a responsive interface that makes every interaction feel intentional.</p><h2>Designed around your identity</h2><p>From typography and colour to page hierarchy and conversion paths, every element supports a consistent brand. The result is a website that feels unmistakably yours on desktop, tablet and mobile.</p><h2>Creative work with a practical purpose</h2><p>We design company websites, landing pages, campaign experiences, UI systems, brand identities and supporting digital graphics. Each direction is refined with you before development begins.</p>',
			'deliverables' => "UX and content structure\nResponsive interface design\nCustom visual direction\nConversion-focused page layouts\nDesign system and handoff",
			'icon'         => 'bi-bezier2',
			'label'        => 'Design & UX',
		),
		'Logo & Brand Design' => array(
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
		'Domain & Web Hosting' => array(
			'excerpt'      => 'Domain, email and hosting infrastructure managed with responsive support and dependable monitoring.',
			'content'      => '<p>Your domain is the foundation of your online identity. We help select, register, configure and transfer domains, then connect them to reliable hosting and business email.</p><h2>One accountable technical partner</h2><p>Hosting is matched to the actual needs of the website, with SSL, backups, uptime monitoring and support included where required. If you are moving an existing site, we plan the migration to minimise disruption.</p>',
			'deliverables' => "Domain registration and transfer\nWebsite and email hosting\nSSL and DNS configuration\nMigration assistance\nMonitoring and support",
			'icon'         => 'bi-cloud-check',
			'label'        => 'Infrastructure',
		),
		'Google Workspace' => array(
			'excerpt'      => 'Professional Gmail, shared calendars, cloud files and collaboration tools configured around your team.',
			'content'      => '<p>Google Workspace brings business email, calendars, meetings, files and collaboration into one secure environment. We help you choose the right plan, connect your domain and organise the account so your team can begin with confidence.</p><h2>Business communication without the setup friction</h2><p>Our support can cover DNS verification, user accounts, aliases, groups, shared drives, email migration and mobile configuration. We also help establish sensible access and security practices for day-to-day administration.</p>',
			'deliverables' => "Plan and licence guidance\nDomain and DNS verification\nBusiness email setup\nUser, alias and group configuration\nMigration and team onboarding",
			'icon'         => 'bi-google',
			'label'        => 'Email & collaboration',
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
		'Google Ads & Performance Marketing' => array(
			'excerpt'      => 'Search and performance campaigns planned around qualified leads, useful landing pages and measurable action.',
			'content'      => '<p>Paid media performs best when campaign intent, creative, landing pages and tracking are designed together. We structure Google Search and performance campaigns around the actions that matter to your business.</p><h2>Spend with a clear feedback loop</h2><p>Keyword planning, campaign structure, conversion tracking, landing-page recommendations and ongoing optimisation are connected in one measurable workflow. Reporting focuses on lead quality and commercial value rather than reach alone.</p>',
			'deliverables' => "Campaign and keyword strategy\nGoogle Ads account structure\nConversion tracking setup\nLanding-page recommendations\nOptimisation and reporting",
			'icon'         => 'bi-bullseye',
			'label'        => 'Paid acquisition',
		),
		'Website Care' => array(
			'excerpt'      => 'Ongoing updates, backups, security and improvements that keep your website useful after launch.',
			'content'      => '<p>A live website needs regular attention: software changes, content updates, backups, security checks and performance reviews. Our care service gives you a dependable technical partner without the overhead of an in-house web team.</p><h2>Keep the experience current</h2><p>We can handle routine content changes, image replacement, new pages, functionality updates, security hardening and structural improvements. Support is planned around your site and business priorities.</p>',
			'deliverables' => "Core and plugin updates\nBackups and security checks\nContent and image updates\nPerformance monitoring\nPriority technical support",
			'icon'         => 'bi-shield-check',
			'label'        => 'Support & maintenance',
		),
	);

	$aliases = array(
		'Website Design'      => 'Web Design',
		'Logo & Brand Design' => 'Brand & Creative',
		'Domain & Web Hosting'=> 'Domain & Hosting',
	);

	foreach ( $services as $index => $service ) {
		$service_order = array_search( $index, array_keys( $services ), true );
		$post = d4w_find_seeded_post( $index, 'd4w_service' );
		if ( ! $post && isset( $aliases[ $index ] ) ) {
			$post = d4w_find_seeded_post( $aliases[ $index ], 'd4w_service' );
		}
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
			if ( isset( $aliases[ $index ] ) && 0 === strcasecmp( wp_specialchars_decode( $post->post_title, ENT_QUOTES ), $aliases[ $index ] ) ) {
				$update['post_title'] = $index;
				$update['post_name']  = sanitize_title( $index );
			}
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
 * Add a bundled project preview to the Media Library and set it as featured.
 * The theme image remains a reliable fallback if the uploads directory cannot
 * be written during deployment.
 *
 * @param int    $post_id  Project post ID.
 * @param string $filename Bundled image filename.
 * @param string $title    Attachment title.
 * @return int Attachment ID or zero.
 */
function d4w_attach_project_preview( $post_id, $filename, $title ) {
	$source = D4W_DIR . '/assets/images/clients/' . basename( $filename );
	if ( ! file_exists( $source ) || ! is_readable( $source ) ) {
		return 0;
	}

	$bits = wp_upload_bits( basename( $filename ), null, file_get_contents( $source ) );
	if ( ! empty( $bits['error'] ) ) {
		return 0;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $title . ' website preview',
			'post_status'    => 'inherit',
		),
		$bits['file'],
		$post_id,
		true
	);
	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$metadata = wp_generate_attachment_metadata( $attachment_id, $bits['file'] );
	if ( $metadata ) {
		wp_update_attachment_metadata( $attachment_id, $metadata );
	}
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $title . ' website' );
	set_post_thumbnail( $post_id, $attachment_id );
	return (int) $attachment_id;
}

/**
 * Replace the previous portfolio with the verified recent-work collection.
 * New records are prepared first; old projects are removed only after every
 * replacement record has been created successfully.
 *
 * @return bool Whether the replacement completed successfully.
 */
function d4w_upgrade_projects() {
	$dataset_version = '3.7.0';
	if ( $dataset_version === get_option( 'd4w_recent_projects_version' ) ) {
		return true;
	}

	$lock_name = 'd4w_recent_projects_migration_lock';
	$lock_time = (int) get_option( $lock_name, 0 );
	if ( $lock_time && ( time() - $lock_time ) < 600 ) {
		return false;
	}
	if ( $lock_time ) {
		delete_option( $lock_name );
	}
	if ( ! add_option( $lock_name, time(), '', false ) ) {
		return false;
	}

	$projects = array(
		array( 'AndBeyond.Media', 'and-beyond-media', 'and-beyond-media.jpg', 'https://andbeyond.media/', 'AdTech & Programmatic Advertising', 'Website design, responsive development, content architecture', array( 'Web Design', 'AdTech' ), 'An image-led corporate platform presenting programmatic advertising, monetisation tools and publisher-focused ad technology.', 'Organise a technically detailed advertising offer into a confident website that works for publishers, brands and partners.' ),
		array( 'DreamDrip by Seh', 'dreamdrip-by-seh', 'dreamdrip-by-seh.jpg', 'https://dreamdripbyseh.com/', 'Sleepwear & Loungewear', 'Shopify design, e-commerce UX, responsive development', array( 'Web Design', 'E-Commerce' ), 'A soft, elegant Shopify experience for women’s sleepwear and loungewear, built around visual discovery and effortless shopping.', 'Translate a comfort-led fashion brand into a polished storefront that balances product storytelling, collection browsing and mobile conversion.' ),
		array( 'Madhya Pradesh State Rifle Association', 'mpsra', 'mpsra.jpg', 'https://mpsra.org.in/', 'Sports Association', 'Website design, WordPress development, information architecture', array( 'Web Design', 'Sports' ), 'A responsive association website connecting athletes with shooting events, training information and organisational updates.', 'Make competitions, notices, training pathways and association information easy to discover across devices.' ),
		array( 'HospyKare', 'hospykare', 'hospykare.jpg', 'https://www.hospykare.com/', 'Healthcare & Medical Travel', 'Website design, responsive development, conversion journeys', array( 'Web Design', 'Healthcare' ), 'A healthcare platform presenting medical travel assistance, hospital access and coordinated patient-support services.', 'Build clarity and trust around a multi-step healthcare journey for patients seeking dependable treatment support.' ),
		array( 'Langwarrin Health Clinic', 'langwarrin-health-clinic', 'langwarrin-health-clinic.jpg', 'https://langwarrinhealthclinic.com/', 'Primary Healthcare', 'Website design, WordPress development, appointment journeys', array( 'Web Design', 'Healthcare' ), 'A patient-friendly clinic website for doctors, services, billing information, opening hours and appointment access.', 'Help local patients quickly find care information and the right path to contact or book with the clinic.' ),
		array( 'Armet Industries', 'armet-industries', 'armet-industries.jpg', 'https://armetindustries.com/', 'Smart Automation', 'Website design, product presentation, responsive development', array( 'Web Design', 'Automation' ), 'A polished product website for smart touch switches, home automation and hospitality automation solutions.', 'Present a modern automation portfolio with enough visual impact to match the quality of the physical products.' ),
		array( 'Blinq Photo', 'blinq-photo', 'blinq-photo.jpg', 'https://blinqphoto.in/', 'Photography', 'Portfolio design, responsive development, visual storytelling', array( 'Web Design', 'Photography' ), 'A cinematic photography portfolio showcasing architecture, interiors, travel imagery and aerial perspectives.', 'Let the imagery lead while keeping project discovery, photographer identity and enquiries effortless.' ),
		array( 'Smart Sense Technical Services', 'alss-technical-services', 'alss-technical-services.jpg', 'https://alssts.com/', 'Electrical & Technical Services', 'Website design, WordPress development, service architecture', array( 'Web Design', 'Engineering' ), 'A service-led UAE website covering electrical systems, switchgear, MEP, maintenance and safety solutions.', 'Structure a wide technical capability set so customers can quickly identify the relevant engineering service.' ),
		array( 'Manjra Industries', 'manjra-industries', 'manjra-industries.jpg', 'https://manjraindustries.in/', 'Sugar & Bioenergy', 'Corporate website, responsive development, content structure', array( 'Web Design', 'Industrial' ), 'A corporate platform presenting integrated sugar, distillery and co-generation operations with a sustainability focus.', 'Bring multiple industrial business units together in one coherent, accessible digital identity.' ),
		array( 'Crown Developers', 'crown-developers', 'crown-developers.jpg', 'https://crowndevelopers.in/', 'Commercial Real Estate', 'Website design, property presentation, responsive development', array( 'Web Design', 'Real Estate' ), 'A real-estate destination positioning commercial spaces as a connected business hub for growth and opportunity.', 'Translate the location, commercial promise and project identity into a persuasive property experience.' ),
		array( 'Cobra Equipments', 'cobra-equipments', 'cobra-equipments.jpg', 'https://cobraequipments.com/', 'Construction Equipment', 'Corporate website, product presentation, responsive development', array( 'Web Design', 'Industrial' ), 'A robust equipment website presenting loaders and construction machinery through clear product-led journeys.', 'Give a heavy-equipment range a modern, credible digital presence with straightforward product discovery.' ),
		array( 'Metro Fan', 'metrofan', 'metrofan.jpg', 'https://metrofan.in/', 'Fans & Home Appliances', 'Website design, product catalogue, responsive development', array( 'Web Design', 'Consumer Products' ), 'A product-rich brand website for fans and home appliances, balancing catalogue depth with everyday usability.', 'Modernise an established consumer brand while making product ranges easy to browse on every screen.' ),
		array( 'Walchand Builders', 'walchand-builders', 'walchand-builders.jpg', 'https://walchandbuilders.in/', 'Real Estate', 'Website design, property presentation, WordPress development', array( 'Web Design', 'Real Estate' ), 'A premium real-estate website presenting developments, project details and the builder’s approach to urban living.', 'Create an aspirational property experience that still keeps project facts and enquiry paths clear.' ),
		array( 'Shree Varad Homes', 'shree-varad-homes', 'shree-varad-homes.jpg', 'https://shreevaradhomes.com/', 'Residential Real Estate', 'Landing experience, responsive development, lead generation', array( 'Web Design', 'Real Estate' ), 'A focused residential property experience for contemporary 2 and 3 BHK homes in Vasai.', 'Turn a residential project’s key advantages, amenities and location into an inviting lead-generation journey.' ),
		array( '3SQUARE', '3-square-fittings', '3-square-fittings.jpg', 'https://3squarefittings.com/', 'Furniture Fittings', 'E-commerce design, product catalogue, responsive development', array( 'Web Design', 'E-Commerce' ), 'A product catalogue and commerce experience for furniture feet, bases, handles, knobs, hooks and joinery fittings.', 'Make a varied fittings range visually engaging and simple to explore by category and product.' ),
		array( 'Earth Diaries by Deepti Sharma', 'deepti-sharma', 'deepti-sharma.jpg', 'https://deeptisharma.co.in/', 'Environment & Publishing', 'Personal brand website, editorial design, responsive development', array( 'Web Design', 'Publishing' ), 'An editorial personal platform for environmental scientist and author Deepti Sharma, bringing books and ecological ideas together.', 'Create a distinctive home for research, writing and environmental storytelling without losing a personal voice.' ),
		array( 'Dinix Cookware', 'dinix', 'dinix.jpg', 'https://dinix.in/', 'Cookware & E-Commerce', 'E-commerce design, catalogue UX, responsive development', array( 'Web Design', 'E-Commerce' ), 'A modern shopping experience for premium cookware and practical kitchen products organised around everyday discovery.', 'Combine product presentation, category navigation and purchase journeys in a clean mobile-first storefront.' ),
		array( 'EVO Charge', 'evocharge', 'evocharge.jpg', 'https://evocharge.in/', 'EV Charging Infrastructure', 'Corporate website, responsive development, information architecture', array( 'Web Design', 'Clean Mobility' ), 'A green-mobility platform communicating a nationwide vision for accessible, high-speed electric-vehicle charging.', 'Explain an ambitious infrastructure programme through clear routes, offerings and partnership information.' ),
		array( 'Shri Ram Kripa', 'sr-kripa', 'sr-kripa.jpg', 'https://srkripa.com/', 'Bags & Manufacturing', 'E-commerce design, product catalogue, responsive development', array( 'Web Design', 'E-Commerce' ), 'A visual catalogue for premium and luxury jewellery bags across art leather, non-woven, jute and specialist ranges.', 'Showcase a broad manufactured-bag collection with strong category discovery and direct enquiry paths.' ),
		array( 'Essnd Global', 'essnd-global', 'essnd-global.jpg', 'http://essndglobal.com/', 'Healthcare Manufacturing', 'Corporate website, responsive development, product presentation', array( 'Web Design', 'Healthcare' ), 'A healthcare manufacturing website presenting topical, dermatology, cosmetic and personal-care product capabilities.', 'Communicate manufacturing credibility, product breadth and quality-led processes in a clear corporate experience.' ),
	);

	$old_project_ids = get_posts(
		array(
			'post_type'      => 'd4w_project',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	$new_project_ids = array();
	$new_media_ids   = array();

	foreach ( $projects as $order => $project ) {
		$content = '<p>' . esc_html( $project[7] ) . '</p><h2>A focused digital experience</h2><p>' . esc_html( $project[8] ) . '</p><p>The result is a responsive, content-managed website that gives the organisation a clear visual identity and makes its key information easier to explore.</p>';
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'd4w_project',
				'post_status'  => 'draft',
				'post_title'   => $project[0],
				'post_name'    => $project[1],
				'post_excerpt' => $project[7],
				'post_content' => $content,
				'menu_order'   => $order,
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			foreach ( $new_project_ids as $cleanup_id ) {
				wp_delete_post( $cleanup_id, true );
			}
			foreach ( $new_media_ids as $cleanup_media_id ) {
				wp_delete_attachment( $cleanup_media_id, true );
			}
			delete_option( $lock_name );
			return false;
		}

		$new_project_ids[] = (int) $post_id;
		$meta = array(
			'_d4w_dataset_key'         => $project[1],
			'_d4w_bundled_image'       => 'clients/' . $project[2],
			'_d4w_client'              => $project[0],
			'_d4w_year'                => '2026',
			'_d4w_url'                 => $project[3],
			'_d4w_industry'            => $project[4],
			'_d4w_services'            => $project[5],
			'_d4w_challenge'           => $project[8],
			'_d4w_outcome'             => 'A responsive, easy-to-manage digital presence with stronger content hierarchy, visual clarity and direct paths to the organisation’s most important information.',
			'_d4w_featured_case_study' => $order < 4 ? '1' : '0',
		);
		foreach ( $meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
		wp_set_object_terms( $post_id, $project[6], 'd4w_project_type', false );
		$media_id = d4w_attach_project_preview( $post_id, $project[2], $project[0] );
		if ( $media_id ) {
			$new_media_ids[] = $media_id;
		}
	}

	foreach ( $new_project_ids as $new_project_id ) {
		$published = wp_update_post( array( 'ID' => $new_project_id, 'post_status' => 'publish' ), true );
		if ( is_wp_error( $published ) ) {
			foreach ( $new_project_ids as $cleanup_id ) {
				wp_delete_post( $cleanup_id, true );
			}
			foreach ( $new_media_ids as $cleanup_media_id ) {
				wp_delete_attachment( $cleanup_media_id, true );
			}
			delete_option( $lock_name );
			return false;
		}
	}

	foreach ( $old_project_ids as $old_project_id ) {
		$thumbnail_id = get_post_thumbnail_id( $old_project_id );
		if ( $thumbnail_id && (int) get_post_field( 'post_parent', $thumbnail_id ) === (int) $old_project_id ) {
			wp_delete_attachment( $thumbnail_id, true );
		}
		wp_delete_post( $old_project_id, true );
	}
	foreach ( $new_project_ids as $order => $new_project_id ) {
		wp_update_post(
			array(
				'ID'        => $new_project_id,
				'post_name' => $projects[ $order ][1],
			)
		);
	}

	update_option( 'd4w_recent_projects_version', $dataset_version );
	delete_option( $lock_name );
	return true;
}

/**
 * Create the editable WhatsApp product catalogue shown in the supplied menu
 * reference. Copy is original to Design4web and does not imply a Meta or
 * third-party platform partnership.
 */
function d4w_upgrade_products() {
	$products = array(
		'whatsapp-business-api' => array(
			'title'    => 'WhatsApp Business API',
			'excerpt'  => 'Plan, onboard and connect an official business messaging setup built for campaigns, notifications and multi-agent support.',
			'content'  => '<p>Move beyond a single-device inbox and create a structured WhatsApp communication system for your business. Design4web helps assess readiness, coordinate platform onboarding and shape the website, campaign and integration work around the official WhatsApp Business Platform.</p><h2>One foundation for marketing and service</h2><p>Bring approved message templates, team access, lead capture and essential customer journeys into a practical launch plan. We keep responsibilities, third-party costs and approval requirements clear from the beginning.</p>',
			'kicker'   => 'Business messaging foundation',
			'icon'     => 'bi-briefcase-fill',
			'accent'   => '#19b86a',
			'features' => "Readiness and eligibility review\nBusiness account onboarding guidance\nTemplate and journey planning\nMulti-agent inbox setup support\nCRM, website and webhook planning\nLaunch testing and team handover",
			'steps'    => "Assess | Review the business account, phone number, use cases and required integrations.\nConfigure | Coordinate provider onboarding, access, templates and team structure.\nConnect | Link forms, landing pages, campaigns or business tools where required.\nLaunch | Test the journeys, train the team and define the next optimisation cycle.",
			'note'     => 'WhatsApp/Meta approval, message charges and any third-party platform subscription are separate and remain subject to the provider’s current policies.',
		),
		'whatsapp-marketing' => array(
			'title'    => 'WhatsApp Marketing',
			'excerpt'  => 'Permission-led broadcasts, campaign journeys and conversion tracking designed to turn customer attention into useful action.',
			'content'  => '<p>WhatsApp marketing works when the message is timely, relevant and easy to act on. We shape an opt-in journey, audience structure, message templates and landing experience that support campaigns without losing the human quality of the channel.</p><h2>Campaigns connected to the customer journey</h2><p>Launch product updates, offers, reminders and follow-ups with clear calls to action. Audience segments, scheduling and performance reviews help your team learn what creates replies, leads and sales.</p>',
			'kicker'   => 'Broadcast, engage and grow',
			'icon'     => 'bi-broadcast-pin',
			'accent'   => '#6b5cff',
			'features' => "Opt-in and audience strategy\nCampaign message planning\nApproved template support\nBroadcast scheduling workflow\nLanding page and CTA integration\nPerformance and conversion review",
			'steps'    => "Plan | Define the audience, goal, consent path and campaign offer.\nCreate | Build the message sequence, creative assets and destination experience.\nActivate | Configure segments, templates, timing and campaign tracking.\nImprove | Review delivery, clicks, replies and qualified outcomes.",
			'note'     => 'Campaigns must follow WhatsApp consent, template and messaging-quality rules. Platform and message charges are billed separately by the selected provider.',
		),
		'whatsapp-chatbots' => array(
			'title'    => 'WhatsApp Chatbots',
			'excerpt'  => 'No-code conversation flows that answer common questions, qualify leads and route customers to the right next step.',
			'content'  => '<p>Create dependable, rule-based chat journeys without turning every request into a manual task. We map customer intent, structure the flow and connect helpful replies, menus, forms and hand-off points into an experience your team can maintain.</p><h2>Automation with a clear escape route</h2><p>Chatbots can guide product discovery, capture enquiries, share information and resolve frequent questions. Human hand-off remains visible so customers are never trapped inside an automation loop.</p>',
			'kicker'   => 'No-code flow automation',
			'icon'     => 'bi-diagram-3-fill',
			'accent'   => '#ff7a45',
			'features' => "Conversation and intent mapping\nDrag-and-drop flow configuration\nLead qualification questions\nFAQ and product guidance\nHuman-agent hand-off\nTesting and optimisation",
			'steps'    => "Map | Identify common questions, decision points and the right human hand-offs.\nWrite | Create concise prompts, replies and error-recovery paths.\nBuild | Configure and connect the approved conversation flow.\nTest | Check every branch on real devices before launch.",
			'note'     => 'Flow-builder access and message usage depend on the selected WhatsApp platform plan.',
		),
		'ai-whatsapp-chatbot' => array(
			'title'    => 'AI WhatsApp Chatbot',
			'excerpt'  => 'A knowledge-led AI assistant that can understand natural questions, respond around the clock and escalate with context.',
			'content'  => '<p>An AI assistant can answer varied customer questions without forcing people through a rigid menu. We organise the approved knowledge, define guardrails and connect the assistant to the customer journey so replies remain useful and on-brand.</p><h2>Human-like speed with human oversight</h2><p>The experience can support multiple languages, product questions, lead qualification and routine service requests. Clear fallback and escalation rules keep complex or sensitive conversations with the right team member.</p>',
			'kicker'   => 'AI-assisted customer experience',
			'icon'     => 'bi-robot',
			'accent'   => '#ff3eb5',
			'features' => "Knowledge-base preparation\nNatural-language response design\nBrand tone and guardrails\nMultilingual journey planning\nCRM or API integration planning\nHuman escalation with context",
			'steps'    => "Scope | Choose the questions and outcomes suitable for AI assistance.\nPrepare | Organise approved content, policies, products and tone guidance.\nTrain | Configure knowledge, guardrails, tools and escalation behaviour.\nOptimise | Review unresolved questions and improve the knowledge continuously.",
			'note'     => 'AI responses require review, maintained source content and appropriate human oversight. AI-message and platform usage costs are separate.',
		),
		'whatsapp-link-qr' => array(
			'title'    => 'WhatsApp Link & QR',
			'excerpt'  => 'Click-to-chat links and trackable QR journeys that move people from websites, print and physical spaces into a conversation.',
			'content'  => '<p>Make it effortless for a customer to begin the right WhatsApp conversation. We create click-to-chat destinations, pre-filled messages and QR touchpoints that can be used across landing pages, campaigns, packaging, signage and events.</p><h2>A small interaction with measurable intent</h2><p>Different links can support different campaigns or locations, helping teams understand where conversations begin. Every destination is checked on desktop and mobile and presented with a clear reason to engage.</p>',
			'kicker'   => 'Click, scan and start talking',
			'icon'     => 'bi-link-45deg',
			'accent'   => '#0da9a0',
			'features' => "Click-to-chat link setup\nPre-filled message planning\nBranded QR artwork\nWebsite WhatsApp button\nCampaign-specific destinations\nTracking and placement guidance",
			'steps'    => "Define | Choose the number, message intent and customer entry points.\nCreate | Build the link, pre-filled message and branded QR treatment.\nPlace | Add the journey to web, social, print or physical touchpoints.\nMeasure | Compare scans, clicks and qualified conversations by campaign.",
			'note'     => 'QR and link performance depends on placement, consent and the availability of the connected WhatsApp number.',
		),
		'whatsapp-blue-tick' => array(
			'title'    => 'WhatsApp Blue Tick',
			'excerpt'  => 'Application-readiness support for businesses seeking an official WhatsApp verified badge and a more trusted presence.',
			'content'  => '<p>A verified badge helps customers recognise that they are speaking with an authentic business account. Design4web can review the digital footprint and account readiness, organise required information and guide the application workflow.</p><h2>Build credibility before applying</h2><p>Verification depends on factors such as business verification, an approved display name, messaging quality, public notability and Meta’s assessment. We focus on the controllable parts and communicate the decision boundary clearly.</p>',
			'kicker'   => 'Verification readiness',
			'icon'     => 'bi-patch-check-fill',
			'accent'   => '#1887f2',
			'features' => "Account-readiness checklist\nBusiness and display-name review\nDigital presence audit\nApplication information support\nMessaging-quality guidance\nStatus and next-step assistance",
			'steps'    => "Review | Check business verification, display name, account activity and public presence.\nPrepare | Gather accurate business details and supporting references.\nApply | Submit through the authorised WhatsApp platform workflow.\nRespond | Review the outcome and plan any eligible next step.",
			'note'     => 'The verified badge is awarded solely at Meta’s discretion. Design4web cannot guarantee approval or influence Meta’s final decision.',
		),
	);

	foreach ( $products as $order => $product ) {
		$post = get_page_by_path( $order, OBJECT, 'd4w_product' );
		if ( ! $post ) {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'd4w_product',
					'post_status'  => 'publish',
					'post_title'   => $product['title'],
					'post_name'    => $order,
					'post_excerpt' => $product['excerpt'],
					'post_content' => $product['content'],
					'menu_order'   => array_search( $order, array_keys( $products ), true ),
				)
			);
		} else {
			$post_id = $post->ID;
			$update  = array( 'ID' => $post_id, 'menu_order' => array_search( $order, array_keys( $products ), true ) );
			if ( ! trim( $post->post_excerpt ) ) {
				$update['post_excerpt'] = $product['excerpt'];
			}
			if ( ! trim( $post->post_content ) ) {
				$update['post_content'] = $product['content'];
			}
			wp_update_post( $update );
		}

		if ( ! $post_id || is_wp_error( $post_id ) ) {
			continue;
		}
		$meta = array(
			'_d4w_product_kicker'   => $product['kicker'],
			'_d4w_product_icon'     => $product['icon'],
			'_d4w_product_accent'   => $product['accent'],
			'_d4w_product_features' => $product['features'],
			'_d4w_product_steps'    => $product['steps'],
			'_d4w_product_note'     => $product['note'],
		);
		foreach ( $meta as $key => $value ) {
			if ( ! get_post_meta( $post_id, $key, true ) ) {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}
}

/**
 * Create editable pricing cards and a small admin-managed social feed.
 */
function d4w_upgrade_growth_supporting_content() {
	$plans = array(
		array( 'Foundation', 'A focused launch package for businesses preparing their first structured WhatsApp customer journey.', 'Custom scope', 'setup engagement', 'Custom scope', 'project or annual plan', 'Start here', "Readiness and platform guidance\nBusiness API setup support\nOne priority customer journey\nLink, QR and website entry points\nLaunch testing and handover", 'Plan my launch', false ),
		array( 'Growth Engine', 'An ongoing marketing system for teams ready to run campaigns, capture leads and improve follow-up.', 'Custom scope', 'managed monthly', 'Custom scope', 'campaign programme', 'Most popular', "Everything in Foundation\nAudience and opt-in planning\nCampaign templates and creative\nBroadcast workflow support\nLanding page integration\nMonthly performance review", 'Build my growth plan', true ),
		array( 'Automation Suite', 'A connected automation programme for chatbots, AI assistance and deeper business-tool integrations.', 'Custom scope', 'managed monthly', 'Custom scope', 'implementation programme', 'For scale', "Everything in Growth Engine\nNo-code chatbot journeys\nAI knowledge preparation\nCRM and API planning\nHuman escalation design\nOptimisation roadmap", 'Scope automation', false ),
	);
	foreach ( $plans as $order => $plan ) {
		$post = d4w_find_seeded_post( $plan[0], 'd4w_plan' );
		if ( ! $post ) {
			$post_id = wp_insert_post( array( 'post_type' => 'd4w_plan', 'post_status' => 'publish', 'post_title' => $plan[0], 'post_content' => $plan[1], 'menu_order' => $order ) );
		} else {
			$post_id = $post->ID;
			wp_update_post( array( 'ID' => $post_id, 'menu_order' => $order ) );
		}
		if ( ! $post_id || is_wp_error( $post_id ) ) {
			continue;
		}
		$meta = array(
			'_d4w_plan_monthly'      => $plan[2],
			'_d4w_plan_monthly_note' => $plan[3],
			'_d4w_plan_yearly'       => $plan[4],
			'_d4w_plan_yearly_note'  => $plan[5],
			'_d4w_plan_badge'        => $plan[6],
			'_d4w_plan_features'     => $plan[7],
			'_d4w_plan_cta'          => $plan[8],
			'_d4w_plan_featured'     => $plan[9] ? '1' : '0',
		);
		foreach ( $meta as $key => $value ) {
			if ( '' === (string) get_post_meta( $post_id, $key, true ) ) {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}

	if ( ! get_posts( array( 'post_type' => 'd4w_social', 'post_status' => 'any', 'posts_per_page' => 1 ) ) ) {
		$social_posts = array(
			array( 'Website systems in motion', 'A closer look at responsive interfaces, thoughtful details and clean digital hand-offs.', 'project-1.jpg' ),
			array( 'Brand moments with purpose', 'Identity and campaign ideas designed to remain recognisable across every touchpoint.', 'project-4.jpg' ),
			array( 'Campaign ideas made tangible', 'Visual experiments, launch thinking and conversion-focused creative from the studio.', 'project-3.jpg' ),
		);
		foreach ( $social_posts as $order => $social ) {
			$post_id = wp_insert_post( array( 'post_type' => 'd4w_social', 'post_status' => 'publish', 'post_title' => $social[0], 'post_excerpt' => $social[1], 'menu_order' => $order ) );
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_d4w_social_platform', 'Studio feed' );
				update_post_meta( $post_id, '_d4w_social_handle', 'Design4web' );
				update_post_meta( $post_id, '_d4w_bundled_image', $social[2] );
			}
		}
	}

	$reviews = get_posts( array( 'post_type' => 'd4w_testimonial', 'post_status' => 'any', 'posts_per_page' => -1, 'no_found_rows' => true ) );
	foreach ( $reviews as $review ) {
		if ( ! get_post_meta( $review->ID, '_d4w_review_source', true ) ) {
			update_post_meta( $review->ID, '_d4w_review_source', 'Client feedback' );
		}
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
 * Find or create a menu item while preserving editor-managed navigation.
 *
 * @param int    $menu_id   Menu term ID.
 * @param string $title     Item title.
 * @param string $url       Item URL.
 * @param int    $parent_id Parent menu item ID.
 * @param int    $object_id Optional post ID.
 * @param string $object    Optional post type.
 * @return int
 */
function d4w_ensure_menu_item( $menu_id, $title, $url, $parent_id = 0, $object_id = 0, $object = '' ) {
	$items = wp_get_nav_menu_items( $menu_id );
	foreach ( $items ?: array() as $item ) {
		$object_match = $object_id && (int) $item->object_id === (int) $object_id && $object === $item->object;
		$title_match  = ! $object_id && 0 === strcasecmp( wp_specialchars_decode( $item->title, ENT_QUOTES ), $title );
		if ( ! $object_match && ! $title_match ) {
			continue;
		}
		if ( $parent_id && (int) $item->menu_item_parent !== (int) $parent_id ) {
			wp_update_nav_menu_item(
				$menu_id,
				$item->ID,
				array(
					'menu-item-title'     => $item->title,
					'menu-item-parent-id' => $parent_id,
					'menu-item-object-id' => $object_id ?: $item->object_id,
					'menu-item-object'    => $object ?: $item->object,
					'menu-item-type'      => $object_id ? 'post_type' : $item->type,
					'menu-item-url'       => $object_id ? '' : $url,
					'menu-item-status'    => 'publish',
				)
			);
		}
		return (int) $item->ID;
	}

	$args = array(
		'menu-item-title'     => $title,
		'menu-item-parent-id' => $parent_id,
		'menu-item-status'    => 'publish',
	);
	if ( $object_id && $object ) {
		$args['menu-item-object-id'] = $object_id;
		$args['menu-item-object']    = $object;
		$args['menu-item-type']      = 'post_type';
	} else {
		$args['menu-item-url']  = $url;
		$args['menu-item-type'] = 'custom';
	}
	$item_id = wp_update_nav_menu_item( $menu_id, 0, $args );
	return is_wp_error( $item_id ) ? 0 : (int) $item_id;
}

/**
 * Keep the primary navigation aligned with the approved site architecture.
 * Product children remain dynamic and keep their admin-defined order.
 *
 * @param int $menu_id Menu term ID.
 */
function d4w_upgrade_primary_menu( $menu_id ) {
	if ( ! $menu_id ) {
		return;
	}

	$desired_items = array();
	$desired_items['Home'] = d4w_ensure_menu_item( $menu_id, 'Home', home_url( '/' ) );
	$desired_items['About'] = d4w_ensure_menu_item( $menu_id, 'About', d4w_page_url( 'about' ) );
	$desired_items['Services'] = d4w_ensure_menu_item( $menu_id, 'Services', get_post_type_archive_link( 'd4w_service' ) ?: home_url( '/services/' ) );
	$products_id = d4w_ensure_menu_item( $menu_id, 'Products', get_post_type_archive_link( 'd4w_product' ) ?: home_url( '/products/' ) );
	$desired_items['Products'] = $products_id;
	$desired_items['Clients'] = d4w_ensure_menu_item( $menu_id, 'Clients', get_post_type_archive_link( 'd4w_project' ) ?: home_url( '/work/' ) );
	$desired_items['Testimonial'] = d4w_ensure_menu_item( $menu_id, 'Testimonial', home_url( '/#reviews' ) );
	$desired_items['Contact Us'] = d4w_ensure_menu_item( $menu_id, 'Contact Us', d4w_page_url( 'contact' ) );

	if ( ! $products_id ) {
		return;
	}
	$products = get_posts(
		array(
			'post_type'      => 'd4w_product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'no_found_rows'  => true,
		)
	);
	foreach ( $products as $product ) {
		d4w_ensure_menu_item( $menu_id, $product->post_title, get_permalink( $product ), $products_id, $product->ID, 'd4w_product' );
	}

	$items       = wp_get_nav_menu_items( $menu_id );
	$desired_ids = array_values( array_filter( array_map( 'intval', $desired_items ) ) );
	foreach ( $items ?: array() as $item ) {
		if ( 0 === (int) $item->menu_item_parent && ! in_array( (int) $item->ID, $desired_ids, true ) ) {
			wp_delete_post( $item->ID, true );
		}
	}

	$items       = wp_get_nav_menu_items( $menu_id );
	$product_nav = array();
	foreach ( $items ?: array() as $item ) {
		if ( 'd4w_product' === $item->object && (int) $item->menu_item_parent === $products_id ) {
			$product_nav[] = $item;
		}
	}
	usort(
		$product_nav,
		function ( $a, $b ) {
			return (int) get_post_field( 'menu_order', $a->object_id ) <=> (int) get_post_field( 'menu_order', $b->object_id );
		}
	);

	$position = 1;
	$placed   = array();
	foreach ( $desired_items as $title => $item_id ) {
		if ( ! $item_id ) {
			continue;
		}
		wp_update_post( array( 'ID' => $item_id, 'menu_order' => $position++ ) );
		$placed[] = (int) $item_id;
		if ( 'Products' === $title ) {
			foreach ( $product_nav as $product_item ) {
				wp_update_post( array( 'ID' => $product_item->ID, 'menu_order' => $position++ ) );
				$placed[] = (int) $product_item->ID;
			}
		}
	}
	foreach ( $items ?: array() as $item ) {
		if ( ! in_array( (int) $item->ID, $placed, true ) ) {
			wp_update_post( array( 'ID' => $item->ID, 'menu_order' => $position++ ) );
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
	$pricing_id    = d4w_ensure_page( 'Pricing', 'pricing' );
	$about_template = $about_id ? get_post_meta( $about_id, '_wp_page_template', true ) : '';
	$contact_template = $contact_id ? get_post_meta( $contact_id, '_wp_page_template', true ) : '';
	$pricing_template = $pricing_id ? get_post_meta( $pricing_id, '_wp_page_template', true ) : '';
	if ( $about_id && ( ! $about_template || 'default' === $about_template ) ) {
		update_post_meta( $about_id, '_wp_page_template', 'page-about.php' );
	}
	if ( $contact_id && ( ! $contact_template || 'default' === $contact_template ) ) {
		update_post_meta( $contact_id, '_wp_page_template', 'page-contact.php' );
	}
	if ( $pricing_id && ( ! $pricing_template || 'default' === $pricing_template ) ) {
		update_post_meta( $pricing_id, '_wp_page_template', 'page-pricing.php' );
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
						array( 'Products', get_post_type_archive_link( 'd4w_product' ) ?: home_url( '/products/' ) ),
						array( 'Clients', get_post_type_archive_link( 'd4w_project' ) ?: home_url( '/work/' ) ),
						array( 'Testimonial', home_url( '/#reviews' ) ),
						array( 'Contact Us', $contact_id ? get_permalink( $contact_id ) : home_url( '/contact/' ) ),
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

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( ! empty( $locations['primary'] ) ) {
		d4w_upgrade_primary_menu( (int) $locations['primary'] );
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
	if ( ! d4w_upgrade_projects() ) {
		return;
	}
	d4w_upgrade_products();
	d4w_upgrade_growth_supporting_content();
	d4w_upgrade_supporting_content();
	d4w_upgrade_site_structure();
	if ( '1.0' !== get_option( 'd4w_footer_content_version' ) ) {
		set_theme_mod( 'd4w_footer_intro', 'We are a web designing and digital marketing company focused on results, growth and innovation. From crafting a conversion-driven website to amplifying your social reach, our digital experts help you reach new heights in the digital space.' );
		set_theme_mod( 'd4w_instagram_url', 'https://www.instagram.com/designforwebdevelopment/' );
		set_theme_mod( 'd4w_facebook_url', 'https://www.facebook.com/designforwebdevelopment' );
		set_theme_mod( 'd4w_youtube_url', 'https://www.youtube.com/@designforwebdevelopment' );
		set_theme_mod( 'd4w_linkedin_url', 'https://www.linkedin.com/company/design4webdevelopment/' );
		update_option( 'd4w_footer_content_version', '1.0' );
	}
	if ( '1.0' !== get_option( 'd4w_whatsapp_destination_version' ) ) {
		set_theme_mod( 'd4w_whatsapp', '917718958220' );
		set_theme_mod( 'd4w_whatsapp_message', 'Hi' );
		update_option( 'd4w_whatsapp_destination_version', '1.0' );
	}
	update_option( 'd4w_schema_version', D4W_SCHEMA_VERSION );
	flush_rewrite_rules( false );
}
add_action( 'init', 'd4w_run_schema_upgrade', 99 );

<?php
/**
 * Idempotent site structure and content upgrade routines.
 *
 * @package Design4Web
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'D4W_SCHEMA_VERSION', '3.0.0' );

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
		if ( $order < 3 && ! metadata_exists( 'post', $post_id, '_d4w_featured_case_study' ) ) {
			update_post_meta( $post_id, '_d4w_featured_case_study', '1' );
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
 * Ensure the primary menu exposes services, products and pricing after an
 * in-place upgrade without replacing the user's other menu choices.
 *
 * @param int $menu_id Menu term ID.
 */
function d4w_upgrade_primary_menu( $menu_id ) {
	if ( ! $menu_id ) {
		return;
	}
	d4w_ensure_menu_item( $menu_id, 'Services', get_post_type_archive_link( 'd4w_service' ) ?: home_url( '/services/' ) );
	$products_id = d4w_ensure_menu_item( $menu_id, 'Products', get_post_type_archive_link( 'd4w_product' ) ?: home_url( '/products/' ) );
	d4w_ensure_menu_item( $menu_id, 'Work', get_post_type_archive_link( 'd4w_project' ) ?: home_url( '/work/' ) );
	d4w_ensure_menu_item( $menu_id, 'Pricing', d4w_page_url( 'pricing' ) );

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
	$desired     = array( 'Home', 'About', 'Services', 'Products', 'Work', 'Pricing', 'Journal', 'Contact' );
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
	foreach ( $desired as $title ) {
		foreach ( $items ?: array() as $item ) {
			if ( (int) $item->menu_item_parent || 0 !== strcasecmp( wp_specialchars_decode( $item->title, ENT_QUOTES ), $title ) ) {
				continue;
			}
			wp_update_post( array( 'ID' => $item->ID, 'menu_order' => $position++ ) );
			$placed[] = (int) $item->ID;
			if ( 'Products' === $title ) {
				foreach ( $product_nav as $product_item ) {
					wp_update_post( array( 'ID' => $product_item->ID, 'menu_order' => $position++ ) );
					$placed[] = (int) $product_item->ID;
				}
			}
			break;
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
						array( 'Work', get_post_type_archive_link( 'd4w_project' ) ?: home_url( '/work/' ) ),
						array( 'Pricing', $pricing_id ? get_permalink( $pricing_id ) : home_url( '/pricing/' ) ),
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
	d4w_upgrade_projects();
	d4w_upgrade_products();
	d4w_upgrade_growth_supporting_content();
	d4w_upgrade_supporting_content();
	d4w_upgrade_site_structure();
	update_option( 'd4w_schema_version', D4W_SCHEMA_VERSION );
	flush_rewrite_rules( false );
}
add_action( 'init', 'd4w_run_schema_upgrade', 99 );

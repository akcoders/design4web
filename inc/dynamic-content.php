<?php
/**
 * Dynamic content types and their admin interfaces.
 *
 * All content types in this file are managed in wp-admin and intentionally do
 * not expose public archives, single URLs, query variables, or REST endpoints.
 * Theme templates can still retrieve published items with WP_Query.
 *
 * @package Design4Web
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the allowed enquiry workflow statuses.
 *
 * @return array<string, string>
 */
function d4w_get_inquiry_statuses() {
	return array(
		'new'       => __( 'New', 'design4web' ),
		'contacted' => __( 'Contacted', 'design4web' ),
		'qualified' => __( 'Qualified', 'design4web' ),
		'closed'    => __( 'Closed', 'design4web' ),
		'spam'      => __( 'Spam', 'design4web' ),
	);
}

/**
 * Sanitize an enquiry workflow status.
 *
 * @param mixed $status Status value.
 * @return string
 */
function d4w_sanitize_inquiry_status( $status ) {
	$status   = sanitize_key( (string) $status );
	$statuses = d4w_get_inquiry_statuses();

	return isset( $statuses[ $status ] ) ? $status : 'new';
}

/**
 * Determine whether the current user may edit registered dynamic meta.
 *
 * @param bool   $allowed  Existing authorization result.
 * @param string $meta_key Meta key.
 * @param int    $post_id  Post ID.
 * @return bool
 */
function d4w_dynamic_meta_auth( $allowed, $meta_key, $post_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	return $post_id > 0 && current_user_can( 'edit_post', $post_id );
}

/**
 * Register private, admin-managed content types and their metadata.
 */
function d4w_register_dynamic_content() {
	$shared_args = array(
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => false,
		'show_in_rest'        => false,
		'has_archive'         => false,
		'query_var'           => false,
		'rewrite'             => false,
		'map_meta_cap'        => true,
	);

	register_post_type(
		'd4w_process',
		array_merge(
			$shared_args,
			array(
				'labels'        => array(
					'name'               => __( 'Process Steps', 'design4web' ),
					'singular_name'      => __( 'Process Step', 'design4web' ),
					'menu_name'          => __( 'Process Steps', 'design4web' ),
					'add_new'            => __( 'Add Step', 'design4web' ),
					'add_new_item'       => __( 'Add New Process Step', 'design4web' ),
					'edit_item'          => __( 'Edit Process Step', 'design4web' ),
					'new_item'           => __( 'New Process Step', 'design4web' ),
					'view_item'          => __( 'View Process Step', 'design4web' ),
					'search_items'       => __( 'Search Process Steps', 'design4web' ),
					'not_found'          => __( 'No process steps found.', 'design4web' ),
					'not_found_in_trash' => __( 'No process steps found in Trash.', 'design4web' ),
					'all_items'          => __( 'All Process Steps', 'design4web' ),
				),
				'menu_icon'     => 'dashicons-editor-ol',
				'menu_position' => 24,
				'supports'      => array( 'title', 'editor', 'page-attributes' ),
			)
		)
	);

	register_post_type(
		'd4w_faq',
		array_merge(
			$shared_args,
			array(
				'labels'        => array(
					'name'               => __( 'FAQs', 'design4web' ),
					'singular_name'      => __( 'FAQ', 'design4web' ),
					'menu_name'          => __( 'FAQs', 'design4web' ),
					'add_new'            => __( 'Add FAQ', 'design4web' ),
					'add_new_item'       => __( 'Add New FAQ', 'design4web' ),
					'edit_item'          => __( 'Edit FAQ', 'design4web' ),
					'new_item'           => __( 'New FAQ', 'design4web' ),
					'view_item'          => __( 'View FAQ', 'design4web' ),
					'search_items'       => __( 'Search FAQs', 'design4web' ),
					'not_found'          => __( 'No FAQs found.', 'design4web' ),
					'not_found_in_trash' => __( 'No FAQs found in Trash.', 'design4web' ),
					'all_items'          => __( 'All FAQs', 'design4web' ),
				),
				'menu_icon'     => 'dashicons-editor-help',
				'menu_position' => 25,
				'supports'      => array( 'title', 'editor', 'page-attributes' ),
			)
		)
	);

	register_post_type(
		'd4w_team',
		array_merge(
			$shared_args,
			array(
				'labels'        => array(
					'name'               => __( 'Team Members', 'design4web' ),
					'singular_name'      => __( 'Team Member', 'design4web' ),
					'menu_name'          => __( 'Team', 'design4web' ),
					'add_new'            => __( 'Add Member', 'design4web' ),
					'add_new_item'       => __( 'Add New Team Member', 'design4web' ),
					'edit_item'          => __( 'Edit Team Member', 'design4web' ),
					'new_item'           => __( 'New Team Member', 'design4web' ),
					'view_item'          => __( 'View Team Member', 'design4web' ),
					'search_items'       => __( 'Search Team Members', 'design4web' ),
					'not_found'          => __( 'No team members found.', 'design4web' ),
					'not_found_in_trash' => __( 'No team members found in Trash.', 'design4web' ),
					'all_items'          => __( 'All Team Members', 'design4web' ),
				),
				'menu_icon'     => 'dashicons-groups',
				'menu_position' => 26,
				'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			)
		)
	);

	register_post_type(
		'd4w_inquiry',
		array_merge(
			$shared_args,
			array(
				'labels'        => array(
					'name'               => __( 'Enquiries', 'design4web' ),
					'singular_name'      => __( 'Enquiry', 'design4web' ),
					'menu_name'          => __( 'Enquiries', 'design4web' ),
					'edit_item'          => __( 'View Enquiry', 'design4web' ),
					'view_item'          => __( 'View Enquiry', 'design4web' ),
					'search_items'       => __( 'Search Enquiries', 'design4web' ),
					'not_found'          => __( 'No enquiries found.', 'design4web' ),
					'not_found_in_trash' => __( 'No enquiries found in Trash.', 'design4web' ),
					'all_items'          => __( 'All Enquiries', 'design4web' ),
				),
				'map_meta_cap'  => false,
				'capabilities'  => array(
					'edit_post'              => 'manage_options',
					'read_post'              => 'manage_options',
					'delete_post'            => 'manage_options',
					'edit_posts'             => 'manage_options',
					'edit_others_posts'      => 'manage_options',
					'publish_posts'          => 'manage_options',
					'read_private_posts'     => 'manage_options',
					'delete_posts'           => 'manage_options',
					'delete_private_posts'   => 'manage_options',
					'delete_published_posts' => 'manage_options',
					'delete_others_posts'    => 'manage_options',
					'edit_private_posts'     => 'manage_options',
					'edit_published_posts'   => 'manage_options',
					'create_posts'           => 'do_not_allow',
				),
				'menu_icon'     => 'dashicons-email-alt2',
				'menu_position' => 27,
				'supports'      => array( 'title', 'editor' ),
			)
		)
	);

	$meta_fields = array(
		'd4w_process' => array(
			'_d4w_process_number' => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_process_icon'   => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_html_class' ),
		),
		'd4w_faq'     => array(
			'_d4w_faq_category' => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
		),
		'd4w_team'    => array(
			'_d4w_team_role'     => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_team_email'    => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ),
			'_d4w_team_linkedin' => array( 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' ),
		),
		'd4w_inquiry' => array(
			'_d4w_inquiry_name'    => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_inquiry_email'   => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ),
			'_d4w_inquiry_phone'   => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_inquiry_service' => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_inquiry_budget'  => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_inquiry_timeline'=> array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_inquiry_source'  => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_key' ),
			'_d4w_inquiry_status'  => array( 'type' => 'string', 'sanitize_callback' => 'd4w_sanitize_inquiry_status' ),
		),
	);

	foreach ( $meta_fields as $post_type => $fields ) {
		foreach ( $fields as $meta_key => $args ) {
			register_post_meta(
				$post_type,
				$meta_key,
				array(
					'type'              => $args['type'],
					'single'            => true,
					'default'           => '_d4w_inquiry_status' === $meta_key ? 'new' : '',
					'show_in_rest'      => false,
					'sanitize_callback' => $args['sanitize_callback'],
					'auth_callback'     => 'd4w_dynamic_meta_auth',
				)
			);
		}
	}
}
add_action( 'init', 'd4w_register_dynamic_content' );

/**
 * Render the nonce shared by dynamic meta boxes.
 */
function d4w_dynamic_meta_nonce_field() {
	wp_nonce_field( 'd4w_save_dynamic_meta', 'd4w_dynamic_meta_nonce' );
}

/**
 * Add dynamic content meta boxes.
 */
function d4w_add_dynamic_meta_boxes() {
	add_meta_box( 'd4w_process_details', __( 'Step Details', 'design4web' ), 'd4w_process_meta_box', 'd4w_process', 'side', 'default' );
	add_meta_box( 'd4w_faq_details', __( 'FAQ Details', 'design4web' ), 'd4w_faq_meta_box', 'd4w_faq', 'side', 'default' );
	add_meta_box( 'd4w_team_details', __( 'Member Details', 'design4web' ), 'd4w_team_meta_box', 'd4w_team', 'side', 'default' );
	add_meta_box( 'd4w_inquiry_details', __( 'Enquiry Details', 'design4web' ), 'd4w_inquiry_meta_box', 'd4w_inquiry', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'd4w_add_dynamic_meta_boxes' );

/**
 * Render a standard text field used by the dynamic meta boxes.
 *
 * @param int    $post_id     Post ID.
 * @param string $key         Meta key.
 * @param string $label       Field label.
 * @param string $type        Input type.
 * @param string $description Optional help text.
 */
function d4w_dynamic_meta_field( $post_id, $key, $label, $type = 'text', $description = '' ) {
	$value = get_post_meta( $post_id, $key, true );
	?>
	<p>
		<label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label><br>
		<input class="widefat" type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
		<?php if ( $description ) : ?>
			<small style="display:block;margin-top:4px"><?php echo esc_html( $description ); ?></small>
		<?php endif; ?>
	</p>
	<?php
}

/**
 * Render process step fields.
 *
 * @param WP_Post $post Current post.
 */
function d4w_process_meta_box( $post ) {
	d4w_dynamic_meta_nonce_field();
	d4w_dynamic_meta_field( $post->ID, '_d4w_process_number', __( 'Display number', 'design4web' ), 'text', __( 'For example: 01', 'design4web' ) );
	d4w_dynamic_meta_field( $post->ID, '_d4w_process_icon', __( 'Bootstrap icon class', 'design4web' ), 'text', __( 'For example: bi-lightbulb', 'design4web' ) );
}

/**
 * Render FAQ fields.
 *
 * @param WP_Post $post Current post.
 */
function d4w_faq_meta_box( $post ) {
	d4w_dynamic_meta_nonce_field();
	d4w_dynamic_meta_field( $post->ID, '_d4w_faq_category', __( 'Category', 'design4web' ), 'text', __( 'Used to group and filter questions.', 'design4web' ) );
}

/**
 * Render team member fields.
 *
 * @param WP_Post $post Current post.
 */
function d4w_team_meta_box( $post ) {
	d4w_dynamic_meta_nonce_field();
	d4w_dynamic_meta_field( $post->ID, '_d4w_team_role', __( 'Role / position', 'design4web' ) );
	d4w_dynamic_meta_field( $post->ID, '_d4w_team_email', __( 'Email address', 'design4web' ), 'email' );
	d4w_dynamic_meta_field( $post->ID, '_d4w_team_linkedin', __( 'LinkedIn URL', 'design4web' ), 'url' );
}

/**
 * Render enquiry details and status control.
 *
 * @param WP_Post $post Current post.
 */
function d4w_inquiry_meta_box( $post ) {
	$name     = get_post_meta( $post->ID, '_d4w_inquiry_name', true );
	$email    = get_post_meta( $post->ID, '_d4w_inquiry_email', true );
	$phone    = get_post_meta( $post->ID, '_d4w_inquiry_phone', true );
	$service  = get_post_meta( $post->ID, '_d4w_inquiry_service', true );
	$budget   = get_post_meta( $post->ID, '_d4w_inquiry_budget', true );
	$timeline = get_post_meta( $post->ID, '_d4w_inquiry_timeline', true );
	$source   = get_post_meta( $post->ID, '_d4w_inquiry_source', true );
	$status   = d4w_sanitize_inquiry_status( get_post_meta( $post->ID, '_d4w_inquiry_status', true ) );
	$statuses = d4w_get_inquiry_statuses();

	d4w_dynamic_meta_nonce_field();
	?>
	<p><strong><?php esc_html_e( 'Name', 'design4web' ); ?></strong><br><?php echo esc_html( $name ?: '—' ); ?></p>
	<p><strong><?php esc_html_e( 'Email', 'design4web' ); ?></strong><br>
		<?php if ( $email ) : ?><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a><?php else : ?>—<?php endif; ?>
	</p>
	<p><strong><?php esc_html_e( 'Phone', 'design4web' ); ?></strong><br>
		<?php if ( $phone ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a><?php else : ?>—<?php endif; ?>
	</p>
	<p><strong><?php esc_html_e( 'Service', 'design4web' ); ?></strong><br><?php echo esc_html( $service ?: '—' ); ?></p>
	<p><strong><?php esc_html_e( 'Budget', 'design4web' ); ?></strong><br><?php echo esc_html( $budget ?: '—' ); ?></p>
	<p><strong><?php esc_html_e( 'Timeline', 'design4web' ); ?></strong><br><?php echo esc_html( $timeline ?: '—' ); ?></p>
	<p><strong><?php esc_html_e( 'Source', 'design4web' ); ?></strong><br><?php echo esc_html( $source ?: '—' ); ?></p>
	<p>
		<label for="_d4w_inquiry_status"><strong><?php esc_html_e( 'Status', 'design4web' ); ?></strong></label><br>
		<select class="widefat" id="_d4w_inquiry_status" name="_d4w_inquiry_status">
			<?php foreach ( $statuses as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

/**
 * Save dynamic meta box fields.
 *
 * @param int $post_id Post ID.
 */
function d4w_save_dynamic_meta( $post_id ) {
	if ( ! isset( $_POST['d4w_dynamic_meta_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['d4w_dynamic_meta_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'd4w_save_dynamic_meta' ) ) {
		return;
	}

	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );
	$fields    = array(
		'd4w_process' => array(
			'_d4w_process_number' => 'sanitize_text_field',
			'_d4w_process_icon'   => 'sanitize_html_class',
		),
		'd4w_faq'     => array(
			'_d4w_faq_category' => 'sanitize_text_field',
		),
		'd4w_team'    => array(
			'_d4w_team_role'     => 'sanitize_text_field',
			'_d4w_team_email'    => 'sanitize_email',
			'_d4w_team_linkedin' => 'esc_url_raw',
		),
		'd4w_inquiry' => array(
			'_d4w_inquiry_status' => 'd4w_sanitize_inquiry_status',
		),
	);

	if ( ! isset( $fields[ $post_type ] ) ) {
		return;
	}

	foreach ( $fields[ $post_type ] as $meta_key => $sanitize_callback ) {
		if ( ! isset( $_POST[ $meta_key ] ) ) {
			continue;
		}

		$value = call_user_func( $sanitize_callback, wp_unslash( $_POST[ $meta_key ] ) );
		if ( '' === $value && '_d4w_inquiry_status' !== $meta_key ) {
			delete_post_meta( $post_id, $meta_key );
		} else {
			update_post_meta( $post_id, $meta_key, $value );
		}
	}
}
add_action( 'save_post', 'd4w_save_dynamic_meta' );

/**
 * Store a contact enquiry as a private admin record.
 *
 * The caller remains responsible for nonce verification, bot protection, and
 * any request-level rate limiting before invoking this helper.
 *
 * @param array<string, mixed> $data Name, email, phone, service and message.
 * @return int|WP_Error Enquiry post ID on success, otherwise a WP_Error.
 */
function d4w_store_inquiry( $data ) {
	if ( ! is_array( $data ) ) {
		return new WP_Error( 'd4w_invalid_inquiry', __( 'Invalid enquiry data.', 'design4web' ) );
	}

	$name    = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
	$email   = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';
	$phone   = isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '';
	$service = isset( $data['service'] ) ? sanitize_text_field( $data['service'] ) : '';
	$budget  = isset( $data['budget'] ) ? sanitize_text_field( $data['budget'] ) : '';
	$timeline= isset( $data['timeline'] ) ? sanitize_text_field( $data['timeline'] ) : '';
	$source  = isset( $data['source'] ) ? sanitize_key( $data['source'] ) : '';
	$message = isset( $data['message'] ) ? sanitize_textarea_field( $data['message'] ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		return new WP_Error( 'd4w_incomplete_inquiry', __( 'A name, valid email address and message are required.', 'design4web' ) );
	}

	$received_at = current_datetime()->format( 'd M Y, H:i' );
	$post_id     = wp_insert_post(
		array(
			'post_type'    => 'd4w_inquiry',
			'post_status'  => 'private',
			'post_title'   => sprintf(
				/* translators: 1: sender name, 2: date and time received. */
				__( '%1$s — %2$s', 'design4web' ),
				$name,
				$received_at
			),
			'post_content' => $message,
			'meta_input'   => array(
				'_d4w_inquiry_name'    => $name,
				'_d4w_inquiry_email'   => $email,
				'_d4w_inquiry_phone'   => $phone,
				'_d4w_inquiry_service' => $service,
				'_d4w_inquiry_budget'  => $budget,
				'_d4w_inquiry_timeline'=> $timeline,
				'_d4w_inquiry_source'  => $source,
				'_d4w_inquiry_status'  => 'new',
			),
		),
		true
	);

	return $post_id;
}

/**
 * Set useful columns for the dynamic content list tables.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function d4w_process_admin_columns( $columns ) {
	return array(
		'cb'                 => $columns['cb'],
		'title'              => __( 'Step', 'design4web' ),
		'd4w_process_number' => __( 'Number', 'design4web' ),
		'd4w_process_icon'   => __( 'Icon', 'design4web' ),
		'menu_order'         => __( 'Order', 'design4web' ),
		'date'               => $columns['date'],
	);
}
add_filter( 'manage_d4w_process_posts_columns', 'd4w_process_admin_columns' );

/**
 * Set FAQ admin columns.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function d4w_faq_admin_columns( $columns ) {
	return array(
		'cb'               => $columns['cb'],
		'title'            => __( 'Question', 'design4web' ),
		'd4w_faq_category' => __( 'Category', 'design4web' ),
		'menu_order'       => __( 'Order', 'design4web' ),
		'date'             => $columns['date'],
	);
}
add_filter( 'manage_d4w_faq_posts_columns', 'd4w_faq_admin_columns' );

/**
 * Set team admin columns.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function d4w_team_admin_columns( $columns ) {
	return array(
		'cb'            => $columns['cb'],
		'd4w_thumbnail' => __( 'Photo', 'design4web' ),
		'title'         => __( 'Name', 'design4web' ),
		'd4w_team_role' => __( 'Role', 'design4web' ),
		'd4w_team_email'=> __( 'Email', 'design4web' ),
		'menu_order'    => __( 'Order', 'design4web' ),
		'date'          => $columns['date'],
	);
}
add_filter( 'manage_d4w_team_posts_columns', 'd4w_team_admin_columns' );

/**
 * Set enquiry admin columns.
 *
 * @param array<string, string> $columns Existing columns.
 * @return array<string, string>
 */
function d4w_inquiry_admin_columns( $columns ) {
	return array(
		'cb'                  => $columns['cb'],
		'title'               => __( 'Sender', 'design4web' ),
		'd4w_inquiry_email'   => __( 'Email', 'design4web' ),
		'd4w_inquiry_phone'   => __( 'Phone', 'design4web' ),
		'd4w_inquiry_service' => __( 'Service', 'design4web' ),
		'd4w_inquiry_status'  => __( 'Status', 'design4web' ),
		'date'                => __( 'Received', 'design4web' ),
	);
}
add_filter( 'manage_d4w_inquiry_posts_columns', 'd4w_inquiry_admin_columns' );

/**
 * Render custom dynamic-content list-table columns.
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 */
function d4w_dynamic_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'd4w_process_number':
			echo esc_html( get_post_meta( $post_id, '_d4w_process_number', true ) ?: '—' );
			break;
		case 'd4w_process_icon':
			$icon = get_post_meta( $post_id, '_d4w_process_icon', true );
			echo $icon ? '<code>' . esc_html( $icon ) . '</code>' : '—';
			break;
		case 'd4w_faq_category':
			echo esc_html( get_post_meta( $post_id, '_d4w_faq_category', true ) ?: '—' );
			break;
		case 'd4w_thumbnail':
			echo get_the_post_thumbnail( $post_id, array( 50, 50 ), array( 'style' => 'width:50px;height:50px;object-fit:cover;border-radius:50%;' ) ) ?: '—'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			break;
		case 'd4w_team_role':
			echo esc_html( get_post_meta( $post_id, '_d4w_team_role', true ) ?: '—' );
			break;
		case 'd4w_team_email':
		case 'd4w_inquiry_email':
			$email = get_post_meta( $post_id, '_' . $column, true );
			if ( $email ) {
				echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
			} else {
				echo '—';
			}
			break;
		case 'd4w_inquiry_phone':
			$phone = get_post_meta( $post_id, '_d4w_inquiry_phone', true );
			if ( $phone ) {
				echo '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a>';
			} else {
				echo '—';
			}
			break;
		case 'd4w_inquiry_service':
			echo esc_html( get_post_meta( $post_id, '_d4w_inquiry_service', true ) ?: '—' );
			break;
		case 'd4w_inquiry_status':
			$status   = d4w_sanitize_inquiry_status( get_post_meta( $post_id, '_d4w_inquiry_status', true ) );
			$statuses = d4w_get_inquiry_statuses();
			echo esc_html( $statuses[ $status ] );
			break;
		case 'menu_order':
			echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
			break;
	}
}
add_action( 'manage_d4w_process_posts_custom_column', 'd4w_dynamic_admin_column_content', 10, 2 );
add_action( 'manage_d4w_faq_posts_custom_column', 'd4w_dynamic_admin_column_content', 10, 2 );
add_action( 'manage_d4w_team_posts_custom_column', 'd4w_dynamic_admin_column_content', 10, 2 );
add_action( 'manage_d4w_inquiry_posts_custom_column', 'd4w_dynamic_admin_column_content', 10, 2 );

/**
 * Mark order columns as sortable.
 *
 * @param array<string, string> $columns Sortable columns.
 * @return array<string, string>
 */
function d4w_dynamic_sortable_columns( $columns ) {
	$columns['menu_order'] = 'menu_order';
	return $columns;
}
add_filter( 'manage_edit-d4w_process_sortable_columns', 'd4w_dynamic_sortable_columns' );
add_filter( 'manage_edit-d4w_faq_sortable_columns', 'd4w_dynamic_sortable_columns' );
add_filter( 'manage_edit-d4w_team_sortable_columns', 'd4w_dynamic_sortable_columns' );

/**
 * Apply a useful default order to dynamic content list tables.
 *
 * @param WP_Query $query Current query.
 */
function d4w_dynamic_admin_default_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$post_type = $query->get( 'post_type' );
	if ( in_array( $post_type, array( 'd4w_process', 'd4w_faq', 'd4w_team' ), true ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
	}

	if ( 'd4w_inquiry' === $post_type && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'DESC' );
	}
}
add_action( 'pre_get_posts', 'd4w_dynamic_admin_default_order' );

<?php
/**
 * Product, pricing and social-feed content managed from wp-admin.
 *
 * @package Design4Web
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the public Products collection and supporting private content.
 */
function d4w_register_growth_content() {
	register_post_type(
		'd4w_product',
		array(
			'labels' => array(
				'name'          => __( 'Products', 'design4web' ),
				'singular_name' => __( 'Product', 'design4web' ),
				'add_new_item'  => __( 'Add New Product', 'design4web' ),
				'edit_item'     => __( 'Edit Product', 'design4web' ),
				'all_items'     => __( 'All Products', 'design4web' ),
			),
			'public'       => true,
			'menu_icon'    => 'dashicons-screenoptions',
			'menu_position'=> 22,
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'products' ),
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

	$private_args = array(
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => false,
		'show_in_rest'        => false,
		'has_archive'         => false,
		'query_var'           => false,
		'rewrite'             => false,
	);

	register_post_type(
		'd4w_plan',
		array_merge(
			$private_args,
			array(
				'labels'        => array(
					'name'          => __( 'Pricing Plans', 'design4web' ),
					'singular_name' => __( 'Pricing Plan', 'design4web' ),
					'menu_name'     => __( 'Pricing Plans', 'design4web' ),
					'add_new_item'  => __( 'Add New Pricing Plan', 'design4web' ),
					'edit_item'     => __( 'Edit Pricing Plan', 'design4web' ),
				),
				'menu_icon'     => 'dashicons-money-alt',
				'menu_position' => 23,
				'supports'      => array( 'title', 'editor', 'page-attributes' ),
			)
		)
	);

	register_post_type(
		'd4w_social',
		array_merge(
			$private_args,
			array(
				'labels'        => array(
					'name'          => __( 'Social Posts', 'design4web' ),
					'singular_name' => __( 'Social Post', 'design4web' ),
					'menu_name'     => __( 'Social Feed', 'design4web' ),
					'add_new_item'  => __( 'Add Social Post', 'design4web' ),
					'edit_item'     => __( 'Edit Social Post', 'design4web' ),
				),
				'menu_icon'     => 'dashicons-instagram',
				'menu_position' => 28,
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			)
		)
	);

	$meta_fields = array(
		'd4w_product' => array(
			'_d4w_product_icon'     => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_html_class' ),
			'_d4w_product_kicker'   => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_product_features' => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field' ),
			'_d4w_product_steps'    => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field' ),
			'_d4w_product_accent'   => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_hex_color' ),
			'_d4w_product_note'     => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field' ),
		),
		'd4w_plan'    => array(
			'_d4w_plan_monthly'      => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_plan_yearly'       => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_plan_monthly_note' => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_plan_yearly_note'  => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_plan_badge'        => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_plan_features'     => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field' ),
			'_d4w_plan_cta'          => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_plan_featured'     => array( 'type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean' ),
		),
		'd4w_social'  => array(
			'_d4w_social_url'      => array( 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' ),
			'_d4w_social_platform' => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
			'_d4w_social_handle'   => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
		),
	);

	foreach ( $meta_fields as $post_type => $fields ) {
		foreach ( $fields as $key => $args ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => $args['type'],
					'single'            => true,
					'show_in_rest'      => false,
					'sanitize_callback' => $args['sanitize_callback'],
					'auth_callback'     => function ( $allowed, $meta_key, $post_id ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
						return $post_id > 0 && current_user_can( 'edit_post', $post_id );
					},
				)
			);
		}
	}
}
add_action( 'init', 'd4w_register_growth_content', 8 );

/**
 * Render a reusable admin text field.
 *
 * @param WP_Post $post        Current post.
 * @param string  $key         Meta key.
 * @param string  $label       Label.
 * @param string  $type        Input type.
 * @param string  $description Help text.
 */
function d4w_growth_field( $post, $key, $label, $type = 'text', $description = '' ) {
	?>
	<p><label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label><br>
	<input class="widefat" type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_post_meta( $post->ID, $key, true ) ); ?>">
	<?php if ( $description ) : ?><small style="display:block;margin-top:4px"><?php echo esc_html( $description ); ?></small><?php endif; ?></p>
	<?php
}

/**
 * Render a reusable admin textarea.
 *
 * @param WP_Post $post        Current post.
 * @param string  $key         Meta key.
 * @param string  $label       Label.
 * @param string  $description Help text.
 */
function d4w_growth_textarea( $post, $key, $label, $description = '' ) {
	?>
	<p><label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label><br>
	<textarea class="widefat" rows="6" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>"><?php echo esc_textarea( get_post_meta( $post->ID, $key, true ) ); ?></textarea>
	<?php if ( $description ) : ?><small style="display:block;margin-top:4px"><?php echo esc_html( $description ); ?></small><?php endif; ?></p>
	<?php
}

/**
 * Add product, pricing and social meta boxes.
 */
function d4w_growth_meta_boxes() {
	add_meta_box( 'd4w_product_details', __( 'Product Experience', 'design4web' ), 'd4w_product_meta_box', 'd4w_product', 'normal', 'high' );
	add_meta_box( 'd4w_plan_details', __( 'Plan Details', 'design4web' ), 'd4w_plan_meta_box', 'd4w_plan', 'normal', 'high' );
	add_meta_box( 'd4w_social_details', __( 'Social Post Details', 'design4web' ), 'd4w_social_meta_box', 'd4w_social', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'd4w_growth_meta_boxes' );

/** Product fields. */
function d4w_product_meta_box( $post ) {
	wp_nonce_field( 'd4w_save_growth_meta', 'd4w_growth_meta_nonce' );
	d4w_growth_field( $post, '_d4w_product_icon', __( 'Bootstrap icon class', 'design4web' ), 'text', __( 'Example: bi-whatsapp', 'design4web' ) );
	d4w_growth_field( $post, '_d4w_product_kicker', __( 'Hero kicker', 'design4web' ) );
	d4w_growth_field( $post, '_d4w_product_accent', __( 'Accent colour', 'design4web' ), 'color' );
	d4w_growth_textarea( $post, '_d4w_product_features', __( 'Key features', 'design4web' ), __( 'One feature per line.', 'design4web' ) );
	d4w_growth_textarea( $post, '_d4w_product_steps', __( 'How it works', 'design4web' ), __( 'One step per line using: Title | Description', 'design4web' ) );
	d4w_growth_textarea( $post, '_d4w_product_note', __( 'Important note', 'design4web' ), __( 'Use for third-party pricing, eligibility or approval disclaimers.', 'design4web' ) );
}

/** Pricing-plan fields. */
function d4w_plan_meta_box( $post ) {
	wp_nonce_field( 'd4w_save_growth_meta', 'd4w_growth_meta_nonce' );
	d4w_growth_field( $post, '_d4w_plan_monthly', __( 'Monthly / managed price', 'design4web' ), 'text', __( 'Use “Custom” when a quote is required.', 'design4web' ) );
	d4w_growth_field( $post, '_d4w_plan_monthly_note', __( 'Monthly price note', 'design4web' ) );
	d4w_growth_field( $post, '_d4w_plan_yearly', __( 'Project / annual price', 'design4web' ) );
	d4w_growth_field( $post, '_d4w_plan_yearly_note', __( 'Project / annual price note', 'design4web' ) );
	d4w_growth_field( $post, '_d4w_plan_badge', __( 'Badge', 'design4web' ), 'text', __( 'Example: Most popular', 'design4web' ) );
	d4w_growth_field( $post, '_d4w_plan_cta', __( 'Button label', 'design4web' ) );
	d4w_growth_textarea( $post, '_d4w_plan_features', __( 'Included features', 'design4web' ), __( 'One item per line.', 'design4web' ) );
	?>
	<p><label><input type="checkbox" name="_d4w_plan_featured" value="1" <?php checked( get_post_meta( $post->ID, '_d4w_plan_featured', true ), '1' ); ?>> <strong><?php esc_html_e( 'Highlight this plan', 'design4web' ); ?></strong></label></p>
	<?php
}

/** Social-post fields. */
function d4w_social_meta_box( $post ) {
	wp_nonce_field( 'd4w_save_growth_meta', 'd4w_growth_meta_nonce' );
	d4w_growth_field( $post, '_d4w_social_url', __( 'Post URL', 'design4web' ), 'url' );
	d4w_growth_field( $post, '_d4w_social_platform', __( 'Platform', 'design4web' ), 'text', __( 'Example: Instagram', 'design4web' ) );
	d4w_growth_field( $post, '_d4w_social_handle', __( 'Handle', 'design4web' ), 'text', __( 'Example: @design4web', 'design4web' ) );
}

/**
 * Save growth-content metadata.
 *
 * @param int $post_id Post ID.
 */
function d4w_save_growth_meta( $post_id ) {
	if ( ! isset( $_POST['d4w_growth_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['d4w_growth_meta_nonce'] ) ), 'd4w_save_growth_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );
	$groups    = array(
		'd4w_product' => array(
			'_d4w_product_icon'     => 'sanitize_html_class',
			'_d4w_product_kicker'   => 'sanitize_text_field',
			'_d4w_product_features' => 'sanitize_textarea_field',
			'_d4w_product_steps'    => 'sanitize_textarea_field',
			'_d4w_product_accent'   => 'sanitize_hex_color',
			'_d4w_product_note'     => 'sanitize_textarea_field',
		),
		'd4w_plan'    => array(
			'_d4w_plan_monthly'      => 'sanitize_text_field',
			'_d4w_plan_yearly'       => 'sanitize_text_field',
			'_d4w_plan_monthly_note' => 'sanitize_text_field',
			'_d4w_plan_yearly_note'  => 'sanitize_text_field',
			'_d4w_plan_badge'        => 'sanitize_text_field',
			'_d4w_plan_features'     => 'sanitize_textarea_field',
			'_d4w_plan_cta'          => 'sanitize_text_field',
		),
		'd4w_social'  => array(
			'_d4w_social_url'      => 'esc_url_raw',
			'_d4w_social_platform' => 'sanitize_text_field',
			'_d4w_social_handle'   => 'sanitize_text_field',
		),
	);

	if ( ! isset( $groups[ $post_type ] ) ) {
		return;
	}

	foreach ( $groups[ $post_type ] as $key => $callback ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$value = call_user_func( $callback, wp_unslash( $_POST[ $key ] ) );
		if ( '' === (string) $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	if ( 'd4w_plan' === $post_type ) {
		update_post_meta( $post_id, '_d4w_plan_featured', isset( $_POST['_d4w_plan_featured'] ) ? '1' : '0' );
	}
}
add_action( 'save_post', 'd4w_save_growth_meta' );

/**
 * Make product dropdown entries richer without requiring a custom walker.
 *
 * @param string   $title Menu item title.
 * @param WP_Post  $item  Menu item object.
 * @param stdClass $args  Menu arguments.
 * @param int      $depth Current depth.
 * @return string
 */
function d4w_product_menu_title( $title, $item, $args, $depth ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	if ( $depth < 1 || 'd4w_product' !== $item->object || ! $item->object_id ) {
		return $title;
	}
	$icon    = get_post_meta( $item->object_id, '_d4w_product_icon', true ) ?: 'bi-box';
	$summary = d4w_card_excerpt( $item->object_id, 7 );
	return '<span class="d4w-product-menu-icon"><i class="bi ' . esc_attr( $icon ) . '"></i></span><span class="d4w-product-menu-copy"><strong>' . esc_html( wp_strip_all_tags( $title ) ) . '</strong><small>' . esc_html( $summary ) . '</small></span>';
}
add_filter( 'nav_menu_item_title', 'd4w_product_menu_title', 10, 4 );

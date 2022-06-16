<?php
/**
 * Fieldmanager Fields for Profile Post Type
 *
 * @package shiro
 */

/**
 * Add fields to profile post type.
 */
function wmf_profile_fields() {
	$last_name = new Fieldmanager_Textfield(
		array(
			'label'       => __( 'Last Name', 'shiro-admin' ),
			'description' => __( 'This field is required to enable correct sorting via last name.', 'shiro-admin' ),
			'name'        => 'last_name',
		)
	);

	$last_name->add_meta_box( __( 'Sorting', 'shiro-admin' ), 'profile', 'normal', 'high' );

	$contact_links = new Fieldmanager_Group(
		array(
			'label'          => __( 'Contact Link', 'shiro-admin' ),
			'name'           => 'contact_links',
			'add_more_label' => __( 'Add Contact Link', 'shiro-admin' ),
			'sortable'       => true,
			'limit'          => 0,
			'children'       => array(
				'title' => new Fieldmanager_Textfield( __( 'Link Title', 'shiro-admin' ) ),
				'link'  => new Fieldmanager_Link( __( 'Link', 'shiro-admin' ) ),
			),
		)
	);

	$contact_links->add_meta_box( __( 'List of Contact Links', 'shiro-admin' ), 'profile' );

	$info = new Fieldmanager_Group(
		array(
			'name'           => 'profile_info',
			'label'          => __( 'Profile Info', 'shiro-admin' ),
			'serialize_data' => false,
			'add_to_prefix'  => false,
			'children'       => array(
				'profile_role'     => new Fieldmanager_Textfield( __( 'Role', 'shiro-admin' ) ),
				'profile_featured' => new Fieldmanager_Checkbox( __( 'Featured?', 'shiro-admin' ) ),
			),
		)
	);
	$info->add_meta_box( __( 'Profile Info', 'shiro-admin' ), 'profile' );

	$user = new Fieldmanager_Autocomplete(
		array(
			'name'       => 'connected_user',
			'datasource' => new Fieldmanager_Datasource_Post(
				array(
					'query_args' => array(
						'post_type' => 'guest-author',
					),
				)
			),
		)
	);
	$user->add_meta_box( __( 'Connected User', 'shiro-admin' ), 'profile' );
}
add_action( 'fm_post_profile', 'wmf_profile_fields' );

/**
 * Add fields for the role taxonomy
 */
function wmf_role_fields() {
	$display_intro = new Fieldmanager_Checkbox(
		array(
			'name'        => 'display_intro',
			'description' => __( 'Should the archive for this role display an intro section? This uses the text from Appearance > Customize > Profile Pages > Profiles List Page Text', 'shiro-admin' ),
		)
	);

	$display_intro->add_term_meta_box( __( 'Display Intro?', 'shiro-admin' ), 'role' );


	$featured_term = new Fieldmanager_Checkbox(
		array(
			'name'        => 'featured_term',
			'description' => __( 'Should this role be featured at the top of the parent archive page?', 'shiro-admin' ),
		)
	);

	$featured_term->add_term_meta_box( __( 'Featured Term?', 'shiro-admin' ), 'role' );


	// Get the current term ID.
	// We don't need to validate this because we're only using it if it exists and providing a fallback if not.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotValidated
	$current_term_id = absint( $_GET['tag_ID'] ?? 0 );


	// New WP_Query for posts with this role assigned.
	$current_term_args = array(
		'post_type' => 'profile',
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		'tax_query' => array(
			array(
				'taxonomy'         => 'role',
				'terms'            => $current_term_id,
				'include_children' => false,
			),
		), // WPCS: Slow query okay.
	);
	$current_term_query = new WP_Query( $current_term_args );
	$current_term_posts = [];

	// Create array for profile selector.
	while ( $current_term_query->have_posts() ) {
		$current_term_query->the_post();
		$current_term_posts[ get_the_ID() ] = get_the_title();
	}

	$no_posts_args = empty ( $current_term_posts )
		? array(
			'attributes' => array(
				'disabled' => 'disabled',
			),
			'description' => __( 'There are no profiles assigned to this role.', 'shiro-admin' ),
		)
		: [];

	// Create select box for executive profile.
	$executive = new Fieldmanager_Select(
		wp_parse_args(
			$no_posts_args,
			array(
				'name'        => 'role_executive',
				'description' => __( 'Select a profile to feature as the department executive on the Staff & Contractors page.', 'shiro-admin' ),
				'options'     =>
					// Combine arrays without re-indexing.
					array( 0 => __( 'Please select an executive for this department', 'shiro-admin' ) )
					+ $current_term_posts,
			)
		)
	);

	$executive->add_term_meta_box( __( 'Department Executive', 'shiro-admin' ), 'role' );

	// Create checkbox group for expert profiles.
	$experts = new Fieldmanager_Checkboxes(
		wp_parse_args(
			$no_posts_args,
			array(
				'name'        => 'role_experts',
				'description' => __( 'Select multiple profiles to feature as the department experts on the Staff & Contractors page.', 'shiro-admin' ),
				'options'     => $current_term_posts,
			)
		)
	);

	$experts->add_term_meta_box( __( 'Department Experts', 'shiro-admin' ), 'role' );


	$button = new Fieldmanager_Group(
		array(
			'name'     => 'role_button',
			'label'    => __( 'Role read more link', 'shiro-admin' ),
			'children' => array(
				'link_to_archive' => new Fieldmanager_Checkbox(
					array(
						'label'       => __( 'Link to archive?', 'shiro-admin' ),
						'description' => __( 'Select this option to display a link to this role archive on the parent archive page.', 'shiro-admin' ),
					),
				),
				'text' => new Fieldmanager_Textfield( __( 'Override link text for archive link', 'shiro-admin' ) ),
				'link' => new Fieldmanager_Link( __( 'Override link URL for archive link', 'shiro-admin' ) ),
			),
		)
	);

	$button->add_term_meta_box( __( 'Output Read More Link?', 'shiro-admin' ), 'role' );
}
add_action( 'fm_term_role', 'wmf_role_fields' );

/**
 * Save User profile to author meta, so it can be filtered later.
 *
 * @param int    $meta_id    ID of meta key.
 * @param int    $post_id    ID of attached post.
 * @param string $meta_key   Meta key value, connected_user.
 * @param string $meta_value Value of Meta key.
 */
function wmf_save_user_profile( $meta_id, $post_id, $meta_key, $meta_value ) {
	if ( 'connected_user' !== $meta_key ) {
		return;
	}

	$current_user = get_metadata( 'post', $post_id, 'connected_user', true );
	if ( ! empty( $current_user ) ) {
		delete_user_meta( absint( $current_user ), 'profile_id' );
	}

	$user = get_user_by( 'ID', absint( $meta_value ) );
	if ( $user ) {
		update_user_meta( $user->ID, 'profile_id', $post_id );
	}
}
add_action( 'update_postmeta', 'wmf_save_user_profile', 10, 4 );

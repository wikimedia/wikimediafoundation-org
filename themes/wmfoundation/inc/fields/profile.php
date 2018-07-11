<?php
/**
 * Fieldmanager Fields for Profile Post Type
 *
 * @package wmfoundation
 */

/**
 * Add fields to profile post type.
 */
function wmf_profile_fields() {
	$last_name = new Fieldmanager_Textfield(
		array(
			'label'       => __( 'Last Name', 'wmfoundation' ),
			'description' => __( 'This field is required to enable correct sorting via last name.', 'wmfoundation' ),
			'name'        => 'last_name',
		)
	);

	$last_name->add_meta_box( __( 'Sorting', 'wmfoundation' ), 'profile', 'normal', 'high' );

	$contact_links = new Fieldmanager_Group(
		array(
			'label'          => __( 'Contact Link', 'wmfoundation' ),
			'name'           => 'contact_links',
			'add_more_label' => __( 'Add Contact Link', 'wmfoundation' ),
			'sortable'       => true,
			'limit'          => 0,
			'children'       => array(
				'title' => new Fieldmanager_Textfield( __( 'Link Title', 'wmfoundation' ) ),
				'link'  => new Fieldmanager_Link( __( 'Link', 'wmfoundation' ) ),
			),
		)
	);

	$contact_links->add_meta_box( __( 'List of Contact Links', 'wmfoundation' ), 'profile' );

	$info = new Fieldmanager_Group(
		array(
			'name'           => 'profile_info',
			'label'          => __( 'Profile Info', 'wmfoundation' ),
			'serialize_data' => false,
			'add_to_prefix'  => false,
			'children'       => array(
				'profile_role'     => new Fieldmanager_Textfield( __( 'Role', 'wmfoundation' ) ),
				'profile_featured' => new Fieldmanager_Checkbox( __( 'Featured?', 'wmfoundation' ) ),
			),
		)
	);
	$info->add_meta_box( __( 'Profile Info', 'wmfoundation' ), 'profile' );

	$user = new Fieldmanager_Autocomplete(
		array(
			'name'       => 'connected_user',
			'datasource' => new Fieldmanager_Datasource_User(
				array(
					'store_property' => 'ID',
				)
			),
		)
	);
	$user->add_meta_box( __( 'Connected User', 'wmfoundation' ), 'profile' );
}
add_action( 'fm_post_profile', 'wmf_profile_fields' );

/**
 * Add fields for the role taxonomy
 */
function wmf_role_fields() {
	$featured_term = new Fieldmanager_Checkbox(
		array(
			'name' => 'featured_term',
		)
	);

	$featured_term->add_term_meta_box( 'Featured Term?', 'role' );

	$button = new Fieldmanager_Group(
		array(
			'name'     => 'role_button',
			'label'    => __( 'Button', 'wmfoundation' ),
			'children' => array(
				'text' => new Fieldmanager_Textfield( __( 'Button Text', 'wmfoundation' ) ),
				'link' => new Fieldmanager_Link( __( 'Button Link', 'wmfoundation' ) ),
			),
		)
	);

	$button->add_term_meta_box( '', 'role' );
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

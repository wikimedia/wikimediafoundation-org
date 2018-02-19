<?php
/**
 * Adds profile post type
 *
 * @package wmfoundation
 */

/**
 * Registers the `profile` post type.
 */
function wmf_profile_init() {
	register_post_type(
		'profile', array(
			'labels'            => array(
				'name'                  => __( 'Profiles', 'wmfoundation' ),
				'singular_name'         => __( 'Profile', 'wmfoundation' ),
				'all_items'             => __( 'All Profiles', 'wmfoundation' ),
				'archives'              => __( 'Profile Archives', 'wmfoundation' ),
				'attributes'            => __( 'Profile Attributes', 'wmfoundation' ),
				'insert_into_item'      => __( 'Insert into Profile', 'wmfoundation' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Profile', 'wmfoundation' ),
				'featured_image'        => _x( 'Featured Image', 'profile', 'wmfoundation' ),
				'set_featured_image'    => _x( 'Set featured image', 'profile', 'wmfoundation' ),
				'remove_featured_image' => _x( 'Remove featured image', 'profile', 'wmfoundation' ),
				'use_featured_image'    => _x( 'Use as featured image', 'profile', 'wmfoundation' ),
				'filter_items_list'     => __( 'Filter Profiles list', 'wmfoundation' ),
				'items_list_navigation' => __( 'Profiles list navigation', 'wmfoundation' ),
				'items_list'            => __( 'Profiles list', 'wmfoundation' ),
				'new_item'              => __( 'New Profile', 'wmfoundation' ),
				'add_new'               => __( 'Add New', 'wmfoundation' ),
				'add_new_item'          => __( 'Add New Profile', 'wmfoundation' ),
				'edit_item'             => __( 'Edit Profile', 'wmfoundation' ),
				'view_item'             => __( 'View Profile', 'wmfoundation' ),
				'view_items'            => __( 'View Profiles', 'wmfoundation' ),
				'search_items'          => __( 'Search Profiles', 'wmfoundation' ),
				'not_found'             => __( 'No Profiles found', 'wmfoundation' ),
				'not_found_in_trash'    => __( 'No Profiles found in trash', 'wmfoundation' ),
				'parent_item_colon'     => __( 'Parent Profile:', 'wmfoundation' ),
				'menu_name'             => __( 'Profiles', 'wmfoundation' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'revisions', 'custom-fields', 'thumbnail', 'excerpt' ),
			'has_archive'       => true,
			'rewrite'           => true,
			'query_var'         => true,
			'menu_icon'         => 'dashicons-groups',
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'wmf_profile_init' );

/**
 * Sets the post updated messages for the `profile` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `profile` post type.
 */
function wmf_profile_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['profile'] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Profile updated. <a target="_blank" href="%s">View Profile</a>', 'wmfoundation' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'wmfoundation' ),
		3  => __( 'Custom field deleted.', 'wmfoundation' ),
		4  => __( 'Profile updated.', 'wmfoundation' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Profile restored to revision from %s', 'wmfoundation' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // WPCS: CSRF Input var ok.
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Profile published. <a href="%s">View Profile</a>', 'wmfoundation' ), esc_url( $permalink ) ),
		7  => __( 'Profile saved.', 'wmfoundation' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Profile submitted. <a target="_blank" href="%s">Preview Profile</a>', 'wmfoundation' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9  => sprintf(
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			__( 'Profile scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Profile</a>', 'wmfoundation' ),
			date_i18n( __( 'M j, Y @ G:i', 'wmfoundation' ), strtotime( $post->post_date ) ), esc_url( $permalink )
		),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Profile draft updated. <a target="_blank" href="%s">Preview Profile</a>', 'wmfoundation' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'wmf_profile_updated_messages' );

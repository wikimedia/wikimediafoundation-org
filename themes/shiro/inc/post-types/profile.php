<?php
/**
 * Adds profile post type
 *
 * @package shiro
 */

$wmf_profiles_name      = get_theme_mod( 'wmf_profiles_label', __( 'Profiles', 'shiro' ) );

/**
 * Registers the `profile` post type.
 */
function wmf_profile_init() {
	register_post_type(
		'profile', array(
			'labels'            => array(
				'name'                  => __( '$wmf_profiles_name', 'shiro' ),
				'singular_name'         => __( 'Profile', 'shiro' ),
				'all_items'             => __( 'All Profiles', 'shiro' ),
				'archives'              => __( 'Profile Archives', 'shiro' ),
				'attributes'            => __( 'Profile Attributes', 'shiro' ),
				'insert_into_item'      => __( 'Insert into Profile', 'shiro' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Profile', 'shiro' ),
				'featured_image'        => _x( 'Featured Image', 'profile', 'shiro' ),
				'set_featured_image'    => _x( 'Set featured image', 'profile', 'shiro' ),
				'remove_featured_image' => _x( 'Remove featured image', 'profile', 'shiro' ),
				'use_featured_image'    => _x( 'Use as featured image', 'profile', 'shiro' ),
				'filter_items_list'     => __( 'Filter Profiles list', 'shiro' ),
				'items_list_navigation' => __( 'Profiles list navigation', 'shiro' ),
				'items_list'            => __( 'Profiles list', 'shiro' ),
				'new_item'              => __( 'New Profile', 'shiro' ),
				'add_new'               => __( 'Add New', 'shiro' ),
				'add_new_item'          => __( 'Add New Profile', 'shiro' ),
				'edit_item'             => __( 'Edit Profile', 'shiro' ),
				'view_item'             => __( 'View Profile', 'shiro' ),
				'view_items'            => __( 'View Profiles', 'shiro' ),
				'search_items'          => __( 'Search Profiles', 'shiro' ),
				'not_found'             => __( 'No Profiles found', 'shiro' ),
				'not_found_in_trash'    => __( 'No Profiles found in trash', 'shiro' ),
				'parent_item_colon'     => __( 'Parent Profile:', 'shiro' ),
				'menu_name'             => __( 'Profiles', 'shiro' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'revisions', 'custom-fields', 'thumbnail', 'excerpt' ),
			'has_archive'       => false,
			'rewrite'           => array(
				'with_front' => false,
				'slug'       => __( 'profile', 'shiro' ),
			),
			'query_var'         => true,
			'menu_icon'         => 'dashicons-groups',
			'show_in_rest'      => true,
			'map_meta_cap'      => true,
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
		1  => sprintf( __( 'Profile updated. <a target="_blank" href="%s">View Profile</a>', 'shiro' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'shiro' ),
		3  => __( 'Custom field deleted.', 'shiro' ),
		4  => __( 'Profile updated.', 'shiro' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Profile restored to revision from %s', 'shiro' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // WPCS: CSRF Input var ok.
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Profile published. <a href="%s">View Profile</a>', 'shiro' ), esc_url( $permalink ) ),
		7  => __( 'Profile saved.', 'shiro' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Profile submitted. <a target="_blank" href="%s">Preview Profile</a>', 'shiro' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9  => sprintf(
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			__( 'Profile scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Profile</a>', 'shiro' ),
			date_i18n( __( 'M j, Y @ G:i', 'shiro' ), strtotime( $post->post_date ) ), esc_url( $permalink )
		),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Profile draft updated. <a target="_blank" href="%s">Preview Profile</a>', 'shiro' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'wmf_profile_updated_messages' );

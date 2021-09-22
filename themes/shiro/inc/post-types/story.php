<?php
/**
 * Adds story post type
 *
 * @package shiro
 */

$wmf_stories_name      = get_theme_mod( 'wmf_stories_label', __( 'Stories', 'shiro-admin' ) );

/**
 * Registers the `story` post type.
 */
function wmf_story_init() {
	register_post_type(
		'story', array(
			'labels'            => array(
				'name'                  => __( 'Stories', 'shiro-admin' ),
				'singular_name'         => __( 'Story', 'shiro-admin' ),
				'all_items'             => __( 'All Stories', 'shiro-admin' ),
				'archives'              => __( 'Story Archives', 'shiro-admin' ),
				'attributes'            => __( 'Story Attributes', 'shiro-admin' ),
				'insert_into_item'      => __( 'Insert into Story', 'shiro-admin' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Story', 'shiro-admin' ),
				'featured_image'        => _x( 'Featured Image', 'story', 'shiro-admin' ),
				'set_featured_image'    => _x( 'Set featured image', 'story', 'shiro-admin' ),
				'remove_featured_image' => _x( 'Remove featured image', 'story', 'shiro-admin' ),
				'use_featured_image'    => _x( 'Use as featured image', 'story', 'shiro-admin' ),
				'filter_items_list'     => __( 'Filter Stories list', 'shiro-admin' ),
				'items_list_navigation' => __( 'Stories list navigation', 'shiro-admin' ),
				'items_list'            => __( 'Stories list', 'shiro-admin' ),
				'new_item'              => __( 'New Story', 'shiro-admin' ),
				'add_new'               => __( 'Add New', 'shiro-admin' ),
				'add_new_item'          => __( 'Add New Story', 'shiro-admin' ),
				'edit_item'             => __( 'Edit Story', 'shiro-admin' ),
				'view_item'             => __( 'View Story', 'shiro-admin' ),
				'view_items'            => __( 'View Stories', 'shiro-admin' ),
				'search_items'          => __( 'Search Stories', 'shiro-admin' ),
				'not_found'             => __( 'No Stories found', 'shiro-admin' ),
				'not_found_in_trash'    => __( 'No Stories found in trash', 'shiro-admin' ),
				'parent_item_colon'     => __( 'Parent Story:', 'shiro-admin' ),
				'menu_name'             => __( 'Stories', 'shiro-admin' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'revisions', 'custom-fields', 'thumbnail', 'excerpt' ),
			'has_archive'       => false,
			'rewrite'           => array(
				'with_front' => false,
				'slug'       => __( 'story', 'shiro-admin' ),
			),
			'query_var'         => true,
			'menu_icon'         => 'dashicons-groups',
			'show_in_rest'      => true,
			'map_meta_cap'      => true,
		)
	);
}
add_action( 'init', 'wmf_story_init' );

/**
 * Sets the post updated messages for the `story` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `story` post type.
 */
function wmf_story_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['story'] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Story updated. <a target="_blank" href="%s">View Story</a>', 'shiro-admin' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'shiro-admin' ),
		3  => __( 'Custom field deleted.', 'shiro-admin' ),
		4  => __( 'Story updated.', 'shiro-admin' ),
		/* translators: %s: date and time of the revision */
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Story restored to revision from %s', 'shiro-admin' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Story published. <a href="%s">View Story</a>', 'shiro-admin' ), esc_url( $permalink ) ),
		7  => __( 'Story saved.', 'shiro-admin' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Story submitted. <a target="_blank" href="%s">Preview Story</a>', 'shiro-admin' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9  => sprintf(
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			__( 'Story scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Story</a>', 'shiro-admin' ),
			date_i18n( __( 'M j, Y @ G:i', 'shiro-admin' ), strtotime( $post->post_date ) ), esc_url( $permalink )
		),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Story draft updated. <a target="_blank" href="%s">Preview Story</a>', 'shiro-admin' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'wmf_story_updated_messages' );

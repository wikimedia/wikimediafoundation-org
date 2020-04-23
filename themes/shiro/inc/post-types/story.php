<?php
/**
 * Adds story post type
 *
 * @package shiro
 */

$wmf_stories_name      = get_theme_mod( 'wmf_stories_label', __( 'Stories', 'shiro' ) );

/**
 * Registers the `story` post type.
 */
function wmf_story_init() {
	register_post_type(
		'story', array(
			'labels'            => array(
				'name'                  => __( 'Stories', 'shiro' ),
				'singular_name'         => __( 'Story', 'shiro' ),
				'all_items'             => __( 'All Stories', 'shiro' ),
				'archives'              => __( 'Story Archives', 'shiro' ),
				'attributes'            => __( 'Story Attributes', 'shiro' ),
				'insert_into_item'      => __( 'Insert into Story', 'shiro' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Story', 'shiro' ),
				'featured_image'        => _x( 'Featured Image', 'story', 'shiro' ),
				'set_featured_image'    => _x( 'Set featured image', 'story', 'shiro' ),
				'remove_featured_image' => _x( 'Remove featured image', 'story', 'shiro' ),
				'use_featured_image'    => _x( 'Use as featured image', 'story', 'shiro' ),
				'filter_items_list'     => __( 'Filter Stories list', 'shiro' ),
				'items_list_navigation' => __( 'Stories list navigation', 'shiro' ),
				'items_list'            => __( 'Stories list', 'shiro' ),
				'new_item'              => __( 'New Story', 'shiro' ),
				'add_new'               => __( 'Add New', 'shiro' ),
				'add_new_item'          => __( 'Add New Story', 'shiro' ),
				'edit_item'             => __( 'Edit Story', 'shiro' ),
				'view_item'             => __( 'View Story', 'shiro' ),
				'view_items'            => __( 'View Stories', 'shiro' ),
				'search_items'          => __( 'Search Stories', 'shiro' ),
				'not_found'             => __( 'No Stories found', 'shiro' ),
				'not_found_in_trash'    => __( 'No Stories found in trash', 'shiro' ),
				'parent_item_colon'     => __( 'Parent Story:', 'shiro' ),
				'menu_name'             => __( 'Stories', 'shiro' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'revisions', 'custom-fields', 'thumbnail', 'excerpt' ),
			'has_archive'       => false,
			'rewrite'           => array(
				'with_front' => false,
				'slug'       => __( 'story', 'shiro' ),
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
		1  => sprintf( __( 'Story updated. <a target="_blank" href="%s">View Story</a>', 'shiro' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'shiro' ),
		3  => __( 'Custom field deleted.', 'shiro' ),
		4  => __( 'Story updated.', 'shiro' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Story restored to revision from %s', 'shiro' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // WPCS: CSRF Input var ok.
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Story published. <a href="%s">View Story</a>', 'shiro' ), esc_url( $permalink ) ),
		7  => __( 'Story saved.', 'shiro' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Story submitted. <a target="_blank" href="%s">Preview Story</a>', 'shiro' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9  => sprintf(
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			__( 'Story scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Story</a>', 'shiro' ),
			date_i18n( __( 'M j, Y @ G:i', 'shiro' ), strtotime( $post->post_date ) ), esc_url( $permalink )
		),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Story draft updated. <a target="_blank" href="%s">Preview Story</a>', 'shiro' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'wmf_story_updated_messages' );

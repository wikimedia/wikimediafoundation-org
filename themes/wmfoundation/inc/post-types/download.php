<?php
/**
 * Adds Download post type
 *
 * @package wmfoundation
 */

/**
 * Registers the `download` post type.
 */
function wmf_download_init() {
	register_post_type(
		'download', array(
			'labels'            => array(
				'name'                  => __( 'Downloads', 'wmfoundation' ),
				'singular_name'         => __( 'Download', 'wmfoundation' ),
				'all_items'             => __( 'All Downloads', 'wmfoundation' ),
				'archives'              => __( 'Download Archives', 'wmfoundation' ),
				'attributes'            => __( 'Download Attributes', 'wmfoundation' ),
				'insert_into_item'      => __( 'Insert into Download', 'wmfoundation' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Download', 'wmfoundation' ),
				'featured_image'        => _x( 'Featured Image', 'Download', 'wmfoundation' ),
				'set_featured_image'    => _x( 'Set featured image', 'Download', 'wmfoundation' ),
				'remove_featured_image' => _x( 'Remove featured image', 'Download', 'wmfoundation' ),
				'use_featured_image'    => _x( 'Use as featured image', 'Download', 'wmfoundation' ),
				'filter_items_list'     => __( 'Filter Downloads list', 'wmfoundation' ),
				'items_list_navigation' => __( 'Downloads list navigation', 'wmfoundation' ),
				'items_list'            => __( 'Downloads list', 'wmfoundation' ),
				'new_item'              => __( 'New Download', 'wmfoundation' ),
				'add_new'               => __( 'Add New', 'wmfoundation' ),
				'add_new_item'          => __( 'Add New Download', 'wmfoundation' ),
				'edit_item'             => __( 'Edit Download', 'wmfoundation' ),
				'view_item'             => __( 'View Download', 'wmfoundation' ),
				'view_items'            => __( 'View Downloads', 'wmfoundation' ),
				'search_items'          => __( 'Search Downloads', 'wmfoundation' ),
				'not_found'             => __( 'No Downloads found', 'wmfoundation' ),
				'not_found_in_trash'    => __( 'No Downloads found in trash', 'wmfoundation' ),
				'parent_item_colon'     => __( 'Parent Download:', 'wmfoundation' ),
				'menu_name'             => __( 'Downloads', 'wmfoundation' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'revisions', 'custom-fields', 'thumbnail', 'excerpt' ),
			'has_archive'       => false,
			'rewrite'           => array(
				'with_front' => false,
				'slug'       => __( 'download', 'wmfoundation' ),
			),
			'query_var'         => true,
			'menu_icon'         => 'dashicons-groups',
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'wmf_download_init' );


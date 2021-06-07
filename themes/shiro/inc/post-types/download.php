<?php
/**
 * Adds Download post type
 *
 * @package shiro
 */

/**
 * Registers the `download` post type.
 */
function wmf_download_init() {
	register_post_type(
		'download', array(
			'labels'            => array(
				'name'                  => __( 'Downloads', 'shiro-admin' ),
				'singular_name'         => __( 'Download', 'shiro-admin' ),
				'all_items'             => __( 'All Downloads', 'shiro-admin' ),
				'archives'              => __( 'Download Archives', 'shiro-admin' ),
				'attributes'            => __( 'Download Attributes', 'shiro-admin' ),
				'insert_into_item'      => __( 'Insert into Download', 'shiro-admin' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Download', 'shiro-admin' ),
				'featured_image'        => _x( 'Featured Image', 'Download', 'shiro-admin' ),
				'set_featured_image'    => _x( 'Set featured image', 'Download', 'shiro-admin' ),
				'remove_featured_image' => _x( 'Remove featured image', 'Download', 'shiro-admin' ),
				'use_featured_image'    => _x( 'Use as featured image', 'Download', 'shiro-admin' ),
				'filter_items_list'     => __( 'Filter Downloads list', 'shiro-admin' ),
				'items_list_navigation' => __( 'Downloads list navigation', 'shiro-admin' ),
				'items_list'            => __( 'Downloads list', 'shiro-admin' ),
				'new_item'              => __( 'New Download', 'shiro-admin' ),
				'add_new'               => __( 'Add New', 'shiro-admin' ),
				'add_new_item'          => __( 'Add New Download', 'shiro-admin' ),
				'edit_item'             => __( 'Edit Download', 'shiro-admin' ),
				'view_item'             => __( 'View Download', 'shiro-admin' ),
				'view_items'            => __( 'View Downloads', 'shiro-admin' ),
				'search_items'          => __( 'Search Downloads', 'shiro-admin' ),
				'not_found'             => __( 'No Downloads found', 'shiro-admin' ),
				'not_found_in_trash'    => __( 'No Downloads found in trash', 'shiro-admin' ),
				'parent_item_colon'     => __( 'Parent Download:', 'shiro-admin' ),
				'menu_name'             => __( 'Downloads', 'shiro-admin' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'revisions', 'custom-fields', 'thumbnail', 'excerpt' ),
			'has_archive'       => false,
			'rewrite'           => array(
				'with_front' => false,
				'slug'       => __( 'download', 'shiro-admin' ),
			),
			'query_var'         => true,
			'menu_icon'         => 'dashicons-groups',
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'wmf_download_init' );


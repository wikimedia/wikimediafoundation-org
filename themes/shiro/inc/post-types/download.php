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
				'name'                  => __( 'Downloads', 'shiro' ),
				'singular_name'         => __( 'Download', 'shiro' ),
				'all_items'             => __( 'All Downloads', 'shiro' ),
				'archives'              => __( 'Download Archives', 'shiro' ),
				'attributes'            => __( 'Download Attributes', 'shiro' ),
				'insert_into_item'      => __( 'Insert into Download', 'shiro' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Download', 'shiro' ),
				'featured_image'        => _x( 'Featured Image', 'Download', 'shiro' ),
				'set_featured_image'    => _x( 'Set featured image', 'Download', 'shiro' ),
				'remove_featured_image' => _x( 'Remove featured image', 'Download', 'shiro' ),
				'use_featured_image'    => _x( 'Use as featured image', 'Download', 'shiro' ),
				'filter_items_list'     => __( 'Filter Downloads list', 'shiro' ),
				'items_list_navigation' => __( 'Downloads list navigation', 'shiro' ),
				'items_list'            => __( 'Downloads list', 'shiro' ),
				'new_item'              => __( 'New Download', 'shiro' ),
				'add_new'               => __( 'Add New', 'shiro' ),
				'add_new_item'          => __( 'Add New Download', 'shiro' ),
				'edit_item'             => __( 'Edit Download', 'shiro' ),
				'view_item'             => __( 'View Download', 'shiro' ),
				'view_items'            => __( 'View Downloads', 'shiro' ),
				'search_items'          => __( 'Search Downloads', 'shiro' ),
				'not_found'             => __( 'No Downloads found', 'shiro' ),
				'not_found_in_trash'    => __( 'No Downloads found in trash', 'shiro' ),
				'parent_item_colon'     => __( 'Parent Download:', 'shiro' ),
				'menu_name'             => __( 'Downloads', 'shiro' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'revisions', 'custom-fields', 'thumbnail', 'excerpt' ),
			'has_archive'       => false,
			'rewrite'           => array(
				'with_front' => false,
				'slug'       => __( 'download', 'shiro' ),
			),
			'query_var'         => true,
			'menu_icon'         => 'dashicons-groups',
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'wmf_download_init' );


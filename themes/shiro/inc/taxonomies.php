<?php
/**
 * List of taxonomies
 *
 * @package shiro
 */

/**
 * Registers the `profile_type` taxonomy,
 * for use with 'profile'.
 */
function wmf_add_taxonomies() {
	$default_args = array(
		'hierarchical'      => true,
		'public'            => true,
		'show_in_nav_menus' => true,
		'show_ui'           => true,
		'show_admin_column' => false,
		'rewrite'           => true,
	);

	// Profiles.
	$profile_type_labels = array(
		'name'                       => __( 'Roles', 'shiro-admin' ),
		'singular_name'              => _x( 'Role', 'taxonomy general name', 'shiro-admin' ),
		'search_items'               => __( 'Search Roles', 'shiro-admin' ),
		'popular_items'              => __( 'Popular Roles', 'shiro-admin' ),
		'all_items'                  => __( 'All Roles', 'shiro-admin' ),
		'parent_item'                => __( 'Parent Role', 'shiro-admin' ),
		'parent_item_colon'          => __( 'Parent Role:', 'shiro-admin' ),
		'edit_item'                  => __( 'Edit Role', 'shiro-admin' ),
		'update_item'                => __( 'Update Role', 'shiro-admin' ),
		'view_item'                  => __( 'View Role', 'shiro-admin' ),
		'add_new_item'               => __( 'New Role', 'shiro-admin' ),
		'new_item_name'              => __( 'New Role', 'shiro-admin' ),
		'separate_items_with_commas' => __( 'Separate Roles with commas', 'shiro-admin' ),
		'add_or_remove_items'        => __( 'Add or remove Roles', 'shiro-admin' ),
		'choose_from_most_used'      => __( 'Choose from the most used Roles', 'shiro-admin' ),
		'not_found'                  => __( 'No Roles found.', 'shiro-admin' ),
		'no_terms'                   => __( 'No Roles', 'shiro-admin' ),
		'menu_name'                  => __( 'Roles', 'shiro-admin' ),
		'items_list_navigation'      => __( 'Roles list navigation', 'shiro-admin' ),
		'items_list'                 => __( 'Roles list', 'shiro-admin' ),
		'most_used'                  => _x( 'Most Used', 'profile-type', 'shiro-admin' ),
		'back_to_items'              => __( '&larr; Back to Roles', 'shiro-admin' ),
	);

	$profile_type_args = wp_parse_args(
		$default_args, array(
			'labels'  => $profile_type_labels,
			'show_in_rest' => true,
			'rewrite' => array(
				'with_front' => false,
				'slug'       => __( 'role', 'shiro-admin' ),
			),
		)
	);
	register_taxonomy( 'role', array( 'profile' ), $profile_type_args );

	// Stories.
	$story_type_labels = array(
		'name'                       => __( 'Types', 'shiro-admin' ),
		'singular_name'              => _x( 'Type', 'taxonomy general name', 'shiro-admin' ),
		'search_items'               => __( 'Search Types', 'shiro-admin' ),
		'popular_items'              => __( 'Popular Types', 'shiro-admin' ),
		'all_items'                  => __( 'All Types', 'shiro-admin' ),
		'parent_item'                => __( 'Parent Type', 'shiro-admin' ),
		'parent_item_colon'          => __( 'Parent Type:', 'shiro-admin' ),
		'edit_item'                  => __( 'Edit Type', 'shiro-admin' ),
		'update_item'                => __( 'Update Type', 'shiro-admin' ),
		'view_item'                  => __( 'View Type', 'shiro-admin' ),
		'add_new_item'               => __( 'New Type', 'shiro-admin' ),
		'new_item_name'              => __( 'New Type', 'shiro-admin' ),
		'separate_items_with_commas' => __( 'Separate Types with commas', 'shiro-admin' ),
		'add_or_remove_items'        => __( 'Add or remove Types', 'shiro-admin' ),
		'choose_from_most_used'      => __( 'Choose from the most used Types', 'shiro-admin' ),
		'not_found'                  => __( 'No Types found.', 'shiro-admin' ),
		'no_terms'                   => __( 'No Types', 'shiro-admin' ),
		'menu_name'                  => __( 'Types', 'shiro-admin' ),
		'items_list_navigation'      => __( 'Types list navigation', 'shiro-admin' ),
		'items_list'                 => __( 'Types list', 'shiro-admin' ),
		'most_used'                  => _x( 'Most Used', 'story-type', 'shiro-admin' ),
		'back_to_items'              => __( '&larr; Back to Types', 'shiro-admin' ),
	);

	$story_type_args = wp_parse_args(
		$default_args, array(
			'labels'  => $story_type_labels,
			'rewrite' => array(
				'with_front' => false,
				'slug'       => __( 'stories', 'shiro-admin' ),
			),
		)
	);
	register_taxonomy( 'type', array( 'story' ), $story_type_args );

	$translation_status_type_args = array(
		'hierarchical'      => true,
		'public'            => false,
		'rewrite'           => false,
		'show_admin_column' => ! wmf_is_main_site(),
		'label'             => __( 'Translation Status', 'shiro-admin' ),
	);
	register_taxonomy( 'translation-status', array( 'post', 'page', 'profile' ), $translation_status_type_args );
}
add_action( 'init', 'wmf_add_taxonomies' );

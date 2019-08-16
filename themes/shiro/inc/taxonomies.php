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
		'name'                       => __( 'Roles', 'shiro' ),
		'singular_name'              => _x( 'Role', 'taxonomy general name', 'shiro' ),
		'search_items'               => __( 'Search Roles', 'shiro' ),
		'popular_items'              => __( 'Popular Roles', 'shiro' ),
		'all_items'                  => __( 'All Roles', 'shiro' ),
		'parent_item'                => __( 'Parent Role', 'shiro' ),
		'parent_item_colon'          => __( 'Parent Role:', 'shiro' ),
		'edit_item'                  => __( 'Edit Role', 'shiro' ),
		'update_item'                => __( 'Update Role', 'shiro' ),
		'view_item'                  => __( 'View Role', 'shiro' ),
		'add_new_item'               => __( 'New Role', 'shiro' ),
		'new_item_name'              => __( 'New Role', 'shiro' ),
		'separate_items_with_commas' => __( 'Separate Roles with commas', 'shiro' ),
		'add_or_remove_items'        => __( 'Add or remove Roles', 'shiro' ),
		'choose_from_most_used'      => __( 'Choose from the most used Roles', 'shiro' ),
		'not_found'                  => __( 'No Roles found.', 'shiro' ),
		'no_terms'                   => __( 'No Roles', 'shiro' ),
		'menu_name'                  => __( 'Roles', 'shiro' ),
		'items_list_navigation'      => __( 'Roles list navigation', 'shiro' ),
		'items_list'                 => __( 'Roles list', 'shiro' ),
		'most_used'                  => _x( 'Most Used', 'profile-type', 'shiro' ),
		'back_to_items'              => __( '&larr; Back to Roles', 'shiro' ),
	);

	$profile_type_args = wp_parse_args(
		$default_args, array(
			'labels'  => $profile_type_labels,
			'rewrite' => array(
				'with_front' => false,
				'slug'       => __( 'role', 'shiro' ),
			),
		)
	);
	register_taxonomy( 'role', array( 'profile' ), $profile_type_args );

	// Stories.
	$story_type_labels = array(
		'name'                       => __( 'Types', 'shiro' ),
		'singular_name'              => _x( 'Type', 'taxonomy general name', 'shiro' ),
		'search_items'               => __( 'Search Types', 'shiro' ),
		'popular_items'              => __( 'Popular Types', 'shiro' ),
		'all_items'                  => __( 'All Types', 'shiro' ),
		'parent_item'                => __( 'Parent Type', 'shiro' ),
		'parent_item_colon'          => __( 'Parent Type:', 'shiro' ),
		'edit_item'                  => __( 'Edit Type', 'shiro' ),
		'update_item'                => __( 'Update Type', 'shiro' ),
		'view_item'                  => __( 'View Type', 'shiro' ),
		'add_new_item'               => __( 'New Type', 'shiro' ),
		'new_item_name'              => __( 'New Type', 'shiro' ),
		'separate_items_with_commas' => __( 'Separate Types with commas', 'shiro' ),
		'add_or_remove_items'        => __( 'Add or remove Types', 'shiro' ),
		'choose_from_most_used'      => __( 'Choose from the most used Types', 'shiro' ),
		'not_found'                  => __( 'No Types found.', 'shiro' ),
		'no_terms'                   => __( 'No Types', 'shiro' ),
		'menu_name'                  => __( 'Types', 'shiro' ),
		'items_list_navigation'      => __( 'Types list navigation', 'shiro' ),
		'items_list'                 => __( 'Types list', 'shiro' ),
		'most_used'                  => _x( 'Most Used', 'story-type', 'shiro' ),
		'back_to_items'              => __( '&larr; Back to Types', 'shiro' ),
	);

	$story_type_args = wp_parse_args(
		$default_args, array(
			'labels'  => $story_type_labels,
			'rewrite' => array(
				'with_front' => false,
				'slug'       => __( 'stories', 'shiro' ),
			),
		)
	);
	register_taxonomy( 'type', array( 'story' ), $story_type_args );

	$translation_status_type_args = array(
		'hierarchical'      => true,
		'public'            => false,
		'rewrite'           => false,
		'show_admin_column' => ! wmf_is_main_site(),
		'label'             => __( 'Translation Status', 'shiro' ),
	);
	register_taxonomy( 'translation-status', array( 'post', 'page', 'profile' ), $translation_status_type_args );
}
add_action( 'init', 'wmf_add_taxonomies' );

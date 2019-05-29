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

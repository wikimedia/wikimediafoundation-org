<?php
/**
 * List of taxonomies
 *
 * @package wmfoundation
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
		'name'                       => __( 'Roles', 'wmfoundation' ),
		'singular_name'              => _x( 'Role', 'taxonomy general name', 'wmfoundation' ),
		'search_items'               => __( 'Search Roles', 'wmfoundation' ),
		'popular_items'              => __( 'Popular Roles', 'wmfoundation' ),
		'all_items'                  => __( 'All Roles', 'wmfoundation' ),
		'parent_item'                => __( 'Parent Role', 'wmfoundation' ),
		'parent_item_colon'          => __( 'Parent Role:', 'wmfoundation' ),
		'edit_item'                  => __( 'Edit Role', 'wmfoundation' ),
		'update_item'                => __( 'Update Role', 'wmfoundation' ),
		'view_item'                  => __( 'View Role', 'wmfoundation' ),
		'add_new_item'               => __( 'New Role', 'wmfoundation' ),
		'new_item_name'              => __( 'New Role', 'wmfoundation' ),
		'separate_items_with_commas' => __( 'Separate Roles with commas', 'wmfoundation' ),
		'add_or_remove_items'        => __( 'Add or remove Roles', 'wmfoundation' ),
		'choose_from_most_used'      => __( 'Choose from the most used Roles', 'wmfoundation' ),
		'not_found'                  => __( 'No Roles found.', 'wmfoundation' ),
		'no_terms'                   => __( 'No Roles', 'wmfoundation' ),
		'menu_name'                  => __( 'Roles', 'wmfoundation' ),
		'items_list_navigation'      => __( 'Roles list navigation', 'wmfoundation' ),
		'items_list'                 => __( 'Roles list', 'wmfoundation' ),
		'most_used'                  => _x( 'Most Used', 'profile-type', 'wmfoundation' ),
		'back_to_items'              => __( '&larr; Back to Roles', 'wmfoundation' ),
	);

	$profile_type_args = wp_parse_args(
		$default_args, array(
			'labels' => $profile_type_labels,
		)
	);
	register_taxonomy( 'role', array( 'profile' ), $profile_type_args );

	$translation_status_type_args = array(
		'hierarchical'      => true,
		'public'            => false,
		'rewrite'           => false,
		'show_admin_column' => ! wmf_is_main_site(),
		'label'             => __( 'Translation Status', 'wmfoundation' ),
	);
	register_taxonomy( 'translation-status', array( 'post', 'page', 'profile' ), $translation_status_type_args );
}
add_action( 'init', 'wmf_add_taxonomies' );

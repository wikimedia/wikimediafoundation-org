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
function profile_type_init() {

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
		'name'                       => __( 'Profile Types', 'wmfoundation' ),
		'singular_name'              => _x( 'Profile Type', 'taxonomy general name', 'wmfoundation' ),
		'search_items'               => __( 'Search Profile Types', 'wmfoundation' ),
		'popular_items'              => __( 'Popular Profile Types', 'wmfoundation' ),
		'all_items'                  => __( 'All Profile Types', 'wmfoundation' ),
		'parent_item'                => __( 'Parent Profile Type', 'wmfoundation' ),
		'parent_item_colon'          => __( 'Parent Profile Type:', 'wmfoundation' ),
		'edit_item'                  => __( 'Edit Profile Type', 'wmfoundation' ),
		'update_item'                => __( 'Update Profile Type', 'wmfoundation' ),
		'view_item'                  => __( 'View Profile Type', 'wmfoundation' ),
		'add_new_item'               => __( 'New Profile Type', 'wmfoundation' ),
		'new_item_name'              => __( 'New Profile Type', 'wmfoundation' ),
		'separate_items_with_commas' => __( 'Separate Profile Types with commas', 'wmfoundation' ),
		'add_or_remove_items'        => __( 'Add or remove Profile Types', 'wmfoundation' ),
		'choose_from_most_used'      => __( 'Choose from the most used Profile Types', 'wmfoundation' ),
		'not_found'                  => __( 'No Profile Types found.', 'wmfoundation' ),
		'no_terms'                   => __( 'No Profile Types', 'wmfoundation' ),
		'menu_name'                  => __( 'Profile Types', 'wmfoundation' ),
		'items_list_navigation'      => __( 'Profile Types list navigation', 'wmfoundation' ),
		'items_list'                 => __( 'Profile Types list', 'wmfoundation' ),
		'most_used'                  => _x( 'Most Used', 'profile-type', 'wmfoundation' ),
		'back_to_items'              => __( '&larr; Back to Profile Types', 'wmfoundation' ),
	);

	$profile_type_args = wp_parse_args(
		$default_args, array(
			'labels' => $profile_type_labels,
		)
	);
	register_taxonomy( 'profile-type', array( 'profile' ), $profile_type_args );

	// Teams.
	$team_labels = array(
		'name'                       => __( 'Teams', 'wmfoundation' ),
		'singular_name'              => _x( 'Team', 'taxonomy general name', 'wmfoundation' ),
		'search_items'               => __( 'Search Teams', 'wmfoundation' ),
		'popular_items'              => __( 'Popular Teams', 'wmfoundation' ),
		'all_items'                  => __( 'All Teams', 'wmfoundation' ),
		'parent_item'                => __( 'Parent Team', 'wmfoundation' ),
		'parent_item_colon'          => __( 'Parent Team:', 'wmfoundation' ),
		'edit_item'                  => __( 'Edit Team', 'wmfoundation' ),
		'update_item'                => __( 'Update Team', 'wmfoundation' ),
		'view_item'                  => __( 'View Team', 'wmfoundation' ),
		'add_new_item'               => __( 'New Team', 'wmfoundation' ),
		'new_item_name'              => __( 'New Team', 'wmfoundation' ),
		'separate_items_with_commas' => __( 'Separate Teams with commas', 'wmfoundation' ),
		'add_or_remove_items'        => __( 'Add or remove Teams', 'wmfoundation' ),
		'choose_from_most_used'      => __( 'Choose from the most used Teams', 'wmfoundation' ),
		'not_found'                  => __( 'No Teams found.', 'wmfoundation' ),
		'no_terms'                   => __( 'No Teams', 'wmfoundation' ),
		'menu_name'                  => __( 'Teams', 'wmfoundation' ),
		'items_list_navigation'      => __( 'Teams list navigation', 'wmfoundation' ),
		'items_list'                 => __( 'Teams list', 'wmfoundation' ),
		'most_used'                  => _x( 'Most Used', 'team', 'wmfoundation' ),
		'back_to_items'              => __( '&larr; Back to Teams', 'wmfoundation' ),
	);

	$team_args = wp_parse_args(
		$default_args, array(
			'labels' => $team_labels,
		)
	);
	register_taxonomy( 'team', array( 'profile' ), $team_args );

}
add_action( 'init', 'profile_type_init' );

<?php
/**
 * Fieldmanager Fields for Profile Post Type
 *
 * @package wmfoundation
 */

/**
 * Add fields to profile post type.
 */
function wmf_profile_fields() {
	$contact_links = new Fieldmanager_Group(
		array(
			'label'          => __( 'Contact Link', 'wmfoundation' ),
			'name'           => 'contact_links',
			'add_more_label' => __( 'Add Contact Link', 'wmfoundation' ),
			'sortable'       => true,
			'limit'          => 0,
			'children'       => array(
				'title' => new Fieldmanager_Textfield( __( 'Link Title', 'wmfoundation' ) ),
				'link'  => new Fieldmanager_Link( __( 'Link', 'wmfoundation' ) ),
			),
		)
	);

	$contact_links->add_meta_box( __( 'List of Contact Links', 'wmfoundation' ), 'profile' );

	$info = new Fieldmanager_Textfield(
		array(
			'name'  => 'profile_role',
			'label' => __( 'Role', 'wmfoundation' ),
		)
	);
	$info->add_meta_box( __( 'Profile Info', 'wmfoundation' ), 'profile' );
}
add_action( 'fm_post_profile', 'wmf_profile_fields' );

/**
 * Add fields for the role taxonomy
 */
function wmf_role_fields() {
	$featured_term = new Fieldmanager_Checkbox(
		array(
			'name' => 'featured_term',
		)
	);

	$featured_term->add_term_meta_box( 'Featured Term?', 'role' );

	$button = new Fieldmanager_Group(
		array(
			'name'     => 'role_button',
			'label'    => __( 'Button', 'wmfoundation' ),
			'children' => array(
				'text' => new Fieldmanager_Textfield( __( 'Button Text', 'wmfoundation' ) ),
				'link' => new Fieldmanager_Link( __( 'Button Link', 'wmfoundation' ) ),
			),
		)
	);

	$button->add_term_meta_box( '', 'role' );
}
add_action( 'fm_term_role', 'wmf_role_fields' );

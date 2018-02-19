<?php
/**
 * Fieldmanager Fields for Profile Post Type
 *
 * @package wmfoundation
 */

/**
 * Add fields to profile post type.
 */
function wmfoundation_profile_fields() {
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
add_action( 'fm_post_profile', 'wmfoundation_profile_fields' );

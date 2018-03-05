<?php
/**
 * Fieldmanager Fields for Default page teplates
 *
 * @package wmfoundation
 */

/**
 * Add default page options.
 */
function wmf_default_fields() {
	if ( ! wmf_using_template( 'default' ) ) {
		return;
	}

	$facts = new Fieldmanager_Group(
		array(
			'name'     => 'sidebar_facts',
			'children' => array(
				'callout' => new Fieldmanager_Textfield( __( 'Fact Callout', 'wmfoundation' ) ),
				'caption' => new Fieldmanager_Textfield( __( 'Fact Caption', 'wmfoundation' ) ),
			),
		)
	);
	$facts->add_meta_box( __( 'Sidebar Fact', 'wmfoundation' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_default_fields' );

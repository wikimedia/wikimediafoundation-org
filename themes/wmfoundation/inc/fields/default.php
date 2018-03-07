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

	$downloads = new Fieldmanager_Group(
		array(
			'name'           => 'sidebar_downloads',
			'description'    => __( 'If a file is uploaded, it will be used for a download. Otherwise, an external link can be used', 'wmfoundation' ),
			'limit'          => 0,
			'add_more_label' => __( 'Add Another Download', 'wmfoundation' ),
			'children'       => array(
				'title' => new Fieldmanager_Textfield( __( 'Download Title', 'wmfoundation' ) ),
				'file'  => new Fieldmanager_Media(
					array(
						'label' => __( 'Download File', 'wmfoundation' ),
					)
				),
				'link'  => new Fieldmanager_Link( __( 'Download Link', 'wmfoundation' ) ),
			),
		)
	);
	$downloads->add_meta_box( __( 'Downloads', 'wmfoundation' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_default_fields' );

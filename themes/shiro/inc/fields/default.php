<?php
/**
 * Fieldmanager Fields for Default page teplates
 *
 * @package shiro
 */

/**
 * Add default page options.
 */
function wmf_default_fields() {
	if ( ! wmf_using_template( 'default' ) || (int) get_option( 'page_on_front' ) === (int) wmf_get_fields_post_id() ) {
		return;
	}

	$facts = new Fieldmanager_Group(
		array(
			'name'     => 'sidebar_facts',
			'children' => array(
				'callout' => new Fieldmanager_Textfield( __( 'Fact Callout', 'shiro' ) ),
				'caption' => new Fieldmanager_Textfield( __( 'Fact Caption', 'shiro' ) ),
			),
		)
	);
	$facts->add_meta_box( __( 'Sidebar Fact', 'shiro' ), 'page' );

	$downloads = new Fieldmanager_Group(
		array(
			'name'           => 'sidebar_downloads',
			'description'    => __( 'If a file is uploaded, it will be used for a download. Otherwise, an external link can be used', 'shiro' ),
			'limit'          => 0,
			'add_more_label' => __( 'Add Another Download', 'shiro' ),
			'children'       => array(
				'title' => new Fieldmanager_Textfield( __( 'Download Title', 'shiro' ) ),
				'file'  => new Fieldmanager_Media(
					array(
						'label' => __( 'Download File', 'shiro' ),
					)
				),
				'link'  => new Fieldmanager_Link( __( 'Download Link', 'shiro' ) ),
			),
		)
	);
	$downloads->add_meta_box( __( 'Downloads', 'shiro' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_default_fields' );

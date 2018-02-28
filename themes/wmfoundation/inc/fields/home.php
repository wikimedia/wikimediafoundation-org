<?php
/**
 * Fieldmanager Fields for Landing page template
 *
 * @package wmfoundation
 */

/**
 * Add home page options.
 */
function wmf_home_fields() {
	if ( (int) get_option( 'page_on_front' ) !== (int) wmf_get_fields_post_id() ) {
		return;
	}

	$focus_blocks = new Fieldmanager_Group(
		array(
			'name'           => 'focus_blocks',
			'add_more_label' => __( 'Add Block', 'wmfoundation' ),
			'sortable'       => true,
			'limit'          => 0,
			'children'       => array(
				'image'     => new Fieldmanager_Media( __( 'Background Image', 'wmfoundation' ) ),
				'heading'   => new Fieldmanager_Textfield( __( 'Heading', 'wmfoundation' ) ),
				'content'   => new Fieldmanager_TextArea( __( 'Content', 'wmfoundation' ) ),
				'link_uri'  => new Fieldmanager_Link( __( 'Link URI', 'wmfoundation' ) ),
				'link_text' => new Fieldmanager_Textfield( __( 'Link Text', 'wmfoundation' ) ),
			),
		)
	);
	$focus_blocks->add_meta_box( __( 'Focus Blocks', 'wmfoundation' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_home_fields' );

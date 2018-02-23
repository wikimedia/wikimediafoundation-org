<?php
/**
 * Fieldmanager Fields for Connect Module
 *
 * @package wmfoundation
 */

/**
 * Add connect page options.
 */
function wmf_connect_fields() {
	$connect = new Fieldmanager_Group(
		array(
			'name'     => 'connect',
			'children' => array(
				'hide'                  => new Fieldmanager_Checkbox( __( 'Hide Connect Module', 'wmfoundation' ) ),

				// Headings.
				'pre_heading'           => new Fieldmanager_Textfield( __( 'Section Pre Heading', 'wmfoundation' ) ),
				'heading'               => new Fieldmanager_Textfield( __( 'Section Heading', 'wmfoundation' ) ),

				// Subscribe Box.
				'subscribe_heading'     => new Fieldmanager_Textfield( __( 'Subscribe Heading', 'wmfoundation' ) ),
				'subscribe_content'     => new Fieldmanager_RichTextArea( __( 'Subscribe Content', 'wmfoundation' ) ),
				'subscribe_placeholder' => new Fieldmanager_Textfield( __( 'Email Input Placeholder', 'wmfoundation' ) ),
				'subscribe_button'      => new Fieldmanager_Textfield( __( 'Subscribe Button Text', 'wmfoundation' ) ),

				// Contact box.
				'contact_heading'       => new Fieldmanager_Textfield( __( 'Contact Heading', 'wmfoundation' ) ),
				'contact_content'       => new Fieldmanager_RichTextArea( __( 'Contact Content', 'wmfoundation' ) ),
				'contact_link'          => new Fieldmanager_Textfield( __( 'Contact Link', 'wmfoundation' ) ),
				'contact_link_text'     => new Fieldmanager_Textfield( __( 'Contact Link Text', 'wmfoundation' ) ),

			),
		)
	);
	$connect->add_meta_box( __( 'Connect', 'wmfoundation' ), array( 'page', 'post', 'profile' ) );
}
add_action( 'fm_post_post', 'wmf_connect_fields' );
add_action( 'fm_post_page', 'wmf_connect_fields' );
add_action( 'fm_post_profile', 'wmf_connect_fields' );

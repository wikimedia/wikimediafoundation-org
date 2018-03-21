<?php
/**
 * Fieldmanager Fields for Page Specific CTA
 *
 * @package wmfoundation
 */

/**
 * Add page_cta page options.
 */
function wmf_page_cta_fields() {
	$cta = new Fieldmanager_Group(
		array(
			'name'     => 'page_cta',
			'children' => array(
				'image'            => new Fieldmanager_Media( __( 'Image', 'wmfoundation' ) ),
				'heading'          => new Fieldmanager_Textfield( __( 'Section Heading', 'wmfoundation' ) ),
				'content'          => new Fieldmanager_RichTextArea( __( 'Content', 'wmfoundation' ) ),
				'link_uri'         => new Fieldmanager_Link( __( 'Share URI', 'wmfoundation' ) ),
				'link_text'        => new Fieldmanager_Textfield( __( 'Message', 'wmfoundation' ) ),
				'background_color' => new Fieldmanager_Radios(
					array(
						'label'         => __( 'Color', 'wmfoundation' ),
						'default_value' => 'blue',
						'options'       => array(
							'blue'  => __( 'Blue', 'wmfoundation' ),
							'green' => __( 'Green', 'wmfoundation' ),
						),
					)
				),
			),
		)
	);
	$cta->add_meta_box( __( 'Page CTA', 'wmfoundation' ), array( 'page', 'post' ) );
}
add_action( 'fm_post_post', 'wmf_page_cta_fields' );
add_action( 'fm_post_page', 'wmf_page_cta_fields' );

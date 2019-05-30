<?php
/**
 * Fieldmanager Fields for Page Specific CTA
 *
 * @package shiro
 */

/**
 * Add page_cta page options.
 */
function wmf_page_cta_fields() {
	$cta = new Fieldmanager_Group(
		array(
			'name'     => 'page_cta',
			'children' => array(
				'image'            => new Fieldmanager_Media( __( 'Image', 'shiro' ) ),
				'heading'          => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro' ) ),
				'content'          => new Fieldmanager_RichTextArea( __( 'Content', 'shiro' ) ),
				'link_uri'         => new Fieldmanager_Link( __( 'Share URI', 'shiro' ) ),
				'link_text'        => new Fieldmanager_Textfield( __( 'Message', 'shiro' ) ),
				'background_color' => new Fieldmanager_Radios(
					array(
						'label'         => __( 'Color', 'shiro' ),
						'default_value' => 'blue',
						'options'       => array(
							'blue'  => __( 'Blue', 'shiro' ),
							'green' => __( 'Green', 'shiro' ),
						),
					)
				),
			),
		)
	);
	$cta->add_meta_box( __( 'Page CTA', 'shiro' ), array( 'page', 'post' ) );
}
add_action( 'fm_post_post', 'wmf_page_cta_fields' );
add_action( 'fm_post_page', 'wmf_page_cta_fields' );

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
				'image'            => new Fieldmanager_Media( __( 'Image', 'shiro-admin' ) ),
				'heading'          => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro-admin' ) ),
				'content'          => new Fieldmanager_RichTextArea( __( 'Content', 'shiro-admin' ) ),
				'link_uri'         => new Fieldmanager_Link( __( 'Share URI', 'shiro-admin' ) ),
				'link_text'        => new Fieldmanager_Textfield( __( 'Message', 'shiro-admin' ) ),
				'background_color' => new Fieldmanager_Radios(
					array(
						'label'         => __( 'Color', 'shiro-admin' ),
						'default_value' => 'blue',
						'options'       => array(
							'blue'  => __( 'Blue', 'shiro-admin' ),
							'green' => __( 'Green', 'shiro-admin' ),
						),
					)
				),
			),
		)
	);
	$cta->add_meta_box( __( 'Page CTA', 'shiro-admin' ), array( 'page', 'post' ) );
}
add_action( 'fm_post_post', 'wmf_page_cta_fields' );
add_action( 'fm_post_page', 'wmf_page_cta_fields' );

<?php
/**
 * Fieldmanager Fields for Off Site Links Module
 *
 * @package shiro
 */

/**
 * Add links page options.
 */
function wmf_links_fields() {
	if ( wmf_using_template( 'page-list' ) ) {
		return;
	}

	$links = new Fieldmanager_Group(
		array(
			'name'     => 'off_site_links',
			'children' => array(
				'heading' => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro-admin' ) ),
				'links'   => new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Link', 'shiro-admin' ),
						'sortable'       => true,
						'limit'          => 0,
						'children'       => array(
							'heading' => new Fieldmanager_Textfield( __( 'Heading', 'shiro-admin' ) ),
							'uri'     => new Fieldmanager_Link( __( 'URI', 'shiro-admin' ) ),
							'content' => new Fieldmanager_RichTextArea(
								array(
									'label'           => __( 'Content', 'shiro-admin' ),
									'buttons_1'       => array( 'bold', 'italic', 'strikethrough', 'underline' ),
									'buttons_2'       => array(),
									'editor_settings' => array(
										'quicktags'     => false,
										'media_buttons' => false,
									),
								)
							),
						),
					)
				),

			),
		)
	);
	$links->add_meta_box( __( 'Off Site Links', 'shiro-admin' ), array( 'page', 'post', 'profile' ) );
}
add_action( 'fm_post_post', 'wmf_links_fields' );
add_action( 'fm_post_page', 'wmf_links_fields' );
add_action( 'fm_post_profile', 'wmf_links_fields' );

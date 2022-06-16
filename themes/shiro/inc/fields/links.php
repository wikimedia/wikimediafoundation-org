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
		[
			'name'     => 'off_site_links',
			'children' => [
				'heading' => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro-admin' ) ),
				'links'   => new Fieldmanager_Group(
					[
						'add_more_label' => __( 'Add Link', 'shiro-admin' ),
						'sortable'       => true,
						'limit'          => 0,
						'children'       => [
							'heading' => new Fieldmanager_Textfield( __( 'Heading', 'shiro-admin' ) ),
							'uri'     => new Fieldmanager_Link( __( 'URI', 'shiro-admin' ) ),
							'content' => new Fieldmanager_RichTextArea(
								[
									'label'           => __( 'Content', 'shiro-admin' ),
									'buttons_1'       => [ 'bold', 'italic', 'strikethrough', 'underline' ],
									'buttons_2'       => [],
									'editor_settings' => [
										'quicktags'     => false,
										'media_buttons' => false,
									],
								]
							),
						],
					]
				),

			],
		]
	);
	$links->add_meta_box( __( 'Off Site Links', 'shiro-admin' ), [ 'page', 'post' ] );
}
add_action( 'fm_post_post', 'wmf_links_fields' );
add_action( 'fm_post_page', 'wmf_links_fields' );

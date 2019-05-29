<?php
/**
 * Fieldmanager Fields for Landing page template
 *
 * @package shiro
 */

/**
 * Add landing page options.
 */
function wmf_landing_fields() {
	$is_landing_page = wmf_using_template( 'page-landing' );
	$is_home         = (int) get_option( 'page_on_front' ) === (int) wmf_get_fields_post_id();

	if ( $is_landing_page ) {
		$social = new Fieldmanager_Group(
			array(
				'name'     => 'social_share',
				'children' => array(
					'heading'  => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro' ) ),
					'uri'      => new Fieldmanager_Link( __( 'Share URI', 'shiro' ) ),
					'message'  => new Fieldmanager_Textfield( __( 'Message', 'shiro' ) ),
					'services' => new Fieldmanager_Checkboxes(
						array(
							'label'   => __( 'Services', 'shiro' ),
							'options' => array(
								'twitter'  => __( 'Twitter', 'shiro' ),
								'facebook' => __( 'Facebook', 'shiro' ),
							),
						)
					),
				),
			)
		);
		$social->add_meta_box( __( 'Social Share', 'shiro' ), 'page' );
	}

	if ( $is_landing_page || $is_home ) {
		$framing_copy = new Fieldmanager_Group(
			array(
				'name'     => 'framing_copy',
				'children' => array(
					'pre_heading' => new Fieldmanager_Textfield( __( 'Section Pre-heading', 'shiro' ) ),
					'heading'     => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro' ) ),
					'copy'        => new Fieldmanager_Group(
						array(
							'add_more_label' => __( 'Add Framing Copy', 'shiro' ),
							'sortable'       => true,
							'limit'          => 0,
							'children'       => array(
								'image'     => new Fieldmanager_Media( __( 'Image', 'shiro' ) ),
								'heading'   => new Fieldmanager_Textfield( __( 'Copy Heading', 'shiro' ) ),
								'copy'      => new Fieldmanager_RichTextArea( __( 'Content', 'shiro' ) ),
								'link_url'  => new Fieldmanager_Link( __( 'Link URI', 'shiro' ) ),
								'link_text' => new Fieldmanager_Textfield( __( 'Link Text', 'shiro' ) ),
								'links'     => new Fieldmanager_Group(
									array(
										'add_more_label' => __( 'Add Link', 'shiro' ),
										'limit'          => 2,
										'children'       => array(
											'link_url'  => new Fieldmanager_Link( __( 'Link URI', 'shiro' ) ),
											'link_text' => new Fieldmanager_Textfield( __( 'Link Text', 'shiro' ) ),
										),
									)
								),
							),
						)
					),
				),
			)
		);
		$framing_copy->add_meta_box( __( 'Framing Copy', 'shiro' ), 'page' );
	}

	$facts = new Fieldmanager_Group(
		array(
			'name'     => 'page_facts',
			'children' => array(
				'image' => new Fieldmanager_Media(
					array(
						'label'       => __( 'Background Image*', 'shiro' ),
						'description' => __( '*This is a required element for the facts to show properly.', 'shiro' ),
					)
				),
				'facts' => new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Fact', 'shiro' ),
						'sortable'       => true,
						'limit'          => 3,
						'children'       => array(
							'heading' => new Fieldmanager_Textfield( __( 'Heading', 'shiro' ) ),
							'content' => new Fieldmanager_Textfield( __( 'Content', 'shiro' ) ),
						),
					)
				),
			),
		)
	);
	$facts->add_meta_box( __( 'Facts', 'shiro' ), 'page' );

	$featured_post = new Fieldmanager_Textfield(
		array(
			'name' => 'featured_post_sub_title',
		)
	);
	$featured_post->add_meta_box( __( 'Featured Post Subtitle', 'shiro' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_landing_fields' );

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
	$is_landing_page = ( wmf_using_template( 'page-landing'  ) || wmf_using_template( 'page-report-landing-long' ) );
	$is_home         = (int) get_option( 'page_on_front' ) === (int) wmf_get_fields_post_id();

	if ( $is_landing_page ) {
		$social = new Fieldmanager_Group(
			array(
				'name'     => 'social_share',
				'children' => array(
					'heading'  => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro-admin' ) ),
					'uri'      => new Fieldmanager_Link( __( 'Share URI', 'shiro-admin' ) ),
					'message'  => new Fieldmanager_Textfield( __( 'Message', 'shiro-admin' ) ),
					'services' => new Fieldmanager_Checkboxes(
						array(
							'label'   => __( 'Services', 'shiro-admin' ),
							'options' => array(
								'twitter'  => __( 'Twitter', 'shiro-admin' ),
								'facebook' => __( 'Facebook', 'shiro-admin' ),
							),
						)
					),
				),
			)
		);
		$social->add_meta_box( __( 'Social Share', 'shiro-admin' ), 'page' );
	}

	if ( $is_landing_page || $is_home ) {
		$framing_copy = new Fieldmanager_Group(
			array(
				'name'     => 'framing_copy',
				'children' => array(
					'pre_heading' => new Fieldmanager_Textfield( __( 'Section Pre-heading', 'shiro-admin' ) ),
					'heading'     => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro-admin' ) ),
					'copy'        => new Fieldmanager_Group(
						array(
							'add_more_label' => __( 'Add Framing Copy', 'shiro-admin' ),
							'sortable'       => true,
							'limit'          => 0,
							'children'       => array(
								'image'     => new Fieldmanager_Media( __( 'Image', 'shiro-admin' ) ),
								'heading'   => new Fieldmanager_Textfield( __( 'Copy Heading', 'shiro-admin' ) ),
								'copy'      => new Fieldmanager_RichTextArea( __( 'Content', 'shiro-admin' ) ),
								'link_url'  => new Fieldmanager_Link( __( 'Link URI', 'shiro-admin' ) ),
								'link_text' => new Fieldmanager_Textfield( __( 'Link Text', 'shiro-admin' ) ),
								'links'     => new Fieldmanager_Group(
									array(
										'add_more_label' => __( 'Add Link', 'shiro-admin' ),
										'limit'          => 2,
										'children'       => array(
											'link_url'  => new Fieldmanager_Link( __( 'Link URI', 'shiro-admin' ) ),
											'link_text' => new Fieldmanager_Textfield( __( 'Link Text', 'shiro-admin' ) ),
										),
									)
								),
							),
						)
					),
				),
			)
		);
		$framing_copy->add_meta_box( __( 'Framing Copy', 'shiro-admin' ), 'page' );
	}

	$facts = new Fieldmanager_Group(
		array(
			'name'     => 'page_facts',
			'children' => array(
				'image' => new Fieldmanager_Media(
					array(
						'label'       => __( 'Background Image*', 'shiro-admin' ),
						'description' => __( '*This is a required element for the facts to show properly.', 'shiro-admin' ),
					)
				),
				'facts' => new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Fact', 'shiro-admin' ),
						'sortable'       => true,
						'limit'          => 3,
						'children'       => array(
							'heading' => new Fieldmanager_Textfield( __( 'Heading', 'shiro-admin' ) ),
							'content' => new Fieldmanager_Textfield( __( 'Content', 'shiro-admin' ) ),
						),
					)
				),
			),
		)
	);
	$facts->add_meta_box( __( 'Facts', 'shiro-admin' ), 'page' );

	$featured_post = new Fieldmanager_Textfield(
		array(
			'name' => 'featured_post_sub_title',
		)
	);
	$featured_post->add_meta_box( __( 'Featured Post Subtitle', 'shiro-admin' ), 'page' );

	if ( wmf_using_template( 'page-report-landing' ) ) {
		$sidebar_menu_label = new Fieldmanager_Textfield(
			array(
				'name'     => 'landing_page_sidebar_menu_label',
				'sanitize' => 'wp_kses_post'
			)
		);
		$sidebar_menu_label->add_meta_box( __( 'Sidebar Menu Label', 'shiro-admin' ), 'page' );
	}
}
add_action( 'fm_post_page', 'wmf_landing_fields' );

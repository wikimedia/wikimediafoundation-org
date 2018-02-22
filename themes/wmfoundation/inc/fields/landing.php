<?php
/**
 * Fieldmanager Fields for Landing page template
 *
 * @package wmfoundation
 */

/**
 * Add landing page options.
 */
function wmf_landing_fields() {
	if ( ! wmf_using_template( 'page-landing' ) ) {
		return;
	}

	$header_opts = new Fieldmanager_Textfield(
		array(
			'name' => 'sub_title',
		)
	);
	$header_opts->add_meta_box( __( 'Subtitle', 'wmfoundation' ), 'page' );

	$intro = new Fieldmanager_Textarea(
		array(
			'name' => 'page_intro',
		)
	);
	$intro->add_meta_box( __( 'Page Intro', 'wmfoundation' ), 'page' );

	$social = new Fieldmanager_Group(
		array(
			'name'     => 'social_share',
			'children' => array(
				'heading'  => new Fieldmanager_Textfield( __( 'Section Heading', 'wmfoundation' ) ),
				'uri'      => new Fieldmanager_Link( __( 'Share URI', 'wmfoundation' ) ),
				'message'  => new Fieldmanager_Textfield( __( 'Message', 'wmfoundation' ) ),
				'services' => new Fieldmanager_Checkboxes(
					array(
						'label'   => __( 'Services', 'wmfoundation' ),
						'options' => array(
							'twitter'  => __( 'Twitter', 'wmfoundation' ),
							'facebook' => __( 'Facebook', 'wmfoundation' ),
						),
					)
				),
			),
		)
	);
	$social->add_meta_box( __( 'Social Share', 'wmfoundation' ), 'page' );

	$framing_copy = new Fieldmanager_Group(
		array(
			'name'     => 'framing_copy',
			'children' => array(
				'pre_heading' => new Fieldmanager_Textfield( __( 'Section Pre-heading', 'wmfoundation' ) ),
				'heading'     => new Fieldmanager_Textfield( __( 'Section Heading', 'wmfoundation' ) ),
				'copy'        => new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Framing Copy', 'wmfoundation' ),
						'sortable'       => true,
						'limit'          => 0,
						'children'       => array(
							'image'     => new Fieldmanager_Media( __( 'Image', 'wmfoundation' ) ),
							'heading'   => new Fieldmanager_Textfield( __( 'Copy Heading', 'wmfoundation' ) ),
							'copy'      => new Fieldmanager_RichTextArea( __( 'Content', 'wmfoundation' ) ),
							'link_url'  => new Fieldmanager_Link( __( 'Link URI', 'wmfoundation' ) ),
							'link_text' => new Fieldmanager_Textfield( __( 'Link Text', 'wmfoundation' ) ),
						),
					)
				),
			),
		)
	);
	$framing_copy->add_meta_box( __( 'Framing Copy', 'wmfoundation' ), 'page' );

	$facts = new Fieldmanager_Group(
		array(
			'name'           => 'page_facts',
			'add_more_label' => __( 'Add Fact', 'wmfoundation' ),
			'sortable'       => true,
			'limit'          => 3,
			'children' => array(
				'image'    => new Fieldmanager_Media( __( 'Background Image', 'wmfoundation' ) ),
				'heading'  => new Fieldmanager_Textfield( __( 'Heading', 'wmfoundation' ) ),
				'content'  => new Fieldmanager_Textfield( __( 'Content', 'wmfoundation' ) ),
			),
		)
	);
	$facts->add_meta_box( __( 'Facts', 'wmfoundation' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_landing_fields' );

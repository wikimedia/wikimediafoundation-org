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

	$projects = new Fieldmanager_Group(
		array(
			'name'     => 'projects_module',
			'children' => array(
				'pre_heading' => new Fieldmanager_Textfield( __( 'Section Pre-Heading', 'wmfoundation' ) ),
				'heading'     => new Fieldmanager_Textfield( __( 'Section Heading', 'wmfoundation' ) ),
				'content'     => new Fieldmanager_RichTextArea(
					array(
						'label'           => __( 'Section Content', 'wmfoundation' ),
						'buttons_1'       => array( 'bold', 'italic', 'strikethrough', 'underline' ),
						'buttons_2'       => array(),
						'editor_settings' => array(
							'quicktags'     => false,
							'media_buttons' => false,
						),
					)
				),
				'link_uri'    => new Fieldmanager_Link( __( 'Section Link URI', 'wmfoundation' ) ),
				'link_text'   => new Fieldmanager_Textfield( __( 'Section Link Text', 'wmfoundation' ) ),
				'projects'    => new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Project', 'wmfoundation' ),
						'sortable'       => true,
						'limit'          => 2,
						'children'       => array(
							'link_uri' => new Fieldmanager_Link( __( 'Project URI', 'wmfoundation' ) ),
							'bg_image' => new Fieldmanager_Media( __( 'Background Image', 'wmfoundation' ) ),
							'image'    => new Fieldmanager_Media( __( 'Project Icon', 'wmfoundation' ) ),
							'heading'  => new Fieldmanager_Textfield( __( 'Project Name', 'wmfoundation' ) ),
							'content'  => new Fieldmanager_TextArea( __( 'Project Description', 'wmfoundation' ) ),
						),
					)
				),

			),
		)
	);
	$projects->add_meta_box( __( 'Projects', 'wmfoundation' ), array( 'page' ) );
}
add_action( 'fm_post_page', 'wmf_home_fields' );

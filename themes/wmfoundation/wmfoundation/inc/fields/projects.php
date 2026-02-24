<?php
/**
 * Fieldmanager Fields for projects module
 *
 * @package wmfoundation
 */

/**
 * Add project page options.
 */
function wmf_project_fields() {
	if ( (int) get_option( 'page_on_front' ) !== (int) wmf_get_fields_post_id() && ! wmf_using_template( 'page-landing' ) ) {
		return;
	}

	$projects = new Fieldmanager_Group(
		array(
			'name'     => 'projects_module',
			'children' => array(
				'heading'   => new Fieldmanager_Textfield( __( 'Section Heading', 'wmfoundation' ) ),
				'content'   => new Fieldmanager_RichTextArea(
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
				'link_uri'  => new Fieldmanager_Link( __( 'Section Link URI', 'wmfoundation' ) ),
				'link_text' => new Fieldmanager_Textfield( __( 'Section Link Text', 'wmfoundation' ) ),
				'projects'  => new Fieldmanager_Group(
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
add_action( 'fm_post_page', 'wmf_project_fields' );

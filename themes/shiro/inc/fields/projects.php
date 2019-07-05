<?php
/**
 * Fieldmanager Fields for projects module
 *
 * @package shiro
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
				'heading'   => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro' ) ),
				'content'   => new Fieldmanager_RichTextArea(
					array(
						'label'           => __( 'Section Content', 'shiro' ),
						'buttons_1'       => array( 'bold', 'italic', 'strikethrough', 'underline' ),
						'buttons_2'       => array(),
						'editor_settings' => array(
							'quicktags'     => false,
							'media_buttons' => false,
						),
					)
				),
				'link_uri'  => new Fieldmanager_Link( __( 'Section Link URI', 'shiro' ) ),
				'link_text' => new Fieldmanager_Textfield( __( 'Section Link Text', 'shiro' ) ),
				'projects'  => new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Project', 'shiro' ),
						'sortable'       => true,
						'limit'          => 2,
						'children'       => array(
							'link_uri' => new Fieldmanager_Link( __( 'Project URI', 'shiro' ) ),
							'bg_image' => new Fieldmanager_Media( __( 'Background Image', 'shiro' ) ),
							'image'    => new Fieldmanager_Media( __( 'Project Icon', 'shiro' ) ),
							'heading'  => new Fieldmanager_Textfield( __( 'Project Name', 'shiro' ) ),
							'content'  => new Fieldmanager_TextArea( __( 'Project Description', 'shiro' ) ),
						),
					)
				),

			),
		)
	);
	$projects->add_meta_box( __( 'Projects', 'shiro' ), array( 'page' ) );
}
add_action( 'fm_post_page', 'wmf_project_fields' );

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
				'heading'   => new Fieldmanager_Textfield( __( 'Section Heading', 'shiro-admin' ) ),
				'content'   => new Fieldmanager_RichTextArea(
					array(
						'label'           => __( 'Section Content', 'shiro-admin' ),
						'buttons_1'       => array( 'bold', 'italic', 'strikethrough', 'underline' ),
						'buttons_2'       => array(),
						'editor_settings' => array(
							'quicktags'     => false,
							'media_buttons' => false,
						),
					)
				),
				'link_uri'  => new Fieldmanager_Link( __( 'Section Link URI', 'shiro-admin' ) ),
				'link_text' => new Fieldmanager_Textfield( __( 'Section Link Text', 'shiro-admin' ) ),
				'projects'  => new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Project', 'shiro-admin' ),
						'sortable'       => true,
						'limit'          => 2,
						'children'       => array(
							'link_uri' => new Fieldmanager_Link( __( 'Project URI', 'shiro-admin' ) ),
							'bg_image' => new Fieldmanager_Media( __( 'Background Image', 'shiro-admin' ) ),
							'image'    => new Fieldmanager_Media( __( 'Project Icon', 'shiro-admin' ) ),
							'heading'  => new Fieldmanager_Textfield( __( 'Project Name', 'shiro-admin' ) ),
							'content'  => new Fieldmanager_TextArea( __( 'Project Description', 'shiro-admin' ) ),
						),
					)
				),

			),
		)
	);
	$projects->add_meta_box( __( 'Projects', 'shiro-admin' ), array( 'page' ) );
}
add_action( 'fm_post_page', 'wmf_project_fields' );

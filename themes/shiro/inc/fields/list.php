<?php
/**
 * Fieldmanager Fields for List page template
 *
 * @package shiro
 */

/**
 * Add list page options.
 */
function wmf_list_fields() {
	if ( ! wmf_using_template( [ 'page-list', 'page-report', 'page-report-section', 'page-data' ] ) ) {
		return;
	}

	$list = new Fieldmanager_Group(
		array(
			'name'           => 'list',
			'limit'          => 0,
			'sortable'       => true,
			'add_more_label' => __( 'Add A List Section', 'shiro-admin' ),
			'extra_elements' => 1,
			'children'       => array(
				'title'       => new Fieldmanager_TextField( __( 'Section Title', 'shiro-admin' ) ),
				'description' => new Fieldmanager_RichTextArea( __( 'Section Description', 'shiro-admin' ) ),
				'links'       => new Fieldmanager_Group(
					array(
						'label'          => __( 'List of Links', 'shiro-admin' ),
						'limit'          => 0,
						'sortable'       => true,
						'extra_elements' => 0,
						'add_more_label' => __( 'Add A List Item', 'shiro-admin' ),
						'children'       => array(
							'title'       => new Fieldmanager_TextField( __( 'List Item Title', 'shiro-admin' ) ),
							'image'       => new Fieldmanager_Media( __( 'Featured Image', 'shiro-admin' ) ),
							'subhead'     => new Fieldmanager_Textarea( __( 'List Item Subheading', 'shiro-admin' ) ),
							'description' => new Fieldmanager_RichTextArea( __( 'List Item Description', 'shiro-admin' ) ),
							'link'        => new Fieldmanager_Link( __( 'List Item Link', 'shiro-admin' ) ),
							'offsite'     => new Fieldmanager_Checkbox( __( 'Show Off Site Link Icon', 'shiro-admin' ) ),
						),
					)
				),
			),
		)
	);

	$list->add_meta_box( __( 'List', 'shiro-admin' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_list_fields' );

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
	if ( ! wmf_using_template( 'page-list' ) && ! wmf_using_template( 'page-report' ) && ! wmf_using_template( 'page-data' ) ) {
		return;
	}

	$list = new Fieldmanager_Group(
		array(
			'name'           => 'list',
			'limit'          => 0,
			'sortable'       => true,
			'add_more_label' => __( 'Add A List Section', 'shiro' ),
			'extra_elements' => 1,
			'children'       => array(
				'title'       => new Fieldmanager_TextField( __( 'Section Title', 'shiro' ) ),
				'description' => new Fieldmanager_RichTextArea( __( 'Section Description', 'shiro' ) ),
				'links'       => new Fieldmanager_Group(
					array(
						'label'          => __( 'List of Links', 'shiro' ),
						'limit'          => 0,
						'sortable'       => true,
						'extra_elements' => 0,
						'add_more_label' => __( 'Add A List Item', 'shiro' ),
						'children'       => array(
							'title'       => new Fieldmanager_TextField( __( 'List Item Title', 'shiro' ) ),
							'image'       => new Fieldmanager_Media( __( 'Featured Image', 'shiro' ) ),
							'subhead'     => new Fieldmanager_Textarea( __( 'List Item Subheading', 'shiro' ) ),
							'description' => new Fieldmanager_RichTextArea( __( 'List Item Description', 'shiro' ) ),
							'link'        => new Fieldmanager_Link( __( 'List Item Link', 'shiro' ) ),
							'offsite'     => new Fieldmanager_Checkbox( __( 'Show Off Site Link Icon', 'shiro' ) ),
						),
					)
				),
			),
		)
	);

	$list->add_meta_box( __( 'List', 'shiro' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_list_fields' );

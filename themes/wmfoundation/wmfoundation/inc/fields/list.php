<?php
/**
 * Fieldmanager Fields for List page template
 *
 * @package wmfoundation
 */

/**
 * Add list page options.
 */
function wmf_list_fields() {
	if ( ! wmf_using_template( 'page-list' ) ) {
		return;
	}

	$list = new Fieldmanager_Group(
		array(
			'name'           => 'list',
			'limit'          => 0,
			'sortable'       => true,
			'add_more_label' => __( 'Add A List Section', 'wmfoundation' ),
			'extra_elements' => 1,
			'children'       => array(
				'title'       => new Fieldmanager_TextField( __( 'Section Title', 'wmfoundation' ) ),
				'description' => new Fieldmanager_RichTextArea( __( 'Section Description', 'wmfoundation' ) ),
				'links'       => new Fieldmanager_Group(
					array(
						'label'          => __( 'List of Links', 'wmfoundation' ),
						'limit'          => 0,
						'sortable'       => true,
						'extra_elements' => 0,
						'add_more_label' => __( 'Add A List Item', 'wmfoundation' ),
						'children'       => array(
							'title'       => new Fieldmanager_TextField( __( 'List Item Title', 'wmfoundation' ) ),
							'image'       => new Fieldmanager_Media( __( 'Featured Image', 'wmfoundation' ) ),
							'subhead'     => new Fieldmanager_Textarea( __( 'List Item Subheading', 'wmfoundation' ) ),
							'description' => new Fieldmanager_RichTextArea( __( 'List Item Description', 'wmfoundation' ) ),
							'link'        => new Fieldmanager_Link( __( 'List Item Link', 'wmfoundation' ) ),
							'offsite'     => new Fieldmanager_Checkbox( __( 'Show Off Site Link Icon', 'wmfoundation' ) ),
						),
					)
				),
			),
		)
	);

	$list->add_meta_box( __( 'List', 'wmfoundation' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_list_fields' );

<?php
/**
 * Fieldmanager Fields for Data page template
 *
 * @package shiro
 */

/**
 * Add Data page options.
 */
function wmf_datapage_fields() {
	if ( ! wmf_using_template( 'page-data' ) ) {
		return;
	}

	$stats_featured = new Fieldmanager_Group(
		array(
			'name'     => 'stats_featured',
			'children' => array(
				'main_image'     => new Fieldmanager_Media( __( 'Main image', 'shiro' ) ),
				'explanation'      => new Fieldmanager_RichTextArea( __( 'Explanation', 'shiro' ) ),
				'copy'        => new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Stat', 'shiro' ),
						'sortable'       => true,
						'limit'          => 0,
						'children'       => array(
							'image'     => new Fieldmanager_Media( __( 'Icon', 'shiro' ) ),
							'heading'   => new Fieldmanager_Textfield( __( 'Stat heading', 'shiro' ) ),
							'desc'      => new Fieldmanager_RichTextArea( __( 'Description', 'shiro' ) ),
						),
					)
				),
				'updated-date'   => new Fieldmanager_Textfield( __( 'Updated date note', 'shiro' ) ),
			),
		)
	);
	$stats_featured->add_meta_box( __( 'Featured Stats', 'shiro' ), 'page' );

	$stats_graph = new Fieldmanager_Group(
		array(
			'name'     => 'stats_graph',
			'children' => array(
				'explanation' => new Fieldmanager_RichTextArea( __( 'Explanation', 'shiro' ) ),
				'data' 		=> new Fieldmanager_Textfield( __( 'Data (JSON)', 'shiro' ) ),
				'copy' 		=> new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Stat', 'shiro' ),
						'sortable'       => true,
						'limit'          => 1,
						'children'       => array(
							'image'     => new Fieldmanager_Media( __( 'Icon', 'shiro' ) ),
							'heading'   => new Fieldmanager_Textfield( __( 'Stat heading', 'shiro' ) ),
							'desc'      => new Fieldmanager_RichTextArea( __( 'Description', 'shiro' ) ),
						),
					)
				),
				'updated-date'   => new Fieldmanager_Textfield( __( 'Updated date note', 'shiro' ) ),
			),
		)
	);
	$stats_graph->add_meta_box( __( 'Stats graph', 'shiro' ), 'page' );

	$stats_plain = new Fieldmanager_Group(
		array(
			'name'     => 'stats_plain',
			'children' => array(
				'subheadline'   => new Fieldmanager_Textfield( __( 'Section subheadline', 'shiro' ) ),
				'headline'   => new Fieldmanager_Textfield( __( 'Section headline', 'shiro' ) ),
				'subtitle' => new Fieldmanager_RichTextArea( __( 'Subtitle', 'shiro' ) ),
				'copy' 		=> new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Stat', 'shiro' ),
						'sortable'       => true,
						'limit'          => 3,
						'children'       => array(
							'heading'   => new Fieldmanager_Textfield( __( 'Stat heading', 'shiro' ) ),
							'desc'      => new Fieldmanager_RichTextArea( __( 'Description', 'shiro' ) ),
						),
					)
				),
				'updated-date'   => new Fieldmanager_Textfield( __( 'Updated date note', 'shiro' ) ),
			),
		)
	);
	$stats_plain->add_meta_box( __( 'Stats plain', 'shiro' ), 'page' );

}
add_action( 'fm_post_page', 'wmf_datapage_fields' );

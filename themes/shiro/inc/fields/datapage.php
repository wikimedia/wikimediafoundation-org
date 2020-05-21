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
				'time-format' 		=> new Fieldmanager_Textfield( __( 'Time format for tooltip – day (default) or month', 'shiro' ) ),
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

	$stats_profiles = new Fieldmanager_Group(
		array(
			'name'     => 'stats_profiles',
			'children' => array(
				'subheadline'   => new Fieldmanager_Textfield( __( 'Section subheadline', 'shiro' ) ),
				'headline'   => new Fieldmanager_Textfield( __( 'Section headline', 'shiro' ) ),
				'subtitle' => new Fieldmanager_RichTextArea( __( 'Subtitle (optional)', 'shiro' ) ),
				'explanation' => new Fieldmanager_RichTextArea( __( 'Explanation', 'shiro' ) ),
				'icons' 		=> new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Icon', 'shiro' ),
						'sortable'       => false,
						'limit'          => 3,
						'children'       => array(
							'image'     => new Fieldmanager_Media( __( 'Icon', 'shiro' ) ),
						),
					)
				),
				'data' 		=> new Fieldmanager_Textfield( __( 'Data (JSON)', 'shiro' ) ),
				'labels' 		=> new Fieldmanager_Textfield( __( 'Labels from above (e.g. ["Languages", "Editors", "Articles", "Pageviews", "Native"])', 'shiro' ) ),
				'filter-instruction' 		=> new Fieldmanager_Textfield( __( 'Instruction for users to filter view', 'shiro' ) ),
				'maxf1' 		=> new Fieldmanager_Textfield( __( 'Max radius (Feature 1/circles)', 'shiro' ) ),
				'maxf2' 		=> new Fieldmanager_Textfield( __( 'Max height (Feature 2/rectangles)', 'shiro' ) ),
				'masterunit' 		=> new Fieldmanager_Textfield( __( 'Value for 1 unit (Feature 3/ellipses)', 'shiro' ) ),
				'updated-date'   => new Fieldmanager_Textfield( __( 'Updated date note', 'shiro' ) ),
			),
		)
	);
	$stats_profiles->add_meta_box( __( 'Stats profiles', 'shiro' ), 'page' );

}
add_action( 'fm_post_page', 'wmf_datapage_fields' );

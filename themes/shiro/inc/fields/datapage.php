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
				'main_image'     => new Fieldmanager_Media( __( 'Main image', 'shiro-admin' ) ),
				'explanation'      => new Fieldmanager_RichTextArea( __( 'Explanation', 'shiro-admin' ) ),
				'copy'        => new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Stat', 'shiro-admin' ),
						'sortable'       => true,
						'limit'          => 0,
						'children'       => array(
							'image'     => new Fieldmanager_Media( __( 'Icon', 'shiro-admin' ) ),
							'heading'   => new Fieldmanager_Textfield( __( 'Stat heading', 'shiro-admin' ) ),
							'desc'      => new Fieldmanager_RichTextArea( __( 'Description', 'shiro-admin' ) ),
						),
					)
				),
				'updated-date'   => new Fieldmanager_Textfield( __( 'Updated date note', 'shiro-admin' ) ),
			),
		)
	);
	$stats_featured->add_meta_box( __( 'Featured Stats', 'shiro-admin' ), 'page' );

	$stats_graph = new Fieldmanager_Group(
		array(
			'name'     => 'stats_graph',
			'children' => array(
				'explanation' => new Fieldmanager_RichTextArea( __( 'Explanation', 'shiro-admin' ) ),
				'data' 		=> new Fieldmanager_Textfield( __( 'Data (JSON)', 'shiro-admin' ) ),
				'time-format' 		=> new Fieldmanager_Textfield( __( 'Time format for tooltip – day (default) or month', 'shiro-admin' ) ),
				'copy' 		=> new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Stat', 'shiro-admin' ),
						'sortable'       => true,
						'limit'          => 1,
						'children'       => array(
							'image'     => new Fieldmanager_Media( __( 'Icon', 'shiro-admin' ) ),
							'heading'   => new Fieldmanager_Textfield( __( 'Stat heading', 'shiro-admin' ) ),
							'desc'      => new Fieldmanager_RichTextArea( __( 'Description', 'shiro-admin' ) ),
						),
					)
				),
				'updated-date'   => new Fieldmanager_Textfield( __( 'Updated date note', 'shiro-admin' ) ),
			),
		)
	);
	$stats_graph->add_meta_box( __( 'Stats graph', 'shiro-admin' ), 'page' );

	$stats_plain = new Fieldmanager_Group(
		array(
			'name'     => 'stats_plain',
			'children' => array(
				'subheadline'   => new Fieldmanager_Textfield( __( 'Section subheadline', 'shiro-admin' ) ),
				'headline'   => new Fieldmanager_Textfield( __( 'Section headline', 'shiro-admin' ) ),
				'subtitle' => new Fieldmanager_RichTextArea( __( 'Subtitle', 'shiro-admin' ) ),
				'copy' 		=> new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Stat', 'shiro-admin' ),
						'sortable'       => true,
						'limit'          => 3,
						'children'       => array(
							'heading'   => new Fieldmanager_Textfield( __( 'Stat heading', 'shiro-admin' ) ),
							'desc'      => new Fieldmanager_RichTextArea( __( 'Description', 'shiro-admin' ) ),
						),
					)
				),
				'updated-date'   => new Fieldmanager_Textfield( __( 'Updated date note', 'shiro-admin' ) ),
			),
		)
	);
	$stats_plain->add_meta_box( __( 'Stats plain', 'shiro-admin' ), 'page' );

	$stats_profiles = new Fieldmanager_Group(
		array(
			'name'     => 'stats_profiles',
			'children' => array(
				'subheadline'   => new Fieldmanager_Textfield( __( 'Section subheadline', 'shiro-admin' ) ),
				'headline'   => new Fieldmanager_Textfield( __( 'Section headline', 'shiro-admin' ) ),
				'subtitle' => new Fieldmanager_RichTextArea( __( 'Subtitle (optional)', 'shiro-admin' ) ),
				'explanation' => new Fieldmanager_RichTextArea( __( 'Explanation', 'shiro-admin' ) ),
				'icons' 		=> new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Icon', 'shiro-admin' ),
						'sortable'       => false,
						'limit'          => 3,
						'children'       => array(
							'image'     => new Fieldmanager_Media( __( 'Icon', 'shiro-admin' ) ),
						),
					)
				),
				'data' 		=> new Fieldmanager_Textfield( __( 'Data (JSON)', 'shiro-admin' ) ),
				'labels' 		=> new Fieldmanager_Textfield( __( 'Labels from above (e.g. ["Languages", "Editors", "Articles", "Pageviews", "Native"])', 'shiro-admin' ) ),
				'filter-instruction' 		=> new Fieldmanager_Textfield( __( 'Instruction for users to filter view', 'shiro-admin' ) ),
				'maxf1' 		=> new Fieldmanager_Textfield( __( 'Max radius (Feature 1/circles)', 'shiro-admin' ) ),
				'maxf2' 		=> new Fieldmanager_Textfield( __( 'Max height (Feature 2/rectangles)', 'shiro-admin' ) ),
				'masterunit' 		=> new Fieldmanager_Textfield( __( 'Value for 1 unit (Feature 3/ellipses)', 'shiro-admin' ) ),
				'updated-date'   => new Fieldmanager_Textfield( __( 'Updated date note', 'shiro-admin' ) ),
			),
		)
	);
	$stats_profiles->add_meta_box( __( 'Stats profiles', 'shiro-admin' ), 'page' );

}
add_action( 'fm_post_page', 'wmf_datapage_fields' );

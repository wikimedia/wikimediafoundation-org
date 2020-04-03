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
				'copy'        => new Fieldmanager_Group(
					array(
						'add_more_label' => __( 'Add Stat', 'shiro' ),
						'sortable'       => true,
						'limit'          => 0,
						'children'       => array(
							'image'     => new Fieldmanager_Media( __( 'Icon', 'shiro' ) ),
							'heading'   => new Fieldmanager_Textfield( __( 'Stat heading', 'shiro' ) ),
							'copy'      => new Fieldmanager_RichTextArea( __( 'Description', 'shiro' ) ),
						),
					)
				),
			),
		)
	);
	$stats_featured->add_meta_box( __( 'Featured Stats', 'shiro' ), 'page' );

}
add_action( 'fm_post_page', 'wmf_datapage_fields' );

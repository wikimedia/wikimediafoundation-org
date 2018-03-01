<?php
/**
 * Fieldmanager Fields for List page template
 *
 * @package wmfoundation
 */

/**
 * Add list page options.
 */
function wmf_related_pages() {
	if ( wmf_using_template( 'page-landing' ) ) {
		return;
	}

	$related_pages = new Fieldmanager_Group(
		array(
			'name'           => 'related_pages',
			'children'       => array(
				'title' => new Fieldmanager_TextField( __( 'Headline', 'wmfoundation' ) ),
				'links' => new Fieldmanager_Checkboxes(
					array(
						'label'       => __( 'List of Posts to pull from', 'wmfoundation' ),
						'description' => __( 'Select as many as are applicable. Three posts will be selected from this list for display.' ),
						'options'     => wmf_get_pages_options(),
					)
				),
			),
		)
	);

	$related_pages->add_meta_box( __( 'Related Pages', 'wmfoundation' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_related_pages' );

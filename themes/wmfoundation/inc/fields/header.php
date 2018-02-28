<?php
/**
 * Fieldmanager Fields for Header options
 *
 * @package wmfoundation
 */

/**
 * Add header options.
 */
function wmf_header_fields() {
	$header_opts = new Fieldmanager_Group(
		array(
			'name'     => 'page_header_background',
			'label'    => __( 'Background', 'wmfoundation' ),
			'children' => array(
				'color' => new Fieldmanager_Radios(
					array(
						'label'   => __( 'Color', 'wmfoundation' ),
						'options' => array(
							''     => __( 'Blue', 'wmfoundation' ),
							'pink' => __( 'Pink', 'wmfoundation' ),
						),
					)
				),
				'image' => new Fieldmanager_Media( __( 'Image', 'wmfoundation' ) ),
			),
		)
	);

	$header_opts->add_meta_box( __( 'Header Options', 'wmfoundation' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_header_fields', 1 );

<?php
/**
 * Fieldmanager Fields for Landing page template
 *
 * @package wmfoundation
 */

/**
 * Add landing page options.
 */
function wmf_landing_fields() {
	if ( ! wmf_using_template( 'page-landing' ) ) {
		return;
	}

	$header_opts = new Fieldmanager_Textfield(
		array(
			'name' => 'sub_title',
		)
	);

	$header_opts->add_meta_box( __( 'Subtitle', 'wmfoundation' ), 'page' );
}
add_action( 'fm_post_page', 'wmf_landing_fields' );

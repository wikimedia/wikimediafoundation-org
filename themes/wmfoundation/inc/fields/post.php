<?php
/**
 * Fieldmanager Fields for Post template
 *
 * @package wmfoundation
 */

/**
 * Add post options.
 */
function wmf_post_fields() {
	$opts = array(
		'home' => __( 'Home Page', 'wmfoundation' ),
	);

	$featured_on = new Fieldmanager_Checkboxes(
		array(
			'name'    => 'featured_on',
			'options' => $opts + wmf_get_landing_pages_options(),
		)
	);
	$featured_on->add_meta_box( __( 'Featured On:', 'wmfoundation' ), 'post' );
}
add_action( 'fm_post_post', 'wmf_post_fields' );

<?php
/**
 * Fieldmanager Fields for Landing page template
 *
 * @package wmfoundation
 */

/**
 * Add landing page options.
 */
function wmf_intro_field() {
	$social = new Fieldmanager_Checkboxes(
		array(
			'name'          => 'share_links',
			'options'       => array(
				'twitter'  => __( 'Twitter', 'wmfoundation' ),
				'facebook' => __( 'Facebook', 'wmfoundation' ),
			),
			'default_value' => array( 'twitter', 'facebook' ),
		)
	);

	$social->add_meta_box( __( 'Share This Post On:', 'wmfoundation' ), array( 'post', 'page' ) );
}
add_action( 'fm_post_page', 'wmf_intro_field' );
add_action( 'fm_post_post', 'wmf_intro_field' );

<?php
/**
 * Fieldmanager Fields for Landing page template
 *
 * @package shiro
 */

/**
 * Add landing page options.
 */
function wmf_intro_field() {
	$social = new Fieldmanager_Checkboxes(
		array(
			'name'          => 'share_links',
			'options'       => array(
				'twitter'  => __( 'Twitter', 'shiro-admin' ),
				'facebook' => __( 'Facebook', 'shiro-admin' ),
			),
			'default_value' => array( 'twitter', 'facebook' ),
		)
	);

	$social->add_meta_box( __( 'Share This Post On:', 'shiro-admin' ), array( 'post', 'page' ) );
}
add_action( 'fm_post_page', 'wmf_intro_field' );
add_action( 'fm_post_post', 'wmf_intro_field' );

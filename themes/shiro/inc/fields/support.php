<?php
/**
 * Fieldmanager Fields for Off Site Links Module
 *
 * @package shiro
 */

/**
 * Add support page options.
 */
function wmf_support_fields() {
	$support = new Fieldmanager_Checkbox(
		array(
			'name'        => 'hide_support_module',
			'label'       => __( 'Hide Support Module', 'shiro-admin' ),
			'description' => __( 'If enabled, the support module will not be shown with this content.', 'shiro-admin' ),
		)
	);
	$support->add_meta_box( __( 'Support Module', 'shiro-admin' ), array( 'page', 'post', 'profile' ) );
}
add_action( 'fm_post_post', 'wmf_support_fields' );
add_action( 'fm_post_page', 'wmf_support_fields' );
add_action( 'fm_post_profile', 'wmf_support_fields' );

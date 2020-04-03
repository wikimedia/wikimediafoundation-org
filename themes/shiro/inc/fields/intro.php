<?php
/**
 * Fieldmanager Fields for Page Intro options
 *
 * @package shiro
 */

/**
 * Add intro options.
 */
function wmf_intro_fields() {
	$intro = new Fieldmanager_RichTextArea(
		array(
			'name' => 'page_intro',
		)
	);
	$intro->add_meta_box( __( 'Page Intro', 'shiro' ), array( 'post', 'page' ) );
}
add_action( 'fm_post_post', 'wmf_intro_fields', 5 );
$is_report_page = wmf_using_template( 'page-report' );
if ( $is_report_page ) {
    add_action( 'fm_post_page', 'wmf_intro_fields', 5 );
}
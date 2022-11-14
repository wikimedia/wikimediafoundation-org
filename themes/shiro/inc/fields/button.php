<?php
/**
 * Adds button option to Landing and List Templates.
 *
 * @package shiro
 */

/**
 * Add button options.
 */
function wmf_button_fields() {
	if ( ! wmf_using_template( array( 'page-landing', 'page-list', 'page-report', 'page-report-landing', 'page-data' ) ) ) {
		return;
	}

	$button = new FieldManager_Group(
		array(
			'name'     => 'intro_button',
			'children' => array(
				'title' => new Fieldmanager_TextField( __( 'Title', 'shiro-admin' ) ),
				'link'  => new Fieldmanager_Link( __( 'Link', 'shiro-admin' ) ),
			),
		)
	);
	$button->add_meta_box( __( 'Intro Button', 'shiro-admin' ), array( 'page' ) );
}
add_action( 'fm_post_page', 'wmf_button_fields', 5 );

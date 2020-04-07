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
	if ( ! wmf_using_template( 'page-landing' ) && ! wmf_using_template( 'page-list' ) && ! wmf_using_template( 'page-report' ) && ! wmf_using_template( 'page-data' ) ) {
		return;
	}

	$button = new FieldManager_Group(
		array(
			'name'     => 'intro_button',
			'children' => array(
				'title' => new Fieldmanager_TextField( __( 'Title', 'shiro' ) ),
				'link'  => new Fieldmanager_Link( __( 'Link', 'shiro' ) ),
			),
		)
	);
	$button->add_meta_box( __( 'Intro Button', 'shiro' ), array( 'page' ) );
}
add_action( 'fm_post_page', 'wmf_button_fields', 5 );

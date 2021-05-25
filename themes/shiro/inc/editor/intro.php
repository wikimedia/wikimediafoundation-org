<?php
/**
 * Fieldmanager Fields for Page Intro options
 *
 * @package shiro
 */

namespace WMF\Fields\Page_Intro;

use Fieldmanager_RichTextArea;
use function WMF\Editor\is_using_block_editor;

/**
 * Name of the meta field.
 * @var string
 */
const FIELD_NAME = 'page_intro';

/**
 * Bootstrap functionality for this meta field.
 */
add_action( 'init', __NAMESPACE__ . '\\register_meta_fields' );
add_action( 'init', __NAMESPACE__ . '\\register_fieldmanager_fields' );

/**
 * Register meta fields for use in the REST API.
 */
function register_meta_fields() {

	$meta_args = [
		'type'         => 'string',
		'description'  => __( 'Intro field, displayed before post content in single views', 'shiro-admin' ),
		'single'       => true,
		'show_in_rest' => true,
	];

	// The Intro field is available on all Posts.
	register_post_meta( 'post', FIELD_NAME, $meta_args );

	// This should only be used on Pages using the "report" template.
	register_post_meta( 'page', FIELD_NAME, $meta_args );
}

/**
 * Add Fieldmanager meta box for the classic editor.
 *
 * XXX: This can be removed once the classic editor is no longer used.
 */
function register_fieldmanager_fields() {
	add_action( 'fm_post_post', __NAMESPACE__ . '\\wmf_intro_fields', 5 );

	if ( wmf_using_template( 'page-report' ) ) {
		add_action( 'fm_post_page', __NAMESPACE__ . '\\wmf_intro_fields', 5 );
	}
}

/**
 * Add Fieldmanager meta box for the classic editor.
 *
 * XXX: This can be removed once the classic editor is no longer used.
 */
function wmf_intro_fields() {
	$current_screen = get_current_screen();

	// Don't register fields in the block editor.
	if ( $current_screen && $current_screen->is_block_editor() ) {
		return;
	}

	// Don't register fields if the post should be using the block editor
	// (otherwise, Fieldmanager will unset any changes to this field when
	// processing the classic meta boxes).
	if ( is_using_block_editor() ) {
		return;
	}

	$intro = new Fieldmanager_RichTextArea(
		array(
			'name' => 'page_intro',
		)
	);
	$intro->add_meta_box( __( 'Page Intro', 'shiro-admin' ), array( 'post', 'page' ) );
}

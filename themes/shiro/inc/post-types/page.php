<?php
/**
 * Registers additional supports for the core "page" post type.
 *
 * @package shiro
 */

/**
 * Registers the "hide page title" meta field
 */
function wmf_page_init() {
	register_post_meta( 'page', '_wmf_hide_title', [
		'type' => 'boolean',
		'single' => true,
		'show_in_rest' => true,
	] );
}

add_action( 'init', 'wmf_page_init' );

/**
 * Filter the body class to include "hide-page-title" if the hide title meta is set.
 *
 * @param string[] $body_classes Body classes assigned to the request.
 * @return string[] Updated body classes.
 */
function wmf_page_filter_body_class( $body_classes ) {
	if ( get_post_type() === 'page' && get_post_meta( get_the_ID(), '_wmf_hide_title', true ) ) {
		$body_classes[] = 'hide-page-title';
	}

	return $body_classes;
}

add_filter( 'body_class', 'wmf_page_filter_body_class' );


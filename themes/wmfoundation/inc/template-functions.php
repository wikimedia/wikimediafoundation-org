<?php
/**
 * Additional features to allow styling of the templates
 *
 * @package wmfoundation
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function wmf_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'wmf_body_classes' );

/**
 * Parse template data, get back container class.
 *
 * @return string Container classes to add.
 */
function wmf_get_header_container_class() {
	$class = '';

	if ( has_post_thumbnail() ) {
		$template = basename( get_page_template() );

		switch ( $template ) {
			case 'page-landing.php':
				$class .= ' featured-photo--content-left';
				break;

			default:
				$class .= ' featured-photo--content-centered';
				break;
		}
	} else {
		$class .= ' minimal--short';
	}

	if ( is_page() ) {
		$bg_opts = get_post_meta( get_the_ID(), 'page_header_background', true );

		$class .= isset( $bg_opts['color'] ) && 'pink' === $bg_opts['color'] ? ' header-pink' : '';
	}

	return $class;
}

/**
 * Parse template data, get back header button class.
 *
 * @return string button classes to add.
 */
function wmf_get_header_cta_button_class() {
	$class = '';

	$bg_opts = get_post_meta( get_the_ID(), 'page_header_background', true );

	$class .= is_page() && isset( $bg_opts['color'] ) && 'pink' === $bg_opts['color'] ? ' btn-blue' : ' btn-pink';

	return $class;
}

function wmf_get_the_title() {
	$title = '';

	if ( is_single() ) {
		$title = get_the_title();
	}

	if ( is_post_type_archive() ) {
		$post_type_object = get_post_type_object( $post_type );
		$title            = isset( $post_type_object->labels->singular_name ) ? $post_type_object->labels->singular_name : '';
	}

	if ( is_tax() ) {
		$title = single_term_title( '', false );
	}

	return $title;
}

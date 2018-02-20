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
		$template = get_page_template();

		switch ( $template ) {
			case 'landing':
				$class .= 'featured-photo--content-left';
				break;

			default:
				$class .= 'featured-photo--content-centered';
				break;
		}
	} else {
		$class .= 'minimal--short';
	}

	return $class;
}

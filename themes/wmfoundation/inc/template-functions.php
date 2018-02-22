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

/**
 * Get all the child terms for a parent organized by hierarchy
 *
 * @param int    $parent_id ID to query against.
 * @param string $taxonomy  Taxonomy name.
 * @return array List of organized IDs.
 */
function wmf_get_term_hierarchy( $parent_id, $taxonomy ) {
	$children = array();
	$term_array = array();
	$terms = get_terms( $taxonomy, array(
		'orderby' => 'id',
		'fields'  => 'id=>parent',
		'get'     => 'all',
	) );

	foreach ( $terms as $term_id => $parent ) {

		if ( 0 < $parent ) {
			$children[ $parent ][] = $term_id;
		}
	}

	foreach( $children[ $parent_id ] as $child_id ) {
		$term_array[ $child_id ] = $children[ $child_id ];
	}

	return $term_array;
}

/**
 * Get posts for an individual term.
 *
 * @param int    $term_id  Term to query against.
 * @param string $taxonomy Taxonomy name.
 * @return array List of and term name.
 */
function wmf_get_term_posts( $term_id, $taxonomy ) {
	$term_query = get_term( $term_id, $taxonomy );
	$posts              = new WP_Query(
		array(
			'post_type' => 'profile',
			'fields'    => 'ids',
			'tax_query' => array( // Slow query ok.
				array(
					'taxonomy' => 'role',
					'field'    => 'term_id',
					'terms'    => $term_id,
				),
			),
		)
	);

	return array(
		'posts' => $posts->posts,
		'name'  => $term_query->name,
	);

}

/**
 * Organize posts by their child terms in taxonomy
 *
 * @param int    $term_id  ID of parent term.
 * @param string $taxonomy Taxonomy to check against.
 * @return array list of organized posts or empty array.
 */
function wmf_get_posts_by_child_terms( $term_id, $taxonomy = 'role' ) {
	$post_list   = array();

	$child_terms = wmf_get_term_hierarchy( $term_id, 'role' );

	foreach ( $child_terms as $parent_id => $children ) {

		$featured_term = get_term_meta( $parent_id, 'featured_term', true );

		if ( true === boolval( $featured_term ) ) {
			$post_list = array(
				$parent_id => wmf_get_term_posts( $parent_id, $taxonomy )
			 ) + $post_list;
		} else {
			$post_list[ $parent_id ] = wmf_get_term_posts( $parent_id, $taxonomy );
		}

		$post_list[ $parent_id ]['children'] = array();

		foreach ( $children as $child_id ) {
			$post_list[ $parent_id ]['children'][ $child_id ] = wmf_get_term_posts( $child_id, $taxonomy );
		}
	}

	return $post_list;
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

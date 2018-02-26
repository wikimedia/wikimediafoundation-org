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

	if ( ( is_single() || is_page() ) && has_post_thumbnail() ) {
		$post_type = get_post_type();

		if ( in_array( $post_type, array( 'profile' ), true ) ) {
			$class = ' minimal--short';
		} else {
			$template = basename( get_page_template() );

			switch ( $template ) {
				case 'page-landing.php':
					$class = ' featured-photo--content-left';
					break;

				default:
					$class = ' featured-photo--content-centered';
					break;
			}
		}
	} else {
		$class = ' minimal--short';
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
 * @param int $parent_id ID to query against.
 * @return array List of organized IDs.
 */
function wmf_get_role_hierarchy( $parent_id ) {
	$children   = array();
	$term_array = array();
	$terms      = get_terms(
		'role', array(
			'orderby' => 'id',
			'fields'  => 'id=>parent',
			'get'     => 'all',
		)
	);

	foreach ( $terms as $term_id => $parent ) {
		if ( 0 < $parent ) {
			$children[ $parent ][] = $term_id;
		}
	}

	foreach ( $children[ $parent_id ] as $child_id ) {
		$term_array[ $child_id ] = $children[ $child_id ];
	}

	return $term_array;
}

/**
 * Get posts for an individual term.
 *
 * @param int $term_id  Term to query against.
 * @return array List of and term name.
 */
function wmf_get_role_posts( $term_id ) {
	$term_query = get_term( $term_id, 'role' );
	$posts      = new WP_Query(
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
 * For posts that are not parent posts (i.e. Staff & Contractors)
 * This will simply return a list of posts for a term.
 *
 * @param int $term_id  ID of parent term.
 * @return array list of organized posts or empty array.
 */
function wmf_get_posts_by_child_roles( $term_id ) {
	$post_list = array();

	$term = get_term( $term_id );
	if ( 0 !== $term->parent ) {
		$post_list[ $term_id ] = wmf_get_role_posts( $term_id );
		return $post_list;
	}

	$cached_posts = wp_cache_get( 'wmf_terms_list_' . $term_id );

	if ( ! empty( $cached_posts ) ) {
		return $cached_posts;
	}

	$child_terms = wmf_get_role_hierarchy( $term_id, 'role' );

	foreach ( $child_terms as $parent_id => $children ) {
		$featured_term = get_term_meta( $parent_id, 'featured_term', true );

		if ( true === boolval( $featured_term ) ) {
			$post_list = array(
				$parent_id => wmf_get_role_posts( $parent_id ),
			) + $post_list;
		} else {
			$post_list[ $parent_id ] = wmf_get_role_posts( $parent_id );
		}

		$post_list[ $parent_id ]['children'] = array();

		foreach ( $children as $child_id ) {
			$post_list[ $parent_id ]['children'][ $child_id ] = wmf_get_role_posts( $child_id );
		}
	}

	wp_cache_set( 'wmf_terms_list_' . $term_id, $post_list );

	return $post_list;
}

/**
 * Clear cache for a term and its parent
 *
 * @param int $term_id Term ID to clear.
 */
function wmf_clear_role_cache( $term_id ) {
	wp_cache_delete( 'wmf_terms_list_' . $term_id );

	if ( ! empty( $term->parent ) ) {
		wp_cache_delete( 'wmf_terms_list_' . $term->parent );
	}
}
add_action( 'edit_role', 'wmf_clear_role_cache', 10, 1 );
add_action( 'create_role', 'wmf_clear_role_cache', 10, 1 );
add_action( 'delete_role', 'wmf_clear_role_cahce', 10, 1 );

/**
 * Clears the `wmf_landing_pages_opts` cache when a page is updated.
 *
 * @param int $post_id The post ID.
 */
function wmf_clear_page_cache( $post_id ) {
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	wp_cache_delete( 'wmf_landing_pages_opts' );
}
add_action( 'save_post_page', 'wmf_clear_page_cache' );

/**
 * Clears the `wmf_featured_posts_for` context cache when a post is updated.
 *
 * @param int $post_id The post ID.
 */
function wmf_clear_post_cache( $post_id ) {
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	$contexts = array(
		'home' => 'Home', // We don't need this to translate.
	);

	$contexts = $contexts + wmf_get_landing_pages_options();

	foreach ( $contexts as $context ) {
		$cache_key = md5( 'wmf_featured_posts_for' . $context );
		wp_cache_delete( $cache_key );
	}
}
add_action( 'save_post_post', 'wmf_clear_post_cache' );

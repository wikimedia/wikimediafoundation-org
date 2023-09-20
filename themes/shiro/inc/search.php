<?php
/**
 * Search related functions.
 *
 * @package shiro
 */

/**
 * Restrict search results to news (posts) and pages.
 *
 * @param object $query Full WP_Query object.
 *
 * @return void
 */
function wmf_restrict_search( $query ) {
	if ( $query->is_search() && $query->is_main_query() && ! isset( $_GET['post_type'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$query->set( 'post_type', [
			'post',
			'page',
		] );
	}
}
add_action( 'pre_get_posts', 'wmf_restrict_search' );

/**
 * Custom sort for search results based on query parameters.
 * Orders by relevance or date based on the 'orderby' parameter.
 * If nothing is set, it default to relevance.
 *
 * @param WP_Query $query The WordPress Query instance.
 * @return void
 */
function wmf_custom_sort( WP_Query $query ) {
	if ( $query->is_search() && $query->is_main_query() ) {
		$order_by = sanitize_text_field( $_GET['orderby'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		switch ( $order_by ) {
			case 'date':
				$order_direction = isset( $_GET['order'] ) ? strtoupper( sanitize_text_field( $_GET['order'] ) ) : 'DESC';
				$query->set( 'order', $order_direction );
				$query->set( 'orderby', 'date' );
				break;

			default:
				$query->set( 'orderby', 'relevance' );
				break;
		}
	}
}
add_action( 'pre_get_posts', 'wmf_custom_sort' );

/**
 * Get the current URL with merged query parameters.
 *
 * @param array $additional_params Additional query parameters to merge.
 * @return string The updated URL.
 */
function wmf_set_custom_sort_url( $additional_params = [] ) {
	$current_url = home_url( add_query_arg( NULL, NULL ) );
	$parsed_url = parse_url( $current_url );

	$current_query_params = [];
	if ( isset( $parsed_url['query'] ) ) {
		parse_str( $parsed_url['query'], $current_query_params );
	}

	$merged_query_params = array_merge( $current_query_params, $additional_params );
	return $parsed_url['path'] . '?' . http_build_query( $merged_query_params );
}

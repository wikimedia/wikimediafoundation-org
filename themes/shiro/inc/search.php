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
 * Order search by relevance.
 *
 * @param object $query Full WP_Query object.
 *
 * @return void
 */
function wmf_order_search_by_relevance( $query ) {
	if ( $query->is_search() && $query->is_main_query() ) {
		$query->set( 'orderby', 'relevance' );
		$query->set( 'order', 'DESC' );
	}
}
add_action( 'pre_get_posts', 'wmf_order_search_by_relevance' );

<?php
/**
 * Search related functions.
 *
 * @package shiro
 */

/**
 * Remove post_type = 'profile' from search results.
 *
 * @param object $query Full WP_Query object.
 *
 * @return void
 */
function wmf_remove_profile_from_search( $query ) {
	if ( $query->is_search() && $query->is_main_query() && ! isset( $_GET['post_type'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$query->set( 'post_type', [
			'post',
			'page',
		] );
	}
}
add_action( 'pre_get_posts', 'wmf_remove_profile_from_search' );

<?php
/**
 * Query related functions.
 *
 * @package shiro
 */

/**
 * Remove featured post from displaying on news page.
 *
 * @param object $query Full WP_Query object.
 */
function wmf_remove_featured_post_from_news( $query ) {
	if ( ! is_admin() && is_home() && $query->is_main_query() ) {
		$posts_page_id = get_option( 'page_for_posts' );
		$featured_post = get_post_meta( $posts_page_id, 'featured_post', true );

		if ( empty( $featured_post ) ) {
			return;
		}

		$query->set( 'post__not_in', [ $featured_post ] );
	}
}
add_action( 'pre_get_posts', 'wmf_remove_featured_post_from_news' );

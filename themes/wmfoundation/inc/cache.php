<?php
/**
 * Functions related to clearing cache when updates are made.
 *
 * @package wmfoundation
 */

/**
 * Clear cache for a term and its parent
 *
 * @param int $term_id Term ID to clear.
 */
function wmf_clear_role_cache( $term_id ) {
	wp_cache_delete( 'wmf_terms_list_' . $term_id );

	$term = get_term( $term_id, 'role' );

	if ( ! $term || is_wp_error( $term ) ) {
		return;
	}

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
 * Clears the wmf_profiles_opts cache when a profile is
 * created or updated.
 *
 * @param int $post_id The post ID.
 */
function wmf_clear_profile_cache( $post_id ) {
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	wp_cache_delete( 'wmf_profiles_opts' );

	$terms    = get_the_terms( $post_id, 'role' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return;
	}

	$term_ids = wp_list_pluck( $terms, 'term_id' );

	if ( ! empty( $term_ids ) ) {
		wp_cache_delete( md5( sprintf( 'wmf_profiles_for_term_%s', $term_ids[0] ) ) );
	}
}
add_action( 'save_post_profile', 'wmf_clear_profile_cache' );

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

	wp_cache_delete( 'wmf_posts_for_post_' . $post_id );
	wp_cache_delete( 'wmf_posts_opts' );
}
add_action( 'save_post_post', 'wmf_clear_post_cache' );

/**
 * Clears the `wmf_image_credits_{id}` cache when any content is updated.
 *
 * @param int $post_id The post ID.
 */
function wmf_clear_credits_cache( $post_id ) {
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	$cache_key = md5( sprintf( 'wmf_image_credits_%s', $post_id ) );

	wp_cache_delete( $cache_key );
}
add_action( 'save_post', 'wmf_clear_credits_cache' );

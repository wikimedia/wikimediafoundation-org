<?php
/**
 * Setup some baseline custom fields functions
 *
 * @package wmfoundation
 */

/**
 * Verify a template is being used on the backend.
 *
 * @param mixed $template_name Template to check against, without .php as a string (single template) or array (multiple templates).
 * @return bool
 */
function wmf_using_template( $template_name ) {
	$id = wmf_get_fields_post_id();

	if ( empty( $id ) ) {
		return false;
	}
	$page_template = get_post_meta( $id, '_wp_page_template', true );

	if ( ! empty( $page_template ) ) {
		$current_page_template = explode( '.', $page_template );

		// Allow an array or string.
		if ( is_array( $template_name ) && in_array( $current_page_template[0], $template_name, true ) ) {
			return true;
		} elseif ( $current_page_template[0] === $template_name ) {
			return true;
		}
	}

	return false;
}

/**
 * Gets the post ID for the edited post.
 *
 * @return int
 */
function wmf_get_fields_post_id() {
	$post_request_id = filter_input(
		INPUT_POST, 'post_ID', FILTER_CALLBACK, array(
			'options' => 'intval',
		)
	);
	$get_request_id  = filter_input(
		INPUT_GET, 'post', FILTER_CALLBACK, array(
			'options' => 'intval',
		)
	);

	return ! empty( $get_request_id ) ? $get_request_id : $post_request_id;
}

/**
 * Gets available landing pages in an array suitable for fieldmanager options.
 *
 * @return array
 */
function wmf_get_landing_pages_options() {
	$landing_pages = wp_cache_get( 'wmf_landing_pages_opts' );

	if ( empty( $landing_pages ) ) {
		$landing_pages = array();

		$args  = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'no_found_rows'  => true,
			'posts_per_page' => 100,
			'meta_query'     => array(
				array(
					'key'   => '_wp_page_template',
					'value' => 'page-landing.php',
				),
			),
		); // WPCS: slow query ok.
		$pages = new WP_Query( $args );

		if ( $pages->have_posts() ) {
			while ( $pages->have_posts() ) {
				$pages->the_post();
				$landing_pages[ get_the_ID() ] = get_the_title();
			}
		}
		wp_reset_postdata();

		wp_cache_add( 'wmf_landing_pages_opts', $landing_pages );
	}

	return $landing_pages;
}

require get_template_directory() . '/inc/fields/header.php';
require get_template_directory() . '/inc/fields/home.php';
require get_template_directory() . '/inc/fields/landing.php';
require get_template_directory() . '/inc/fields/page-cta.php';
require get_template_directory() . '/inc/fields/post.php';
require get_template_directory() . '/inc/fields/profile.php';
require get_template_directory() . '/inc/fields/connect.php';
require get_template_directory() . '/inc/fields/listing.php';
require get_template_directory() . '/inc/fields/links.php';
require get_template_directory() . '/inc/fields/support.php';

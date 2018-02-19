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
function wmfoundation_using_template( $template_name ) {

	$id = wmfoundation_get_fields_post_id();

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
function wmfoundation_get_fields_post_id() {
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

require get_template_directory() . '/inc/fields/profile.php';

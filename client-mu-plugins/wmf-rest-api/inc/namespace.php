<?php
/**
 * Custom WordPress REST API functionality for Wikimedia Foundation.
 *
 * @package wmf-rest-api
 */

namespace WMF\RESTAPI;

use WP_Error;

/**
 * Setup filters and actions for the namespace.
 */
function bootstrap() {
	add_filter( 'rest_authentication_errors', __NAMESPACE__ . '\\restrict_public_rest_api_access' );
}

/**
 * Restrict public REST API access.
 *
 * Used to pass a WP_Error from an authentication method back to the API.
 *
 * @param WP_Error $errors WP_Error if authentication error, null if authentication method wasn't used, true if authentication succeeded.
 *
 * @return WP_Error|null|true
 */
function restrict_public_rest_api_access( $errors ) {
	// Check if a previous authentication was applied
	// and pass that result without modification.
	if ( true === $errors || is_wp_error( $errors ) ) {
		return $errors;
	}

	// Return an unauthorized response error if the user
	// does not have editing capabilities.
	if ( ! current_user_can( 'edit_posts' ) ) {
		return new WP_Error(
			'rest_disabled',
			__( 'You do not have sufficient permissions.', 'wmf-rest-api' ),
			[ 'status' => rest_authorization_required_code() ]
		);
	}

	return $errors;
}

<?php
/**
 * Adding security functions.
 *
 * @package wikimedia-contest;
 */

namespace WMF\Security;

/**
 * Booting up the security functionalities.
 */
function init() {
	add_action( 'send_headers', __NAMESPACE__ . '\\enable_strict_transport_security' ); // Making sure of HTTPS
	add_action( 'send_headers', __NAMESPACE__ . '\\set_content_security_policy' ); // Policy for content security
	add_action( 'send_headers', __NAMESPACE__ . '\\set_x_content_type_options' ); // Option of X Content Type
	add_action( 'send_headers', __NAMESPACE__ . '\\set_referrer_policy' ); // Policy for referrer
	add_action( 'send_headers', __NAMESPACE__ . '\\set_permissions_policy' ); // Policy for permissions
}

/**
 * Functioning for HSTS, requirement of HTTPS for all connections.
 */
function enable_strict_transport_security() {
	header( 'Strict-Transport-Security: max-age=31536000' );
}

/**
 * Function for setting strict Policy of Content Security.
 * Only allowing content of Wikimedia domain.
 */
function set_content_security_policy() {
	header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; font-src 'self';");
}

/**
 * Setting the X-Content-Type-Options for no sniffing of MIME type.
 */
function set_x_content_type_options() {
	header( 'X-Content-Type-Options: nosniff' );
}

/**
 * Function for setting Referrer Policy. No referrer information.
 */
function set_referrer_policy() {
	header( 'Referrer-Policy: no-referrer' );
}

/**
 * Finally, function for setting the Permissions Policy.
 * We're allowing only the fullscreen feature from our domain.
 */
function set_permissions_policy() {
	header( 'Permissions-Policy: fullscreen=(self)' );
}

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
	add_action( 'send_headers', __NAMESPACE__ . '\\enable_strict_transport_security' ); // Making sure of HTTPS.
	add_action( 'send_headers', __NAMESPACE__ . '\\set_content_security_policy' ); // Policy for content security.
	add_action( 'send_headers', __NAMESPACE__ . '\\set_x_content_type_options' ); // Option of X Content Type.
	add_action( 'send_headers', __NAMESPACE__ . '\\set_referrer_policy' ); // Policy for referrer.
	add_action( 'send_headers', __NAMESPACE__ . '\\set_permissions_policy' ); // Policy for permissions.
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
	/**
	 * IMPORTANT NOTICE
	 *
	 * Because of many errors being watched in the WMF site, the current
	 * implementation of our Content-Security-Policy (CSP) is having the 'unsafe-inline'
	 * directive for the scripts and also the styles. This, even resolving the errors,
	 * is allowing potential Cross-Site Scripting (XSS) attacks.
	 *
	 * TODO:
	 *
	 * 1. Removal of 'unsafe-inline' directive for scripts and styles: This directive
	 *    is making less the efficiency of the CSP against attacks of XSS.
	 *
	 * 2. Alternatives being proposed:
	 *
	 *    a. Using a nonce: This would need adding a nonce unique to each inline script/style tag.
	 *
	 *    b. Using a hash: This involves mapping and making hashes for all inline scripts/styles
	 *       and including these hashes in the CSP as exceptions.
	 *
	 * Please make a note that both strategies will demand substantial effort and testing to make
	 * sure that functionality of site remains not affected.
	 */

	$csp_allowed = [
		"default-src 'self'",
		"script-src 'self' 'unsafe-inline' https://piwik.wikimedia.org https://stats.wp.com https://pixel.wp.com https://www.youtube.com https://player.vimeo.com http://localhost https://localhost http://localhost:8080",
		"frame-src 'self' https://www.youtube.com https://player.vimeo.com",
		"style-src 'self' 'unsafe-inline'",
		"img-src 'self' data: https://piwik.wikimedia.org",
		"font-src 'self' data:",
		"connect-src 'self' wss://public-api.wordpress.com",
	];

	header( "Content-Security-Policy: " . implode( '; ', $csp_allowed ) );
	header( 'X-Frame-Options: SAMEORIGIN' );
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

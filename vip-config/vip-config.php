<?php

/**
 * Hi there, VIP dev!
 *
 * vip-config.php is where you put things you'd usually put in wp-config.php. Don't worry about database settings
 * and such, we've taken care of that for you. This is just for if you need to define an API key or something
 * of that nature.
 *
 * Happy Coding!
 *
 * - The WordPress.com VIP Team
 **/

$http_host          = $_SERVER['HTTP_HOST'] ?? '';
$request_uri        = $_SERVER['REQUEST_URI'] ?? '';
$redirect_to_domain = 'wikimediafoundation.org';
$redirect_domains   = [
    'www.wikimediafoundation.org',
    'm.wikimediafoundation.org',
	'wikimediafoundation.com',
	'wikimediafoundation.info',
	'wikimediafoundation.net',
	'wikipediazero.org',
];
if (
    	'/cache-healthcheck?' !== $request_uri &&
    	$redirect_to_domain !== $http_host  &&
    	in_array( $http_host, $redirect_domains, true )
    ) {
    header( 'Location: https://' . $redirect_to_domain . $request_uri, true, 301 );
    exit;
}
define( 'VIP_MAINTENANCE_MODE', false );

// Enforce 2FA on Wednesday, Sept. 4 at 1800 UTC
define( 'VIP_2FA_TIME_GATE', strtotime( '2019-09-04 18:00:00' ) );

// Provide Gravity Forms license key through config rather than requiring users to manually input it.
define( 'GF_LICENSE_KEY', '112b41c94e39756b66180bcd20520e9e' );

// Redirect soundlogo.wikimedia.org
if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
    $http_host   = $_SERVER['HTTP_HOST']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $request_uri = $_SERVER['REQUEST_URI']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

    $redirect_to_domain = 'wikimediafoundation.org/about/soundlogo';
    $redirect_domains   = array(
        'soundlogo.wikimedia.org',
    );

    /**
     * Safety checks for redirection:
     * 1. Don't redirect for '/cache-healthcheck?' or monitoring will break
     * 2. Don't redirect in WP CLI context
     */
    if (
            '/cache-healthcheck?' !== $request_uri && // Do not redirect VIP's monitoring.
            ! ( defined( 'WP_CLI' ) && WP_CLI ) && // Do not redirect WP-CLI commands.
            $redirect_to_domain !== $http_host && in_array( $http_host, $redirect_domains, true )
        ) {
        header( 'Location: https://' . $redirect_to_domain . $request_uri, true, 301 );
        exit;
    }
}
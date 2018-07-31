<?php
/**
 * Modifications for Safe Redirect plugin.
 *
 * @package wmfoundation
 */

// Make redirects case sensitive.
add_filter( 'srm_case_insensitive_redirects', '__return_false' );

/**
 * Increase redirect limit.
 *
 * return int
 */
function wmfoundation_srm_max_redirects() {
	return 250;
}
add_filter( 'srm_max_redirects', 'wmfoundation_srm_max_redirects' );

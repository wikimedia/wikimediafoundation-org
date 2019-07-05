<?php
/**
 * Modifications for Safe Redirect plugin.
 *
 * @package shiro
 */

// Make redirects case sensitive.
add_filter( 'srm_case_insensitive_redirects', '__return_false' );

/**
 * Increase redirect limit.
 *
 * @return int
 */
function wmf_srm_max_redirects() {
	return 250;
}
add_filter( 'srm_max_redirects', 'wmf_srm_max_redirects' );

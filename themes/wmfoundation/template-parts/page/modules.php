<?php
/**
 * Loads requested page modules.
 *
 * @package wmfoundation
 */

$modules = wmf_get_template_data();

if ( empty( $modules ) || ! is_array( $modules ) ) {
	return;
}

foreach ( $modules as $module ) {
	get_template_part( 'template-parts/page/page', $module );
}

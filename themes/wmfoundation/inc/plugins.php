<?php
/**
 * Loads plugins used by the theme.
 *
 * @package wmfoundation
 */

if ( function_exists( 'wpcom_vip_load_plugin' ) ) {

	// Field Manager.
	wpcom_vip_load_plugin( 'fieldmanager' );

	// Safe SVG.
	wpcom_vip_load_plugin( 'safe-svg' );

	wpcom_vip_load_plugin( 'co-authors-plus' );
}

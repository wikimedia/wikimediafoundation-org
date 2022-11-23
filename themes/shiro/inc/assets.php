<?php
/**
 * Functionality related to finding and loading assets.
 */

namespace WMF\Assets;

use Asset_Loader\Manifest;

/**
 * Get asset manifest path.
 * Uses dev server if running, otherwise loads from production asset manifest.
 * @return string|null
 */
function get_manifest_path() {
	return  Manifest\get_active_manifest( [
		get_template_directory() . '/assets/dist/asset-manifest.json',
		get_template_directory() . '/assets/dist/production-asset-manifest.json'
	] );
}

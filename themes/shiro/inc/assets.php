<?php
/**
 * Functionality related to finding and loading assets.
 */

namespace WMF\Assets;

use Asset_Loader\Manifest;

/**
 * Get the array of valid theme build manifests.
 *
 * @return array
 */
function get_manifests() : array {
	return [
		get_template_directory() . '/assets/dist/development-asset-manifest.json',
		get_template_directory() . '/assets/dist/production-asset-manifest.json',
	];
}

/**
 * Check through the available manifests to find the first which includes the
 * target asset. This allows some assets to be loaded from a running DevServer
 * while others load from production files on disk.
 *
 * @param string $target_asset Desired asset within the manifest.
 * @return string|null
 */
function get_manifest_path( $target_asset ) {
	foreach ( get_manifests() as $manifest_path ) {
		$asset_uri = Manifest\get_manifest_resource( $manifest_path, $target_asset );
		if ( ! empty( $asset_uri ) ) {
			return $manifest_path;
		}
	}
}

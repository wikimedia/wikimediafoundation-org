<?php
/**
 * Handle enqueuing block assets.
 */

namespace Simple_Editorial_Comments\Assets;

use Asset_Loader;

/**
 * Connect namespace functions to actions & hooks.
 */
function bootstrap() : void {
	if ( ! function_exists( 'Asset_Loader\\enqueue_asset' ) ) {
		trigger_error( 'Simple Editorial Comments expects humanmade/asset-loader to be installed and active' );
		return;
	}

	add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_assets' );
}

/**
 * Enqueue the JS bundle in the block editor.
 */
function enqueue_assets() : void {
	$plugin_path = trailingslashit( plugin_dir_path( dirname( __FILE__, 1 ) ) );

	$manifest = Asset_Loader\Manifest\get_active_manifest( [
		$plugin_path . 'build/development-asset-manifest.json',
		$plugin_path . 'build/production-asset-manifest.json',
	] );

	Asset_Loader\enqueue_asset(
		$manifest,
		'simple-editorial-comments.js',
		[
			'dependencies' => [
				'wp-blocks',
				'wp-components',
				'wp-edit-post',
				'wp-element',
				'wp-i18n',
			],
			'handle'  => 'simple-editorial-comments',
		]
	);
	Asset_Loader\enqueue_asset(
		$manifest,
		'simple-editorial-comments.css',
		[
			'dependencies' => [],
			'handle' => 'simple-editorial-comments',
		]
	);
}

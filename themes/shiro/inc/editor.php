<?php
/**
 * Functionality related to supporting the stepped migration to block editor content.
 */

namespace WMF\Editor;

use Asset_Loader;
use WMF\Assets;

/**
 * Bootstrap hooks relevant to the block editor.
 */
function bootstrap() {
	add_filter( 'body_class', __NAMESPACE__ . '\\body_class' );
	add_filter( 'allowed_block_types', __NAMESPACE__ . '\\filter_blocks' );
	add_action( 'after_setup_theme', __NAMESPACE__ . '\\add_theme_supports' );
	add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_block_editor_assets' );
	add_filter( 'block_categories', __NAMESPACE__ . '\\add_block_categories' );
}

/**
 * Add "has-blocks" class to posts with block content.
 *
 * Used to support a stepped migration to the new block editor styles.
 *
 * @param string[] $body_classes Body classes.
 * @return string[] Updated classlist array.
 */
function body_class( $body_classes ) {
	if ( is_singular() && has_blocks( get_queried_object_id() ) ) {
		$body_classes[] = 'has-blocks';
	}

	return $body_classes;
}

/**
 * Filter the allowed blocks to an include list of blocks that we deem as
 * relevant to the project. Can return true to include all blocks, or false to
 * include no blocks.
 *
 * @param bool|string[] $allowed_blocks
 * @return bool|string[]
 */
function filter_blocks( $allowed_blocks ) {
	return [
		// Custom blocks
		'shiro/banner',
		'shiro/blog-list',
		'shiro/blog-post-heading',
		'shiro/card',
		'shiro/home-page-hero',
		'shiro/stairs',
		'shiro/stair',
		'shiro/landing-page-hero',
		'shiro/mailchimp-subscribe',

		// Core blocks
		'core/paragraph',
		'core/image',
		'core/heading',
		'core/list',
		'core/table',
		'core/audio',
		'core/video',
		'core/file',
		'core/columns',
		'core/column',
		'core/group',
		'core/separator',
		'core/spacer',
		'core/embed',
		'core/freeform',
		'core/missing',
		'core/block',
		'core/button',
		'core/buttons',
		'core/latest-posts',
	];
}

/**
 * Add theme supports for editor functionality.
 */
function add_theme_supports() {

	// Add support and default values for block editor styles.
	add_theme_support( 'editor-styles' );
	$css_file = is_rtl() ? 'editor-style.rtl.css' : 'editor-style.css';
	add_editor_style( $css_file );

	// Define alternate font sizes selectable in the editor (the default
	// for body copy is 18px / 1.75 on desktop; 16px / 1.75 on mobile).
	add_theme_support( 'editor-font-sizes', [
		[ 'name' => __( 'Small', 'shiro' ),   'shortName' => __( 'S', 'shiro' ),  'size' => 14, 'slug' => 'small'   ],
		[ 'name' => __( 'Medium', 'shiro' ),  'shortName' => __( 'M', 'shiro' ),  'size' => 20, 'slug' => 'medium'  ],
		[ 'name' => __( 'Large', 'shiro' ),   'shortName' => __( 'L', 'shiro' ),  'size' => 24, 'slug' => 'large'   ],
		[ 'name' => __( 'X-Large', 'shiro' ), 'shortName' => __( 'XL', 'shiro' ), 'size' => 32, 'slug' => 'xlarge'  ],
		[ 'name' => __( 'Jumbo', 'shiro' ),   'shortName' => __( 'J', 'shiro' ),  'size' => 40, 'slug' => 'jumbo'   ],
	] );

	// Remove the ability to set custom font sizes in the editor.
	add_theme_support( 'disable-custom-font-sizes' );

	// Define colors selectable in the editor.
	add_theme_support( 'editor-color-palette', [
		[ 'name' => __( 'Base 0', 'shiro' ),    'slug' => 'base0',    'color' => '#000000' ],
		[ 'name' => __( 'Base 10', 'shiro' ),   'slug' => 'base10',   'color' => '#202122' ],
		[ 'name' => __( 'Base 20', 'shiro' ),   'slug' => 'base20',   'color' => '#54595d' ],
		[ 'name' => __( 'Base 30', 'shiro' ),   'slug' => 'base30',   'color' => '#72777d' ],
		[ 'name' => __( 'Base 50', 'shiro' ),   'slug' => 'base50',   'color' => '#a2a9b1' ],
		[ 'name' => __( 'Base 70', 'shiro' ),   'slug' => 'base70',   'color' => '#c8ccd1' ],
		[ 'name' => __( 'Base 80', 'shiro' ),   'slug' => 'base80',   'color' => '#eaecf0' ],
		[ 'name' => __( 'Base 90', 'shiro' ),   'slug' => 'base90',   'color' => '#f8f9fa' ],
		[ 'name' => __( 'Base 100', 'shiro' ),  'slug' => 'base100',  'color' => '#ffffff' ],
		[ 'name' => __( 'Accent 30', 'shiro' ), 'slug' => 'accent30', 'color' => '#2a4b8d' ],
		[ 'name' => __( 'Accent 50', 'shiro' ), 'slug' => 'accent50', 'color' => '#3366cc' ],
		[ 'name' => __( 'Accent 90', 'shiro' ), 'slug' => 'accent90', 'color' => '#eaf3ff' ],
		[ 'name' => __( 'Red 30', 'shiro' ),    'slug' => 'red30',    'color' => '#b32424' ],
		[ 'name' => __( 'Red 50', 'shiro' ),    'slug' => 'red50',    'color' => '#dd3333' ],
		[ 'name' => __( 'Red 90', 'shiro' ),    'slug' => 'red90',    'color' => '#fee7e6' ],
		[ 'name' => __( 'Yellow 30', 'shiro' ), 'slug' => 'yellow30', 'color' => '#ac6600' ],
		[ 'name' => __( 'Yellow 50', 'shiro' ), 'slug' => 'yellow50', 'color' => '#ffcc33' ],
		[ 'name' => __( 'Yellow 90', 'shiro' ), 'slug' => 'yellow90', 'color' => '#fef6e7' ],
		[ 'name' => __( 'Green 30', 'shiro' ),  'slug' => 'green30',  'color' => '#14866d' ],
		[ 'name' => __( 'Green 50', 'shiro' ),  'slug' => 'green50',  'color' => '#00af89' ],
		[ 'name' => __( 'Green 90', 'shiro' ),  'slug' => 'green90',  'color' => '#d5fdf4' ],
	] );

	// Disable custom color and gradient selection in the editor.
	add_theme_support( 'disable-custom-colors' );
	add_theme_support( 'editor-gradient-presets', [] );
	add_theme_support( 'disable-custom-gradients' );

	// Allow for "wide" and "full" alignment options on blocks that support them.
	add_theme_support( 'align-wide' );
}

function enqueue_block_editor_assets() {

	$manifest = Assets\get_manifest_path();

	Asset_Loader\enqueue_asset(
		$manifest,
		'editor.js',
		[
			'dependencies' => [
				'wp-dom-ready',
				'wp-i18n',
				'wp-blocks',
				'wp-compose',
				'wp-hooks',
				'wp-token-list',
			],
			'handle' => 'shiro_editor_js',
		]
	);

	Asset_Loader\enqueue_asset(
		$manifest,
		is_rtl() ? 'editor.rtl.css' : 'editor.css',
		[
			'handle' => 'shiro_editor_css',
		]
	);
}

/**
 * Add categories relevant to Wikimedia
 *
 * @param array $categories Original categories
 * @return array Modified categories
 */
function add_block_categories( $categories ) {
	return array_merge(
		array(
			array(
				'slug' => 'wikimedia',
				'title' => __( 'Wikimedia', 'shiro' ),
			),
		),
		$categories
	);
}

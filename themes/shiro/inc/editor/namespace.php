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
	add_filter( 'allowed_block_types', __NAMESPACE__ . '\\filter_blocks', 10, 2 );
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
 * @param \WP_Post      $post
 *
 * @return bool|string[]
 */
function filter_blocks( $allowed_blocks, \WP_Post $post ) {
	$blocks = [
		// Custom blocks
		'shiro/banner',
		'shiro/blog-list',
		'shiro/card',
		'shiro/contact',
		'shiro/double-heading',
		'shiro/share-article',
		'shiro/spotlight',
		'shiro/stairs',
		'shiro/stair',
		'shiro/toc',
		'shiro/toc-columns',
		'shiro/tweet-this',
		'shiro/mailchimp-subscribe',
		'shiro/inline-languages',
		'shiro/external-link',
		'shiro/profile',
		'shiro/profile-list',
		'shiro/unseen-artist',
		'shiro/unseen-facts',
		'shiro/unseen-footer',
		'shiro/unseen-intro',

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
		'core/quote',
	];

	if ( $post->post_type === 'post' ) {
		$blocks[] = 'shiro/read-more-categories';
		$blocks[] = 'shiro/blog-post-heading';
	}

	if ( $post->post_type === 'page' ) {
		$blocks[] = 'shiro/home-page-hero';
		$blocks[] = 'shiro/landing-page-hero';
	}

	return $blocks;
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
		[ 'name' => __( 'Small', 'shiro-admin' ),   'shortName' => __( 'S', 'shiro-admin' ),  'size' => 14, 'slug' => 'small'   ],
		[ 'name' => __( 'Medium', 'shiro-admin' ),  'shortName' => __( 'M', 'shiro-admin' ),  'size' => 20, 'slug' => 'medium'  ],
		[ 'name' => __( 'Large', 'shiro-admin' ),   'shortName' => __( 'L', 'shiro-admin' ),  'size' => 24, 'slug' => 'large'   ],
		[ 'name' => __( 'X-Large', 'shiro-admin' ), 'shortName' => __( 'XL', 'shiro-admin' ), 'size' => 32, 'slug' => 'xlarge'  ],
		[ 'name' => __( 'Jumbo', 'shiro-admin' ),   'shortName' => __( 'J', 'shiro-admin' ),  'size' => 40, 'slug' => 'jumbo'   ],
	] );

	// Remove the ability to set custom font sizes in the editor.
	add_theme_support( 'disable-custom-font-sizes' );

	// Define colors selectable in the editor.
	add_theme_support( 'editor-color-palette', [
		[ 'name' => __( 'Base 0', 'shiro-admin' ),    'slug' => 'base0',    'color' => '#000000' ],
		[ 'name' => __( 'Base 10', 'shiro-admin' ),   'slug' => 'base10',   'color' => '#202122' ],
		[ 'name' => __( 'Base 20', 'shiro-admin' ),   'slug' => 'base20',   'color' => '#54595d' ],
		[ 'name' => __( 'Base 30', 'shiro-admin' ),   'slug' => 'base30',   'color' => '#72777d' ],
		[ 'name' => __( 'Base 50', 'shiro-admin' ),   'slug' => 'base50',   'color' => '#a2a9b1' ],
		[ 'name' => __( 'Base 70', 'shiro-admin' ),   'slug' => 'base70',   'color' => '#c8ccd1' ],
		[ 'name' => __( 'Base 80', 'shiro-admin' ),   'slug' => 'base80',   'color' => '#eaecf0' ],
		[ 'name' => __( 'Base 90', 'shiro-admin' ),   'slug' => 'base90',   'color' => '#f8f9fa' ],
		[ 'name' => __( 'Base 100', 'shiro-admin' ),  'slug' => 'base100',  'color' => '#ffffff' ],
		[ 'name' => __( 'Blue 50', 'shiro-admin' ),   'slug' => 'blue50',   'color' => '#3a25ff' ],
		[ 'name' => __( 'Blue 90', 'shiro-admin' ),   'slug' => 'blue90',   'color' => '#eeeaff' ],
		[ 'name' => __( 'Red 50', 'shiro-admin' ),    'slug' => 'red50',    'color' => '#d40356' ],
		[ 'name' => __( 'Red 90', 'shiro-admin' ),    'slug' => 'red90',    'color' => '#fbe9f1' ],
		[ 'name' => __( 'Yellow 50', 'shiro-admin' ), 'slug' => 'yellow50', 'color' => '#fffd33' ],
		[ 'name' => __( 'Yellow 90', 'shiro-admin' ), 'slug' => 'yellow90', 'color' => '#fef6e7' ],
		[ 'name' => __( 'Light Blue', 'shiro-admin' ), 'slug' => 'light-blue', 'color' => '#effafd' ],
		[ 'name' => __( 'Wiki Blue', 'shiro-admin' ), 'slug' => 'wiki-blue', 'color' => '#3366CC' ],
	] );

	// Disable custom color and gradient selection in the editor.
	add_theme_support( 'disable-custom-colors' );
	add_theme_support( 'editor-gradient-presets', [] );
	add_theme_support( 'disable-custom-gradients' );

	// Allow for "wide" and "full" alignment options on blocks that support them.
	add_theme_support( 'align-wide' );
}

/**
 * Return the post that is being edited.
 *
 * @return false|array|\WP_Post|null
 */
function get_admin_post() {
	// 1. We can't verify a nonce because we didn't create this request.
	// 2. A core sanitization function isn't used, but the value is carefully checked.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$post_id            = $_GET['post'] ?? false;

	if ( is_numeric( $post_id ) && (int) $post_id > 0 ) {
		return get_post( $post_id );
	}

	return false;
}

/**
 * Determine whether the current admin post has blocks.
 */
function admin_post_has_blocks(): bool {
	$post = get_admin_post();

	return $post && has_blocks( $post->post_content );
}

/**
 * Determine whether the current admin post is a new post.
 */
function admin_post_is_new(): bool {
	$post = get_admin_post();

	return ! $post || $post->post_content === '';
}

/**
 * Determine whether the field manager meta boxes should be shown.
 */
function is_using_block_editor(): bool {
	return admin_post_is_new() || admin_post_has_blocks();
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
				'wp-block-editor',
				'wp-components',
				'wp-compose',
				'wp-element',
				'wp-hooks',
				'wp-token-list',
			],
			'handle' => 'shiro_editor_js',
		]
	);

	$languages = wmf_get_translations();

	wp_localize_script(
		'shiro_editor_js',
		'shiroEditorVariables',
		array(
			'themeUrl'      => get_stylesheet_directory_uri(),
			'languages'     => $languages,
			'siteLanguage'  => $languages[0]['shortname'],
			'wmfIsMainSite' => wmf_is_main_site(),
		)
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
				'title' => __( 'Wikimedia', 'shiro-admin' ),
			),
		),
		$categories
	);
}

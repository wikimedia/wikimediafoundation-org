<?php
/**
 * Modifications to the block editor for Wikimedia.
 */

namespace WMF\Editor;

/**
 * Bootstrap hooks relevant to the block editor.
 */
function bootstrap() {
	add_action( 'allowed_block_types', __NAMESPACE__ . '\\filter_blocks' );
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
		'shiro/blog-post-heading',
		'shiro/cards',
		'shiro/card',
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

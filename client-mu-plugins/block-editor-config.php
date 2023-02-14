<?php
/**
 * Block editor configuration.
 *
 * The shiro theme has a very restricted allowlist of blocks that are enabled.
 * If we want to make use of blocks not defined in core or the theme, this is
 * the place to specify them.
 */

add_filter( 'allowed_block_types_all', 'wmf_filter_shiro_allowed_block_types', 11 ); // After shiro theme filter.

/**
 * Filter the allowed block types.
 *
 * @param [] $allowed_block_types Array of block types which can be registered for the editor.
 * @return [] Filtered array of allowed block types.
 */
function wmf_filter_shiro_allowed_block_types( $allowed_block_types ) {

	// The allowed block types can be "true", meaning all blocks are enabled.
	if ( ! is_array( $allowed_block_types ) ) {
		return $allowed_block_types;
	}

	return array_merge(
		$allowed_block_types,
		[
			// Supported third-party blocks
			'vegalite-plugin/visualization',
			'vegalite-plugin/responsive-container',
		]
	);
}

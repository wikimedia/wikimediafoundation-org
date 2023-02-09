<?php
/**
 * Register blocks on the PHP side.
 */

namespace Simple_Editorial_Comments\Blocks;

/**
 * Connect namespace functions to actions & hooks.
 */
function bootstrap() : void {
	add_action( 'init', __NAMESPACE__ . '\\register_blocks' );
}

/**
 * Register our block with the PHP framework.
 */
function register_blocks() : void {
	register_block_type_from_metadata(
		dirname( __DIR__ ) . '/src/blocks/editorial-comment',
		[
			'render_callback' => __NAMESPACE__ . '\\do_not_render',
		]
	);
	register_block_type_from_metadata(
		dirname( __DIR__ ) . '/src/blocks/hidden-group',
		[
			'render_callback' => __NAMESPACE__ . '\\do_not_render',
		]
	);
}

/**
 * A render callback that does not render. Prevents either editorial block from
 * displaying on the site frontend.
 *
 * @param array $attributes Block attributes.
 * @return string Empty string.
 */
function do_not_render( $attributes ) : string {
	return '';
}

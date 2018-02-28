<?php
/**
 * Handles focus blocks loop.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['blocks'] ) || ! is_array( $template_args['blocks'] ) ) {
	return;
}

$block_count = 0;

foreach ( $template_args['blocks'] as $block ) {
	if ( empty( $block ) || ! is_array( $block ) ) {
		continue;
	}

	$block_count++;

	$block['class'] = 0 === $block_count % 2 ? 'img-right-content-left' : 'img-left-content-right';

	wmf_get_template_part( 'template-parts/modules/focus-blocks/block', $block );
}

<?php
/**
 * Handles focus blocks loop.
 *
 * @package shiro
 */

$template_args = $args;

if ( empty( $template_args['blocks'] ) || ! is_array( $template_args['blocks'] ) ) {
	return;
}

$block_count = 0;

?><div class="mw-980 flex flex-medium flex-space-between"><?php
foreach ( $template_args['blocks'] as $block ) {
	if ( empty( $block ) || ! is_array( $block ) ) {
		continue;
	}

	$block_count++;

	$block['class'] = 0 === $block_count % 2 ? 'img-right-content-left' : 'img-left-content-right';

	get_template_part( 'template-parts/modules/focus-blocks/block', null, $block );
}
?></div><?php

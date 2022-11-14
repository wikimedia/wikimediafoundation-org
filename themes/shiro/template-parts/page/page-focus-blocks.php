<?php
/**
 * Sets up page focus blocks module.
 *
 * Used on the Home Page template.
 *
 * @package shiro
 */

$template_args = array(
	'blocks' => get_post_meta( get_the_ID(), 'focus_blocks', true ),
);
get_template_part( 'template-parts/modules/focus-blocks/blocks', null, $template_args );

<?php
/**
 * Sets up page focus blocks module.
 *
 * Used on the Home Page template.
 *
 * @package wmfoundation
 */

$template_args = array(
	'blocks' => get_post_meta( get_the_ID(), 'focus_blocks', true ),
);
wmf_get_template_part( 'template-parts/modules/focus-blocks/blocks', $template_args );

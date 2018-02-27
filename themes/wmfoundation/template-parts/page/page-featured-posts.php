<?php
/**
 * Sets up Featured Posts module.
 *
 * Used on Landing Page and Home Page template.
 *
 * @package wmfoundation
 */

$template_args = array(
	'context'  => get_the_ID(),
	'subtitle' => get_post_meta( get_the_ID(), 'featured_post_sub_title', true ),
	'class'    => 'bg-black',
);
wmf_get_template_part( 'template-parts/modules/featured/posts', $template_args );

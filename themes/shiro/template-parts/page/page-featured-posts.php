<?php
/**
 * Sets up Featured Posts module.
 *
 * Used on Landing Page and Home Page template.
 *
 * @package shiro
 */

$context = is_front_page() ? 'home' : get_the_ID();

$template_args = array(
	'context'  => $context,
	'subtitle' => get_post_meta( get_the_ID(), 'featured_post_sub_title', true ),
);
get_template_part( 'template-parts/modules/featured/posts', null, $template_args );

<?php
/**
 * Sets up page intro.
 *
 * Event though this is "page" it can also be used on News Posts
 *
 * @package shiro
 */

$intro = get_the_content();
\WMF\Images\Credits::get_instance( get_the_ID() )->set_images_from_content( $intro );
$template_args = array(
	'intro'  => $intro,
	'button' => get_post_meta( get_the_ID(), 'intro_button', true ),
);
get_template_part( 'template-parts/modules/intro/page', null, $template_args );

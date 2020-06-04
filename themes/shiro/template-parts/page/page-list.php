<?php
/**
 * Pulls in data for list template
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'list', true );
if ( ! empty( $template_args[0]['description'] ) ) {
	\WMF\Images\Credits::get_instance( get_the_ID() )->set_images_from_content( $template_args[0]['description'] );
}

wmf_get_template_part( 'template-parts/modules/list/list', $template_args );

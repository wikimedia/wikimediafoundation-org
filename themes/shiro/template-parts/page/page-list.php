<?php
/**
 * Pulls in data for list template
 *
 * @package shiro
 */

$template_args = (array) get_post_meta( get_the_ID(), 'list', true );
foreach ( $template_args as $list_section ) {
	if ( ! empty( $list_section['description'] ) ) {
		\WMF\Images\Credits::get_instance( get_the_ID() )->set_images_from_content( $list_section['description'] );
	}
}

get_template_part( 'template-parts/modules/list/list', null, $template_args );

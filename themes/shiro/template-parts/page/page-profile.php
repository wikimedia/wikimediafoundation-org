<?php
/**
 * Set up the featured profile module
 *
 * Pull profile ID and other meta and pass it along to template
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'featured_profile', true );


if ( ! empty( $template_args ) && ! empty( $template_args['profile_id'] ) ) {
	$template_args['image_id'] = get_post_thumbnail_id( $template_args['profile_id'] );
	$template_args['link']     = get_the_permalink( $template_args['profile_id'] );
	wmf_get_template_part( 'template-parts/modules/profile/card', $template_args );
}

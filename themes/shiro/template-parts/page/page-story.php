<?php
/**
 * Set up the featured story module
 *
 * Pull story ID and other meta and pass it along to template
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'featured_story', true );


if ( ! empty( $template_args ) && ! empty( $template_args['story_id'] ) ) {
	$template_args['image_id'] = get_post_thumbnail_id( $template_args['story_id'] );
	$template_args['link']     = get_the_permalink( $template_args['story_id'] );
	get_template_part( 'template-parts/modules/stories/card', null, $template_args );
}

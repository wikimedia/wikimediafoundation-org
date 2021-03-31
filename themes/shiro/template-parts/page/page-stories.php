<?php
/**
 * Set up the stories module
 *
 * Pull stories meta and pass it along to template
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'stories', true );

$rand_translation = wmf_get_random_translation(
	'stories', array(
		'source' => 'meta',
	)
);

$template_args['rand_translation_title'] = empty( $rand_translation['pre_heading'] ) ? '' : $rand_translation['pre_heading'];

if ( ! empty( $template_args ) ) {
	get_template_part( 'template-parts/modules/stories/list', null, $template_args );
}

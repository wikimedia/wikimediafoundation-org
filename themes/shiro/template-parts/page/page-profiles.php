<?php
/**
 * Set up the profiles module
 *
 * Pull profiles meta and pass it along to template
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'profiles', true );

$rand_translation = wmf_get_random_translation(
	'profiles', array(
		'source' => 'meta',
	)
);

$template_args['rand_translation_title'] = empty( $rand_translation['pre_heading'] ) ? '' : $rand_translation['pre_heading'];

if ( ! empty( $template_args ) ) {
	get_template_part( 'template-parts/modules/profiles/list', null, $template_args );
}

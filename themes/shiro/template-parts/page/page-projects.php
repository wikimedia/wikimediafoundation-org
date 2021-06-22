<?php
/**
 * Sets up page projects module.
 *
 * Used on the Home Page template.
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'projects_module', true );

$rand_translation = wmf_get_random_translation(
	'projects_module', array(
		'source' => 'meta',
	)
);

$template_args['rand_translation_title'] = empty( $rand_translation['pre_heading'] ) ? '' : $rand_translation['pre_heading'];

get_template_part( 'template-parts/modules/projects/projects', null, $template_args );

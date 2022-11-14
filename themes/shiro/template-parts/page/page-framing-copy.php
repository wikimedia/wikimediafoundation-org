<?php
/**
 * Sets up page framing copy module.
 *
 * Used on Landing Page and Home Page template.
 *
 * @package shiro
 */

$framing_copy  = get_post_meta( get_the_ID(), 'framing_copy', true );
$template_args = array(
	'pre_heading' => isset( $framing_copy['pre_heading'] ) ? $framing_copy['pre_heading'] : '',
	'heading'     => isset( $framing_copy['heading'] ) ? $framing_copy['heading'] : '',
	'modules'     => isset( $framing_copy['copy'] ) ? $framing_copy['copy'] : array(),
);

$rand_translation = wmf_get_random_translation(
	'framing_copy', array(
		'source' => 'meta',
	)
);

$template_args['rand_translation_title'] = empty( $rand_translation['pre_heading'] ) ? '' : $rand_translation['pre_heading'];

get_template_part( 'template-parts/modules/section/framing-copy', null, $template_args );

<?php
/**
 * Sets up page framing copy module.
 *
 * Used on Landing Page and Home Page template.
 *
 * @package wmfoundation
 */

$framing_copy  = get_post_meta( get_the_ID(), 'framing_copy', true );
$template_args = array(
	'pre_heading' => isset( $framing_copy['pre_heading'] ) ? $framing_copy['pre_heading'] : '',
	'heading'     => isset( $framing_copy['heading'] ) ? $framing_copy['heading'] : '',
	'modules'     => isset( $framing_copy['copy'] ) ? $framing_copy['copy'] : array(),
);
wmf_get_template_part( 'template-parts/modules/section/framing-copy', $template_args );

<?php
/**
 * Sets up page facts module.
 *
 * Used on Landing Page and Home Page template.
 *
 * @package wmfoundation
 */

$template_args = get_post_meta( get_the_ID(), 'page_facts', true );

if ( ! empty( $template_args ) ) {
	$facts = empty( $template_args['facts'] ) ? array() : $template_args['facts'];

	if ( ! empty( $facts ) && is_array( $facts ) ) {
		if ( 1 === count( $facts ) ) {
			$template_args = $template_args + $facts[0];
			wmf_get_template_part( 'template-parts/modules/fact/single', $template_args );
		} else {
			wmf_get_template_part( 'template-parts/modules/fact/multiple', $template_args );
		}
	}
}

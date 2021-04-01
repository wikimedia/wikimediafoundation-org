<?php
/**
 * Sets up page Listincs module.
 *
 * Used on Landing Page template and Profile Page.
 *
 * @package shiro
 */

$template_args = get_post_meta( get_the_ID(), 'listings', true );

if ( ! empty( $template_args ) ) {
	$listings = empty( $template_args['listings'] ) ? array() : $template_args['listings'];

	if ( ! empty( $listings ) && is_array( $listings ) ) {
		if ( 1 === count( $listings ) ) {
			$template_args = $template_args + $listings[0];
			get_template_part( 'template-parts/modules/employment/single', null, $template_args );
		} else {
			get_template_part( 'template-parts/modules/employment/multiple', null, $template_args );
		}
	}
}

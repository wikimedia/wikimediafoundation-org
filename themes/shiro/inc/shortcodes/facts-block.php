<?php
/**
 * Placeholder shortcode for the FM facts field.
 */

namespace WMF\Shortcodes\Facts_Block;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_facts_block', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_facts_block] wrapper shortcode.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string
 */
function shortcode_callback( $atts = [] ) {
	$html = shortcode_content( get_the_ID() );

	return wp_kses_post( $html );
}

/**
 *
 * @param int $post_id The post ID.
 *
 * @return string
 */
function shortcode_content( int $post_id ) : string {
	$template_args = get_post_meta( get_the_ID(), 'page_facts', true );
	if ( empty( $template_args ) || empty( $template_args['facts'] ) ) {
		return '';
	}

	if ( 1 === count( $template_args['facts'] ) ) {
		$template_args += $template_args['facts'][0];
		ob_start();
		wmf_get_template_part( 'template-parts/modules/fact/single', $template_args );
		return (string) ob_get_clean();
	}
	ob_start();
	wmf_get_template_part( 'template-parts/modules/fact/multiple', $template_args );
	return (string) ob_get_clean();
}

<?php
/**
 * Placeholder shortcode for the FM framing copy field.
 */

namespace WMF\Shortcodes\Framing;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_framing', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_framing] wrapper shortcode.
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
	$framing_copy  = get_post_meta( get_the_ID(), 'framing_copy', true );
	$template_args = array(
		'pre_heading' => $framing_copy['pre_heading'] ?? '',
		'heading'     => $framing_copy['heading'] ?? '',
		'modules'     => $framing_copy['copy'] ?? array(),
	);

	$rand_translation = wmf_get_random_translation(
		'framing_copy',
		array(
			'source' => 'meta',
		)
	);

	$template_args['rand_translation_title'] = empty( $rand_translation['pre_heading'] ) ? '' : $rand_translation['pre_heading'];

	ob_start();

	wmf_get_template_part( 'template-parts/modules/section/framing-copy', $template_args );

	return (string) ob_get_clean();
}

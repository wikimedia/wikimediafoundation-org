<?php
/**
 * Placeholder shortcode for the FM lists field.
 */

namespace WMF\Shortcodes\Connect;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_connect', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_connect] wrapper shortcode.
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
 * Render the shortcode HTML.
 *
 * @param int $post_id The post ID.
 *
 * @return string
 */
function shortcode_content( int $post_id ) : string {
	$template_args = get_post_meta( $post_id, 'connect', true );
	if ( empty( $template_args ) ) {
		return '';
	}

	$rand_translation = wmf_get_random_translation(
		'connect',
		array(
			'source' => 'meta',
		)
	);

	$template_args['rand_translation_title'] = empty( $rand_translation['pre_heading'] ) ? '' : $rand_translation['pre_heading'];

	ob_start();
	wmf_get_template_part( 'template-parts/modules/general/connect', $template_args );

	return (string) ob_get_clean();
}

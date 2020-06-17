<?php
/**
 * Placeholder shortcode for the FM profiles field.
 */

namespace WMF\Shortcodes\Profiles;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_profiles', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_profiles] wrapper shortcode.
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
	$template_args = get_post_meta( get_the_ID(), 'profiles', true );

	$rand_translation = wmf_get_random_translation(
		'profiles',
		array(
			'source' => 'meta',
		)
	);

	$template_args['rand_translation_title'] = empty( $rand_translation['pre_heading'] ) ? '' : $rand_translation['pre_heading'];

	if ( empty( $template_args ) ) {
		return '';
	}

	ob_start();

	wmf_get_template_part( 'template-parts/modules/profiles/list', $template_args );

	return (string) ob_get_clean();
}

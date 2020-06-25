<?php
/**
 * Placeholder shortcode for the FM projects field.
 */

namespace WMF\Shortcodes\Projects;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_projects', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_projects] wrapper shortcode.
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
	$template_args = get_post_meta( $post_id, 'projects_module', true );
	if ( empty( $template_args ) ) {
		return '';
	}

	$rand_translation = wmf_get_random_translation(
		'projects_module',
		array(
			'source' => 'meta',
		)
	);

	$template_args['rand_translation_title'] = empty( $rand_translation['pre_heading'] ) ? '' : $rand_translation['pre_heading'];

	ob_start();

	wmf_get_template_part( 'template-parts/modules/projects/projects', $template_args );

	return (string) ob_get_clean();
}

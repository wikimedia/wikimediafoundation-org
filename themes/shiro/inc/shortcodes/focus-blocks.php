<?php
/**
 * Placeholder shortcode for the FM focus field.
 */

namespace WMF\Shortcodes\Focus;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_focus', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_focus] wrapper shortcode.
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
	$template_args = array(
		'blocks' => get_post_meta( $post_id, 'focus_blocks', true ),
	);

	ob_start();

	wmf_get_template_part( 'template-parts/modules/focus-blocks/blocks', $template_args );

	return (string) ob_get_clean();
}

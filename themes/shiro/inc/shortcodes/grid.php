<?php
/**
 * Placeholder shortcode for the grid element.
 *
 * @package WMF
 */

namespace WMF\Shortcodes\Grid;

/**
 * Bootstrap
 */
function init() {
	add_shortcode( 'wmf_grid_col', __NAMESPACE__ . '\\shortcode_callback' );
}

/**
 * Define a [wmf_grid_col] wrapper shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @param string $content Contents.
 *
 * @return string
 */
function shortcode_callback( $atts = [], $content = null ) {
	if ( empty( $content ) ) {
		return '';
	}
	$atts    = shortcode_atts(
		[
			'size' => '', // Corresponds to the CSS class that defines the column width.
			'elem' => '', // A container element for the shortcode content.
		],
		$atts,
		'wmf_grid_col'
	);
	$content = do_shortcode( $content );
	$html    = '<div class="' . esc_attr( $atts['size'] ) . '">';
	if ( $atts['elem'] ) {
		$html .= '<' . $atts['elem'] . '>';
	}
	$html .= $content;
	if ( $atts['elem'] ) {
		$html .= '</' . $atts['elem'] . '>';
	}
	$html .= '</div>';

	return wp_kses_post( $html );
}

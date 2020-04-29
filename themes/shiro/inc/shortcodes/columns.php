<?php
/**
 * Define the [wmf_columns] and [wmf_column ... ] shortcodes.
 *
 * @package shiro
 *
 * @example
 *
 * [wmf_columns]
 * [wmf_column]
 * <table> ... </table>
 * [/wmf_column]
 * [wmf_column]
 * <img ... />
 * [/wmf_column]
 * [/wmf_columns]
 */

/**
 * Define a [wmf_column] wrapper shortcode that creates a dumb HTML wrapper.
 *
 * @param array  $atts    Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_column_shortcode_callback( $atts = [], $content = '' ) {
	$content = do_shortcode( $content );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );
	return '<div class="column">' . wp_kses_post( $content ) . '</div>';
}
add_shortcode( 'wmf_column', 'wmf_column_shortcode_callback' );

/**
 * Define a [wmf_columns] wrapper shortcode that creates the HTML wrapper for one
 * or more [wmf_column] shortcode items.
 *
 * Supports a "reverse" attribute which causes the columns to render in reverse
 * order on mobile. Usage:
 *
 *     [wmf_columns reverse] ... [/wmf_columns]
 *
 * @param array  $atts    Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_columns_shortcode_callback( $atts = [], $content = '' ) {
	$reverse = empty( $atts ) ? false : in_array( 'reverse', array_map( 'strtolower', $atts ), true );
	$content = do_shortcode( $content );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );
	$classes = $reverse ? 'columns-wrapper columns-mobile-reverse' : 'columns-wrapper';
	return '<div class="' . esc_attr( $classes ) . '">' . wp_kses_post( $content ) . '</div>';
}
add_shortcode( 'wmf_columns', 'wmf_columns_shortcode_callback' );

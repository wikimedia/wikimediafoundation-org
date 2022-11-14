<?php
/**
 * Define the [wmf_facts] and [wmf_fact ... ] shortcodes.
 *
 * @package shiro
 *
 * @example
 *
 * [wmf_facts]
 * [wmf_fact value="2" label="Requests granted" color="#00a99d"]
 * [wmf_fact value="35" label="Total requests"]
 * [/wmf_facts]
 */

/**
 * Define a [wmf_fact] wrapper shortcode that creates a dumb HTML wrapper.
 *
 * @param array  $atts    Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_fact_shortcode_callback( $atts, $content = '' ) {
	$defaults = [
		'value' => 0,
		'label' => '',
		'color' => '',
	];
	$atts     = shortcode_atts( $defaults, $atts, 'wmf_fact' );
	$style    = ! empty( $atts['color'] ) ? 'color:' . $atts['color'] : '';

	ob_start();
	?>
	<span class="fact-item" style="<?php echo esc_attr( $style ); ?>">
		<strong><?php echo esc_html( $atts['value'] ); ?></strong>
		<span class="fact-label"><?php echo esc_html( $atts['label'] ); ?></span>
	</span>
	<?php

	return (string) ob_get_clean();
}
add_shortcode( 'wmf_fact', 'wmf_fact_shortcode_callback' );

/**
 * Define a [wmf_facts] wrapper shortcode that creates the HTML wrapper for one
 * or more [wmf_fact] shortcode items.
 *
 * @param array  $atts    Shortcode attributes array.
 * @param string $content Content wrapped by shortcode.
 * @return string Rendered shortcode output.
 */
function wmf_facts_shortcode_callback( $atts = [], $content = '' ) {
	$content = do_shortcode( $content );
	$content = preg_replace( '/\s*<br\s*\/?>\s*/', '', $content );
	return '<div class="facts-wrapper">' . wp_kses_post( $content ) . '</div>';
}
add_shortcode( 'wmf_facts', 'wmf_facts_shortcode_callback' );

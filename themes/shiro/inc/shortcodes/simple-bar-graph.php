<?php
/**
 * Define the [simple_bar_graph] shortcode.
 *
 * @package shiro
 */

/**
 * Define a [wmf_fact] wrapper shortcode that creates a dumb HTML wrapper.
 *
 * @param array  $atts    Shortcode attributes array.
 * @return string Rendered shortcode output.
 */
function wmf_simple_bar_graph_shortcode_callback( $atts ) {
	$defaults = [
        'max'    => 0,
        'value'  => 0,
        'color'  => '#92278f',
        'width'  => 200,
        'height' => 14,
	];
	$atts     = shortcode_atts( $defaults, $atts, 'simple_bar_graph' );

	if ( empty( $atts['max'] ) ) {
		return '';
	}

	$viewbox = sprintf(
		'0 0 %d %d',
		$atts['width'],
		$atts['height'],
	);

	$bar_width = 2 + floor( (int) $atts['value'] / (int) $atts['max'] * ( (int) $atts['width'] - 2 ) );

	ob_start();
	?>
	<svg viewBox="<?php echo esc_attr( $viewbox ); ?>" width="<?php echo esc_attr( $atts['width'] ); ?>" height="<?php echo esc_attr( $atts['height'] ); ?>">
	<rect x="0" y="0" width="<?php echo esc_attr( $bar_width ); ?>" height="<?php echo esc_attr( $atts['height'] ); ?>" fill="<?php echo esc_attr( $atts['color'] ); ?>">
	</svg>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'simple_bar_graph', 'wmf_simple_bar_graph_shortcode_callback' );

<?php
/**
 * Define the [simple_bar_graph] shortcode.
 *
 * @package shiro
 */

/**
 * Define a [simple_bar_graph] shortcode that renders an inline bar graph using SVG.
 *
 * @param array $atts Shortcode attributes array.
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
		$atts['height']
	);

	$values = array_map( 'trim', explode( ',', (string) $atts['value'] ) );
	$colors = array_map( 'trim', explode( ',', (string) $atts['color'] ) );
	foreach ( $values as $idx => $value ) {
		$values[ $idx ] = [
			'color' => ( $idx + 1 <= count( $colors ) ) ? $colors[ $idx ] : $colors[0],
			'width' => floor( (int) $value / (int) $atts['max'] * ( (int) $atts['width'] ) ),
			'x'     => ( 0 === $idx ) ? 0 : $values[ $idx - 1 ]['x'] + $values[ $idx - 1 ]['width'],
		];
	}

	ob_start();
	?>
	<svg viewBox="<?php echo esc_attr( $viewbox ); ?>" width="<?php echo esc_attr( $atts['width'] ); ?>" height="<?php echo esc_attr( $atts['height'] ); ?>">
		<?php
		foreach ( $values as $value ) {
			?>
			<rect x="<?php echo esc_attr( $value['x'] ); ?>" y="0" width="<?php echo esc_attr( $value['width'] ); ?>" height="<?php echo esc_attr( $atts['height'] ); ?>" fill="<?php echo esc_attr( $value['color'] ); ?>"></rect>
			<?php
		}
		?>
	</svg>
	<?php
	return (string) ob_get_clean();
}
add_shortcode( 'simple_bar_graph', 'wmf_simple_bar_graph_shortcode_callback' );

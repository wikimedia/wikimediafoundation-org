<?php
/**
 * The Stats Graph Module.
 *
 * @package shiro
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args ) || ! is_array( $template_args ) ) {
	return;
}

$bg_opts = get_post_meta( get_the_ID(), 'page_header_background', true );
$header_accent_color = isset( $bg_opts['color'] ) && 'pink' === $bg_opts['color'] ? 'pink' : '';

?>

<div class="stats-graph-container w-100p bg-white mod-margin-bottom bg-ltgray">
	<div class="mw-980 data-ungrid flex flex-medium flex-wrap flex-space-between std-mod">
		<div class="ungrid-line-connector <?php echo esc_attr($header_accent_color); ?>"></div>
		<div class="data-bite w-100p mar-bottom wysiwyg">
			<?php wmf_get_template_part( 'template-parts/modules/data-vis/data-bite', $template_args['copy'] ); ?>
		</div>
		<div class="w-68p graph-visualization">
			<div id="linechart1" class="d3-chart" data-chart-raw="<?php echo esc_attr($template_args['data']); ?>" data-stroke-color="<?php echo esc_attr($header_accent_color); ?>">
				
			</div>
		</div>
		<div class="w-32p">
			<p><?php echo wp_kses_post( wpautop( $template_args['explanation'] ) ); ?></p>
		</div>
	</div>
</div>
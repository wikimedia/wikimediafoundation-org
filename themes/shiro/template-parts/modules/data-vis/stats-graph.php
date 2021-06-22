<?php
/**
 * The Stats Graph Module.
 *
 * @package shiro
 */

$template_args = $args;

if ( empty( $template_args['copy']['heading'] ) ) {
	return;
}

$bg_opts = get_post_meta( get_the_ID(), 'page_header_background', true );
$header_accent_color = isset( $bg_opts['color'] ) && 'pink' === $bg_opts['color'] ? 'pink' : '';

$allowed_tags = [ 'span' => [ 'class' => [], 'style' => [] ], 'em' => [], 'strong' => [], 'a' => [ 'href' => [], 'class' => [], 'title' => [], 'rel' => [] ], 'p' => [], 'br' => [], 'sup' => [] ];

?>

<div class="stats-graph-container w-100p mod-margin-bottom_sm bg-ltgray">

	<div class="mw-980 flex flex-medium flex-wrap flex-space-between std-mod">
		<div class="ungrid-line-connector <?php echo esc_attr($header_accent_color); ?>"></div>

		<div class="data-bite w-100p mar-bottom wysiwyg">
			<?php get_template_part( 'template-parts/modules/data-vis/data-bite', null, $template_args['copy'] ); ?>
		</div>

		<div class="w-68p graph-visualization">
			<div id="linechart-label" class="w-100p" style="opacity: 0">
				<span id="label-date"></span>: <span id="label-name"></span>
			</div>
			<div id="linechart1" class="d3-linechart" data-chart-raw="<?php echo esc_attr($template_args['data']); ?>" data-time-format="<?php echo esc_attr($template_args['time-format']); ?>" data-stroke-color="<?php echo esc_attr($header_accent_color); ?>">
			</div>
		</div>

		<div class="w-32p wysiwyg">
			<div class="mar-bottom">
				<?php echo wp_kses( $template_args['explanation'], $allowed_tags ); ?>
			</div>
			<p class="updated-date"><?php echo esc_html( $template_args['updated-date'] ); ?></p>
		</div>
	</div>

</div>

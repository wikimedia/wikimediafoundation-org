<?php
/**
 * The Stats Profiles Module.
 *
 * @package shiro
 */

$template_args = $args;

if ( empty( $template_args['headline'] ) ) {
	return;
}

$labels = $template_args['labels'];
$labelsList = json_decode($labels);
$filterInstruction = $template_args['filter-instruction'];
$data = $template_args['data'];
$dataLength = count(json_decode($data));
$maxf1 = $template_args['maxf1'];
$maxf2 = $template_args['maxf2'];
$masterunit = $template_args['masterunit'];
$iconMedia = ! empty( $template_args['icons'] ) ? $template_args['icons'] : [];
$icons = [];
for ($i = 0; $i <= count($iconMedia); $i++) {
	$icon = is_numeric( $iconMedia[$i]['image'] ) ? wp_get_attachment_image_url( $iconMedia[$i]['image'] ) : '';
    array_push($icons, $icon);
}

$allowed_tags = [ 'span' => [ 'class' => [], 'style' => [] ], 'em' => [], 'strong' => [], 'a' => [ 'href' => [], 'class' => [], 'title' => [], 'rel' => [] ], 'p' => [], 'br' => [], 'sup' => [] ];

?>

<div class="stats-graph-container w-100p mod-margin-bottom_sm bg-ltgray">

	<div class="mw-980 flex flex-medium flex-wrap flex-space-between std-mod">
		<div class="w-100p heading mar-bottom_lg wysiwyg">
			<p class="double-heading__secondary is-style-h5">
				<?php echo esc_html( $template_args['subheadline'] ); ?>
			</p>
			<h2 class="double-heading__primary is-style-h3">
				<?php echo esc_html( $template_args['headline'] ); ?>
			</h2>
			<p><?php echo wp_kses( $template_args['subtitle'], $allowed_tags ); ?></p>
		</div>

		<div class="w-50p graph-visualization">
			<div id="profilechart1" class="d3-dataprofiles" data-chart-raw="<?php echo esc_attr($data)?>" data-chart-labels="<?php echo esc_attr($labels) ?>" data-chart-icons="<?php echo esc_attr(wp_json_encode($icons))?>" data-chart-masterunit="<?php echo esc_attr($masterunit)?>" data-max-feature-1="<?php echo esc_attr($maxf1)?>" data-max-feature-2="<?php echo esc_attr($maxf2)?>" data-chart-except="true" data-slice-start="0" data-slice-end="1">
			</div>
		</div>

		<div class="w-50p wysiwyg">
			<div class="mar-bottom">
				<?php echo wp_kses( $template_args['explanation'], $allowed_tags ); ?>
			</div>
			<p class="updated-date"><?php echo esc_html( $template_args['updated-date'] ); ?>
			</p>
		</div>

		<div class="w-100p chart-options">
			<ul>
				<li>
					<input type="checkbox" id="feature1" name="feature1" checked>
					<label for="feature1"><?php echo esc_html( $labelsList[1] ); ?></label>
				</li>
				<li>
					<input type="checkbox" id="feature2" name="feature2" checked>
					<label for="feature2"><?php echo esc_html( $labelsList[2] ); ?></label>
				</li>
				<li>
					<input type="checkbox" id="feature3" name="feature3" checked>
					<label for="feature3"><?php echo esc_html( $labelsList[3] ); ?></label>
				</li>
			</ul>
			<p display="inline-block"><?php echo esc_html( $filterInstruction ); ?></p>
		</div>

		<div class="w-100p graph-visualization">
			<div id="profilechart2" class="d3-dataprofiles" data-chart-raw="<?php echo esc_attr($data)?>" data-chart-labels="<?php echo esc_attr($labels) ?>" data-chart-icons="<?php echo esc_attr(wp_json_encode($icons))?>" data-chart-masterunit='<?php echo esc_attr($masterunit)?>' data-max-feature-1="<?php echo esc_attr($maxf1)?>" data-max-feature-2="<?php echo esc_attr($maxf2)?>" data-chart-except="false" data-slice-start="1" data-slice-end="<?php echo esc_attr( $dataLength ); ?>">
			</div>
		</div>
	</div>

</div>

<?php
/**
 * The Stats Graph Module.
 *
 * @package shiro
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['headline'] ) ) {
	return;
}

$no_of_modules = count($template_args['copy']);
$width = ($no_of_modules <= 2) ? 'w-50p' : 'w-32p';

$allowed_tags = [ 'span' => [ 'class' => [], 'style' => [] ], 'em' => [], 'strong' => [], 'a' => [ 'href' => [], 'class' => [], 'title' => [], 'rel' => [] ], 'p' => [], 'br' => [], 'sup' => [] ];

?>

<div class="stats-plain-container w-100p bg-white mod-margin-bottom">
	<div class="mw-980">
		
		<div class="heading mar-bottom_lg wysiwyg">
			<h3 class="h3 color-gray uppercase"><?php echo esc_html( $template_args['subheadline'] ); ?></h3>
			<h2 class="h2"><?php echo esc_html( $template_args['headline'] ); ?></h2>
			<p><?php echo wp_kses( $template_args['subtitle'], $allowed_tags ); ?></p>
		</div>

		<div class="flex flex-medium flex-wrap flex-space-between mar-bottom">
			<?php for ($i = 0; $i < $no_of_modules; $i++) { 
			$this_module = $template_args['copy'][$i];
			?>
			<div class="<?php echo esc_attr($width); ?> wysiwyg">
				<h3 class="h3"><?php echo esc_html( $this_module['heading'] ); ?></h3>
				<div class="mar-top">
					<?php echo wp_kses( $this_module['desc'], $allowed_tags ); ?>
				</div>
			</div>
			<?php } ?>
		</div>

		<div>
			<p class="updated-date"><?php echo esc_html( $template_args['updated-date'] ); ?></p>
		</div>

	</div>
</div>
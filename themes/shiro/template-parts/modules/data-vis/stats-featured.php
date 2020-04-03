<?php
/**
 * The Stats Featuered Module.
 *
 * @package shiro
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args ) || ! is_array( $template_args ) ) {
	return;
}

$bg_opts = get_post_meta( get_the_ID(), 'page_header_background', true );
$header_accent_color = isset( $bg_opts['color'] ) && 'pink' === $bg_opts['color'] ? 'pink' : '';

$main_image = ! empty( $template_args['main_image'] ) ? $template_args['main_image'] : '';
$main_image = is_numeric( $main_image ) ? wp_get_attachment_image_url( $main_image, 'large' ) : $main_image;
$no_of_modules = count($template_args['copy']);
?>

<div class="data-ungrid mw-980 flex flex-medium flex-wrap flex-space-between">
	<div class="ungrid-line <?php echo esc_attr($header_accent_color); ?>"></div>
	<div class="w-100p">
		<?php 
		$first_stats_section = $template_args['copy'][0];
		$icon1 = ! empty( $first_stats_section['image'] ) ? $first_stats_section['image'] : '';
		$icon1 = is_numeric( $icon1 ) ? wp_get_attachment_image_url( $icon1 ) : $icon1;
		?>

		<div class="ungrid-top-box data-bite wysiwyg w-32p">
			<h2 class="h2"><span class="icon-container" style="background-image: url(<?php echo esc_url( $icon1 ); ?>)"></span><?php echo $first_stats_section['heading']; ?></h2>
			<?php echo $first_stats_section['desc']; ?>
		</div>			
		
	</div>
	<div class="main-image-container w-68p">
		<img src="<?php echo esc_url( $main_image ); ?>">
	</div>
	<div class="w-32p">
		<?php 
		for ($i = 1; $i < $no_of_modules; $i++) {
			$stats_section = $template_args['copy'][$i];
			$icon = ! empty( $stats_section['image'] ) ? $stats_section['image'] : '';
			$icon = is_numeric( $icon ) ? wp_get_attachment_image_url( $icon ) : $icon;
			?>
		<div class="data-bite wysiwyg">
			<h2 class="h2"><span class="icon-container" style="background-image: url(<?php echo esc_url( $icon ); ?>)"></span><?php echo $stats_section['heading']; ?></h2>
			<?php echo $stats_section['desc']; ?>
		</div>

		<?php } 
		?>	
	</div>
</div>
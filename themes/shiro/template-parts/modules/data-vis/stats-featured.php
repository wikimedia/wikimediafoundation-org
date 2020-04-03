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
$ungrid_color = isset( $bg_opts['color'] ) && 'pink' === $bg_opts['color'] ? 'pink' : '';

?>

<!-- change class and add class in css -->
<div class="framing-copy-ungrid mw-980">
	<div class="ungrid-line <?php echo esc_attr($ungrid_color); ?>"></div>

	<?php foreach ( $template_args as $i => $stats_section ) {
		$image     = ! empty( $stats_section[0]['image'] ) ? $stats_section[0]['image'] : '';
		$image     = is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'image_16x9_small' ) : $image;

		?>
	<div class="mod-margin-bottom wysiwyg">
		<!-- style in css -->
		<div class="icon-container" style="width: 30px; height: 30px; background-size: contain; background-repeat: no-repeat; background-image: url(<?php echo esc_url( $image ); ?>)"></div>
		<h1><?php echo $stats_section[0]['heading']; ?></h1>
		<?php echo $stats_section[0]['copy']; ?>
	</div>

	<?php } ?>
</div>
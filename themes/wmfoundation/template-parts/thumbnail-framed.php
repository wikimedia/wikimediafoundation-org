<?php
/**
 * Template for a an image inside of another image frame.
 *
 * @package wmfoundation
 */

$data = wmf_get_template_data();

if ( empty( $data['container_image'] ) || empty( $data['inner_image'] ) ) {
	return;
}

$container_image = $data['container_image'];
$inner_image     = $data['inner_image'];
$container_class = empty( $data['container_class'] ) ? '' : ' ' . $data['container_class'];

?>

<div class="mw-1017 staff-image-container<?php echo esc_attr( $container_class ); ?>">
	<div class="staff-image-bkg duotone-inline-img-container--blue">
		<img src="<?php echo esc_url( $container_image ); ?>" alt="">
	</div>

	<div class="staff-image">
		<?php echo wp_get_attachment_image( $inner_image, 'large' ); ?>
	</div>
</div>

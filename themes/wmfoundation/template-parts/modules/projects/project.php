<?php
/**
 * Handles single project.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['link_uri'] ) || ( empty( $template_args['image'] ) && empty( $template_args['heading'] ) && empty( $template_args['content'] ) ) ) {
	return;
}

$bg_image = ! empty( $template_args['bg_image'] ) ? $template_args['bg_image'] : '';
$bg_image = is_numeric( $bg_image ) ? wp_get_attachment_image_url( $bg_image, 'full' ) : $bg_image;

$image     = ! empty( $template_args['image'] ) ? $template_args['image'] : '';
$image_alt = is_numeric( $image ) ? get_post_meta( $image, '_wp_attachment_image_alt', true ) : __( 'Logo image.', 'wmfoundation' );
$image     = is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'image_square_medium' ) : $image;
$image     = str_replace( '&crop=1', '', $image );

$class = empty( $template_args['class'] ) ? '_map' : $template_args['class'];

?>

<div class="w-50p home-project-list-item home-project-list-item<?php echo esc_attr( $class ); ?>">
	<a href="<?php echo esc_url( $template_args['link_uri'] ); ?>">
		<?php if ( ! empty( $bg_image ) ) : ?>
		<div class="bg-img-container">
			<div class="bg-img" style="background-image: url(<?php echo esc_url( $bg_image ); ?>);"></div>
		</div>
		<?php endif; ?>

		<div class="home-project-list-item-content">
			<?php if ( ! empty( $image ) ) : ?>
			<div class="home-project-logo">
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $template_args['heading'] ) ) : ?>
			<p class="h3 color-white mar-bottom"><?php echo esc_html( $template_args['heading'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $template_args['content'] ) ) : ?>
				<p class="color-white"><?php echo esc_html( $template_args['content'] ); ?></p>
			<?php endif; ?>
		</div>
	</a>
</div>

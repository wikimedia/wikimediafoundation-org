<?php
/**
 * Handles single project.
 *
 * @package shiro
 */

$template_args = $args;

if ( empty( $template_args['link_uri'] ) || ( empty( $template_args['image'] ) && empty( $template_args['heading'] ) && empty( $template_args['content'] ) ) ) {
	return;
}

$image     = ! empty( $template_args['image'] ) ? $template_args['image'] : '';
$image_alt = is_numeric( $image ) ? get_post_meta( $image, '_wp_attachment_image_alt', true ) : __( 'Logo image.', 'shiro' );
$image     = is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'image_square_medium' ) : $image;
$image     = str_replace( '&crop=1', '', $image );

$class = empty( $template_args['class'] ) ? '_map' : $template_args['class'];

?>

<div class="w-32p home-project-list-item home-project-list-item--gray rounded">
	<a href="<?php echo esc_url( $template_args['link_uri'] ); ?>">
		<div class="home-project-list-item-content">
			<?php if ( ! empty( $image ) ) : ?>
			<div class="home-project-logo">
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $template_args['heading'] ) ) : ?>
			<h3 class="h3 mar-bottom"><?php echo esc_html( $template_args['heading'] ); ?></h3>
			<?php endif; ?>

			<?php if ( ! empty( $template_args['content'] ) ) : ?>
				<p><?php echo esc_html( $template_args['content'] ); ?></p>
			<?php endif; ?>
		</div>
	</a>
</div>

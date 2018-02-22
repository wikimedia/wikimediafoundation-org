<?php
/**
 * Handles page specific CTA.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['image'] ) && empty( $template_args['heading'] ) && empty( $template_args['content'] ) && empty( $template_args['link_uri'] ) ) {
	return;
}

$image = ! empty( $template_args['image'] ) ? $template_args['image'] : '';
$image = is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'full' ) : $image;
?>

<div class="w-100p cta mod-margin-bottom cta-secondary bg-img--blue btn-pink">
	<div class="mw-1360">
		<div class="card">
			<?php if ( ! empty( $image ) ) : ?>
			<div class="bg-img-container">
				<div class="bg-img" style="background-image: url(<?php echo esc_url( $image ); ?>"></div>
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $template_args['heading'] ) || ! empty( $template_args['content'] ) || ! empty( $template_args['link_uri'] ) ) : ?>
			<div class="card-content w-45p">

				<?php if ( ! empty( $template_args['heading'] ) ) : ?>
				<h2 class="h2"><?php echo esc_html( $template_args['heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['content'] ) ) : ?>
				<?php echo wp_kses_post( wpautop( $template_args['content'] ) ); ?>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['link_uri'] ) && ! empty( $template_args['link_text'] ) ) : ?>
				<a class="btn " href="<?php echo esc_url( $template_args['link_uri'] ); ?>"><?php echo esc_html( $template_args['link_text'] ); ?></a>
				<?php endif; ?>

			</div>
			<?php endif; ?>
		</div>

	</div>
</div>

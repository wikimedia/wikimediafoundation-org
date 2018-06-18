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

$bg_class = empty( $template_args['background_color'] ) || 'blue' === $template_args['background_color'] ? 'bg-img--blue btn-pink' : 'bg-img--turquoise btn-blue';
$class    = empty( $template_args['class'] ) ? $bg_class . ' cta-secondary' : $template_args['class'];
?>

<div class="w-100p cta mod-margin-bottom <?php echo esc_attr( $class ); ?>">
	<div class="mw-1360">
		<div class="card">
			<?php if ( ! empty( $image ) ) : ?>
			<div class="bg-img-container">
				<div class="bg-img" style="background-image: url(<?php echo esc_url( $image ); ?>)"></div>
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $template_args['heading'] ) || ! empty( $template_args['content'] ) || ! empty( $template_args['link_uri'] ) ) : ?>
			<div class="card-content w-45p">

				<?php if ( ! empty( $template_args['heading'] ) ) : ?>
				<h2 class="h2"><?php echo esc_html( $template_args['heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['content'] ) ) : ?>
				<div class="mar-bottom mar-top">
					<?php echo wp_kses_post( wpautop( '<p class="cta-description">' . $template_args['content'] . '</p>' ) ); ?>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['link_uri'] ) && ! empty( $template_args['link_text'] ) ) : ?>
				<a class="btn " href="<?php echo esc_url( $template_args['link_uri'] ); ?>"><?php echo esc_html( $template_args['link_text'] ); ?></a>
				<?php endif; ?>

			</div>
			<?php endif; ?>
		</div>

	</div>
</div>

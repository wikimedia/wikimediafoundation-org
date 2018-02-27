<?php
/**
 * Handles single focus block.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['image'] ) && empty( $template_args['heading'] ) && empty( $template_args['content'] ) && empty( $template_args['link_uri'] ) ) {
	return;
}

$image = ! empty( $template_args['image'] ) ? $template_args['image'] : '';
$image = is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'full' ) : $image;

$class = empty( $template_args['class'] ) ? 'img-right-content-left' : $template_args['class'];

?>

<div class="w-100p cta mod-margin-bottom bg-img--turquoise <?php echo esc_attr( $class ); ?>">
	<div class="mw-1360">
		<div class="card">
			<?php if ( ! empty( $image ) ) : ?>
			<div class="bg-img-container">
				<div class="bg-img" style="background-image: url(<?php echo esc_url( $image ); ?>);"></div>
			</div>
			<?php endif; ?>

			<div class="card-content w-45p">

				<?php if ( ! empty( $template_args['heading'] ) ) : ?>
				<h2 class="h2"><?php echo esc_html( $template_args['heading'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['content'] ) ) : ?>
				<p><?php echo esc_html( $template_args['content'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $template_args['link_uri'] ) && ! empty( $template_args['link_text'] ) ) : ?>
				<div class="link-list hover-highlight_dk uppercase">
					<!-- Single link -->
					<a href="<?php echo esc_url( $template_args['link_uri'] ); ?>"><?php echo esc_html( $template_args['link_text'] ); ?></a>
				</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</div>

<?php
/**
 * Handles simple text CTA module.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['image'] ) && empty( $template_args['heading'] ) && empty( $template_args['copy'] ) && empty( $template_args['link_url'] ) && empty( $template_args['link_text'] ) ) {
	return;
}

$image     = ! empty( $template_args['image'] ) ? $template_args['image'] : '';
$image     = is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'full' ) : $image;
$image_alt = is_numeric( $image ) ? wp_get_attachment_image( $image )->src : '';

$links = ! empty( $template_args['links'] ) ? $template_args['links'] : '';
?>

<div class="module-mu w-50p">
	<?php if ( ! empty( $image ) ) : ?>
	<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" class="mar-bottom">
	<?php endif; ?>
	<?php if ( ! empty( $template_args['heading'] ) ) : ?>
	<h3 class="h3"><?php echo esc_html( $template_args['heading'] ); ?></h3>
	<?php endif; ?>
	<?php if ( ! empty( $template_args['copy'] ) ) : ?>
	<div class="wysiwyg">
		<?php echo wp_kses_post( wpautop( $template_args['copy'] ) ); ?>
	</div>
	<?php endif; ?>
	<?php if ( ! empty( $links ) ) :
		foreach( $links as $link ) : ?>
		<div class="link-list hover-highlight uppercase mar-bottom">
			<!-- Single link -->
			<a href="<?php echo esc_url( $link['link_url'] ); ?>"><?php echo esc_html( $link['link_text'] ); ?></a>
		</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php
/**
 * Handles simple text CTA.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['heading'] ) && empty( $template_args['copy'] ) && empty( $template_args['link_url'] ) && empty( $template_args['link_text'] ) ) {
	return;
}
?>

<div class="module-mu w-50p">
	<?php if ( ! empty( $template_args['heading'] ) ) : ?>
	<h3 class="h3"><?php echo esc_html( $template_args['heading'] ); ?></h3>
	<?php endif; ?>
	<?php if ( ! empty( $template_args['copy'] ) ) : ?>
	<div class="wysiwyg">
		<?php echo wp_kses_post( wpautop( $template_args['copy'] ) ); ?>
	</div>
	<?php endif; ?>
	<?php if ( ! empty( $template_args['link_url'] ) && ! empty( $template_args['link_text'] ) ) : ?>
	<div class="link-list hover-highlight uppercase">
		<!-- Single link -->
		<a href="<?php echo esc_url( $template_args['link_url'] ); ?>"><?php echo esc_html( $template_args['link_text'] ); ?></a>
	</div>
	<?php endif; ?>
</div>

<?php
/**
 * Handles off site link item.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( ( empty( $template_args['heading'] ) || empty( $template_args['uri'] ) ) && empty( $template_args['content'] ) ) {
	return;
}

?>


<div class="module-mu w-50p">

	<?php if ( ! empty( $template_args['heading'] ) ) : ?>
	<h3 class="h3 link-external">
		<a href="<?php echo esc_url( $template_args['uri'] ); ?>" target="_blank">
			<?php echo esc_html( $template_args['heading'] ); ?>
			<i class="material-icons external-link-icon">open_in_new</i>
		</a>
	</h3>
	<?php endif; ?>

	<?php if ( ! empty( $template_args['content'] ) ) : ?>
	<div class="wysiwyg">
		<p><?php echo wp_kses_post( strip_tags( $template_args['content'], '<em><span><del><strong>' ) ); ?></p>
	</div>
	<?php endif; ?>

</div>

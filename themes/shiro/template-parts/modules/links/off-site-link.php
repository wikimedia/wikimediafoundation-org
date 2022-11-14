<?php
/**
 * Handles off site link item.
 *
 * @package shiro
 */

$template_args = $args;

if ( ( empty( $template_args['heading'] ) || empty( $template_args['uri'] ) ) && empty( $template_args['content'] ) ) {
	return;
}

$width_class = $template_args['split'] ? 'w-50p' : 'w-100p';

?>


<div class="module-mu <?php echo esc_attr( $width_class ); ?>">

	<?php if ( ! empty( $template_args['heading'] ) ) : ?>
	<h3 class="h3 link-external">
		<a href="<?php echo esc_url( $template_args['uri'] ); ?>" target="_blank">
			<?php echo esc_html( $template_args['heading'] ); ?>
			<?php wmf_show_icon( 'open', 'external-link-icon' ); ?>
		</a>
	</h3>
	<?php endif; ?>

	<?php if ( ! empty( $template_args['content'] ) ) : ?>
	<div class="wysiwyg">
		<p>
		<?php
			echo wp_kses(
				$template_args['content'], array(
					'em'     => array(),
					'span'   => array( 'class', 'id' ),
					'del'    => array(),
					'strong' => array(),
				)
			);
		?>
			</p>
	</div>
	<?php endif; ?>

</div>

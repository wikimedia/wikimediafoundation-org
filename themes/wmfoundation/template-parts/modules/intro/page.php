<?php
/**
 * Handles page intro.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['intro'] ) ) {
	return;
}

$button = ! empty( $template_args['button'] ) ? $template_args['button'] : '';

?>

<div class="page-intro mod-margin-bottom wysiwyg">
	<div class="page-intro-text">
		<?php echo wp_kses_post( wpautop( $template_args['intro'] ) ); ?>
	</div>

	<?php wmf_get_template_part( 'template-parts/modules/intro/button', $button ); ?>
</div>

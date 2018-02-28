<?php
/**
 * The Framing Copy Module.
 *
 * @package wmfoundation
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args['pre_heading'] ) && empty( $template_args['heading'] ) && empty( $template_args['modules'] ) ) {
	return;
}

$rand_translation_title = empty( $template_args['rand_translation_title'] ) ? '' : $template_args['rand_translation_title'];

?>
<div class="flex flex-medium flex-wrap mw-1360 fifty-fifty mod-margin-bottom">
	<?php if ( ! empty( $template_args['pre_heading'] ) && ! empty( $template_args['heading'] ) ) : ?>
	<div class="mw-1360">
		<?php if ( ! empty( $template_args['pre_heading'] ) ) : ?>
		<h3 class="h3 color-gray"><?php echo esc_html( $template_args['pre_heading'] ); ?> — <span><?php echo esc_html( $template_args['rand_translation_title'] ); ?></span></h3>
		<?php endif; ?>
		<?php if ( ! empty( $template_args['heading'] ) ) : ?>
		<h2 class="h2"><?php echo esc_html( $template_args['heading'] ); ?></h2>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	<?php
	foreach ( $template_args['modules'] as $module ) {
		wmf_get_template_part( 'template-parts/modules/mu/text', $module );
	}
	?>
</div>

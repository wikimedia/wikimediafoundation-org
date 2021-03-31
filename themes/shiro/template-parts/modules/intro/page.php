<?php
/**
 * Handles page intro.
 *
 * @package shiro
 */

$template_args = $args;

if ( empty( $template_args['intro'] ) ) {
	return;
}

$button = ! empty( $template_args['button'] ) ? $template_args['button'] : '';

?>

<div class="page-intro mod-margin-bottom wysiwyg">
	<div class="page-intro-text">
		<?php echo wp_kses_post( do_shortcode( wpautop( $template_args['intro'] ) ) ); ?>
	</div>

	<?php get_template_part( 'template-parts/modules/intro/button', null, $button ); ?>
</div>

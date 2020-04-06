<?php
/**
 * Handles simple stat with explanatory text
 *
 * @package shiro
 */

$template_args = wmf_get_template_data();

if ( empty( $template_args ) ) {
	return;
}

$icon = ! empty( $template_args['image'] ) ? $template_args['image'] : '';
$icon = is_numeric( $icon ) ? wp_get_attachment_image_url( $icon ) : $icon;

?>

<h2 class="h2">
	<?php if (!$icon === '') { ?>
		<span class="icon-container" style="background-image: url(<?php echo esc_url( $icon ); ?>)"></span>
	<?php } ?>
	<?php echo esc_html( $template_args['heading'] ); ?>
</h2>
<?php echo wp_kses_post( wpautop( $template_args['desc'] ) ); ?>
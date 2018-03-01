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

$intro = str_replace( '<p>', '<p class="h3 color-gray">', wpautop( $template_args['intro'] ) );
?>

<div class="page-intro mw-1360 mod-margin-bottom">
	<?php echo wp_kses_post( $template_args['intro'] ); ?>
</div>

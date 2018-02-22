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
?>

<div class="page-intro mw-1360 mod-margin-bottom">
	<p class="h3 color-gray"><?php echo esc_html( $template_args['intro'] ); ?></p>
</div>

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
	<div class="page-intro-text">
		<?php echo wp_kses_post( wpautop( $template_args['intro'] ) ); ?>
	</div>
</div>

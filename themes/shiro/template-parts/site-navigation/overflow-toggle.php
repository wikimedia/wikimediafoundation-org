<?php

/**
 * Adds menu toggle button
 *
 * @package shiro
 */

$label = get_theme_mod( 'wmf_toggle_menu_label', __( 'Toggle overflow menu', 'shiro-admin' ) );
?>

<button class="primary-nav__overflow-toggle" hidden
		aria-label="<?php echo esc_attr( $label ); ?>"
		aria-expanded="false">
	<span class="btn-label-a11y"><?php echo esc_html( $label ); ?></span>
	<span class="btn-label-opened"><?php echo esc_html__( 'less' , 'shiro-admin' ); ?></span>
	<span class="btn-label-closed"><?php echo esc_html__( 'more' , 'shiro-admin' ); ?></span>
</button>

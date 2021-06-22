<?php

/**
 * Adds menu toggle button
 *
 * @package shiro
 */

$label = get_theme_mod( 'wmf_toggle_menu_label', __( 'Toggle menu', 'shiro-admin' ) );
?>

<button class="primary-nav-toggle"
		hidden
		aria-label="<?php echo esc_attr( $label ); ?>"
		data-dropdown-toggle="primary-nav"
		aria-expanded="false">
	<span class="btn-label-a11y"><?php echo esc_html( $label ); ?></span>
	<?php wmf_show_icon( 'menu', 'primary-nav-toggle__icon primary-nav-toggle__icon--closed' ); ?>
	<?php wmf_show_icon( 'close', 'primary-nav-toggle__icon primary-nav-toggle__icon--opened' ); ?>
</button>

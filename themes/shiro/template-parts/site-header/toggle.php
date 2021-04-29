<?php

/**
 * Adds menu toggle button
 *
 * @package shiro
 */

$label = get_theme_mod( 'wmf_toggle_menu_label', __( 'Toggle menu', 'shiro' ) );
?>

<button class="mobile-nav-toggle bold" aria-label="<?php echo esc_attr( $label ); ?>">
	<span class="btn-label-a11y"><?php echo esc_html( $label ); ?></span>
	<?php wmf_show_icon( 'menu', 'material' ); ?>
	<img src="<?php echo wmf_get_svg_uri( 'close' ) ?>" alt=""
		 class="icon-close">
</button>

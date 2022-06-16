<?php

/**
 * Adds primary navigation menu
 *
 * @package shiro
 */

if ( has_nav_menu( 'header' ) ) {
	wp_nav_menu(
		array(
			'theme_location' => 'header',
			'menu_class'     => 'primary-nav__items',
			'container'      => '',
			'depth'          => 2,
			'walker'         => new \WMF\Walkers\Walker_Main_Nav(),
		)
	);
}
?>

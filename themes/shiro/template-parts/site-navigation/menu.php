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
<<<<<<< HEAD
			// Remove this when implementing multi-level navigation.
			// For now it's here because the existing menu implementation
			// doesn't support more than one level and looks very odd if
			// you try.
			'depth'          => 1,
=======
			'depth'          => 2,
			'walker'         => new \WMF\Walkers\Walker_Main_Nav(),
>>>>>>> preprod
		)
	);
}
?>
<<<<<<< HEAD

=======
>>>>>>> preprod

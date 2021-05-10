<?php
/**
 * Display a selection of highlighted links.
 *
 * @package shiro
 */

if ( has_nav_menu( 'footer-under-text' ) ) {
	wp_nav_menu(
		[
			'menu'            => 'footer-under-text',
			'menu_class'      => 'site-footer__callout-nav__items',
			'menu_id'         => false,
			'container'       => 'nav',
			'container_class' => 'site-footer__callout-nav'
		]
	);
}

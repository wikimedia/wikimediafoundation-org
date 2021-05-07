<?php

if ( has_nav_menu( 'footer-under-text' ) ) {
	wp_nav_menu(
		array(
			'menu'       => 'footer-under-text',
			'menu_class' => '',
			'container'  => '',
		)
	);
}

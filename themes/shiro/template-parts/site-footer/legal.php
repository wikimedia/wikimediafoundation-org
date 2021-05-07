<?php

$wmf_footer_copyright = get_theme_mod( 'wmf_footer_copyright',
	__( 'This work is licensed under a <a href="https://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0</a> unported license. Some images under <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC BY-SA</a>.',
		'shiro' ) );

echo wp_kses(
	$wmf_footer_copyright, array(
		'a'  => array(
			'href' => array(),
		),
		'br' => array(),
	)
);

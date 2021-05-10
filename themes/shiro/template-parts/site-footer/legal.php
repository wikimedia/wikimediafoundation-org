<?php
/**
 * Display site legal copy.
 *
 * @package shiro
 */

$legal_copy = get_theme_mod( 'wmf_footer_copyright',
	__( 'This work is licensed under a <a href="https://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0</a> unported license. Some images under <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC BY-SA</a>.',
		'shiro' ) );
?>

<div class="site-footer__legal">
	<?php echo wp_kses(
		$legal_copy, array(
			'a'  => array(
				'href' => array(),
			),
			'br' => array(),
		)
	); ?>
</div>

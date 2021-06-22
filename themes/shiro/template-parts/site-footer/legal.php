<?php
/**
 * Display site legal copy.
 *
 * @package shiro
 */

$legal_copy = get_theme_mod( 'wmf_footer_copyright', \WMF\Customizer\Footer::defaults( 'wmf_footer_copyright' ) );
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

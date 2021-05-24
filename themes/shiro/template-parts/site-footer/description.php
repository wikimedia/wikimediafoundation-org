<?php
/**
 * Display the footer description and logo.
 *
 * @package shiro
 */

$logo = get_theme_mod( 'wmf_footer_logo' ) ?: wmf_get_svg_uri( 'logo-horizontal-white' );
$text = get_theme_mod( 'wmf_footer_text', \WMF\Customizer\Footer::defaults( 'wmf_footer_text' ) );
?>
<div class="site-footer__description">
	<img class="site-footer__logo" src="<?php echo esc_url( $logo ); ?>"
		 alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"/>
	<?php if ( ! empty( $text ) ) : ?>
		<p><?php echo wp_kses(
				$text, array(
					'a'  => array(
						'href' => array(),
					),
					'br' => array(),
				)
			); ?></p>
	<?php endif; ?>
</div>

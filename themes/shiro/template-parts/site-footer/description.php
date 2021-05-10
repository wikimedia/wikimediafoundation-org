<?php
/**
 * Display the footer description and logo.
 *
 * @package shiro
 */

$logo = get_theme_mod( 'wmf_footer_logo' ) ?: wmf_get_svg_uri( 'logo-horizontal-white' );
$text = get_theme_mod( 'wmf_footer_text',
	__( 'The Wikimedia Foundation, Inc is a nonprofit charitable organization dedicated to encouraging the growth, development and distribution of free, multilingual content, and to providing the full content of these wiki-based projects to the public free of charge.',
		'shiro' ) );
?>
<div class="site-footer__description">
	<img class="site-footer__logo" src="<?php echo esc_url( $logo ); ?>"
		 alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"/>
	<?php if ( ! empty( $text ) ) : ?>
		<p><?php echo esc_html( $text ); ?></p>
	<?php endif; ?>
</div>

<?php
$wmf_footer_image = get_theme_mod( 'wmf_footer_logo' ) ?: wmf_get_svg_uri( 'logo-horizontal-white' );
$wmf_footer_text  = get_theme_mod( 'wmf_footer_text',
	__( 'The Wikimedia Foundation, Inc is a nonprofit charitable organization dedicated to encouraging the growth, development and distribution of free, multilingual content, and to providing the full content of these wiki-based projects to the public free of charge.',
		'shiro' ) );
?>

<img class="site-footer__logo" src="<?php echo esc_url( $wmf_footer_image ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"/>
<?php if ( ! empty( $wmf_footer_text ) ) : ?>
	<p><?php echo esc_html( $wmf_footer_text ); ?></p>
<?php endif; ?>

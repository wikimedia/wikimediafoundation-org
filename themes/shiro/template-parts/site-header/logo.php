<?php
/**
 * Adds Header logo
 *
 * @package shiro
 */

$wmf_header_image = get_theme_mod( 'wmf_site_logo' );
if ( empty( $wmf_header_image ) ) {
	$wmf_header_image = wmf_get_svg_uri( 'logo-horizontal' );
}
?>

<a href="<?php echo esc_url( get_site_url() ); ?>" class="nav-logo">
	<img class="nav-logo__image" src="<?php echo esc_url( $wmf_header_image ); ?>"
		 alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"/>
</a>

<?php
/**
 * Adds Header logo
 *
 * @package shiro
 */

$logo_uri = get_theme_mod( 'wmf_site_logo' );
$logo_alt = get_bloginfo( 'title' );
if ( empty( $logo_uri ) ) {
	$logo_uri = get_stylesheet_directory_uri() . '/assets/src/svg/logo-horizontal.svg';
}
?>

<a href="<?php echo esc_url( get_site_url() ); ?>" class="nav-logo">
	<span class='btn-label-a11y'><?php bloginfo( 'name' ); ?></span>
	<img class="nav-logo__image" src="<?php echo esc_url( $logo_uri ); ?>"
		 alt="<?php echo esc_attr( $logo_alt ); ?>"/>
</a>

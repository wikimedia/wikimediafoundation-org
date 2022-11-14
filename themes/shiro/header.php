<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package shiro
 */

use WMF\Images\Credits;

$wmf_translation_selected = get_theme_mod( 'wmf_selected_translation_copy', __( 'Languages', 'shiro-admin' ) );
$wmf_translations         = array_filter( wmf_get_translations(), function ( $translation ) {
	return $translation['uri'] !== '';
} );

$wmf_donate_button = get_theme_mod( 'wmf_donate_now_copy', __( 'Donate', 'shiro-admin' ) );
$wmf_donate_uri    = get_theme_mod( 'wmf_donate_now_uri', 'https://donate.wikimedia.org/?utm_medium=wmfSite&utm_campaign=comms' );
$wmf_toggle_menu_label = get_theme_mod( 'wmf_toggle_menu_label', __( 'Toggle menu', 'shiro-admin' ) );
$wmf_skip2_content_label = get_theme_mod( 'wmf_skip2_content_label', __( 'Skip to content', 'shiro-admin' ) );
$wmf_skip2_navigation_label = get_theme_mod( 'wmf_skip2_navigation_label', __( 'Skip to navigation', 'shiro-admin' ) );
$wmf_select_language_label = get_theme_mod( 'wmf_select_language_label', __( 'Select language', 'shiro-admin' ) );
$wmf_current_language_label = get_theme_mod( 'wmf_current_language_label', __( 'Current language:', 'shiro-admin' ) );
$wmf_post_thumbnail_url = get_the_post_thumbnail_url( get_the_ID() );
$wmf_ogmeta_ogimageurl = get_site_option( 'ogmeta_ogimageurl' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php 
// If set, configure 'featured image' as 'og:image'.
if ( $wmf_post_thumbnail_url  && !( is_plugin_active('wordpress-seo/wp-seo.php') )): ?>
    <meta property="og:image" content="<?php echo esc_url( $wmf_post_thumbnail_url ) ?>" />
<?php
// If set, configure 'ogmeta_ogimageurl' as 'og:image'.
elseif ( $wmf_ogmeta_ogimageurl  && !( is_plugin_active('wordpress-seo/wp-seo.php') )): ?>
	<meta property="og:image" content="<?php echo esc_url( $wmf_ogmeta_ogimageurl ) ?>" />
<?php endif; ?>
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div data-dropdown-backdrop></div>
	<a class="skip-link screen-reader-text" href="#content"><?php echo esc_html( $wmf_skip2_content_label ); ?></a>
<div class="mobile-cover"></div>
<div id="page" class="site">
	<header class="<?php echo esc_attr( wmf_get_header_container_class() ); ?>" data-dropdown="primary-nav" data-dropdown-content=".primary-nav__drawer" data-dropdown-toggle=".primary-nav-toggle" data-dropdown-status="uninitialized" data-toggleable="yes" data-trap="inactive" data-backdrop="inactive" data-visible="false">
		<?php get_template_part('template-parts/site-header/wrapper' ); ?>
		<div class="header-inner mw-980">
			<?php get_template_part( 'template-parts/site-navigation/wrapper' ); ?>
			<?php wmf_translation_alert(); ?>

<?php
// Automatically add credits to all content that is not an archive or search.
if ( ! is_archive() && ! is_home() ) {
	Credits::get_instance( get_the_ID() );
}

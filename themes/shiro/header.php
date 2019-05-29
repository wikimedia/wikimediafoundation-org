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

$wmf_translation_selected = get_theme_mod( 'wmf_selected_translation_copy', __( 'Languages', 'shiro' ) );
$wmf_translations         = wmf_get_translations();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div class="mobile-cover">

</div>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'shiro' ); ?></a>
	<a class="skip-link screen-reader-text" href="#menu-header-menu"><?php esc_html_e( 'Skip to navigation', 'shiro' ); ?></a>

	<header class="<?php echo esc_attr( wmf_get_header_container_class() ); ?>" role="banner">
		<div class="top-nav">
			<div class="site-main-nav flex flex-medium flex-align-center mw-980">
				<div class="logo-container logo-container_lg">
					<?php get_template_part( 'template-parts/header/logo' ); ?>
				</div>
				<div class="logo-container logo-container_sm">
					<button class="mobile-nav-toggle bold">
						<?php wmf_show_icon( 'menu', 'material' ); ?>
						<img src="/wp-content/themes/shiro/assets/src/svg/close.svg" alt="" class="icon-close">
					</button>
					<?php get_template_part( 'template-parts/header/logo' ); ?>
				</div>
				<!-- <?php get_template_part( 'template-parts/header/nav-container' ); ?> -->
				<?php get_template_part( 'template-parts/header/navigation' ); ?>
			</div>
		</div>
		<div class="header-inner mw-980">
			<?php wmf_translation_alert(); ?>

<?php
// Automatically add credits to all content that is not an archive or search.
if ( ! is_archive() && ! is_home() ) {
	Credits::get_instance( get_the_ID() );
}

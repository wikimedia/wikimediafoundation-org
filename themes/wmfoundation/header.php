<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wmfoundation
 */

use WMF\Images\Credits;

$wmf_translation_selected = get_theme_mod( 'wmf_selected_translation_copy', __( 'Languages', 'wmfoundation' ) );
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
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'wmfoundation' ); ?></a>

	<header class="<?php echo esc_attr( wmf_get_header_container_class() ); ?>" role="banner">

		<?php get_template_part( 'template-parts/header/background' ); ?>
		<?php if ( false !== $wmf_translations ) : ?>
			<div class="translation-bar">
			<div class="translation-bar-inner mw-1360">
				<div class="translation-icon">
					<?php wmf_show_icon( 'translate', 'material icon-turquoise' ); ?>
					<span class="bold"><?php echo esc_html( $wmf_translation_selected ); ?>:</span>
				</div>

				<ul class="list-inline">
				<?php foreach ( $wmf_translations as $wmf_index => $wmf_translation ) : ?>
					<?php if ( 0 !== $wmf_index ) : ?>
					<li class="divider">&middot;</li>
					<?php endif; ?>
					<li>
						<?php if ( $wmf_translation['selected'] ) : ?>
						<span><?php echo esc_html( $wmf_translation['name'] ); ?></span>
						<?php else : ?>
						<a href="<?php echo esc_url( $wmf_translation['uri'] ); ?>"><?php echo esc_html( $wmf_translation['name'] ); ?></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
				</ul>

				<?php if ( count( $wmf_translations ) > 10 ) : ?>
				<div class="arrow-wrap">
					<span>
						<span class="elipsis">...</span>
						<?php wmf_show_icon( 'trending', 'icon-turquoise material' ); ?>
					</span>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

		<div class="header-inner mw-1360">
			<?php if ( ! is_front_page() ) : ?>
			<div class="site-main-nav">
				<div class="logo-nav-container">
					<?php get_template_part( 'template-parts/header/logo' ); ?>
					<?php get_template_part( 'template-parts/header/nav-container' ); ?>
				</div>
				<?php get_template_part( 'template-parts/header/navigation' ); ?>
			</div>
			<?php endif; ?>

<?php
// Automatically add credits to all content that is not an archive or search.
if ( ! is_archive() || ! is_home() || ! is_front_page() ) {
	Credits::get_instance( get_the_ID() );
}

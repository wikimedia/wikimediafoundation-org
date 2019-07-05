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
$wmf_donate_button = get_theme_mod( 'wmf_donate_now_copy', __( 'Donate', 'shiro' ) );
$wmf_donate_uri    = get_theme_mod( 'wmf_donate_now_uri', 'https://donate.wikimedia.org/?utm_medium=wmfSite&utm_campaign=comms' );
?>
<!DOCTYPE html>
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
					<button class="mobile-nav-toggle bold" aria-label="Toggle menu">
						<span class="btn-label-a11y">Toggle menu</span>
						<?php wmf_show_icon( 'menu', 'material' ); ?>
						<img src="/wp-content/themes/shiro/assets/src/svg/close.svg" alt="" class="icon-close">
					</button>
					<?php get_template_part( 'template-parts/header/logo' ); ?>
				</div>
				<div class="flex flex-medium">
					<?php if ( $wmf_translations !== false ) : ?>
						<?php
							# Find which is the current language and display that
							$selected = array_filter($wmf_translations, function ($lang) {
								return $lang['selected'];
							});

							if ($selected[0]['name'] === "English") {
								$lang_code = "en";
							} else {
								$lang_code = explode('/',$selected[0]['uri'])[3];
							}
						?>
						<div class="language-dropdown">
							<button aria-label="Select language">
								<span class="btn-label-a11y">Current language: </span>
								<img src="/wp-content/themes/shiro/assets/src/svg/language.svg" alt="" class="language-icon">
								<span><?php echo esc_html($lang_code); ?></span>
								<img src="/wp-content/themes/shiro/assets/src/svg/down.svg" alt="" class="down-indicator">
							</button>

							<div class="language-list">
								<ul>
									<?php foreach ( $wmf_translations as $wmf_index => $wmf_translation ) : ?>
										<li>
											<?php if ( $wmf_translation['selected'] ) : ?>
											<a class="selected" href="<?php echo esc_url( $wmf_translation['uri'] ); ?>"><?php echo esc_html( $wmf_translation['name'] ); ?></a>
											<?php else : ?>
											<a href="<?php echo esc_url( $wmf_translation['uri'] ); ?>"><?php echo esc_html( $wmf_translation['name'] ); ?></a>
											<?php endif; ?>
										</li>
									<?php endforeach ?>
								</ul>
							</div>
						</div>
					<?php endif ?>
					<button type="button" class="donate-btn">
						<a href="<?php echo esc_url($wmf_donate_uri); ?>">
							<?php echo esc_html($wmf_donate_button);?>
						</a>
					</button>
				</div>
			</div>
		</div>
		<div class="header-inner mw-980">
			<?php get_template_part( 'template-parts/header/navigation' ); ?>
			<?php wmf_translation_alert(); ?>

<?php
// Automatically add credits to all content that is not an archive or search.
if ( ! is_archive() && ! is_home() ) {
	Credits::get_instance( get_the_ID() );
}

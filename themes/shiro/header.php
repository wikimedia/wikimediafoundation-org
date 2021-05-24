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

$page_id = get_queried_object_id();
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
	<div data-dropdown-backdrop></div>
	<a class="skip-link screen-reader-text" href="#content"><?php echo esc_html( $wmf_skip2_content_label ); ?></a>
<div class="mobile-cover"></div>
<div id="page" class="site">
	<header class="<?php echo esc_attr( wmf_get_header_container_class() ); ?>">
		<div class="top-nav">
			<div class="site-main-nav flex flex-medium flex-align-center mw-980">
				<div class="logo-container logo-container_lg">
					<?php get_template_part( 'template-parts/header/logo' ); ?>
				</div>
				<div class="logo-container logo-container_sm">
					<button class="mobile-nav-toggle bold" aria-label="<?php echo esc_attr( $wmf_toggle_menu_label ); ?>">
						<span class="btn-label-a11y"><?php echo esc_html( $wmf_toggle_menu_label ); ?></span>
						<?php wmf_show_icon( 'menu', 'material' ); ?>
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/src/svg/close.svg" alt="" class="icon-close">
					</button>
					<?php get_template_part( 'template-parts/header/logo' ); ?>
				</div>
				<div class="top-nav-buttons">
					<?php if ( ! empty( $wmf_translations ) ) : ?>
						<?php
							$selected = array_reduce($wmf_translations, function($carry, $item) {
								if (is_string($carry)) {
									return $carry;
								}

								return $item['selected'] ? esc_html( $item['shortname'] ) : null;
							}, null);
						?>
						<div class="language-switcher" data-dropdown="language-switcher" data-open="false">
							<button class="language-switcher__button" data-dropdown-toggle="language-switcher" aria-expanded="false">
								<span class="btn-label-a11y"><?php echo esc_html( $wmf_current_language_label ); ?> </span>
								<?php echo wmf_show_icon( 'translate', 'language-switcher__icon' ); ?>
								<span class="language-switcher__label"><?php echo $selected; ?></span>
							</button>
							<div data-dropdown-content="language-switcher" class="language-switcher__content" hidden>
								<ul>
									<?php foreach ( $wmf_translations as $wmf_index => $wmf_translation ) : ?>
										<li class="language-switcher__language <?php echo $wmf_translation['selected'] ? 'language-switcher__language--selected' : '' ?>">
												<span lang="<?php echo esc_attr( $wmf_translation['shortname'] ); ?>">
													<a href="<?php echo esc_url( $wmf_translation['uri'] ); ?>">
														<span class="language-switcher__language-name">
															<?php echo esc_html( $wmf_translation['name'] ); ?>
														</span>
													</a>
												</span>
										</li>
									<?php endforeach ?>
								</ul>
							</div>
						</div>
					<?php endif; ?>
					<div class="donate-btn">
						<div class="donate-btn--desktop">
							<a href="<?php echo esc_url( $wmf_donate_uri ); ?>&utm_source=<?php echo esc_attr( $page_id ); ?>">
								<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/src/svg/lock-pink.svg" alt="" class="secure">
								<?php echo esc_html( $wmf_donate_button );?>
							</a>
						</div>
						<div class="donate-btn--mobile">
							<a href="<?php echo esc_url( $wmf_donate_uri ); ?>&utm_source=<?php echo esc_attr( $page_id ); ?>">
								<img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/src/svg/heart-pink.svg" alt="<?php echo esc_attr( $wmf_donate_button ); ?>">
							</a>
						</div>
					</div>
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

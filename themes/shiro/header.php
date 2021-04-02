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
$wmf_translations         = array_filter( wmf_get_translations(), function ( $translation ) {
	return $translation['uri'] !== '';
} );

$wmf_donate_button = get_theme_mod( 'wmf_donate_now_copy', __( 'Donate', 'shiro' ) );
$wmf_donate_uri    = get_theme_mod( 'wmf_donate_now_uri', 'https://donate.wikimedia.org/?utm_medium=wmfSite&utm_campaign=comms' );
$wmf_toggle_menu_label = get_theme_mod( 'wmf_toggle_menu_label', __( 'Toggle menu', 'shiro' ) );
$wmf_skip2_content_label = get_theme_mod( 'wmf_skip2_content_label', __( 'Skip to content', 'shiro' ) );
$wmf_skip2_navigation_label = get_theme_mod( 'wmf_skip2_navigation_label', __( 'Skip to navigation', 'shiro' ) );
$wmf_select_language_label = get_theme_mod( 'wmf_select_language_label', __( 'Select language', 'shiro' ) );
$wmf_current_language_label = get_theme_mod( 'wmf_current_language_label', __( 'Current language:', 'shiro' ) );

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
								<svg class="language-switcher__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
									<title>
										language
									</title>
									<path d="M20 18h-1.44a.61.61 0 0 1-.4-.12.81.81 0 0 1-.23-.31L17 15h-5l-1 2.54a.77.77 0 0 1-.22.3.59.59 0 0 1-.4.14H9l4.55-11.47h1.89zm-3.53-4.31L14.89 9.5a11.62 11.62 0 0 1-.39-1.24q-.09.37-.19.69l-.19.56-1.58 4.19zm-6.3-1.58a13.43 13.43 0 0 1-2.91-1.41 11.46 11.46 0 0 0 2.81-5.37H12V4H7.31a4 4 0 0 0-.2-.56C6.87 2.79 6.6 2 6.6 2l-1.47.5s.4.89.6 1.5H0v1.33h2.15A11.23 11.23 0 0 0 5 10.7a17.19 17.19 0 0 1-5 2.1q.56.82.87 1.38a23.28 23.28 0 0 0 5.22-2.51 15.64 15.64 0 0 0 3.56 1.77zM3.63 5.33h4.91a8.11 8.11 0 0 1-2.45 4.45 9.11 9.11 0 0 1-2.46-4.45z" fill="currentColor"/>
								</svg>
								<span class="language-switcher__label"><?php echo $selected; ?></span>
								<svg xmlns="http://www.w3.org/2000/svg" class="language-switcher__caret" width="12" height="12" viewBox="0 0 12 12">
									<title>
										down
									</title>
									<path d="M10.085 2.943L6.05 6.803l-3.947-3.86L1.05 3.996l5 5 5-5z" fill="currentColor"/>
								</svg>
							</button>
							<div data-dropdown-content class="language-switcher__content" hidden>
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

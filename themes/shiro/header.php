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
$wmf_toggle_menu_label = get_theme_mod( 'wmf_toggle_menu_label', __( 'Toggle menu', 'shiro' ) );
$wmf_skip2_content_label = get_theme_mod( 'wmf_skip2_content_label', __( 'Skip to content', 'shiro' ) );
$wmf_skip2_navigation_label = get_theme_mod( 'wmf_skip2_navigation_label', __( 'Skip to navigation', 'shiro' ) );
$wmf_select_language_label = get_theme_mod( 'wmf_select_language_label', __( 'Select language', 'shiro' ) );
$wmf_current_language_label = get_theme_mod( 'wmf_current_language_label', __( 'Current language:', 'shiro' ) );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
<script type="text/javascript">
  var EARTH_DAY_LIVE_OPTIONS = {
    /**
     * Specify view cookie expiration. After initial view, widget will not be
     * displayed to a user again until after this cookie expires. Defaults to 
     * one day.
     */
    cookieExpirationDays: 1, // @type {number}
    
    /**
     * Set the language of the widget. We currently support:
     * 'en': English
     * 'es': Spanish
     * Defaults to null, which will obey the navigator.language setting of the 
     * viewer's browser.
     */
     language: null, // @type {string}
     
     /**
     * Allows you to set a &referrer= URL parameter in the link to earthdaylive2020.org. Use by Action Network
     * forms on the earthdaylive2020.org website. 
     **/
     partnerReferrer: 'wikimediafoundation', //@type {string}
    
     /**
     * Allow you to override the iFrame hostname. Defaults to https://widget.earthdaylive2020.org
     */
     iframeHost: 'https://wikimediafoundation-org-develop.go-vip.co/wp-content/themes/shiro/assets/dist/earthdaylive/', // @type {string}

      
    /**
     * Prevents the widget iframe from loading Google Analytics. Defaults to
     * false. (Google Analytics will also be disabled if doNotTrack is set on
     * the user's browser.)
     */
    disableGoogleAnalytics: true, // @type {boolean}

    /**
     * Always show the widget, even when someone has closed the widget and set the cookie on their device. 
     * Useful for testing. Defaults to false.
     */
    alwaysShowWidget: false, // @type {boolean}

    /**
     * Automatically makes the widget full page. Defaults to false.
     */
    forceFullPageWidget: false, // @type {boolean}
    
    /**
    * For the full page widget, shows a close button "x" and hides the message about the site being 
    * available tomorrow. Defaults to false.
    */
    showCloseButtonOnFullPageWidget: true, // @type {boolean}
    
    /**
     * The date when the sticky footer widget should start showing on your web site.
     * Note: the month is one integer less than the number of the month. E.g. 8 is September, not August.
     * Defaults to new Date(2020, 0, 1) (January 1st, 2020).
     */
    footerDisplayStartDate: new Date(), //@ type {Date object}
    
    /**
     * The date when the full page widget should showing on your web site for 24 hours. 
     * Note: the month is one integer less than the number of the month. E.g. 8 is September, not August.
     * Defaults to new Date(2020, 3, 22) (April 22nd, 2020)
     */
    fullPageDisplayStartDate: new Date(2020, 3, 22), //@ type {Date object}
  };
</script>
<script src="/wp-content/themes/shiro/assets/dist/earthdaylive/widget.js" async></script>
</head>

<body <?php body_class(); ?>>
<div class="mobile-cover"></div>
<div id="page" class="site">
	<header class="<?php echo esc_attr( wmf_get_header_container_class() ); ?>">
		<div class="top-nav">
			<div class="site-main-nav flex flex-medium flex-align-center mw-980">
				<a class="skip-link screen-reader-text" href="#content"><?php echo esc_html( $wmf_skip2_content_label ); ?></a>
				<a class="skip-link screen-reader-text" href="#menu-header-menu"><?php echo esc_html( $wmf_skip2_navigation_label ); ?></a>
				<div class="logo-container logo-container_lg">
					<?php get_template_part( 'template-parts/header/logo' ); ?>
				</div>
				<div class="logo-container logo-container_sm">
					<button class="mobile-nav-toggle bold" aria-label="<?php echo esc_attr( $wmf_toggle_menu_label ); ?>">
						<span class="btn-label-a11y"><?php echo esc_html( $wmf_toggle_menu_label ); ?></span>
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
						<div class="language-dropdown" role="navigation">
							<button aria-label="<?php echo esc_attr( $wmf_select_language_label ); ?>">
								<span class="btn-label-a11y"><?php echo esc_html( $wmf_current_language_label ); ?> </span>
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
                                            <span lang="<?php echo esc_attr( $wmf_translation['shortname'] ); ?>"><a href="<?php echo esc_url( $wmf_translation['uri'] ); ?>"><?php echo esc_html( $wmf_translation['name'] ); ?></a></span>
											<?php endif; ?>
										</li>
									<?php endforeach ?>
								</ul>
							</div>
						</div>
					<?php endif ?>
					<span class="donate-btn">
                        <img src="/wp-content/themes/shiro/assets/src/svg/lock-pink.svg" alt="" class="secure">
						<a href="<?php echo esc_url($wmf_donate_uri); ?>">
							<?php echo esc_html($wmf_donate_button);?>
						</a>
					</span>
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

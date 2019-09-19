<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/fightforthefuture
 * @since      1.0.0
 *
 * @package    Digital_Climate_Strike_WP
 * @subpackage Digital_Climate_Strike_WP/public/partials
 */

$options = get_option($this->plugin_name);
$show_digital_strike_widget = (int) $options['show_digital_strike_widget'];
$language = (string) $options['language'];
$cookie_expiration_days = (int) $options['cookie_expiration_days'];
$iframe_host = (string) $options['iframe_host'];
$disable_google_analytics = (bool) $options['disable_google_analytics'];
$always_show_widget = (bool) $options['always_show_widget'];
$force_full_page_widget = (bool) $options['force_full_page_widget'];
$show_close_button_on_full_page_widget = (bool) $options['show_close_button_on_full_page_widget'];
$footer_display_start_date = (string) $options['footer_display_start_date'];
$full_page_display_start_date = (string) $options['full_page_display_start_date'];

?>
<?php if ($show_digital_strike_widget): ?>
    <script>
        var DIGITAL_CLIMATE_STRIKE_OPTIONS = {
            /**
             * Set the language of the widget. We currently support:
             * 'en': English
             * 'de': German
             * 'es': Spanish
             * 'cs': Czech
             * 'fr': French
             * 'nl': Dutch
             * 'tr': Turkish
             * 'pt': Portuguese
             * Defaults to null, which will obey the navigator.language setting of the
             * viewer's browser.
             */
            language: <?= json_encode($language) ?>, // @type {string}
            /**
             * Specify view cookie expiration. After initial view, widget will not be
             * displayed to a user again until after this cookie expires. Defaults to
             * one day.
             */
            cookieExpirationDays: 1, // @type {number}

            /**
             * Allow you to override the iFrame hostname. Defaults to https://assets.digitalclimatestrike.net
             */
            iframeHost: <?= json_encode($iframe_host, JSON_UNESCAPED_SLASHES) ?>, // @type {string}

            /**
             * Prevents the widget iframe from loading Google Analytics. Defaults to
             * false. (Google Analytics will also be disabled if doNotTrack is set on
             * the user's browser.)
             */
            disableGoogleAnalytics: <?= json_encode($disable_google_analytics) ?>, // @type {boolean}

            /**
             * Always show the widget. Useful for testing. Defaults to false.
             */
            alwaysShowWidget: <?= json_encode($always_show_widget) ?>, // @type {boolean}

            /**
             * Automatically makes the widget full page. Defaults to false.
             */
            forceFullPageWidget: <?= json_encode($force_full_page_widget) ?>, //@type {boolean}

            /**
             * For the full page widget, shows a close button "x" and hides the message about the site being
             * available tomorrow. Defaults to false.
             */
            showCloseButtonOnFullPageWidget: <?= json_encode($show_close_button_on_full_page_widget) ?>, // @type {boolean}

            /**
             * The date when the sticky footer widget should start showing on your web site.
             * Note: the month is one integer less than the number of the month. E.g. 8 is September, not August.
             * Defaults to new Date() (Today).
             */
            footerDisplayStartDate: new Date(
                <?= $this->get_date_field('Y', $footer_display_start_date) ?>,
                <?= $this->get_date_field('m', $footer_display_start_date) ?>,
                <?= $this->get_date_field('d', $footer_display_start_date) ?>
            ),

            /**
             * The date when the full page widget should showing on your web site for 24 hours.
             * Note: the month is one integer less than the number of the month. E.g. 8 is September, not August.
             * Defaults to new Date(2019, 8, 20) (September 20th, 2019)
             */
            fullPageDisplayStartDate: new Date(
                <?= $this->get_date_field('Y', $full_page_display_start_date) ?>,
                <?= $this->get_date_field('m', $full_page_display_start_date) ?>,
                <?= $this->get_date_field('d', $full_page_display_start_date) ?>
            )
        }
    </script>

    <script src="https://assets.digitalclimatestrike.net/widget.js" async></script>
<?php endif; ?>

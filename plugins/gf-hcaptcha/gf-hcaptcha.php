<?php
/*
   Plugin Name: G-Forms hCaptcha
   Description: A new way to monetize your site traffic with the hCaptcha addon for Gravity Forms.
   Version: 1.3.1
   Author: Web & App Easy B.V.
   Author URI: https://www.webandappeasy.com
   Text Domain: gf-hcaptcha
   License: GPL2
*/

define( 'HCAPTCHA_ADDON_VERSION', '1.3.1' );

// Load the translation files
add_action( 'init', function() {
    load_plugin_textdomain( 'gf-hcaptcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );

// Load the addon
add_action( 'gform_loaded', array( 'hCAPTCHA_AddOn', 'load' ), 5 );
class hCAPTCHA_AddOn {
    public static function load() {
        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }

        require_once( 'class-hcaptchaaddon.php' );
        GFAddOn::register( 'hCAPTCHAAddOn' );
    }
}

function hCapctcha_addon() {
    return hCAPTCHAAddOn::get_instance();
}
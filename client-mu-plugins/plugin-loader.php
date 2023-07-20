<?php
/*
 * We recommend all plugins for your site are
 * loaded in code, either from a file like this
 * one or from your theme (if the plugins are
 * specific to your theme and do not need to be
 * loaded as early as this in the WordPress boot
 * sequence.
 *
 * @see https://vip.wordpress.com/documentation/vip-go/understanding-your-vip-go-codebase/
 */

// Wikimedia Foundation REST API.
require_once __DIR__ . '/wmf-rest-api/wmf-rest-api.php';

// wpcom_vip_load_plugin( 'plugin-name' );
// Note the above requires a specific naming structure: /plugin-name/plugin-name.php
// You can also specify a specific root file: wpcom_vip_load_plugin( 'plugin-name/plugin.php' );
wpcom_vip_load_plugin( 'asset-loader' );
wpcom_vip_load_plugin( 'co-authors-plus' );
wpcom_vip_load_plugin( 'fieldmanager' );
wpcom_vip_load_plugin( 'gravityforms' );
wpcom_vip_load_plugin( 'maintenance-mode' );
wpcom_vip_load_plugin( 'safe-redirect-manager' );
wpcom_vip_load_plugin( 'safe-svg' );
wpcom_vip_load_plugin( 'hm-gutenberg-tools/plugin.php' );

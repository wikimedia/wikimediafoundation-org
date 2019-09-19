<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/fightforthefuture
 * @since             1.0.0
 * @package           Digital_Climate_Strike_WP
 *
 * @wordpress-plugin
 * Plugin Name:       Digital Climate Strike WP
 * Plugin URI:        https://github.com/fightforthefuture/digital-climate-strike-wp
 * Description:       This plugin allows you to easily add the Digital #ClimateStrike widget to you wordpress site.
 * Version:           1.0.0
 * Author:            Fight For the Future
 * Author URI:        https://github.com/fightforthefuture
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       digital-climate-strike-wp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DIGITAL_CLIMATE_STRIKE_WP_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-digital-climate-strike-wp-activator.php
 */
function activate_digital_climate_strike_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-digital-climate-strike-wp-activator.php';
	Digital_Climate_Strike_WP_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-digital-climate-strike-wp-deactivator.php
 */
function deactivate_digital_climate_strike_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-digital-climate-strike-wp-deactivator.php';
	Digital_Climate_Strike_WP_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_digital_climate_strike_wp' );
register_deactivation_hook( __FILE__, 'deactivate_digital_climate_strike_wp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-digital-climate-strike-wp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_digital_climate_strike_wp() {

	$plugin = new Digital_Climate_Strike_WP();
	$plugin->run();

}
run_digital_climate_strike_wp();

<?php
/**
 * Plugin action links.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Action_Links\Plugin
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Action_Links\Plugin;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Action_Links\Plugin
 */
class Controller extends Base {

	public function init() {
		Settings::instance()->init();

		$plugin_file = plugin_basename( WPMUDEV_BLC_PLUGIN_FILE );

		add_filter( "plugin_action_links_{$plugin_file}", array( $this, 'action_links' ), 10, 4 );
		add_filter( "network_admin_plugin_action_links_{$plugin_file}", array( $this, 'action_links' ), 10, 4 );
	}

	/**
	 * Sets the plugin action links in plugins page.
	 *
	 * @param array $actions
	 * @param string $plugin_file
	 * @param array $plugin_data
	 * @param string $context
	 *
	 * @return array
	 */
	public function action_links( $actions = array(), $plugin_file = '', $plugin_data = null, $context = '' ) {
		$new_actions = array();

		if ( ! is_array( $actions ) ) {
			$actions = array();
		}

		if ( boolval( Settings::instance()->get( 'use_legacy_blc_version' ) ) ) {
			$new_actions = $this->legacy_action_links();
		} else {
			$new_actions = $this->get_action_links();
		}

		return apply_filters(
			'wpmudev_blc_plugin_action_links',
			wp_parse_args( $actions, $new_actions ),
			$new_actions,
			$actions,
			$plugin_file,
			$plugin_data,
			$context
		);
	}

	/**
	 * Returns the plugin's action links.
	 *
	 * @return array
	 */
	public function get_action_links () {
		$actions = array();
		$dashboard_url   = menu_page_url( 'blc_dash', false );
		$dashboard_label = esc_html__( 'Dashboard', 'broken-link-checker' );
		$docs_url = 'https://wpmudev.com/docs/wpmu-dev-plugins/broken-link-checker';
		$docs_label = esc_html__( 'Docs', 'broken-link-checker' );

		if ( is_multisite() && Utilities::is_network_admin() ) {
			$admin_url     = get_admin_url( get_main_site_id(), 'admin.php' );
			$dashboard_url = add_query_arg(
				array(
					'page' => 'blc_dash',
				),
				$admin_url
			);
		}

		$actions['dashboard'] = "<a href=\"{$dashboard_url}\">{$dashboard_label}</a>";
		$actions['docs'] = "<a href=\"{$docs_url}\" target=\"_blank\">{$docs_label}</a>";

		return $actions;
	}

	/**
	 * Returns the plugin action links in plugins page when legacy mode is active.
	 *
	 * @return array
	 */
	public function legacy_action_links() {
		$actions = array();
		$dashboard_url   = menu_page_url( 'blc_dash', false );
		$settings_url    = menu_page_url( 'link-checker-settings', false );
		$link_url        = menu_page_url( 'view-broken-links', false );
		$dashboard_label = esc_html__( 'Dashboard', 'broken-link-checker' );
		$settings_label  = esc_html__( 'Settings', 'broken-link-checker' );
		$links_label     = esc_html__( 'Broken links', 'broken-link-checker' );


		if ( is_multisite() && Utilities::is_network_admin() ) {
			$admin_url     = get_admin_url( get_main_site_id(), 'admin.php' );
			$dashboard_url = add_query_arg(
				array(
					'page' => 'blc_dash',
				),
				$admin_url
			);
			$settings_url  = add_query_arg(
				array(
					'page' => 'link-checker-settings',
				),
				$admin_url
			);
			$link_url      = add_query_arg(
				array(
					'page' => 'view-broken-links',
				),
				$admin_url
			);
		}

		$actions['dashboard'] = "<a href=\"{$dashboard_url}\">{$dashboard_label}</a>";
		$actions['settings']  = "<a href=\"{$settings_url}\">{$settings_label}</a>";
		$actions['links']     = "<a href=\"{$link_url}\">{$links_label}</a>";

		if ( is_multisite() && ! ( is_main_site() || Utilities::is_network_admin() ) ) {
			unset( $actions['dashboard'] );
		}

		return $actions;
	}

}

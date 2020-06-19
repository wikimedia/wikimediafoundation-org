<?php
/**
 * MultilingualPress 2 to 3 Migration.
 *
 * @package MultilingualPress2to3
 * @wordpress-plugin
 *
 * Plugin Name: MultilingualPress 2 to 3 Migration
 * Description: A WP plugin that allows migrating data from MultilingualPress version 2 to version 3.
 * Version: [*next-version*]
 * Author: Inpsyde
 * License: GPLv2+.
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mlp2to3
 * Domain Path: /languages
 */

namespace Inpsyde\MultilingualPress2to3;

use Inpsyde\MultilingualPress2to3\Handler\HandlerInterface;
use Throwable;

define('MLP2TO3_BASE_PATH', __FILE__);
define('MLP2TO3_BASE_DIR', dirname(MLP2TO3_BASE_PATH));

/**
 * Retrieves the plugin singleton.
 *
 * @return null|HandlerInterface
 */
function handler()
{
    static $instance = null;

    if (is_null($instance)) {
        $bootstrap = require MLP2TO3_BASE_DIR . '/bootstrap.php';

        $instance = $bootstrap([
            'version'           => '[*next-version*]',
            'root_path'         => ABSPATH,
            'base_path'         => MLP2TO3_BASE_PATH,
            'root_url'          => get_bloginfo('url'),
            'base_url'          => plugins_url('', MLP2TO3_BASE_PATH),
            'admin_url'         => get_admin_url(),
            'is_debug'          => WP_DEBUG,
            'main_site_id'      => (defined('BLOG_ID_CURRENT_SITE')
                    ? (int) BLOG_ID_CURRENT_SITE
                    : 1),
        ]);
    }

    return $instance;
}

(function ($isDebugDisplay, $isDebugLog) {
    try {
        handler()->run();
    } catch (Throwable $e) {
        if ($isDebugLog) {
            error_log($e->getMessage());
        }

        if ($isDebugDisplay) {
            throw $e;
        }
    }
})(
    !defined(WP_DEBUG) || (WP_DEBUG && WP_DEBUG_DISPLAY), // Not in WP, or `WP_DEBUG_DISPLAY` is on
    defined(WP_DEBUG) && (WP_DEBUG && WP_DEBUG_DISPLAY) // In WP, and `WP_DEBUG_LOG` is on
);

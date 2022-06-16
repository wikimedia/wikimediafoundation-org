<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\SiteDuplication;

/**
 * Handles (de)activation of all active plugins.
 */
class ActivePlugins
{
    /**
     * Fires the plugin activation hooks for all active plugins.
     *
     * @return int
     */
    public function activate(): int
    {
        /** @var string[] $plugins */
        $plugins = get_option('active_plugins');
        if (!$plugins) {
            return 0;
        }

        foreach ($plugins as $plugin) {
            if (!$plugin || !is_string($plugin)) {
                continue;
            }

            /** This action is documented in wp-admin/includes/plugin.php. */
            do_action('activate_plugin', $plugin, false);

            /** This action is documented in wp-admin/includes/plugin.php. */
            do_action("activate_{$plugin}", false);

            /** This action is documented in wp-admin/includes/plugin.php. */
            do_action('activated_plugin', $plugin, false);
        }

        return count($plugins);
    }

    /**
     * Deactivates all plugins.
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        return get_option('active_plugins') === [] || update_option('active_plugins', []);
    }
}

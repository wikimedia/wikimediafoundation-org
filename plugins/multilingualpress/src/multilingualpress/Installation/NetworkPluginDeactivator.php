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

namespace Inpsyde\MultilingualPress\Installation;

/**
 * Deactivates plugins network-wide by matching (partial) base names against all active plugins.
 */
class NetworkPluginDeactivator
{
    /**
     * Deactivates the given plugins network-wide.
     *
     * @param string[] $plugins
     * @return string[]
     */
    public function deactivatePlugins(string ...$plugins): array
    {
        $active = (array)get_network_option(
            0,
            'active_sitewide_plugins',
            []
        );

        $toDeactivate = $this->filterOutNotActive(array_keys($active), ...$plugins);
        if (!$toDeactivate) {
            return $toDeactivate;
        }

        update_network_option(
            0,
            'active_sitewide_plugins',
            array_diff_key($active, array_flip($toDeactivate))
        );

        return $toDeactivate;
    }

    /**
     * @param string[] $activePlugins
     * @param string[] $targetPlugins
     * @return array
     */
    private function filterOutNotActive(array $activePlugins, string ...$targetPlugins): array
    {
        $toDeactivate = [];
        foreach ($targetPlugins as $target) {
            foreach ($activePlugins as $active) {
                (strpos($active, $target) !== false) and $toDeactivate[] = $target;
            }
        }

        return $toDeactivate;
    }
}

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

use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\SemanticVersionNumber;

/**
 * Updates any installed plugin data to the current version.
 */
class Updater
{
    /**
     * @var PluginProperties
     */
    private $pluginProperties;

    /**
     * @param PluginProperties $pluginProperties
     */
    public function __construct(PluginProperties $pluginProperties)
    {
        $this->pluginProperties = $pluginProperties;
    }

    /**
     * Updates any installed plugin data to the current version.
     *
     * @param SemanticVersionNumber $installedVersion
     */
    public function update(SemanticVersionNumber $installedVersion)
    {
        if (SemanticVersionNumber::FALLBACK_VERSION === (string)$installedVersion) {
            return;
        }
    }

    /**
     * Will perform the necessary rewrites when the plugin is upgraded.
     *
     * When the plugin is upgraded, we need to fix the permalink rewrites.
     *
     * @see https://developer.wordpress.org/reference/hooks/upgrader_process_complete/ upgrader_process_complete
     *
     * @param \WP_Upgrader $upgraderObject
     * @param array $options
     */
    public function rewriteRulesAfterPluginUpgrade(\WP_Upgrader $upgraderObject, array $options)
    {
        $action = $options['action'] ?? '';
        $type = $options['type'] ?? '';

        if ($action !== 'update' || $type !== 'plugin' || empty($options['plugins'])) {
            return;
        }

        foreach ($options['plugins'] as $plugin) {
            ($plugin === $this->pluginProperties->basename()) and flush_rewrite_rules();
        }
    }
}
